@php
    use Illuminate\Support\Str;
@endphp

@component('mail::message')
@php
    $rawBody = $body ?? '';
    $containsHtml = $rawBody !== strip_tags($rawBody);
    $renderedBody = $containsHtml ? $rawBody : Str::markdown($rawBody);
@endphp

{!! $renderedBody !!}

@component('mail::button', ['url' => $bookingLink ?? url('/')])
{{ $buttonLabel ?? 'Zobraziť rezerváciu' }}
@endcomponent
@endcomponent
