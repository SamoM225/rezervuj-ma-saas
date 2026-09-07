<h1>Privacy Notice – Booking Page</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<p>This notice applies to the personal data you enter when booking an appointment on this booking page. It is provided under Articles 13 and 14 of Regulation (EU) 2016/679 (GDPR).</p>

<h2>1. Controller</h2>
<p>The controller of your personal data is the service provider with whom you are booking the appointment:</p>
<ul>
  <li>Name: {{ $tenant->name }}</li>
  <li>Address: {{ $tenant->address }}</li>
  <li>E-mail: {{ $tenant->email }}</li>
  <li>Phone: {{ $tenant->phone }}</li>
</ul>

<h2>2. Processor and technical platform</h2>
<p>This booking page runs on the rezervuj-ma.online platform ({{ $siteUrl }}). Its operator processes your data on behalf of the controller as a processor under a data processing agreement and does not use it for its own purposes, except for securing the platform. The platform uses the following sub-processors: Cloudflare, Inc. (security, attack protection and content delivery; the transfer to the USA is safeguarded by the EU-US Data Privacy Framework and Standard Contractual Clauses) and a transactional e-mail provider established in the European Union. Information on how the platform processes data in its own name (e.g. security logs) is available in its privacy policy: <a href="{{ $links['privacy'] }}">{{ $links['privacy'] }}</a>.</p>

<h2>3. What data we process</h2>
<ul>
  <li>first name and surname,</li>
  <li>e-mail address,</li>
  <li>phone number,</li>
  <li>the chosen service, date and time of the appointment and, where applicable, the chosen staff member,</li>
  <li>any note you write into the booking yourself.</li>
</ul>
<p>Please do not enter health information or other sensitive data in the note unless the nature of the service requires it and the controller has expressly asked you to do so.</p>
{{-- [LAWYER] If the tenant provides medical or cosmetic services where health data is expected in the note, it must have its own legal basis under Art. 9(2) GDPR (e.g. explicit consent) and adjust this sentence. --}}

<h2>4. Purposes and legal bases</h2>
<ul>
  <li><strong>Creating and managing the booking</strong>, including changes or cancellation – performance of a contract or steps prior to entering into it (Art. 6(1)(b) GDPR).</li>
  <li><strong>Booking confirmation and appointment reminders</strong> by e-mail – part of the service provided (Art. 6(1)(b) GDPR).</li>
  <li><strong>Compliance with legal obligations</strong>, such as accounting (Art. 6(1)(c) GDPR).</li>
  <li><strong>Security, abuse prevention and the establishment or defence of legal claims</strong> – legitimate interest (Art. 6(1)(f) GDPR).</li>
  <li><strong>Marketing messages</strong> (news, offers) – solely on the basis of your consent (Art. 6(1)(a) GDPR), which you may withdraw at any time without affecting processing already carried out.</li>
</ul>
{{-- [LAWYER] In some countries (e.g. DE, AT) it may be preferable to obtain consent for appointment reminders or to state them expressly as part of the service in the tenant's terms. --}}

<h2>5. Retention period</h2>
<p>We keep booking data for {{ $retentionDays }} days after the appointment has taken place (or been cancelled); it is then deleted or anonymised. We keep data longer only where the law requires it (e.g. accounting records) or where it is needed to pursue legal claims. Marketing consent remains valid until withdrawn.</p>

<h2>6. Recipients of the data</h2>
<p>We disclose the data to the processor (the rezervuj-ma.online platform) and its sub-processors listed in section 2, and to public authorities where the law obliges us to do so. We do not sell the data or share it with third parties for marketing purposes.</p>

<h2>7. Transfers to third countries</h2>
<p>The data is stored in the European Union (Slovak Republic). When technically delivering the page, Cloudflare, Inc. (USA) may process network data outside the EU; this transfer is safeguarded by the EU-US Data Privacy Framework and the European Commission's Standard Contractual Clauses.</p>

<h2>8. Profiling and automated decision-making</h2>
<p>We do not carry out profiling or automated decision-making producing legal effects concerning you.</p>

<h2>9. Your rights</h2>
<p>Under Articles 15 to 22 GDPR you have the right of access, rectification, erasure, restriction of processing, data portability, the right to object to processing based on legitimate interest, and the right to withdraw consent at any time. You can exercise your rights by e-mail at {{ $tenant->email }} or via the contact details in section 1. We will respond within one month at the latest.</p>
<p>If you believe the processing infringes the law, you have the right to lodge a complaint with the supervisory authority: {{ $authority }}, or with the supervisory authority of the Member State in which you reside.</p>

<h2>10. Children</h2>
<p>The booking page may be used independently by persons aged {{ $minAge }} or older. A booking for a younger person may be made only by their parent or legal guardian.</p>

<h2>11. Cookies</h2>
<p>The booking page uses only strictly necessary cookies needed for its operation and security; it does not use analytics or marketing cookies, so your consent is not required. Details: <a href="{{ $links['cookies'] }}">{{ $links['cookies'] }}</a>.</p>
