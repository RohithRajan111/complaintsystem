<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { 
                    'primary': '#4f46e5', 
                    'primary-hover': '#4338ca', 
                    'primary-light': '#e0e7ff', 
                    'surface': '#ffffff', 
                    'base': '#f3f4f6', 
                    'subtle': '#6b7280', 
                    'strong': '#1f2937', 
                    'border-color': '#e5e7eb', 
                    'success': '#16a34a', 
                    'success-bg': '#dcfce7', 
                    'success-text': '#15803d', 
                    'danger': '#dc2626', 
                    'danger-bg': '#fee2e2', 
                    'danger-text': '#b91c1c', 
                    'warning': '#f59e0b', 
                    'warning-bg': '#fef3c7', 
                    'warning-text': '#b45309', 
                    'info': '#2563eb', 
                    'info-bg': '#dbeafe', 
                    'info-text': '#1d4ed8' 
                }
            }
        }
    }
    </script>
</head>
<body class="bg-base text-strong font-sans min-h-screen">

    <!-- Header -->
    <header class="bg-surface shadow-sm px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-strong">Student Dashboard</h1>
        <div class="flex items-center space-x-4">
            <a href="{{ route('student.profile') }}" class="text-subtle hover:text-strong transition flex items-center gap-2">
                <i class="fa-solid fa-user-gear"></i> Profile
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-subtle px-4 py-2 rounded-md transition flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color flex items-center gap-4">
                <div class="bg-info/10 text-info p-3 rounded-full"><i class="fa-solid fa-hourglass-half fa-xl"></i></div>
                <div>
                    <p class="text-3xl font-bold text-strong">{{ $stats['active'] }}</p>
                    <p class="text-sm text-subtle font-medium">Active Complaints</p>
                </div>
            </div>
            <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color flex items-center gap-4">
                <div class="bg-success/10 text-success p-3 rounded-full"><i class="fa-solid fa-circle-check fa-xl"></i></div>
                <div>
                    <p class="text-3xl font-bold text-strong">{{ $stats['resolved'] }}</p>
                    <p class="text-sm text-subtle font-medium">Resolved Complaints</p>
                </div>
            </div>
            <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color flex items-center gap-4">
                <div class="bg-gray-200/50 text-subtle p-3 rounded-full"><i class="fa-solid fa-list-check fa-xl"></i></div>
                <div>
                    <p class="text-3xl font-bold text-strong">{{ $stats['total'] }}</p>
                    <p class="text-sm text-subtle font-medium">Total Submitted</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="text-2xl font-bold text-strong">My Complaint History</h2>
            <button id="make-complaint-btn" class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-lg transition font-semibold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Make a New Complaint
            </button>
        </div>

        <!-- Complaint Form Area -->
        <div id="complaint-form-area" class="mb-8 hidden"></div>

        <!-- Search and Filter Section -->
        <div class="mb-6 bg-surface p-6 rounded-lg shadow-sm border border-border-color">
            <div class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="search-input" class="block text-sm font-medium text-strong mb-2">Search Complaints</label>
                    <div class="relative">
                        <input type="text" id="search-input" placeholder="Search by title, description, or department..." 
                               class="w-full px-4 py-2 pl-10 border border-border-color rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <i class="fa-solid fa-search absolute left-3 top-3 text-subtle"></i>
                    </div>
                </div>
                <div class="w-full lg:w-48">
                    <label for="status-filter" class="block text-sm font-medium text-strong mb-2">Filter by Status</label>
                    <select id="status-filter" class="w-full px-4 py-2 border border-border-color rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="checking">Checking</option>
                        <option value="solved">Solved</option>
                        <option value="rejected">Rejected</option>
                        <option value="withdrawn">Withdrawn</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button id="search-btn" class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md transition font-semibold">
                        <i class="fa-solid fa-search"></i> Search
                    </button>
                    <button id="clear-search-btn" class="bg-gray-100 hover:bg-gray-200 text-subtle px-4 py-2 rounded-md transition font-semibold">
                        <i class="fa-solid fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Complaints List Area -->
        <div id="complaints-section">
            <div id="loading-indicator" class="hidden text-center py-8">
                <i class="fa-solid fa-spinner fa-spin text-primary text-2xl"></i>
                <p class="text-subtle mt-2">Loading complaints...</p>
            </div>
            
            <!-- Initial complaints list -->
            <div id="complaints-list">
                @if($complaints->isEmpty())
                    <div class="text-center text-subtle py-16 bg-surface rounded-lg shadow-sm border border-border-color">
                        <i class="fa-solid fa-check-circle fa-2x mb-4 text-primary"></i><br>
                        You're all clear! You haven't made any complaints yet.
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($complaints as $complaint)
                            <div class="bg-surface rounded-lg shadow-sm border border-border-color overflow-hidden complaint-card" 
                                 data-title="{{ strtolower($complaint->title) }}" 
                                 data-description="{{ strtolower($complaint->description) }}" 
                                 data-department="{{ strtolower($complaint->department->name) }}"
                                 data-status="{{ $complaint->status }}">
                                <div class="p-5 flex flex-col sm:flex-row justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            @php
                                                $statusClasses = [
                                                    'pending'   => 'bg-warning/10 text-yellow-800 border-warning/20',
                                                    'checking'  => 'bg-info/10 text-blue-800 border-info/20',
                                                    'solved'    => 'bg-success/10 text-green-800 border-success/20',
                                                    'rejected'  => 'bg-danger/10 text-red-800 border-danger/20',
                                                    'withdrawn' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $statusClasses[$complaint->status] ?? 'bg-gray-200' }}">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                            </span>
                                            <h3 class="text-lg font-bold text-strong">{{ $complaint->title }}</h3>
                                        </div>
                                        <p class="text-sm text-subtle">
                                            To: <span class="font-medium">{{ $complaint->department->name }}</span> |
                                            Submitted: <span class="font-medium">{{ $complaint->created_at->format('M d, Y') }}</span> ({{ $complaint->created_at->diffForHumans() }})
                                        </p>
                                    </div>
                                    @if(in_array($complaint->status, ['pending', 'checking']))
                                        <button data-id="{{ $complaint->id }}" class="withdraw-btn bg-danger/10 hover:bg-danger/20 text-danger text-sm px-4 py-2 rounded-md transition font-semibold flex items-center gap-2 self-start">
                                            <i class="fa-solid fa-trash-can"></i> Withdraw
                                        </button>
                                    @endif
                                </div>

                                <div class="px-5 py-4 border-t border-border-color space-y-4 bg-base/50">
                                    <div>
                                        <p class="text-xs text-subtle uppercase font-semibold mb-1">Your Description</p>
                                        <p class="text-strong text-sm whitespace-pre-wrap">{{ $complaint->description }}</p>
                                    </div>
                                    @if ($complaint->attachment_path)
                                        <div>
                                            <p class="text-xs text-subtle uppercase font-semibold mb-1">Your Attachment</p>
                                            <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank" class="inline-flex items-center gap-2 text-info hover:underline text-sm font-medium">
                                                <i class="fa-solid fa-paperclip"></i> View Attachment
                                            </a>
                                        </div>
                                    @endif
                                    @if($complaint->responses->isNotEmpty())
                                        <div class="pt-3 border-t border-border-color">
                                            <p class="text-xs text-subtle uppercase font-semibold mb-2">Department Response</p>
                                            @foreach($complaint->responses as $response)
                                                <div class="bg-white p-3 rounded-md border border-border-color text-sm text-strong mb-2">
                                                    {{ $response->response }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-8" id="pagination-links">
                        {{ $complaints->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Make Complaint Form Template (Hidden) -->
    <div id="complaint-form-template" style="display: none;">
        <div class="bg-surface p-6 rounded-lg shadow-sm border border-border-color">
            <h3 class="text-xl font-bold text-strong mb-4">Submit a New Complaint</h3>
            <form id="make-complaint-form" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-strong mb-2">Department</label>
                        <select name="department_id" id="department_id" required class="w-full px-4 py-2 border border-border-color rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Select Department</option>
                            <!-- Departments will be loaded via AJAX -->
                        </select>
                    </div>
                    <div>
                        <label for="title" class="block text-sm font-medium text-strong mb-2">Title</label>
                        <input type="text" name="title" id="title" required class="w-full px-4 py-2 border border-border-color rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Brief title for your complaint">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-strong mb-2">Description</label>
                    <textarea name="description" id="description" required rows="4" class="w-full px-4 py-2 border border-border-color rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary" placeholder="Describe your complaint in detail"></textarea>
                </div>
                <div class="mb-6">
                    <label for="attachment" class="block text-sm font-medium text-strong mb-2">Attachment (Optional)</label>
                    <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.pdf,.docx" class="w-full px-4 py-2 border border-border-color rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <p class="text-xs text-subtle mt-1">Supported formats: JPG, JPEG, PNG, PDF, DOCX (Max: 2MB)</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-primary hover:bg-primary-hover text-white px-6 py-2 rounded-md transition font-semibold">
                        <i class="fa-solid fa-paper-plane"></i> Submit Complaint
                    </button>
                    <button type="button" id="cancel-complaint-form" class="bg-gray-100 hover:bg-gray-200 text-subtle px-6 py-2 rounded-md transition font-semibold">
                        <i class="fa-solid fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const complaintFormArea = $('#complaint-form-area');
        const searchInput = $('#search-input');
        const statusFilter = $('#status-filter');
        const complaintsList = $('#complaints-list');
        const loadingIndicator = $('#loading-indicator');

        // Search functionality
        function performSearch() {
            const searchTerm = searchInput.val().toLowerCase();
            const statusFilter = $('#status-filter').val();
            
            $('.complaint-card').each(function() {
                const card = $(this);
                const title = card.data('title');
                const description = card.data('description');
                const department = card.data('department');
                const status = card.data('status');
                
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm) || 
                    description.includes(searchTerm) || 
                    department.includes(searchTerm);
                
                const matchesStatus = !statusFilter || status === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    card.show();
                } else {
                    card.hide();
                }
            });
            
            // Show "no results" message if no cards are visible
            const visibleCards = $('.complaint-card:visible');
            if (visibleCards.length === 0 && $('.complaint-card').length > 0) {
                if ($('#no-results-message').length === 0) {
                    complaintsList.append(`
                        <div id="no-results-message" class="text-center text-subtle py-16 bg-surface rounded-lg shadow-sm border border-border-color">
                            <i class="fa-solid fa-search fa-2x mb-4 text-subtle"></i><br>
                            No complaints found matching your search criteria.
                        </div>
                    `);
                }
            } else {
                $('#no-results-message').remove();
            }
        }

        // Search event handlers
        $('#search-btn').on('click', performSearch);
        
        searchInput.on('keyup', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        statusFilter.on('change', performSearch);
        
        // Real-time search as user types
        let searchTimeout;
        searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        // Clear search
        $('#clear-search-btn').on('click', function() {
            searchInput.val('');
            statusFilter.val('');
            $('.complaint-card').show();
            $('#no-results-message').remove();
        });

        // Handle clicking the "Make a New Complaint" button
        $('#make-complaint-btn').on('click', function () {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Loading Form...');

            $.get("{{ route('student.ajax.complaint.form') }}")
                .done(function(data) {
                    complaintFormArea.html(data).removeClass('hidden').slideDown('fast');
                })
                .fail(function() {
                    alert('Could not load the complaint form. Please try again.');
                })
                .always(function() {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-plus"></i> Make a New Complaint');
                });
        });

        // Handle submitting the new complaint form via AJAX
        $(document).on('submit', '#make-complaint-form', function (e) {
            e.preventDefault();
            const submitButton = $(this).find('button[type="submit"]');
            const originalHtml = submitButton.html();
            submitButton.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');

            $.ajax({
                url: "{{ route('student.ajax.complaint.submit') }}",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response.message || 'Complaint submitted successfully!');
                    window.location.reload();
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred. Please check your input and try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMsg = 'Please fix the following errors:\n\n' +
                                   Object.values(errors).map(val => `• ${val[0]}`).join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    alert(errorMsg);
                    submitButton.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Handle clicking the "Cancel" button on the form
        $(document).on('click', '#cancel-complaint-form', function() {
            complaintFormArea.slideUp('fast', function() {
                $(this).html('').addClass('hidden');
            });
        });

        // Handle clicking a "Withdraw" button via AJAX
        $(document).on('click', '.withdraw-btn', function () {
            if (!confirm('Are you sure you want to withdraw this complaint? This action cannot be undone.')) {
                return;
            }
            
            const btn = $(this);
            const originalHtml = btn.html();
            const complaintId = btn.data('id');
            
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: `/student/withdraw-complaint/${complaintId}`,
                type: 'POST',
                success: function(response) {
                    alert(response.message || 'Complaint withdrawn successfully!');
                    window.location.reload();
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'An error occurred while withdrawing the complaint.';
                    alert(errorMsg);
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Enhanced form validation
        $(document).on('input change', '#make-complaint-form input, #make-complaint-form textarea, #make-complaint-form select', function() {
            const field = $(this);
            const isValid = field[0].checkValidity();
            
            if (isValid) {
                field.removeClass('border-danger').addClass('border-success');
            } else {
                field.removeClass('border-success').addClass('border-danger');
            }
        });
    });
    </script>
</body>
</html>