<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <!-- Button uses the theme's primary green color -->
            <button id="newDeptBtn" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200">
                + New Department
            </button>
            <div class="flex gap-2">
                <!-- Input field is styled for the theme -->
                <input type="text" id="deptSearch" placeholder="Search departments..." class="w-full sm:w-64 bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary" value="{{ request('search') }}" />
                <!-- Search button uses the theme's dark charcoal color -->
                <button id="deptSearchBtn" class="bg-strong hover:opacity-90 text-white px-4 py-2 rounded-md text-sm font-semibold">Search</button>
            </div>
        </div>
    </div>

    <!-- Table container is styled for the theme -->
    <div class="overflow-x-auto bg-surface rounded-xl border border-border-color/70 shadow-sm" id="dept_table">
        <table class="min-w-full text-sm">
            <!-- Table header uses a very light gray background -->
            <thead class="border-b-2 border-border-color bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Department Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">HOD Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-color">
                @forelse($departments as $dept)
                    <tr id="row-{{ $dept->id }}" class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-strong">{{ $dept->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-strong" id="dept-name-{{ $dept->id }}">{{ $dept->Dept_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-subtle" id="hod-name-{{ $dept->id }}">{{ $dept->Hod_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-subtle" id="dept-email-{{ $dept->id }}">{{ $dept->Dept_email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- Edit button uses a soft, complementary style -->
                            <button class="edit-btn bg-green-100 text-green-800 text-xs font-semibold px-4 py-1.5 rounded-full hover:bg-green-200 transition" data-id="{{ $dept->id }}">
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                     <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-subtle">No departments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links will automatically inherit the theme styles -->
    <div class="mt-4">
        {{ $departments->links() }}
    </div>
</div>