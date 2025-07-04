@component('mail::message')
# An Update on Your Complaint: #{{ $complaint->id }}

Hello {{ $complaint->student->Stud_name }},

There has been an update regarding your complaint: **"{{ $complaint->title }}"**.

The status has been updated to: **{{ ucfirst($complaint->status) }}**

@if($response->response)
**Response from the department:**
@component('mail::panel')
{{ $response->response }}
@endcomponent
@endif

You can view the full details by logging into your dashboard.

Thanks,<br>
{{ config('app.name') }}
@endcomponent