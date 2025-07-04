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


    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    // ---------------------------------
                    // PRIMARY THEME (Indigo)
                    // ---------------------------------
                    'primary': '#4f46e5',      // Indigo-600: For main buttons, active links, icons
                    'primary-hover': '#4338ca', // Indigo-700: For hover states on primary elements
                    'primary-light': '#e0e7ff', // Indigo-100: For light backgrounds, like active sidebar links or badges

                    // ---------------------------------
                    // NEUTRAL / BASE THEME (Gray)
                    // ---------------------------------
                    'surface': '#ffffff',      // White: For card backgrounds, tables, modals
                    'base': '#f3f4f6',         // Gray-100: For the main page background
                    'subtle': '#6b7280',       // Gray-500: For labels, placeholders, timestamps, helper text
                    'strong': '#1f2937',       // Gray-800: For main text, titles, body content
                    'border-color': '#e5e7eb', // Gray-200: For borders, dividers, form inputs

                    // ----------------------------------------------------
                    // SEMANTIC STATUS COLORS (For alerts, badges, etc.)
                    // ----------------------------------------------------

                    // --- Success (Green) ---
                    'success': '#16a34a',      // Green-600: Main success color for icons, borders
                    'success-bg': '#dcfce7',   // Green-100: Background for success alerts and badges
                    'success-text': '#15803d', // Green-800: Text color for use on success-bg

                    // --- Danger (Red) ---
                    'danger': '#dc2626',       // Red-600: Main danger color for delete icons, error borders
                    'danger-bg': '#fee2e2',    // Red-100: Background for error alerts and badges
                    'danger-text': '#b91c1c',  // Red-700: Text color for use on danger-bg

                    // --- Warning (Amber/Yellow) ---
                    'warning': '#f59e0b',      // Amber-500: Main warning color
                    'warning-bg': '#fef3c7',   // Amber-100: Background for warning alerts and badges
                    'warning-text': '#b45309', // Amber-700: Text color for use on warning-bg
                    
                    // --- Info (Blue) ---
                    'info': '#2563eb',         // Blue-600: Main info color for "In Progress" statuses
                    'info-bg': '#dbeafe',      // Blue-100: Background for info alerts and badges
                    'info-text': '#1d4ed8',    // Blue-700: Text color for use on info-bg
                }
            }
        }
    }
