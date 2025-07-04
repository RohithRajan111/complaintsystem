<div>
    <!-- Log Filter Bar -->
    <div id="logFilterBar" class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Filters (on the left) -->
            <div class="flex flex-wrap gap-3">
                <select id="logUserTypeFilter" class="w-full sm:w-auto bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary">
                    <option value="">All User Types</option>
                    <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="student" {{ request('user_type') == 'student' ? 'selected' : '' }}>Student</option>
                </select>
                <input type="date" id="logDateFilter" class="w-full sm:w-auto bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary" value="{{ request('date') }}">
            </div>
            
            <!-- Search Container (on the right) -->
            <div class="flex gap-2">
                <input type="text" id="logActionSearch" placeholder="Search by action..."
                    class="w-full sm:w-64 bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary" value="{{ request('action') }}">
                <button id="logSearchBtn" class="bg-strong hover:opacity-90 text-white px-4 py-2 rounded-md text-sm font-semibold">Search</button>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto bg-surface rounded-xl border border-border-color/70 shadow-sm" id="logstable">
        <table class="min-w-full text-sm">
            <thead class="border-b-2 border-border-color bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Time</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">User Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">User ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Complaint ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-color">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-3 whitespace-nowrap text-subtle">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-strong">{{ ucfirst($log->user_type) }}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-subtle">{{ $log->user_id ?? 'N/A' }}</td>
                        <td class="px-6 py-3 whitespace-nowrap text-subtle">{{ $log->complaint_id ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-subtle" style="white-space: normal; word-break: break-word;">{{ $log->action }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-subtle">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links for Logs -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>