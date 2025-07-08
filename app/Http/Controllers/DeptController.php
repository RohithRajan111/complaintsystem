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

  public function dashboard(Request $request)
    {
        try {
            $department = Auth::guard('depts')->user();
            Log::info('Department dashboard accessed', ['dept_id' => $department->id]);

            // --- CHANGE 1: Define a base query that is NEVER filtered by the request ---
            $departmentBaseQuery = Complaint::where('Dept_id', $department->id);

            // Calculate stats from the UNFILTERED base query to always show totals
            $statsResult = (clone $departmentBaseQuery)
                ->selectRaw("
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as new_count,
                    COUNT(CASE WHEN status = 'checking' THEN 1 END) as in_progress_count,
                    COUNT(CASE WHEN status = 'solved' AND updated_at >= ? THEN 1 END) as recently_resolved_count
                ", [now()->subDays(7)])
                ->first();

            $stats = [
                'new' => $statsResult->new_count ?? 0,
                'in_progress' => $statsResult->in_progress_count ?? 0,
                'recently_resolved' => $statsResult->recently_resolved_count ?? 0,
            ];

            // --- CHANGE 2: Create a new query for the list and apply ALL filters to it ---
            $listQuery = (clone $departmentBaseQuery)
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($sub) use ($search) {
                        $sub->where('title', 'like', "%{$search}%")
                            ->orWhereHas('student', function ($studentQuery) use ($search) {
                                $studentQuery->where('Stud_name', 'like', "%{$search}%");
                            });
                    });
                })
                // --- THIS IS THE NEW FILTER LOGIC ---
                ->when($request->filled('status'), function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
                // --- END OF NEW FILTER LOGIC ---


            // The rest of your excellent logic now uses the filtered $listQuery
            $sort = $request->get('sort', 'default');
            $idQuery = (clone $listQuery)->select('complaints.id'); // Use the filtered query

            if ($sort === 'default') {
                $idQuery->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'checking' THEN 2 WHEN status = 'solved' THEN 3 WHEN status = 'rejected' THEN 4 WHEN status = 'withdrawn' THEN 5 ELSE 6 END ASC");
            } elseif ($sort === 'newest') {
                $idQuery->orderByDesc('created_at');
            } elseif ($sort === 'oldest') {
                $idQuery->orderBy('created_at', 'asc');
            } elseif ($sort === 'title_asc') {
                $idQuery->orderBy('title', 'asc');
            } elseif ($sort === 'title_desc') {
                $idQuery->orderBy('title', 'desc');
            }

            $idQuery->orderByDesc('created_at');

            // Paginate IDs
            $complaintPage = $idQuery->paginate(10)->appends($request->all()); // appends() is crucial here
            $complaintIds = $complaintPage->pluck('id')->all();

            // Get full models
            $complaintsCollection = collect();
            if (!empty($complaintIds)) {
                $complaintsCollection = Complaint::with(['student:id,Stud_name,Stud_email'])
                    ->whereIn('id', $complaintIds)
                    ->orderByRaw("FIELD(id, " . implode(',', $complaintIds) . ")")
                    ->get();
            }

            $complaintPage->setCollection($complaintsCollection);

            Log::info('Paginated complaints fetched with filters', [
                'count' => $complaintPage->count(),
                'search' => $request->search,
                'status' => $request->status, 
                'sort' => $sort,
            ]);

            return view('dept.dashboard', [
                'complaints' => $complaintPage,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching department dashboard data: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error loading dashboard data. Please try again later.');
        }
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

            SendComplaintResponseNotification::dispatch($complaint, $response);
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
        $department = Auth::guard('depts')->user();

        return view('dept.profile', compact('department'));
    }

   
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