</script>
</head>
<body class="bg-base font-sans text-subtle min-h-screen">


    <nav class="bg-strong text-white px-6 py-4 flex justify-between items-center shadow-md">
        <span class="text-xl font-bold">Department Dashboard</span>
        <div class="flex items-center gap-6">
            <a href="{{ route('showdept.dashboard') }}" class="text-white font-semibold">Dashboard</a>
            <a href="{{ route('dept.profile.show') }}" class="text-gray-300 hover:text-white transition">Profile</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-danger hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        
        <!-- Stat Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color flex items-center gap-4">
                <div class="bg-warning/10 text-warning p-3 rounded-full"><i class="fa-solid fa-inbox fa-xl"></i></div>
                <div>
                    <p class="text-3xl font-bold text-strong">{{ $stats['new'] }}</p>
                    <p class="text-sm text-subtle font-medium">New Complaints</p>
                </div>
            </div>
            <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color flex items-center gap-4">
                <div class="bg-info/10 text-info p-3 rounded-full"><i class="fa-solid fa-spinner fa-xl fa-spin"></i></div>
                <div>
                    <p class="text-3xl font-bold text-strong">{{ $stats['in_progress'] }}</p>
                    <p class="text-sm text-subtle font-medium">In Progress</p>
                </div>
            </div>
            <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color flex items-center gap-4">
                <div class="bg-primary/10 text-primary p-3 rounded-full"><i class="fa-solid fa-check-double fa-xl"></i></div>
                <div>
                    <p class="text-3xl font-bold text-strong">{{ $stats['recently_resolved'] }}</p>
                    <p class="text-sm text-subtle font-medium">Resolved (Last 7 Days)</p>
                </div>
            </div>
        </div>

        <!-- Complaints Table Card -->
        <div class="bg-surface shadow-md rounded-lg border border-border-color overflow-hidden"> 
            <div class="p-6 border-b border-border-color"><h2 class="text-xl font-bold text-strong">Incoming Complaints</h2></div>
            @if($complaints->isEmpty())
                <div class="text-center text-subtle py-16"><i class="fa-solid fa-shield-check fa-2x mb-4 text-primary"></i><br>Excellent! There are no pending complaints.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-base text-strong font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Student</th>
                                <th class="px-6 py-3 text-left">Complaint</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-left">Submitted</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-color">
                            @foreach($complaints as $complaint)
                                <tr class="hover:bg-base transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-strong">{{ $complaint->student->Stud_name }}</div>
                                        <div class="text-xs text-subtle">{{ $complaint->student->Stud_email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-strong truncate" title="{{ $complaint->title }}">#{{ $complaint->id }} - {{ $complaint->title }}</div>
                                        <div class="text-xs text-subtle truncate">{{ Str::limit($complaint->description, 60) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'pending'   => 'bg-warning/10 text-yellow-800', 'checking'  => 'bg-info/10 text-blue-800',
                                                'solved'    => 'bg-primary/10 text-green-800',  'rejected'  => 'bg-danger/10 text-red-800',
                                                'withdrawn' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$complaint->status] ?? 'bg-gray-200' }}">{{ str_replace('_', ' ', $complaint->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-subtle">{{ $complaint->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php $isClosed = in_array($complaint->status, ['solved', 'rejected', 'withdrawn']); @endphp
                                        <button class="view-details-btn text-white px-4 py-2 rounded-md text-xs font-medium transition-colors flex items-center gap-2 mx-auto {{ $isClosed ? 'bg-subtle cursor-not-allowed' : 'bg-primary hover:bg-primary-hover' }}" data-id="{{ $complaint->id }}">
                                            <i class="fa-solid fa-eye"></i> {{ $isClosed ? 'View' : 'Respond' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-base border-t border-border-color">{{ $complaints->links() }}</div>
            @endif
        </div> 
    </main>

    <!-- Complaint Details Modal -->
    <div id="complaintModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="bg-surface rounded-lg shadow-2xl w-full max-w-2xl border border-border-color transform transition-all scale-95 opacity-0" id="modal-content">
            <div class="flex justify-between items-center p-4 border-b border-border-color">
                <h3 class="text-xl font-semibold text-strong flex items-center gap-3"><i class="fa-solid fa-comment-dots text-primary"></i>Complaint Details</h3>
                <button class="close-modal-btn text-subtle hover:text-strong text-2xl">×</button>
            </div>
            
            <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                <div>
                    <p class="text-xs font-semibold text-subtle uppercase">Title</p><h4 id="modal-title" class="font-bold text-strong text-lg"></h4>
                    <p class="text-xs font-semibold text-subtle uppercase mt-3">Student</p><p id="modal-student" class="text-strong"></p>
                </div>
                <div class="space-y-2"><p class="text-xs font-semibold text-subtle uppercase">Description</p><p id="modal-description" class="p-3 bg-base border border-border-color rounded-md whitespace-pre-wrap text-sm"></p></div>
                <div id="modal-attachment-container" class="hidden space-y-2"><p class="text-xs font-semibold text-subtle uppercase">Attachment</p><a id="modal-attachment-link" href="#" target="_blank" class="inline-flex items-center gap-2 text-info hover:underline text-sm font-medium p-2 bg-info/10 border border-info/20 rounded-md"><i class="fa-solid fa-paperclip"></i> View / Download Attachment</a></div>
                <div id="modal-responses-container" class="hidden space-y-2"><p class="text-xs font-semibold text-subtle uppercase">Response History</p><div id="modal-responses" class="text-sm space-y-2 max-h-40 overflow-y-auto p-3 border rounded-md bg-base"></div></div>
                <div id="closed-complaint-message" class="hidden p-3 bg-warning/10 border border-warning/20 text-yellow-800 rounded-md text-center font-medium">This complaint is closed and cannot be modified.</div>
                
                <form id="responseForm" class="space-y-4 pt-5 border-t border-border-color">
                    @csrf
                    <input type="hidden" id="complaint_id" name="complaint_id">
                    <h4 class="text-lg font-semibold text-strong">Update Response</h4>
                    <div id="form-error" class="hidden p-3 bg-danger/10 text-danger rounded-md text-sm border border-danger/20"></div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-strong mb-1">Update Status</label>
                        <select name="status" id="status" class="mt-1 w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" required><option value="">-- Select Status --</option><option value="checking">Checking</option><option value="solved">Solved</option><option value="rejected">Rejected</option></select>
                    </div>
                    <div id="responseBox" class="hidden"><label for="response" class="block text-sm font-medium text-strong mb-1">Response Message (optional)</label><textarea name="response" id="response" class="mt-1 w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" rows="3"></textarea></div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" class="close-modal-btn px-4 py-2 bg-gray-200 text-strong rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit" id="submit-response-btn" class="px-5 py-2 bg-primary text-white rounded-md font-medium hover:bg-primary-hover flex items-center gap-2"><i class="fa-solid fa-paper-plane"></i> Submit Response</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT: Logic is preserved, animations are added -->
    <script>
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const modal = $('#complaintModal');
            const modalContent = $('#modal-content');

            function showModal() {
                modal.removeClass('hidden');
                setTimeout(() => modalContent.removeClass('scale-95 opacity-0'), 50);
            }

            function hideModal() {
                modalContent.addClass('scale-95 opacity-0');
                setTimeout(() => modal.addClass('hidden'), 200);
            }

            $('#status').on('change', function() {
                $('#responseBox').toggleClass('hidden', !['solved', 'rejected'].includes($(this).val()));
            });

            $('.view-details-btn').on('click', function() {
                const complaintId = $(this).data('id');
                $('#responseForm, #closed-complaint-message, #form-error, #responseBox, #modal-responses-container, #modal-attachment-container').addClass('hidden');
                $('#responseForm')[0].reset();

                $.ajax({
                    url: `/dept/complaint/${complaintId}`,
                    method: 'GET',
                    success: function(data) {
                        $('#modal-title').text(data.title);
                        $('#modal-student').text(`${data.student.Stud_name} (${data.student.Stud_email})`);
                        $('#modal-description').text(data.description);
                        $('#complaint_id').val(data.id);
                        if (data.attachment_path) {
                            $('#modal-attachment-link').attr('href', `/storage/${data.attachment_path}`);
                            $('#modal-attachment-container').removeClass('hidden');
                        }
                        if(data.responses && data.responses.length > 0) {
                            $('#modal-responses').html(data.responses.map(res => `<div class="p-2 bg-white rounded border border-border-color"><strong>Dept:</strong> ${res.response} <span class="text-xs text-subtle float-right">${new Date(res.created_at).toLocaleString()}</span></div>`).join(''));
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
                    },
                    error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Could not fetch details.'))
                });
            });

            $('.close-modal-btn').on('click', hideModal);

            $('#responseForm').on('submit', function(e) {
                e.preventDefault();
                const submitButton = $('#submit-response-btn');
                const originalHtml = submitButton.html();
                submitButton.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');
                $('#form-error').addClass('hidden').text('');
                $.ajax({
                    url: $(this).attr('action'), method: 'POST', data: $(this).serialize(),
                    success: (res) => { if (res.success) { hideModal(); alert(res.message); window.location.reload(); } },
                    error: (xhr) => {
                        let errorMsg = 'An unexpected error occurred.';
                        if (xhr.responseJSON) { errorMsg = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('\n') : xhr.responseJSON.message; }
                        $('#form-error').html(errorMsg.replace(/\n/g, '<br>')).removeClass('hidden');
                    },
                    complete: () => submitButton.prop('disabled', false).html(originalHtml)
                });
});
        });
    </script>
</body>
</html>