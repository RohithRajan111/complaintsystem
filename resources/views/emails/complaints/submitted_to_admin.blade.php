@component('mail::message')
# New Complaint Submitted

A new complaint has been submitted that requires attention.

**Complaint ID:** #{{ $complaint->id }}
**Title:** {{ $complaint->title }}
**Submitted By:** {{ $complaint->student->Stud_name }} ({{ $complaint->student->Stud_email }})
**Department:** {{ $complaint->department->Dept_name }}

**Description:**
@component('mail::panel')
{{ $complaint->description }}
@endcomponent

{{-- START: ADDED ATTACHMENT SECTION --}}
@if ($complaint->attachment_path)
**Attachment:**
@component('mail::button', ['url' => asset('storage/' . $complaint->attachment_path)])
View Attached File
@endcomponent
@endif
{{-- END: ADDED ATTACHMENT SECTION --}}


You can view the full complaint details in your dashboard.

Thanks,<br>
{{ config('app.name') }}
@endcomponent