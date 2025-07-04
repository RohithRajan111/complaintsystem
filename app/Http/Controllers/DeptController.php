<?php

namespace App\Http\Controllers;

use App\Jobs\SendComplaintResponseNotification;
use App\Models\ActionLog;
use App\Models\Complaint;
use App\Models\Complaint_Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DeptController extends Controller
{
    public function showlogin()
    {
        Log::info('Dept login page accessed');

        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Dept login attempt', ['email' => $request->input('Dept_email')]);

        $validated = $request->validate([
            'Dept_email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'Dept_email' => $validated['Dept_email'],
            'password' => $validated['password'],
        ];

        if (Auth::guard('depts')->attempt($credentials)) {
            $request->session()->regenerate();

            Log::info('Dept login successful', ['dept_id' => Auth::guard('depts')->id()]);

            return redirect()->route('showdept.dashboard');
        }

        Log::warning('Dept login failed', ['email' => $validated['Dept_email']]);

        throw ValidationException::withMessages([
            'credentials' => 'Sorry, incorrect credentials.',
        ]);
    }

    public function dashboard()
    {
        $department = Auth::guard('depts')->user();
        Log::info('Department dashboard accessed', ['dept_id' => $department->id]);

        // --- START: MODIFIED SECTION ---
        // Get the base query for the department's complaints
        $complaintsQuery = Complaint::where('Dept_id', $department->id);

        // Calculate stats for the cards
        $stats = [
            'new' => (clone $complaintsQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $complaintsQuery)->where('status', 'checking')->count(),
            'recently_resolved' => (clone $complaintsQuery)->where('status', 'solved')
                ->where('updated_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // Paginate the full list of complaints for the table
        $complaints = $complaintsQuery->with(['student', 'responses'])
            ->orderByRaw("
            CASE 
                WHEN status = 'pending' THEN 1
                WHEN status = 'checking' THEN 2
                ELSE 3
            END
        ")
            ->latest()
            ->paginate(10); // Changed to 10 for better view

        Log::info('Paginated complaints fetched for dashboard', ['count' => $complaints->count(), 'page' => $complaints->currentPage()]);

        // Pass both the paginated complaints and the stats to the view
        return view('dept.dashboard', compact('complaints', 'stats'));
        // --- END: MODIFIED SECTION ---
    }

    public function show($id)
    {
        $complaint = Complaint::with(['student', 'responses'])->findOrFail($id);

        if ($complaint->Dept_id !== Auth::guard('depts')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($complaint);
    }

    public function respond(Request $request, $id)
    {
        $complaint = Complaint::with(['student', 'department'])->findOrFail($id);

        if (in_array($complaint->status, ['solved', 'rejected', 'withdrawn'])) {
            Log::warning('Attempt to modify a closed complaint', ['complaint_id' => $id, 'dept_id' => Auth::guard('depts')->id()]);

            return response()->json(['success' => false, 'message' => 'This complaint is already closed and cannot be modified.'], 403);
        }

        Log::info('Department responding to complaint', ['dept_id' => Auth::guard('depts')->id(), 'complaint_id' => $id]);

        $request->validate([
            'status' => 'required|in:checking,solved,rejected',
            'response' => 'required_if:status,solved,rejected|string|nullable',
        ]);

        $complaint->status = $request->status;
        $complaint->save();

        if ($request->status !== 'checking') {
            $response = Complaint_Response::create([
                'Complaint_id' => $complaint->id,
                'Student_id' => $complaint->Student_id,
                'Dept_id' => $complaint->Dept_id,
                'response' => $request->input('response'),
            ]);

            Log::info('Complaint response saved', ['response_id' => $response->id]);

            // --- THIS IS THE CHANGE ---
            // Dispatch the job to send email in the background
            SendComplaintResponseNotification::dispatch($complaint, $response);
            // --- END OF CHANGE ---
        }

        ActionLog::create([
            'user_type' => 'department',
            'user_id' => auth()->guard('depts')->id(),
            'complaint_id' => $complaint->id,
            'action' => "Department updated complaint no:$complaint->id with status: $request->status",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully and student has been notified.',
            'new_status' => $complaint->status,
        ]);
    }

    public function showProfile()
    {
        // Get the currently authenticated department user
        $department = Auth::guard('depts')->user();

        return view('dept.profile', compact('department'));
    }

    /**
     * NEW: Update the department's profile information.
     */
    public function updateProfile(Request $request)
    {
        $department = Auth::guard('depts')->user();

        if (! $department) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'Dept_name' => 'required|string|max:255',
            'Hod_name' => 'required|string|max:255',
            'Dept_email' => 'required|string|email|max:255|unique:depts,Dept_email,'.$department->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // --- The rest of your code for logging and saving is perfect ---
        $originalDeptName = $department->Dept_name;
        $originalHodName = $department->Hod_name;
        $originalDeptEmail = $department->Dept_email;

        $department->Dept_name = $validated['Dept_name'];
        $department->Hod_name = $validated['Hod_name'];
        $department->Dept_email = $validated['Dept_email'];

        $passwordChanged = false;
        if (! empty($validated['password'])) {
            $department->password = Hash::make($validated['password']);
            $passwordChanged = true;
        }

        $updated = $department->save();

        if ($updated) {
            $changes = [];
            if ($originalDeptName !== $validated['Dept_name']) {
                $changes[] = "Department name changed from '{$originalDeptName}' to '{$validated['Dept_name']}'.";
            }
            if ($originalHodName !== $validated['Hod_name']) {
                $changes[] = "HOD name changed from '{$originalHodName}' to '{$validated['Hod_name']}'.";
            }
            if ($originalDeptEmail !== $validated['Dept_email']) {
                $changes[] = "Email changed from '{$originalDeptEmail}' to '{$validated['Dept_email']}'.";
            }
            if ($passwordChanged) {
                $changes[] = 'Password was changed.';
            }

            if (! empty($changes)) {
                $logMessage = 'Department updated their profile. ' . implode(' ', $changes);
                ActionLog::create([
                    'user_type' => 'department',
                    'user_id' => $department->id,
                    'complaint_id' => null,
                    'action' => $logMessage,
                ]);
            }
        }

        return redirect()->route('dept.profile.show')->with('success', 'Profile updated successfully!');
    }


    public function logout(Request $request)
    {
        $userId = Auth::guard('depts')->id();
        Log::info('Department logout', ['dept_id' => $userId]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
