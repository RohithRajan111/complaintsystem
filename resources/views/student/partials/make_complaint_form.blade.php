<div class="bg-surface p-6 rounded-lg shadow-md border border-border-color">
    <form id="make-complaint-form" enctype="multipart/form-data">
        <h3 class="text-xl font-bold text-strong mb-4 border-b border-border-color pb-4">New Complaint Form</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Department -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-strong mb-1">Select Department</label>
                <!-- FIX #2: Added the 'border' class to make the border width 1px -->
                <select id="department_id" name="department_id" required class="w-full border border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">-- Choose a department --</option>
                    @foreach($departments as $dept)
                        <!-- FIX #1: Changed '->name' to '->Dept_name' to match your database column -->
                        <option value="{{ $dept->id }}">{{ $dept->Dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-strong mb-1">Complaint Title</label>
                <!-- FIX #2: Added the 'border' class -->
                <input type="text" id="title" name="title" required class="w-full border border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" placeholder="e.g., Issue with Wi-Fi in Hostel">
            </div>
        </div>
        <!-- Description -->
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-strong mb-1">Detailed Description</label>
            <!-- FIX #2: Added the 'border' class -->
            <textarea id="description" name="description" required rows="5" class="w-full border border-border-color rounded-md shadow-sm focus:border-primary focus:ring-primary" placeholder="Please describe your issue in detail..."></textarea>
        </div>
        <!-- Attachment -->
        <div class="mt-6">
            <label for="attachment" class="block text-sm font-medium text-strong mb-1">Attach a File (Optional)</label>
            <input type="file" id="attachment" name="attachment" class="w-full text-sm text-subtle file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            <p class="text-xs text-subtle mt-1">Max file size: 2MB. Allowed types: JPG, PNG, PDF, DOCX.</p>
        </div>
        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-border-color">
            <button type="button" id="cancel-complaint-form" class="px-4 py-2 bg-gray-200 text-strong rounded-lg hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Submit Complaint
            </button>
        </div>
    </form>
</div>