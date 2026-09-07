<h1>Privacy Policy</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<h2>1. Who is the controller</h2>
<p>Operator: {{ $operator['name'] }}<br>E-mail: {{ $operator['email'] }}</p>
<p>The operator of the service {{ $siteUrl }} (the "platform") is a private individual based in the Slovak Republic. No data protection officer (DPO) has been appointed because the operator does not meet the conditions of Article 37 GDPR; for all data protection matters, contact us at the e-mail address above.</p>
{{-- [LAWYER] Verify whether the scale of processing after launch triggers the obligation to appoint a DPO under Art. 37 GDPR / § 44 Slovak Act No. 18/2018 Coll. --}}

<h2>2. What this policy covers</h2>
<p>This policy applies to processing of personal data for which the platform operator is the controller within the meaning of Article 4(7) GDPR: accounts of service providers (the "tenants"), billing, support, website visitors and platform security.</p>
<p>Personal data you enter on the public booking page of a specific tenant (e.g. <code>{{ $siteUrl }}/business-name/booking</code>) is processed by that tenant as controller. We process it only as a processor under a data processing agreement (<a href="{{ $links['dpa'] }}">DPA</a>). Information about the processing of your booking is provided in the privacy notice the tenant displays on its booking page; in that case, please address requests to exercise your rights primarily to the tenant.</p>

<h2>3. What data we process, why and on which legal basis</h2>

<h3>3.1 Tenant accounts and the dashboard</h3>
<p><strong>Data:</strong> name, e-mail, phone, password (stored only as a hash), two-factor authentication (2FA) secret, language setting, business name and settings, login records.<br><strong>Purpose:</strong> creating and maintaining the account, providing the service, communicating about the service.<br><strong>Legal basis:</strong> performance of a contract – Article 6(1)(b) GDPR.</p>

<h3>3.2 Billing and payments via PayPal</h3>
<p><strong>Data:</strong> payer e-mail, amount, currency, PayPal subscription and transaction identifier, payment date, billing details.<br><strong>Purpose:</strong> processing payment for the Pro plan, issuing accounting documents, complying with accounting and tax obligations.<br><strong>Legal basis:</strong> Article 6(1)(b) GDPR (contract) and Article 6(1)(c) GDPR (legal obligation, in particular Slovak Act No. 431/2002 Coll. on Accounting).</p>
<p>Payments are processed by PayPal (Europe) S.à r.l. et Cie, S.C.A., 22–24 Boulevard Royal, L-2449 Luxembourg, which acts as an independent controller for payment data and is governed by its own privacy policy. The operator never receives or stores payment card numbers.</p>

<h3>3.3 Support and communication</h3>
<p><strong>Data:</strong> name, e-mail, content of the message, account details needed to resolve the request.<br><strong>Purpose:</strong> answering questions, resolving technical problems and complaints.<br><strong>Legal basis:</strong> Article 6(1)(b) GDPR if you are a tenant; otherwise Article 6(1)(f) GDPR – legitimate interest in responding to messages we receive.</p>

<h3>3.4 Website visitors and security logs</h3>
<p><strong>Data:</strong> IP address, browser and device type (user agent), timestamp, requested address (URL), response status code, session identifier.<br><strong>Purpose:</strong> securing operations, detecting and investigating attacks, abuse and errors, defending legal claims.<br><strong>Legal basis:</strong> Article 6(1)(f) GDPR – legitimate interest in the security and functionality of the platform.<br><strong>Retention:</strong> 12 months.</p>

<h3>3.5 Bot and abuse protection on booking pages</h3>
<p>Tenants' public booking pages are protected against automated spam and abuse (rate limiting, security scoring of requests by Cloudflare). For this processing of the IP address and technical request signals the operator acts as an independent controller, because it concerns the security of the whole platform, not a booking with a specific tenant. Legal basis: Article 6(1)(f) GDPR. We do not build any visitor profiles.</p>

<h3>3.6 Transactional e-mails</h3>
<p><strong>Data:</strong> e-mail address, content of the message (booking confirmation, one-time verification code, account change notification, payment receipt).<br><strong>Purpose:</strong> performing the service and securing the account.<br><strong>Legal basis:</strong> Article 6(1)(b) GDPR. These messages are not marketing and cannot be unsubscribed from while the account is active.</p>

<h3>3.7 Marketing e-mails</h3>
<p>We send news and commercial offers solely on the basis of your prior consent (Article 6(1)(a) GDPR and § 116 of Slovak Act No. 452/2021 Coll. on Electronic Communications). You may withdraw consent at any time by clicking the link in every message or by e-mailing {{ $operator['email'] }}; withdrawal does not affect the lawfulness of processing before withdrawal.</p>
{{-- [LAWYER] If the existing-customer exception (§ 116(15) Act No. 452/2021 Coll.) is to be relied on, add its conditions and the opt-out offered at the time the contact is collected. --}}

<h2>4. Recipients of personal data</h2>
<ul>
    <li><strong>Cloudflare, Inc.</strong> (USA) – content delivery network, DNS, attack protection (WAF). Transfer safeguarded by the EU-U.S. Data Privacy Framework and the Commission's standard contractual clauses (Decision 2021/914).</li>
    <li><strong>{{ $emailProvider }}</strong> – delivery of transactional e-mails, servers in the EU; processor.</li>
    <li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong> – payments; independent controller.</li>
    <li><strong>Public authorities</strong> – only where required by law or an enforceable decision.</li>
