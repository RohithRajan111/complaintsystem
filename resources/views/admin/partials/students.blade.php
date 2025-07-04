<div>
    <div class="mb-6">
        <div class="flex justify-end items-center">
            <div class="flex gap-2">
                <input type="text" id="studentSearch" placeholder="Search by name or email..." class="w-full sm:w-64 bg-surface border-border-color rounded-md shadow-sm text-sm focus:border-primary focus:ring-primary" value="{{ request('search') }}" />
                <button id="studentSearchBtn" class="bg-strong hover:opacity-90 text-white px-4 py-2 rounded-md text-sm font-semibold">Search</button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-surface rounded-xl border border-border-color/70 shadow-sm" id="studenttable">
        <table class="min-w-full text-sm">
            <thead class="border-b-2 border-border-color bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Access Revoked</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-strong uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-color">
                @forelse($students as $student)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 whitespace-nowrap text-strong">{{ $student->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-strong">{{ $student->Stud_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-subtle">{{ $student->Stud_email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->is_revoked)
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Yes</span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(!$student->is_revoked)
                                <form action="{{ route('admin.student.revoke', $student->id) }}" method="POST" onsubmit="return confirm('Revoke this student\'s access? This action is final.');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-red-100 text-red-800 text-xs font-semibold px-4 py-1.5 rounded-full hover:bg-red-200 hover:text-red-900 transition">
                                        Revoke
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-danger font-semibold">Access Revoked</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-subtle">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>