<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
<body class="bg-base font-sans text-subtle">

<div class="flex h-screen bg-base">
    <!-- Sidebar -->
    <aside class="w-60 bg-surface shadow-sm flex flex-col border-r border-border-color">
        <div class="p-6 text-xl font-bold text-strong border-b border-border-color">
            Admin Panel
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <button data-url="{{ route('admin.complaints.ajax') }}" class="nav-link w-full text-left flex items-center px-4 py-2.5 rounded-md font-medium text-strong hover:bg-gray-100 transition duration-200">Complaints</button>
            <button data-url="{{ route('admin.logs.ajax') }}" class="nav-link w-full text-left flex items-center px-4 py-2.5 rounded-md font-medium text-strong hover:bg-gray-100 transition duration-200">Logs</button>
            <button data-url="{{ route('admin.students.ajax') }}" class="nav-link w-full text-left flex items-center px-4 py-2.5 rounded-md font-medium text-strong hover:bg-gray-100 transition duration-200">Students</button>
            <button data-url="{{ route('admin.departments.ajax') }}" class="nav-link w-full text-left flex items-center px-4 py-2.5 rounded-md font-medium text-strong hover:bg-gray-100 transition duration-200">Departments</button>
        </nav>
        <div class="p-4 border-t border-border-color">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-strong px-4 py-2 rounded-md transition duration-200">Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-surface p-4 border-b border-border-color">
            <div id="complaint-stats" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $statuses = ['Total'=>'total','Pending'=>'pending','In Progress'=>'checking','Resolved'=>'solved','Rejected'=>'rejected','Withdrawn'=>'withdrawn'];
                    $icons = ['📊','⏳','⚙️','✅','❌','🚫'];
                @endphp
                @foreach($statuses as $label => $id)
                    <div class="bg-surface rounded-lg p-4 border border-border-color">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-xs text-subtle uppercase font-semibold tracking-wider">{{ $label }}</p>
                                <p class="text-3xl font-bold text-strong mt-1" id="stat-{{ $id }}">0</p>
                            </div>
                            <span class="text-2xl text-gray-300">{{ $icons[$loop->index] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-base">
            <div class="container mx-auto px-6 py-8">
                <div id="flash-message-area">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Success</p><p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if(session('error'))
                         <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Error</p><p>{{ session('error') }}</p>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div id="content-area" class="bg-surface shadow-md rounded-lg p-6 border border-border-color"></div>
            </div>
        </main>
    </div>
</div>

<!-- MODALS SECTION -->
<div id="createModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-surface rounded-lg shadow-2xl w-full max-w-md border border-border-color">
        <div class="flex justify-between items-center p-4 border-b border-border-color">
            <h3 class="text-xl font-semibold text-strong">Create New Department</h3>
            <button class="modal-cancel-btn text-subtle hover:text-strong">×</button>
        </div>
        <form id="createDeptForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-strong mb-1">Department Name:</label>
                <input type="text" name="Dept_name" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-medium text-strong mb-1">HOD Name:</label>
                <input type="text" name="Hod_name" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-medium text-strong mb-1">Department Email:</label>
                <input type="email" name="Dept_email" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-medium text-strong mb-1">Password:</label>
                <input type="password" name="password" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t mt-6 border-border-color">
                <button type="button" class="modal-cancel-btn px-4 py-2 bg-gray-200 text-strong rounded-md hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-hover">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-surface rounded-lg shadow-2xl w-full max-w-md border border-border-color">
         <div class="flex justify-between items-center p-4 border-b border-border-color">
            <h3 class="text-xl font-semibold text-strong">Edit Department</h3>
            <button class="modal-cancel-btn text-subtle hover:text-strong">×</button>
        </div>
        <form id="editDeptForm" class="p-6 space-y-4">
            <input type="hidden" id="editDeptId">
            <div>
                <label class="block text-sm font-medium text-strong mb-1">Department Name:</label>
                <input type="text" id="editDeptName" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-medium text-strong mb-1">HOD Name:</label>
                <input type="text" id="editHodName" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-medium text-strong mb-1">Department Email:</label>
                <input type="email" id="editDeptEmail" required class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-medium text-strong mb-1">New Password (optional):</label>
                <input type="password" id="editDeptPassword" class="w-full border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" />
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t mt-6 border-border-color">
                <button type="button" class="modal-cancel-btn px-4 py-2 bg-gray-200 text-strong rounded-md hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-hover">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Global Response Form Modal -->
<div id="responseModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-surface rounded-lg shadow-2xl w-full max-w-lg border border-border-color">
        
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b border-border-color">
            <h3 class="text-xl font-semibold text-strong">Update Response</h3>
            <button id="closeResponseModal" class="text-subtle hover:text-strong">×</button>
        </div>
        
        <!-- Complaint Details Section -->
        <div class="p-6 border-b border-border-color bg-base">
            <h4 id="modalComplaintTitle" class="font-bold text-strong text-lg mb-2">
                <!-- Title will be loaded here by JavaScript -->
            </h4>
            <p id="modalComplaintDescription" class="text-sm text-subtle max-h-40 overflow-y-auto pr-2">
                <!-- Description will be loaded here by JavaScript -->
            </p>

            <!-- START: ATTACHMENT SECTION ADDED HERE -->
            <div id="modalComplaintAttachmentContainer" class="hidden mt-4">
                <a id="modalComplaintAttachmentLink" href="#" target="_blank"
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium p-2 bg-blue-50 border border-blue-200 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    View / Download Attachment
                </a>
            </div>
            <!-- END: ATTACHMENT SECTION ADDED HERE -->

        </div>
        
        <!-- The Form -->
        <form id="responseForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="responseStatus" class="block text-sm font-medium text-strong mb-1">New Status:</label>
                <select id="responseStatus" name="status" class="w-full bg-base border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary">
                    <option value="pending">Pending</option>
                    <option value="checking">Checking</option>
                    <option value="solved">Resolved</option>
                    <option value="rejected">Rejected</option>
                    <option value="withdrawn">Withdrawn</option>
                </select>
            </div>

            <div id="responseTextWrapper" class="hidden">
                <label for="responseText" class="block text-sm font-medium text-strong mb-1">Response Text:</label>
                <textarea id="responseText" name="response" class="w-full bg-base border-border-color rounded-md text-sm" rows="4" placeholder="Enter response..."></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t mt-6 border-border-color">
                <button type="button" id="cancelResponse" class="px-4 py-2 bg-slate-200 text-strong rounded-lg hover:bg-slate-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover">Save Update</button>
            </div>
        </form>
    </div>
</div>


<script>
    // Main jQuery document ready function, runs when the page is fully loaded.
    $(document).ready(function() {
        
        // --- INITIAL SETUP & GLOBAL VARIABLES ---
        const contentArea = $('#content-area');
        // Set CSRF token for all future AJAX requests to work with Laravel's security.
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // --- CORE FUNCTIONS ---

        /**
         * Loads content into the main area via an AJAX GET request.
         * @param {string} url - The URL to fetch content from.
         * @param {object} button - The sidebar button that was clicked.
         * @param {function} callback - A function to run after the content is successfully loaded.
         */
        function loadContent(url, button, callback) {
            if(button) setActiveTab(button);
            contentArea.html('<div class="text-center text-primary font-semibold py-20">Loading...</div>');
            
            $.get(url).done(function(data) {
                contentArea.html(data);
                if (typeof callback === 'function') callback();
            }).fail(function() {
                contentArea.html('<div class="text-center text-danger font-semibold py-20">Error loading content. Please try again.</div>');
            });
        }

        /**
         * Sets the visual style for the active tab in the sidebar.
         * @param {object} button - The sidebar button that was clicked.
         */
        function setActiveTab(button) {
            if(button) {
                // Using the "Coastal Breeze" theme classes
                $('.nav-link').removeClass('bg-highlight/40 text-primary font-bold').addClass('text-strong hover:bg-highlight/20');
                $(button).addClass('bg-highlight/40 text-primary font-bold').removeClass('text-strong hover:bg-highlight/20');
            }
        }

        /**
         * Fetches and updates the complaint statistics in the header.
         */
        function loadComplaintStats() {
            $.get('{{ route("admin.complaints.counts") }}').done(function (data) {
                $('#stat-total').text(data.total || 0);
                $('#stat-pending').text(data.pending || 0);
                $('#stat-checking').text(data.checking || 0);
                $('#stat-solved').text(data.solved || 0);
                $('#stat-rejected').text(data.rejected || 0);
                $('#stat-withdrawn').text(data.withdrawn || 0);
            });
        }

        // --- TAB-SPECIFIC EVENT BINDING FUNCTIONS ---

        /**
         * Binds all necessary events for the COMPLAINTS tab.
         */
        function bindComplaintEvents() {
            contentArea.off(); // Clear previous event listeners
            
            // --- Event handler for the "Response" button ---
            contentArea.on('click', '.response-btn', function() {
                const button = $(this);
                const complaintId = button.data('id');
                const actionUrl = button.data('action-url');
                const currentStatus = button.data('status');
                const detailsUrl = `/admin/complaint/${complaintId}/details`;

                // Set the form's action and dropdown value
                $('#responseForm').attr('action', actionUrl);
                $('#responseStatus').val(currentStatus);

                // *** MODIFIED: Clear old data from modal, including attachment ***
                $('#modalComplaintTitle').text('Loading details...');
                $('#modalComplaintDescription').text('');
                $('#responseText').val('');
                $('#modalComplaintAttachmentContainer').addClass('hidden'); // Hide attachment by default

                // Trigger change to set initial visibility of the textarea
                $('#responseStatus').trigger('change');

                // Fetch complaint details via AJAX
                $.get(detailsUrl).done(function(data) {
                    if (data.success) {
                        $('#modalComplaintTitle').text(data.title);
                        $('#modalComplaintDescription').text(data.description);
                        $('#responseText').val(data.response_text);
                        
                        // *** MODIFIED: Logic to show attachment link ***
                        if (data.attachment_path) {
                            const attachmentUrl = `/storage/${data.attachment_path}`;
                            $('#modalComplaintAttachmentLink').attr('href', attachmentUrl);
                            $('#modalComplaintAttachmentContainer').removeClass('hidden');
                        }

                    } else {
                        $('#modalComplaintTitle').text('Error Loading Details');
                        $('#modalComplaintDescription').text(data.message || 'Could not retrieve complaint information.');
                    }
                }).fail(function() {
                    $('#modalComplaintTitle').text('Error Loading Details');
                    $('#modalComplaintDescription').text('A server error occurred. Please try again.');
                }).always(function() {
                    // Show the modal only after the AJAX call is complete
                    $('#responseModal').removeClass('hidden').addClass('flex');
                });
            });

            // Sub-function to reload the complaints list with current filters.
            function loadFiltered() {
                const params = {
                    search: contentArea.find('#complaintSearch').val(),
                    status: contentArea.find('#statusFilter').val(),
                    dept: contentArea.find('#deptFilter').val()
                };
                loadContent('{{ route("admin.complaints.ajax") }}?' + $.param(params), null, bindComplaintEvents);
            }

            // Sub-function to update the Excel download link with all filters.
            function updateExcelLink() {
                const baseUrl = '{{ route("admin.complaints.export") }}';
                const params = {
                    search: contentArea.find('#complaintSearch').val(),
                    status: contentArea.find('#statusFilter').val(),
                    dept: contentArea.find('#deptFilter').val(),
                    start_id: contentArea.find('#startId').val(),
                    end_id: contentArea.find('#endId').val()
                };
                const queryString = $.param(params);
                contentArea.find('#downloadExcelBtn').attr('href', baseUrl + '?' + queryString);
            }
            
            // Initial call and event binding for the Excel link.
            updateExcelLink(); 
            contentArea.on('change keyup', '#complaintSearch, #statusFilter, #deptFilter, #startId, #endId', updateExcelLink);

            // Event listeners for filtering the table and handling pagination.
            contentArea.on('click', '#searchButton', loadFiltered);
            contentArea.on('keypress', '#complaintSearch', function(e) { if (e.key === 'Enter') loadFiltered(); });
            contentArea.on('change', '#statusFilter, #deptFilter', loadFiltered);
            // Updated pagination selector to match the working version
            contentArea.on('click', 'a[href*="?page="]', function(e) { e.preventDefault(); loadContent($(this).attr('href'), null, bindComplaintEvents); });
        }

        /** Binds all necessary events for the LOGS tab. */
        function bindLogEvents() {
            contentArea.off();
            function loadFiltered() {
                loadContent('{{ route("admin.logs.ajax") }}?' + $.param({
                    user_type: contentArea.find('#logUserTypeFilter').val(),
                    action: contentArea.find('#logActionSearch').val(),
                    date: contentArea.find('#logDateFilter').val()
                }), null, bindLogEvents);
            }
            contentArea.on('change', '#logUserTypeFilter, #logDateFilter', loadFiltered);
            contentArea.on('click', '#logSearchBtn', loadFiltered);
            contentArea.on('keypress', '#logActionSearch', function(e) { if (e.key === 'Enter') loadFiltered(); });
            // Updated pagination selector to match the working version
            contentArea.on('click', 'a[href*="?page="]', function(e) { e.preventDefault(); loadContent($(this).attr('href'), null, bindLogEvents); });
        }

        /** Binds all necessary events for the STUDENTS tab. */
        function bindStudentEvents() {
            contentArea.off();
            function loadFiltered() {
                loadContent('{{ route("admin.students.ajax") }}?' + $.param({ search: contentArea.find('#studentSearch').val() }), null, bindStudentEvents);
            }
            contentArea.on('click', '#studentSearchBtn', loadFiltered);
            contentArea.on('keypress', '#studentSearch', function(e) { if (e.key === 'Enter') loadFiltered(); });
            // Updated pagination selector to match the working version
            contentArea.on('click', 'a[href*="?page="]', function(e) { e.preventDefault(); loadContent($(this).attr('href'), null, bindStudentEvents); });
        }

        /** Binds all necessary events for the DEPARTMENTS tab. */
        function bindDepartmentEvents() {
            contentArea.off();
            function loadFiltered() {
                loadContent('{{ route("admin.departments.ajax") }}?' + $.param({ search: contentArea.find('#deptSearch').val() }), null, bindDepartmentEvents);
            }
            contentArea.on('click', '#deptSearchBtn', loadFiltered);
            contentArea.on('keypress', '#deptSearch', function(e) { if (e.key === 'Enter') loadFiltered(); });
            // Updated pagination selector to match the working version
            contentArea.on('click', 'a[href*="?page="]', function(e) { e.preventDefault(); loadContent($(this).attr('href'), null, bindDepartmentEvents); });
            
            contentArea.on('click', '#newDeptBtn', function() { $('#createModal').removeClass('hidden').addClass('flex'); });
            contentArea.on('click', '.edit-btn', function() {
                $.get('/admin/department/edit/' + $(this).data('id'), function (data) {
                    $('#editDeptId').val(data.id);
                    $('#editDeptName').val(data.Dept_name);
                    $('#editHodName').val(data.Hod_name);
                    $('#editDeptEmail').val(data.Dept_email);
                    $('#editDeptPassword').val('');
                    $('#editModal').removeClass('hidden').addClass('flex');
                });
            });
        }
        
        // --- GLOBAL EVENT LISTENERS (for elements outside the contentArea, like modals) ---
        
        // Listener for the global response modal's status dropdown.
        $('#responseStatus').on('change', function() {
            if (['solved', 'rejected'].includes($(this).val())) {
                $('#responseTextWrapper').slideDown('fast');
            } else {
                $('#responseTextWrapper').slideUp('fast');
                $('#responseText').val('');
            }
        });
        // Listeners for closing the response modal.
        $('#closeResponseModal, #cancelResponse').on('click', function() { $('#responseModal').addClass('hidden').removeClass('flex'); });
        
        // Listener for the department creation form.
        $('#createDeptForm').submit(function (e) {
            e.preventDefault();
            $.post('{{ route("admin.department.store") }}', $(this).serialize())
                .done(function (res) {
                    $('#createModal').addClass('hidden').removeClass('flex');
                    $('#createDeptForm')[0].reset();
                    $('.nav-link:contains("Departments")').click();
                })
                .fail(function (xhr) { alert('Error: ' + (xhr.responseJSON.message || 'Could not create department.')); });
        });

        // Listener for the department edit form.
        $('#editDeptForm').submit(function (e) {
            e.preventDefault();
            const id = $('#editDeptId').val();
            const formData = {
                Dept_name: $('#editDeptName').val(),
                Hod_name: $('#editHodName').val(),
                Dept_email: $('#editDeptEmail').val(),
                password: $('#editDeptPassword').val()
            };
            $.post('/admin/department/update/' + id, formData, function (res) {
                $('#editModal').addClass('hidden').removeClass('flex');
                $('#dept-name-' + id).text(formData.Dept_name);
                $('#hod-name-' + id).text(formData.Hod_name);
                $('#dept-email-' + id).text(formData.Dept_email);
            }).fail(function (xhr) { alert('Error: ' + (xhr.responseJSON.message || 'Could not update department.')); });
        });
        
        // General listener for all other modal "Cancel" buttons.
        $('.modal-cancel-btn').on('click', function() { $(this).closest('.fixed').addClass('hidden').removeClass('flex'); });

        // --- INITIAL PAGE SETUP & NAVIGATION ---

        // Main Sidebar Navigation Click Handler
        $('.nav-link').on('click', function() {
            const button = $(this);
            const url = button.data('url');
            const text = button.text().trim();
            let callback = null;
            if (text === 'Complaints') { callback = bindComplaintEvents; } 
            else if (text === 'Logs') { callback = bindLogEvents; } 
            else if (text === 'Students') { callback = bindStudentEvents; } 
            else if (text === 'Departments') { callback = bindDepartmentEvents; }
            loadContent(url, button, callback);
        });

        // Initial actions when the page first loads.
        loadComplaintStats();
        $('.nav-link').first().click(); // Automatically load the first tab (Complaints).
    });
</script>

</body>
</html>
