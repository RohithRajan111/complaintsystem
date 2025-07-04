<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>

    <h2>Complaint Report - #{{ $complaint->id }}</h2>

    <p><strong>Title:</strong> {{ $complaint->title }}</p>
    <p><strong>Submitted By:</strong> {{ $complaint->student->Stud_name }} ({{ $complaint->student->Stud_email }})</p>
    <p><strong>Department:</strong> {{ $complaint->department->Dept_name }}</p>
    <p><strong>Status:</strong> {{ ucfirst($complaint->status) }}</p>
    <p><strong>Submitted On:</strong> {{ $complaint->created_at->format('d M Y, h:i A') }}</p>

    <h4>Description:</h4>
    <p>{{ $complaint->description }}</p>

    @if($complaint->responses->count())
        <h4>Responses:</h4>
        @foreach($complaint->responses as $response)
            <p><strong>- {{ $response->created_at->format('d M Y, h:i A') }}</strong><br>
            {{ $response->response }}</p>
        @endforeach
    @else
        <p><em>No responses yet.</em></p>
    @endif

</body>
</html>
