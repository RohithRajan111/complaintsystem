<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input { @apply w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen">

    <header class="bg-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <span class="text-2xl font-bold text-indigo-600">Department Dashboard</span>
            <div class="flex items-center gap-6">
                <a href="{{ route('showdept.dashboard') }}" class="font-semibold text-indigo-600">Dashboard</a>
                <a href="{{ route('dept.profile.show') }}" class="text-gray-600 hover:text-indigo-600 transition">Profile</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        
        <!-- Stat Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-5">
                <div class="bg-yellow-100 text-yellow-600 rounded-full h-12 w-12 flex items-center justify-center"><i class="fa-solid fa-inbox fa-lg"></i></div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['new'] }}</p>
                    <p class="text-sm text-gray-500 font-medium">New Complaints</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-5">
                <div class="bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex items-center justify-center"><i class="fa-solid fa-spinner fa-lg"></i></div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
                    <p class="text-sm text-gray-500 font-medium">In Progress</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-5">
                <div class="bg-green-100 text-green-600 rounded-full h-12 w-12 flex items-center justify-center"><i class="fa-solid fa-check-double fa-lg"></i></div>
                <div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['recently_resolved'] }}</p>
                    <p class="text-sm text-gray-500 font-medium">Resolved (Last 7 Days)</p>
                </div>
            </div>
        </div>

        <!-- Complaints Table Card -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden"> 
            <div class="p-6 border-b border-gray-200"><h2 class="text-xl font-bold text-gray-900">Incoming Complaints</h2></div>
            
            <!-- START: UPDATED FILTER FORM -->
            <form method="GET" action="{{ route('showdept.dashboard') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-6 bg-gray-50/50 border-b border-gray-200">
                <!-- Search Input -->
                <div class="flex items-center gap-2 w-full md:w-1/2 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or student name" class="form-input pl-10 py-2 w-full"/>
                </div>

                <!-- Filter and Sort Inputs -->
                <div class="flex items-center gap-2">
                    <!-- Status Filter Dropdown -->
                    <select name="status" class="form-input py-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="checking" {{ request('status') == 'checking' ? 'selected' : '' }}>Checking</option>
                        <option value="solved" {{ request('status') == 'solved' ? 'selected' : '' }}>Solved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>

                    <!-- Sort Dropdown -->
                    <select name="sort" class="form-input py-2">
                        <option value="default" {{ request('sort') === 'default' ? 'selected' : '' }}>Sort by Status</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Title A–Z</option>
                        <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title Z–A</option>
                    </select>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        Filter
                    </button>
                </div>
            </form>
            <!-- END: UPDATED FILTER FORM -->


            @if($complaints->isEmpty())
                <div class="text-center text-gray-500 py-20">
                    <i class="fa-solid fa-magnifying-glass fa-3x mb-4 text-indigo-500"></i><br>
                    <span class="font-medium">No complaints found.</span><br> 
                    Try adjusting your search or filter criteria.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Complaint</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Submitted</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($complaints as $complaint)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap"><div class="font-medium text-gray-900">{{ $complaint->student->Stud_name }}</div><div class="text-xs text-gray-500">{{ $complaint->student->Stud_email }}</div></td>
                                    <td class="px-6 py-4 max-w-sm"><div class="font-semibold text-gray-900 truncate" title="{{ $complaint->title }}">#{{ $complaint->id }} - {{ $complaint->title }}</div><div class="text-xs text-gray-500 truncate">{{ Str::limit($complaint->description, 60) }}</div></td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'pending'   => 'bg-yellow-100 text-yellow-800', 'checking'  => 'bg-blue-100 text-blue-800',
                                                'solved'    => 'bg-green-100 text-green-800',  'rejected'  => 'bg-red-100 text-red-800',
                                                'withdrawn' => 'bg-gray-100 text-gray-700',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$complaint->status] ?? 'bg-gray-200' }}">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $complaint->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php $isClosed = in_array($complaint->status, ['solved', 'rejected', 'withdrawn']); @endphp
                                        <button class="view-details-btn text-white px-4 py-2 rounded-lg text-xs font-medium transition-colors flex items-center gap-2 mx-auto shadow-sm {{ $isClosed ? 'bg-gray-400' : 'bg-indigo-600 hover:bg-indigo-700' }}" data-id="{{ $complaint->id }}">
                                            <i class="fa-solid fa-eye"></i> {{ $isClosed ? 'View' : 'Respond' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination links will now include all filters automatically -->
                <div class="p-4 bg-gray-50 border-t border-gray-200">{{ $complaints->links() }}</div>
            @endif
        </div> 
    </main>

    <!-- Complaint Details Modal -->
    <div id="complaintModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl border border-gray-200 transform transition-all scale-95 opacity-0" id="modal-content">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3"><i class="fa-solid fa-comment-dots text-indigo-600"></i>Complaint Details</h3>
                <button class="close-modal-btn text-gray-400 hover:text-gray-800 text-2xl">×</button>
            </div>
            
            <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                <div><p class="text-xs font-semibold text-gray-500 uppercase">Title</p><h4 id="modal-title" class="font-bold text-gray-900 text-lg"></h4></div>
                <div><p class="text-xs font-semibold text-gray-500 uppercase">Student</p><p id="modal-student" class="text-gray-800"></p></div>
                <div class="space-y-2"><p class="text-xs font-semibold text-gray-500 uppercase">Description</p><p id="modal-description" class="p-3 bg-gray-50 border border-gray-200 rounded-md whitespace-pre-wrap text-sm"></p></div>
                <div id="modal-attachment-container" class="hidden space-y-2"><p class="text-xs font-semibold text-gray-500 uppercase">Attachment</p><a id="modal-attachment-link" href="#" target="_blank" class="inline-flex items-center gap-2 text-indigo-600 hover:underline text-sm font-medium p-2 bg-indigo-50 border border-indigo-200 rounded-md"><i class="fa-solid fa-paperclip"></i> View Attachment</a></div>
                <div id="modal-responses-container" class="hidden space-y-2"><p class="text-xs font-semibold text-gray-500 uppercase">Response History</p><div id="modal-responses" class="text-sm space-y-2 max-h-40 overflow-y-auto p-3 border rounded-md bg-gray-50"></div></div>
                <div id="closed-complaint-message" class="hidden p-3 bg-yellow-100 border border-yellow-200 text-yellow-800 rounded-md text-center font-medium">This complaint is closed and cannot be modified.</div>
                
                <form id="responseForm" class="space-y-4 pt-5 border-t border-gray-200">
                    <input type="hidden" id="complaint_id" name="complaint_id">
                    <h4 class="text-lg font-semibold text-gray-900">Update Response</h4>
                    <div id="form-error" class="hidden p-3 bg-red-100 text-red-700 rounded-md text-sm border border-red-200"></div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                        <select name="status" id="status" class="form-input" required><option value="">-- Select Status --</option><option value="checking">Checking</option><option value="solved">Solved</option><option value="rejected">Rejected</option></select>
                    </div>
                    <div id="responseBox" class="hidden"><label for="response" class="block text-sm font-medium text-gray-700 mb-1">Response Message (required for solved/rejected)</label><textarea name="response" id="response" class="form-input" rows="3"></textarea></div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" class="close-modal-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Cancel</button>
                        <button type="submit" id="submit-response-btn" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 flex items-center gap-2"><i class="fa-solid fa-paper-plane"></i> Submit Response</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT (No changes needed) -->
    <script>
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const modal = $('#complaintModal'), modalContent = $('#modal-content');
            const showModal = () => { modal.removeClass('hidden'); setTimeout(() => modalContent.removeClass('scale-95 opacity-0'), 10); };
            const hideModal = () => { modalContent.addClass('scale-95 opacity-0'); setTimeout(() => modal.addClass('hidden'), 200); };

            $('#status').on('change', function() { $('#responseBox').css('display', ['solved', 'rejected'].includes($(this).val()) ? 'block' : 'none'); });

            $('.view-details-btn').on('click', function() {
                const complaintId = $(this).data('id');
                $('#responseForm, #closed-complaint-message, #form-error, #responseBox, #modal-responses-container, #modal-attachment-container').addClass('hidden');
                $('#responseForm')[0].reset();

                $.get(`/dept/complaint/${complaintId}`).done(data => {
                    $('#modal-title').text(`#${data.id} - ${data.title}`);
                    $('#modal-student').text(`${data.student.Stud_name} (${data.student.Stud_email})`);
                    $('#modal-description').text(data.description);
                    $('#complaint_id').val(data.id);

                    if (data.attachment_path) {
                        $('#modal-attachment-link').attr('href', `/storage/${data.attachment_path}`);
                        $('#modal-attachment-container').removeClass('hidden');
                    }
                    if (data.responses && data.responses.length > 0) {
                        $('#modal-responses').html(data.responses.map(res => `<div class="p-2 bg-white rounded border border-gray-200"><strong>Dept:</strong> ${res.response} <span class="text-xs text-gray-500 float-right">${new Date(res.created_at).toLocaleString()}</span></div>`).join(''));
                        $('#modal-responses-container').removeClass('hidden');
                    }
                    if (['solved', 'rejected', 'withdrawn'].includes(data.status)) {
                        $('#closed-complaint-message').removeClass('hidden');
                    } else {
                        $('#responseForm').removeClass('hidden');
                        $('#status').val(data.status === 'pending' ? '' : data.status).trigger('change');
                    }
                    $('#responseForm').attr('action', `/dept/respond/${complaintId}`);
                    showModal();
                }).fail(xhr => alert('Error: ' + (xhr.responseJSON?.message || 'Could not fetch details.')));
            });

            $('.close-modal-btn').on('click', hideModal);

            $('#responseForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#submit-response-btn');
                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');
                $('#form-error').addClass('hidden').text('');

                $.ajax({
                    url: $(this).attr('action'), method: 'POST', data: $(this).serialize(),
                    success: res => { if (res.success) { hideModal(); alert(res.message); window.location.reload(); } },
                    error: xhr => {
                        let msg = xhr.responseJSON?.message || 'An unexpected error occurred.';
                        if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).join('\n');
                        $('#form-error').html(msg.replace(/\n/g, '<br>')).removeClass('hidden');
                    },
                    complete: () => btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Submit Response')
                });
            });
        });
    </script>
</body>
</html>