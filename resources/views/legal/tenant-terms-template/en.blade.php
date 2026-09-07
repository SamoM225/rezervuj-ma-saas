<h1>Online booking terms</h1>
<p class="legal-meta">Effective from {{ $effective }}</p>

<h2>1. Service provider</h2>
<p>Appointments on this page are provided by <strong>{{ $tenant->name }}</strong> (the "provider").
    @if($tenant->address)<br>Address: {{ $tenant->address }}@endif
    @foreach($locations as $location)<br>Location {{ $location->name }}{{ $location->address ? ': '.$location->address : '' }}@endforeach
    @if($tenant->email)<br>E-mail: <a href="mailto:{{ $tenant->email }}">{{ $tenant->email }}</a>@endif
    @if($tenant->phone)<br>Phone: <a href="tel:{{ preg_replace('/\s+/', '', $tenant->phone) }}">{{ $tenant->phone }}</a>@endif
</p>
<p>The booking page is operated technically by the rezervuj-ma.online platform as the provider's processor. The platform is not a party to the booking or to the service provided.</p>

<h2>2. How a booking is made</h2>
<p>You choose an appointment in the online form on this page. A booking can be made no later than {{ $advanceHours }} hours before its start.
    @if($requireConfirmation)
        Submitting the form creates a request for an appointment, which the provider confirms by e-mail. The booking becomes binding only once confirmed.
    @else
        After submitting you receive a confirmation by e-mail and the booking is binding on both parties.
    @endif
    While you fill in your details the chosen time is held for a few minutes so that nobody else takes it in the meantime.</p>

<h2>3. Prices and payment</h2>
<p>Prices shown next to the services are final prices for one provision of the service unless stated otherwise. Where a price is marked as indicative, the provider sets the exact amount after a consultation according to the scope of the service. Payment is made to the provider on site after the service; the booking page does not require or accept online payment.</p>

<h2>4. Changing and cancelling an appointment</h2>
<p>You can cancel an appointment using the link in the confirmation e-mail
    @if($cancelHours > 0)
        up to {{ $cancelHours }} hours before its start. After that, please contact the provider by phone or e-mail.
    @else
        at any time before its start.
    @endif
    Changes of the appointment are handled by the provider by phone or e-mail. The provider may cancel or move an appointment for serious operational reasons; in that case it informs you without delay and offers an alternative. Repeatedly failing to show up without cancelling entitles the provider to refuse further online bookings.</p>

<h2>5. Provision of the service</h2>
<p>Before providing the service the provider may ask for information needed to perform it safely (for example allergies or contraindications) and may refuse the service or suggest an alternative if performing it would not be appropriate. Booking an appointment is not a promise to perform a specific procedure where its suitability depends on an assessment on site.</p>

<h2>6. Personal data</h2>
<p>The provider processes the data from your booking as the controller in accordance with its <a href="{{ $privacyUrl }}">privacy notice</a>. The data are used to handle, confirm and remind you of the appointment.</p>

<h2>7. Complaints and disputes</h2>
<p>Complaints and suggestions are handled by the provider preferably in person or by e-mail using the contacts in section 1. If you are a consumer, you have the right to turn to {{ $adrBody }}. The booking is governed by the law of the country where the provider is established; mandatory consumer protection rules of the country of your residence are not affected.</p>
