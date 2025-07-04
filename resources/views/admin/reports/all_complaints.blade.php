<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h2 style="text-align: center;">All Complaints Report</h2>

<table>
    <thead>
        <tr>
            <th>#ID</th>
            <th>Title</th>
            <th>Student</th>
            <th>Department</th>
            <th>Status</th>
            <th>Submitted On</th>
        </tr>
    </thead>
    <tbody>
        @foreach($complaints as $complaint)
            <tr>
                <td>{{ $complaint->id }}</td>
                <td>{{ $complaint->title }}</td>
                <td>{{ $complaint->student->Stud_name }}</td>
                <td>{{ $complaint->department->Dept_name }}</td>
                <td>{{ ucfirst($complaint->status) }}</td>
                <td>{{ $complaint->created_at->format('d M Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
