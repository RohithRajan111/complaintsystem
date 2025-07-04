<h3>Edit Department</h3>

<form id="edit-dept-form">
    @csrf
    <div class="form-group">
        <label>Department Name</label>
        <input type="text" name="name" value="{{ $department->name }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ $department->email }}" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
</form>

<script>
    $('#edit-dept-form').on('submit', function(e) {
        e.preventDefault();
        $('#ajax-loader').show();

        $.post(`/admin/departments/{{ $department->id }}`, $(this).serialize())
            .done(function(response) {
                if (response.success) {
                    loadSection('departments'); // reload the updated list
                } else {
                    alert('Update failed.');
                }
            })
            .fail(function() {
                alert('An error occurred while updating.');
            })
            .always(function() {
                $('#ajax-loader').hide();
            });
    });
</script>
