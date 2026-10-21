<x-mail::message>
# Manpower Request Received

Hi {{ $manpowerRequest->contact_person }},

We have received the manpower request for **{{ $manpowerRequest->business_name }}**.

<x-mail::panel>
**Reference No:** {{ $manpowerRequest->reference }}
</x-mail::panel>

**Requested positions:**

@foreach ($manpowerRequest->positions as $position)
- {{ $position->position }} × {{ $position->number_required }}
@endforeach

Our team will review your request and get back to you. Please keep your reference number for follow-ups.

Thanks,<br>
{{ config('app.name') }}

---

*This is an automated email. Please do not reply to this message.*
</x-mail::message>
