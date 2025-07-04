<!-- //css -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">

<table id="example" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Department</th>
                <th>Submitted</th>
                <th>Created</th>
            </tr>
        </thead>
    </table>



<!-- 
    https://code.jquery.com/jquery-3.7.1.js
https://cdn.datatables.net/2.3.2/js/dataTables.js -->

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

<script>
   new DataTable('#example', {
    ajax: 'http://complaint.test/data/complaints-datatable-e',
    processing: true,
    serverSide: true,
    columns: [
        { data: 'id' },
        { data: 'title' },
        { data: 'status' },
        { data: 'department_name' },
        { data: 'student_name' },
        { data: 'created_at' }
    ]
});

</script>