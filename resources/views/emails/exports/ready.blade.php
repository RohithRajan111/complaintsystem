@component('mail::message')
# Your Export is Ready

The complaint report you requested has been generated and is now available for download.

**File Name:** {{ $fileName }}

Click the button below to download your file. This link will be valid for a limited time.

@component('mail::button', ['url' => $downloadUrl])
Download Report
@endcomponent

If you did not request this export, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent