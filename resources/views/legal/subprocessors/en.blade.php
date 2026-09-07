<h1>List of Sub-processors</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<h2>1. Purpose of this list</h2>
<p>This list forms Annex III to the Data Processing Agreement (the "DPA") available at <a href="{{ $links['dpa'] }}">{{ $links['dpa'] }}</a>. The operator of the rezervuj-ma.online platform ({{ $siteUrl }}) acts as a processor towards its customers (tenants) and, in providing the service, engages the further processors (sub-processors) listed below. Processor: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }}.</p>

<h2>2. General authorisation and notification of changes</h2>
<p>By entering into the DPA, the tenant grants a general authorisation to engage the sub-processors listed here. The processor will announce any intended addition or replacement of a sub-processor at least 30 days in advance by e-mail to the address associated with the tenant's account and, at the same time, by updating this page.</p>
<p>The tenant may object to a change in writing at {{ $operator['email'] }} within 30 days of the notification. If the objection cannot reasonably be resolved (for example by providing the service without the sub-processor concerned), the tenant may terminate the DPA and the service agreement with effect from the date the change is deployed, without any charges; the tenant may export its data as described in {{ $links['terms'] }}.</p>
{{-- [LAWYER] Verify that the 30-day objection period and the notification method (e-mail + web page) satisfy Art. 28(2) GDPR and Clause 7.7 of Decision 2021/915. --}}

<h2>3. Current sub-processors</h2>
<table>
  <thead>
    <tr>
      <th>Sub-processor</th>
      <th>Service</th>
      <th>Processing location</th>
      <th>Transfer mechanism</th>
      <th>Purpose</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Cloudflare, Inc., 101 Townsend St, San Francisco, CA 94107, USA</td>
      <td>CDN, WAF (web application firewall), DNS, bot management</td>
      <td>Global edge network (EU edge nodes preferred, but technically any location)</td>
      <td>EU-US Data Privacy Framework and Standard Contractual Clauses 2021/914, Module 3</td>
      <td>Security and delivery of the platform (protection against attacks, encrypted connections, abuse prevention)</td>
    </tr>
    <tr>
      <td>{{ $emailProvider }}</td>
      <td>Transactional e-mail delivery (booking confirmations, reminders, one-time codes)</td>
      <td>European Union</td>
      <td>No third-country transfer</td>
      <td>Sending e-mails to the tenant's customers and staff</td>
    </tr>
  </tbody>
</table>
<p>Cloudflare processes mainly network data (IP address, request headers, security cookies) and, transiently, the content of requests directed to the platform. The e-mail provider processes the recipient's e-mail address and the message content for the time needed for delivery.</p>
{{-- [LAWYER] Check the current status of Cloudflare's DPF certification and whether the specific e-mail provider ({{ $emailProvider }}) really processes exclusively within the EU. --}}

<h2>4. Infrastructure that is NOT a sub-processor</h2>
<ul>
  <li><strong>Hosting.</strong> The application and the database run on the processor's own server located in the Slovak Republic. No third party is involved – the processor operates the infrastructure itself, which is why hosting is not listed as a sub-processor.</li>
  <li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong>, 22-24 Boulevard Royal, L-2449 Luxembourg. PayPal processes tenants' subscription payments as an independent controller under its own terms. It receives no data about the tenant's customers and is not a sub-processor under the DPA. Further information is provided in the Privacy Policy: <a href="{{ $links['privacy'] }}">{{ $links['privacy'] }}</a>.</li>
</ul>

<h2>5. Jurisdiction of the ICT infrastructure (Regulation (EU) 2023/2854)</h2>
<p>In accordance with Chapter VI of the Data Act, we disclose that the ICT infrastructure used to provide the service is located in the Slovak Republic (application and database server) and in Cloudflare's global edge network (CDN and security layer). Data at rest is stored exclusively in the Slovak Republic; Cloudflare's edge network transits the data and caches it for short periods. Measures against unlawful access by third-country public authorities are described in the DPA and in {{ $links['terms'] }}.</p>

<h2>6. Change log</h2>
<ul>
  <li>{{ $effective }} – first version</li>
</ul>
