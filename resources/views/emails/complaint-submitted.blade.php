@component('mail::message')
    # New Complaint Received

    **Complaint Type:** {{ $complaintType }}

    **Subject:** {{ $subject }}

    **From:** {{ $userName }} ({{ $userEmail }})

    **Submitted At:** {{ $submittedAt }}

    ---

    ## Complaint Details

    {{ $message }}

    ---

    @component('mail::button', ['url' => route('admin.complaints.index', [], false)])
        View in Admin Panel
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
