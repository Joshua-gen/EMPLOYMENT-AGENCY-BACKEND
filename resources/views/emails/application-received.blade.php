<x-mail::message>
# Application Received

Hi {{ $applicant->name }},

Thank you for applying. We have received your application.

<x-mail::panel>
**Reference No:** {{ $applicant->reference }}
@if ($applicant->job)

**Position:** {{ $applicant->job->title }} ({{ $applicant->job->country }})
@endif
</x-mail::panel>

Please keep this reference number. You will need it when following up on your application.

Thanks,<br>
{{ config('app.name') }}

---

*This is an automated email. Please do not reply to this message.*
</x-mail::message>
