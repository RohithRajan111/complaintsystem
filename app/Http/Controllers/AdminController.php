<?php

namespace App\Http\Controllers;

use App\Jobs\ExportComplaintsJob;
use App\Models\ActionLog;
use App\Models\Complaint;
use App\Models\Complaint_Response;
use App\Models\Dept;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer; // Corrected: Use the XLSX writer

class AdminController extends Controller
{

    public function complaintsDatatable(Request $request)
{
    $columns = [
        0 => 'complaints.id',
        1 => 'complaints.title',
        2 => 'complaints.status',
        3 => 'depts.Dept_name',
        4 => 'students.Stud_name',
        5 => 'complaints.created_at',
    ];

    $totalData = Complaint::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $orderColumnIndex = $request->input('order.0.column');
    $orderColumn = $columns[$orderColumnIndex] ?? 'complaints.id';
    $orderDir = $request->input('order.0.dir') ?? 'asc';

    $search = $request->input('search.value');

    $query = Complaint::select(
            'complaints.id',
            'complaints.title',
            'complaints.status',
            'depts.Dept_name as department_name',
            'students.Stud_name as student_name',
            'complaints.created_at'
        )
        ->join('students', 'complaints.student_id', '=', 'students.id')
        ->join('depts', 'complaints.Dept_id', '=', 'depts.id');

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('complaints.title', 'like', "%{$search}%")
              ->orWhere('complaints.status', 'like', "%{$search}%")
              ->orWhere('students.Stud_name', 'like', "%{$search}%")
              ->orWhere('depts.Dept_name', 'like', "%{$search}%");
        });

        $totalFiltered = $query->count();
    }

    $data = $query
        ->offset($start)
        ->limit($limit)
        ->orderBy($orderColumn, $orderDir)
        ->get();

    $json_data = [
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ];

    return response()->json($json_data);
}