</ul>
<p>The platform is hosted by the operator on the operator's own server located in the Slovak Republic. We do not sell personal data, do not build profiles and do not take automated decisions with legal effects within the meaning of Article 22 GDPR. Current list of processors: <a href="{{ $links['subprocessors'] }}">Sub-processors</a>.</p>

<h2>5. Transfers to third countries</h2>
<p>Data is primarily processed in the EU. The Cloudflare service may involve a transfer to the USA; we rely on the Commission's adequacy decision for the EU-U.S. Data Privacy Framework and, as a fallback, on the standard contractual clauses (2021/914) with supplementary measures (TLS encryption in transit, data minimisation at the edge network). You may request a copy of the safeguards at {{ $operator['email'] }}.</p>

<h2>6. Retention periods</h2>
<table>
    <thead>
        <tr><th>Category</th><th>Retention period</th><th>Reason</th></tr>
    </thead>
    <tbody>
        <tr><td>Invoices and payment records</td><td>10 years from the end of the accounting year</td><td>§ 35 Slovak Act No. 431/2002 Coll. on Accounting</td></tr>
        <tr><td>Tenant account data</td><td>Life of the account + 30 days</td><td>Performance of the contract, data export window</td></tr>
        <tr><td>Security logs</td><td>12 months</td><td>Legitimate interest – security</td></tr>
        <tr><td>One-time verification codes</td><td>24 hours</td><td>Account security</td></tr>
        <tr><td>Support communication</td><td>3 years after the request is closed</td><td>Defence of legal claims</td></tr>
        <tr><td>Consent records (marketing)</td><td>Until withdrawal + 3 years</td><td>Demonstrating consent (Art. 7(1) GDPR)</td></tr>
    </tbody>
</table>
{{-- [LAWYER] Verify the 3-year periods for support and consent records against the general limitation period under the Slovak Civil Code (§ 101). --}}
<p>Backups are automatically overwritten within 30 days, so data deleted from the production database disappears from backups no later than after that period.</p>

<h2>7. Cookies</h2>
<p>We use strictly necessary cookies only (session, CSRF protection, language, trusted device, Cloudflare security cookies), for which no consent is required under § 109(8) of Slovak Act No. 452/2021 Coll. We do not use analytics or marketing cookies. Details: <a href="{{ $links['cookies'] }}">Cookie Policy</a>.</p>

<h2>8. Your rights</h2>
<p>Under Articles 15 to 22 GDPR you have the right:</p>
<ul>
    <li>to access your personal data and obtain a copy (Art. 15),</li>
    <li>to rectification of inaccurate or incomplete data (Art. 16),</li>
    <li>to erasure ("right to be forgotten") where there is no reason for further retention (Art. 17),</li>
    <li>to restriction of processing (Art. 18),</li>
    <li>to data portability in a structured, commonly used format (Art. 20),</li>
    <li>to object to processing based on legitimate interest under Article 6(1)(f) GDPR (Art. 21),</li>
    <li>to withdraw consent at any time where processing is based on consent (Art. 7(3)),</li>
    <li>not to be subject to automated individual decision-making (Art. 22) – we do not carry out such decision-making.</li>
</ul>
<p>You can exercise your rights by e-mail to {{ $operator['email'] }} or directly in your account settings (export and account deletion). We will respond without undue delay and at the latest within one month; for complex requests we may extend this by a further two months, in which case we will inform you. To verify your identity we may ask for confirmation from the e-mail address linked to the account.</p>
<p>If you believe the processing infringes the law, you have the right to lodge a complaint with the supervisory authority: <strong>Úrad na ochranu osobných údajov Slovenskej republiky</strong> (Office for Personal Data Protection of the Slovak Republic), Hraničná 12, 820 07 Bratislava 27, Slovak Republic, <a href="https://dataprotection.gov.sk">dataprotection.gov.sk</a>, or with the supervisory authority of the Member State of your habitual residence, place of work or place of the alleged infringement.</p>

<h2>9. Children</h2>
<p>A tenant account may only be created by a person who has reached the age of 18. We do not knowingly process children's data as controller; the age limit on booking pages is set by the respective tenant in its notice.</p>

<h2>10. Security</h2>
<p>Data in transit is encrypted with TLS 1.2 or higher, passwords are stored only as a strong hash, accounts can be protected with two-factor authentication, access to data is limited by the principle of least privilege, and the platform is protected by the Cloudflare firewall and rate limiting. Daily backups are encrypted and kept for 30 days. We do not store payment card data. More detailed technical and organisational measures are set out in the <a href="{{ $links['dpa'] }}">DPA</a>.</p>

<h2>11. Changes to this policy</h2>
<p>We may update this policy when the service or the law changes. The new version will be published on this page with its version number and effective date; we will inform tenants of material changes by e-mail or a notice in the dashboard at least 14 days in advance.</p>

<h2>12. Legal references</h2>
<p>Regulation (EU) 2016/679 (GDPR); Slovak Act No. 18/2018 Coll. on Personal Data Protection; Slovak Act No. 452/2021 Coll. on Electronic Communications; Slovak Act No. 22/2004 Coll. on Electronic Commerce; Slovak Act No. 431/2002 Coll. on Accounting. Related documents: <a href="{{ $links['terms'] }}">Terms of Service</a>, <a href="{{ $links['dpa'] }}">DPA</a>, <a href="{{ $links['cookies'] }}">Cookies</a>.</p>
