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
                <div class="bg-primary/10 text-primary p-3 rounded-full"><i class="fa-solid fa-circle-check fa-xl"></i></div>
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

        <!-- Complaint Form Area (will be filled by AJAX) -->
        <div id="complaint-form-area" class="mb-8"></div>

        <!-- Complaints List Area (will be filled by AJAX) -->
        <div id="complaints-section">
            <div class="text-center text-subtle py-16">
                <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                <p class="mt-4">Loading your complaints...</p>
            </div>
        </div>
    </main>

    <!-- Scripts (no functional changes, but added button states) -->
   <script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const complaintFormArea = $('#complaint-form-area');
        const complaintsSection = $('#complaints-section');

        function loadContent(url, callback) {
            let loadingHtml = '<div class="text-center text-subtle py-16"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p class="mt-4">Loading...</p></div>';
            complaintsSection.html(loadingHtml);
            
            $.get(url).done(function(data) {
                complaintsSection.html(data);
                if (typeof callback === 'function') callback();
            }).fail(() => complaintsSection.html('<p class="text-center text-danger py-8">Error: Could not load content.</p>'));
        }

        function bindComplaintEvents() {
            complaintsSection.off(); 

            complaintsSection.on('click', '.withdraw-btn', function () {
                if (!confirm('Are you sure you want to withdraw this complaint?')) return;
                
                $.post(`/student/withdraw-complaint/${$(this).data('id')}`)
                    .done((res) => {
                        alert(res.message || 'Complaint withdrawn.');
                        loadContent('{{ route("student.ajax.complaint.list") }}', bindComplaintEvents);
                    })
                    .fail(() => alert('Server error during withdrawal.'));
            });

            complaintsSection.on('click', '.pagination a', function(e) {
                e.preventDefault(); 
                loadContent($(this).attr('href'), bindComplaintEvents); 
            });
        }

        $('#make-complaint-btn').on('click', function () {
            $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Loading Form...');
            $.get("{{ route('student.ajax.complaint.form') }}")
                .done(data => complaintFormArea.html(data).slideDown('fast'))
                .always(() => $(this).prop('disabled', false).html('<i class="fa-solid fa-plus"></i> Make a New Complaint'));
        });

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
                success: function(res) {
                    alert(res.message);
                    complaintFormArea.slideUp('fast', function() { $(this).html(''); });
                    loadContent('{{ route("student.ajax.complaint.list") }}', bindComplaintEvents);
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    let errorMsg = 'An error occurred. Please check your input.';
                    if (errors) {
                        errorMsg = 'Please fix the following errors:\n\n' + 
                                   Object.values(errors).map(val => `- ${val[0]}`).join('\n');
                    }
                    alert(errorMsg);
                },
                complete: () => submitButton.prop('disabled', false).html(originalHtml)
            });
        });
        
        $(document).on('click', '#cancel-complaint-form', () => complaintFormArea.slideUp('fast', function() { $(this).html(''); }));

        loadContent('{{ route("student.ajax.complaint.list") }}', bindComplaintEvents);
    });
    </script>
</body>
</html>