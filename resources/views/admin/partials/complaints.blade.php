<div id="complaintFilterBar" class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col sm:flex-row gap-3 items-center flex-wrap">
            <select id="statusFilter" class="w-full sm:w-auto bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="all">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="checking" {{ request('status') == 'checking' ? 'selected' : '' }}>In Progress</option>
                <option value="solved" {{ request('status') == 'solved' ? 'selected' : '' }}>Resolved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
            </select>
            <select id="deptFilter" class="w-full sm:w-auto bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('dept') == $dept->id ? 'selected' : '' }}>{{ $dept->Dept_name }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 bg-surface border border-border-color rounded-md shadow-sm p-1">
                <label for="startId" class="pl-2 text-xs text-subtle">ID Range:</label>
                <input type="number" id="startId" placeholder="Start" class="w-20 border-0 rounded-md p-1 text-sm focus:ring-0">
                <span class="text-subtle">-</span>
                <input type="number" id="endId" placeholder="End" class="w-20 border-0 rounded-md p-1 text-sm focus:ring-0">
            </div>
            <a id="downloadExcelBtn" href="{{ route('admin.complaints.export') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-sm font-semibold shadow">
                Download Excel
            </a>
        </div>
        <div class="flex gap-2">
            <input id="complaintSearch" type="text" placeholder="Search by title or student..." class="w-full sm:w-64 bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary" value="{{ request('search') }}">
            <button id="searchButton" class="bg-strong hover:opacity-90 text-white px-4 py-2 rounded-md text-sm font-semibold shadow">Search</button>
        </div>
    </div>
</div>

<div class="overflow-x-auto bg-surface rounded-xl border border-border-color/70 shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="border-b-2 border-border-color bg-slate-50">
            <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">Title</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">Department</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">Submitted By</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">Created</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-strong uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $complaint)
                <tr class="border-b border-border-color last:border-b-0 hover:bg-slate-50/50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-strong">{{ $complaint->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-subtle">{{ Str::limit($complaint->title, 40) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @switch($complaint->status)
                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                @case('checking') bg-blue-100 text-blue-800 @break
                                @case('solved') bg-green-100 text-green-800 @break
                                @case('rejected') bg-red-100 text-red-800 @break
                                @case('withdrawn') bg-gray-200 text-gray-700 @break
                            @endswitch">
                            {{ ucfirst($complaint->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-subtle">{{ $complaint->department->Dept_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-subtle">{{ $complaint->student->Stud_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-subtle">{{ $complaint->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-4">
                            <button 
                                class="response-btn text-primary hover:underline font-semibold"
                                data-id="{{ $complaint->id }}" 
                                data-status="{{ $complaint->status }}"
                                data-action-url="{{ route('admin.complaints.updateStatus', $complaint->id) }}">
                                Response
                            </button>
                            <form action="{{ route('admin.complaint.delete', $complaint->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this complaint?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger hover:opacity-80">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-subtle">No complaints found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $complaints->links() }}
</div>