public function complaintsDatatableEager(Request $request)
{
    $columns = [
        0 => 'id',
        1 => 'title',
        2 => 'status',
        3 => 'department.Dept_name',
        4 => 'student.Stud_name',
        5 => 'created_at',
    ];

    $limit = $request->input('length');
    $start = $request->input('start');
    $draw = intval($request->input('draw'));
    $orderColumnIndex = $request->input('order.0.column');
    $orderColumn = $columns[$orderColumnIndex] ?? 'id';
    $orderDir = $request->input('order.0.dir') ?? 'asc';
    $search = $request->input('search.value');

    // Cache total rows (without filter)
    $totalData = Cache::remember('complaints_total', 60, function () {
        return Complaint::count();
    });

    $query = Complaint::with([
            'student:id,Stud_name',
            'department:id,Dept_name'
        ])
        ->select('id', 'title', 'status', 'student_id', 'Dept_id', 'created_at');

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%");
        });

        // Filter by related model names (students/departments) after fetch
        $filtered = $query->get()->filter(function ($item) use ($search) {
            return str_contains(strtolower($item->student->Stud_name ?? ''), strtolower($search)) ||
                   str_contains(strtolower($item->department->Dept_name ?? ''), strtolower($search)) ||
                   true; // retain original matches
        });

        $totalFiltered = $filtered->count();
        $data = $filtered->slice($start)->take($limit)->values();
    } else {
        $totalFiltered = $totalData;
        $data = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();
    }

    // Format data for DataTables
    $formatted = $data->map(function ($item) {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'status' => $item->status,
            'department_name' => $item->department->Dept_name ?? '-',
            'student_name' => $item->student->Stud_name ?? '-',
            'created_at' => $item->created_at->toDateTimeString()
        ];
    });

    return response()->json([
        'draw' => $draw,
        'recordsTotal' => $totalData,
        'recordsFiltered' => $totalFiltered,
        'data' => $formatted,
    ]);
}


    public function complaintsDatatablesss()
    {
         $lastId = 999910; // or from previous page's last item
$query = Complaint::select(
        'complaints.id',
        'complaints.title',
        'complaints.status',
        'depts.Dept_name as department_name',
        'students.Stud_name as student_name',
        'complaints.created_at'
    )
    ->join('students', 'complaints.student_id', '=', 'students.id')
    ->join('depts', 'complaints.Dept_id', '=', 'depts.id')
    ->where('complaints.id', '>', $lastId)
    ->orderBy('complaints.id')
    ->limit(10);

return $query->get();
    }
    public function showadminlogin()
    {
        return view('auth.login');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // public function login(Request $request)
    // {
    //     $validated = $request->validate([
    //         'Admin_email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     $credentials = [
    //         'Admin_email' => $validated['Admin_email'],
    //         'password' => $validated['password'],
    //     ];

    //     if (Auth::guard('admins')->attempt($credentials)) {
    //         $request->session()->regenerate();

    //         return redirect()->route('showadmin.dashboard');
    //     }

    //     throw ValidationException::withMessages([
    //         'credentials' => 'Sorry, incorrect credentials.',
    //     ]);
    // }

    public function deleteComplaint($id)
    {
        try {
            $complaint = Complaint::findOrFail($id);
            $complaint->delete();

            ActionLog::create([
                'user_type' => 'admin',
                'user_id' => Auth::guard('admins')->id(),
                'complaint_id' => $id,
                'action' => "Admin deleted a complaint titled: {$complaint->title}",
            ]);

            return back()->with('success', 'Complaint deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting complaint: '.$e->getMessage());

            return back()->with('error', 'Failed to delete complaint.');
        }
    }

    public function revokeStudent(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->is_revoked = true;
            $student->save();

            ActionLog::create([
                'user_type' => 'admin',
                'user_id' => Auth::guard('admins')->id(),
                'complaint_id' => null,
                'action' => "Admin revoked student: {$student->Stud_name} ({$student->Stud_email})",
            ]);

            return back()->with('success', 'Student account has been revoked.');
        } catch (\Exception $e) {
            Log::error('Error revoking student: '.$e->getMessage());

            return back()->with('error', 'Failed to revoke student.');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admins')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

  public function ajaxComplaints(Request $request)
{
    try {
        $filteredQuery = Complaint::query()
            ->select('complaints.id')
            ->leftJoin('students', 'students.id', '=', 'complaints.Student_id')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('complaints.title', 'like', "%{$request->search}%")
                        ->orWhere('students.Stud_name', 'like', "%{$request->search}%"); 
                });
            })
            ->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
                $q->where('complaints.status', $request->status);
            })
            ->when($request->filled('dept'), function ($q) use ($request) {
                $q->where('complaints.Dept_id', $request->dept);
            })
            ->orderByRaw("
                CASE
                    WHEN complaints.status = 'pending' THEN 1
                    WHEN complaints.status = 'checking' THEN 2
                    WHEN complaints.status = 'solved' THEN 3
                    WHEN complaints.status = 'rejected' THEN 4
                    WHEN complaints.status = 'withdrawn' THEN 5
                    ELSE 6
                END ASC
            ")
            ->orderByDesc('complaints.created_at');

        $complaintPage = $filteredQuery->paginate(10)->appends($request->all());
        $complaintIds = $complaintPage->pluck('id')->all();

        $complaints = collect();
        if (!empty($complaintIds)) {
            $complaints = Complaint::with(['student:id,Stud_name', 'department:id,Dept_name'])
                ->select('id', 'title', 'status', 'Dept_id', 'Student_id', 'created_at')
                ->whereIn('id', $complaintIds)
                ->orderByRaw("FIELD(id, " . implode(',', $complaintIds) . ")")
                ->get();
        }

        $complaintPage->setCollection($complaints);

        $departments = Dept::select('id', 'Dept_name')->get();

        return view('admin.partials.complaints', [
            'complaints' => $complaintPage,
            'departments' => $departments,
        ])->render();

    } catch (\Exception $e) {
        Log::error('Error fetching complaints: ' . $e->getMessage());
        return response('Error loading complaints', 500);
    }
}







    public function ajaxComplaintsWithRange(Request $request)
    {
        try {
            $perPage = 10;
            $page = $request->get('page', 1);
            
            // Calculate offset using ID ranges instead of OFFSET
            $maxId = Complaint::max('id');
            $minId = Complaint::min('id');
            
            $query = Complaint::with(['student', 'department']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($q2) use ($search) {
                            $q2->where('Stud_name', 'like', "%{$search}%");
                        });
                });
            }
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            if ($request->filled('dept')) {
                $query->where('Dept_id', $request->dept);
            }

            if ($page > 1) {
                $previousMaxId = $maxId - (($page - 1) * $perPage);
                $query->where('id', '<=', $previousMaxId);
            }

            $query->orderBy('id', 'desc');
            $complaints = $query->take($perPage)->get();

            $hasMorePages = $complaints->count() == $perPage;
            $pagination = [
                'current_page' => $page,
                'has_more_pages' => $hasMorePages,
                'per_page' => $perPage,
                'total' => null 
            ];

            $departments = Dept::all();
            return view('admin.partials.complaints', compact('complaints', 'departments', 'pagination'))->render();

        } catch (\Exception $e) {
            Log::error('Error fetching complaints: '.$e->getMessage());
            return response('Error loading complaints', 500);
        }
    }

    public function ajaxLogs(Request $request)
    {
        try {
            $query = ActionLog::query();
            if ($request->filled('user_type')) {
                $query->where('user_type', $request->user_type);
            }
            if ($request->filled('action')) {
                $query->where('action', 'like', '%'.$request->action.'%');
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }
            $logs = $query->latest()->paginate(10)->appends($request->all());

            return view('admin.partials.logs', compact('logs'))->render();
        } catch (\Exception $e) {
            Log::error('Error fetching logs: '.$e->getMessage());

            return response('Error loading logs', 500);
        }
    }

    public function ajaxStudents(Request $request)
    {
        try {
            $query = Student::query();
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('Stud_name', 'like', "%{$search}%")
                        ->orWhere('Stud_email', 'like', "%{$search}%");
                });
            }
            
            // Use simplePaginate for better performance
            $students = $query->simplePaginate(10)->appends($request->all());
            return view('admin.partials.students', compact('students'))->render();
        } catch (\Exception $e) {
            Log::error('Error fetching students: '.$e->getMessage());
            return response('Error loading students', 500);
        }
    }

    public function ajaxDepartments(Request $request)
    {
        try {
            $query = Dept::query();
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('Dept_name', 'like', "%{$search}%");
            }
            $departments = $query->paginate(10)->appends($request->all());

            return view('admin.partials.departments', compact('departments'))->render();
        } catch (\Exception $e) {
            Log::error('Error fetching departments: '.$e->getMessage());

            return response('Error loading departments', 500);
        }
    }

    public function editDepartment($id)
    {
        try {
            $department = Dept::findOrFail($id);

            return response()->json($department);
        } catch (\Exception $e) {
            Log::error('Error fetching department: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch department'], 404);
        }
    }

    public function updateComplaintStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,checking,solved,rejected,withdrawn',
                'response' => 'nullable|string|max:1000',
            ]);

            $complaint = Complaint::findOrFail($id);
            $complaint->status = $validated['status'];
            $complaint->save();

            if (in_array($validated['status'], ['solved', 'rejected']) && $request->filled('response')) {
                // Use updateOrCreate to avoid duplicate responses for the same complaint
                Complaint_Response::updateOrCreate(
                    ['Complaint_id' => $complaint->id],
                    [
                        'Student_id' => $complaint->Student_id,
                        'Dept_id' => $complaint->Dept_id,
                        'response' => $validated['response'],
                    ]
                );
            }

            ActionLog::create([
                'user_type' => 'admin',
                'user_id' => Auth::guard('admins')->id(),
                'complaint_id' => $complaint->id,
                'action' => "Admin changed complaint status to: {$validated['status']}",
            ]);

            return redirect()->route('showadmin.dashboard')->with('success', 'Complaint status updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating complaint status: '.$e->getMessage());

            return back()->with('error', 'Failed to update complaint status.');
        }
    }

    public function getComplaintCounts()
    {
        try {
            // Use cache for 5 minutes to avoid repeated expensive queries
            $cacheKey = 'complaint_counts_' . date('Y-m-d-H-i');
            $counts = cache()->remember($cacheKey, 300, function () {
                return [
                    'total' => DB::table('complaints')->count(),
                    'pending' => DB::table('complaints')->where('status', 'pending')->count(),
                    'checking' => DB::table('complaints')->where('status', 'checking')->count(),
                    'solved' => DB::table('complaints')->where('status', 'solved')->count(),
                    'rejected' => DB::table('complaints')->where('status', 'rejected')->count(),
                    'withdrawn' => DB::table('complaints')->where('status', 'withdrawn')->count(),
                ];
            });

            return response()->json($counts);
        } catch (\Exception $e) {
            Log::error('Error fetching complaint counts: '.$e->getMessage());
            return response()->json(['error' => 'Failed to fetch complaint counts'], 500);
        }
    }

    public function getComplaintDetails($id)
    {
        try {
            $complaint = Complaint::with('responses')->findOrFail($id);
            $latestResponse = $complaint->responses->last();

            return response()->json(['success' => true, 'title' => $complaint->title, 'description' => $complaint->description, 'response_text' => $latestResponse ? $latestResponse->response : '']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Attempted to fetch details for non-existent complaint ID: {$id}");

            return response()->json(['success' => false, 'message' => 'Complaint not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching complaint details for ID {$id}: ".$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to fetch details.'], 500);
        }
    }

    public function updateDepartment(Request $request, $id)
    {
        $validated = $request->validate([
            'Dept_name' => 'required|string|max:255',
            'Hod_name' => 'required|string|max:255',
            'Dept_email' => 'required|email|max:255|unique:depts,Dept_email,'.$id,
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $department = Dept::findOrFail($id);
            $department->Dept_name = $validated['Dept_name'];
            $department->Hod_name = $validated['Hod_name'];
            $department->Dept_email = $validated['Dept_email'];

            if (! empty($validated['password'])) {
                $department->password = Hash::make($validated['password']);
            }

            $department->save();

            return response()->json(['message' => 'Department updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating department: '.$e->getMessage());

            return response()->json(['message' => 'Failed to update department.'], 500);
        }
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'Dept_name' => 'required|string|max:255|unique:depts,Dept_name',
            'Hod_name' => 'required|string|max:255',
            'Dept_email' => 'required|email|max:255|unique:depts,Dept_email',
            'password' => 'required|string|min:8',
        ]);

        try {
            Dept::create([
                'Dept_name' => $validated['Dept_name'],
                'Hod_name' => $validated['Hod_name'],
                'Dept_email' => $validated['Dept_email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json(['message' => 'Department created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error creating department: '.$e->getMessage());

            return response()->json(['message' => 'Failed to create department.'], 500);
        }
    }

    public function exportComplaintsToExcel(Request $request)
    {
        $user = Auth::user();

    // Collect all filter parameters from the request
    $filters = $request->only([
        'search', 'status', 'dept', 'start_id', 'end_id'
    ]);

    // Dispatch the job
    ExportComplaintsJob::dispatch($filters, $user);

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Your export has been started. You will receive an email with a download link when it is complete.');

    }
}
