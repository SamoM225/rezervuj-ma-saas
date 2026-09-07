<h1>Terms of Service for rezervuj-ma.online</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<h2>1. Introductory provisions and definitions</h2>
<p>These terms of service (the "Terms") govern the use of the online booking platform available at {{ $siteUrl }} (the "Service"). Operator: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }} (the "Operator").</p>
{{-- [LAWYER] The Operator is currently a private individual without a trade licence. Verify whether the scope and paid nature of the Service triggers a business registration duty under Slovak law and whether the term "Operator" is sufficient. --}}
<ul>
<li><strong>Tenant</strong> means a business or other person who creates an account and uses the Service to manage bookings of their clients.</li>
<li><strong>Customer</strong> means an end user who books an appointment on the Tenant's public booking page ({{ $siteUrl }}/{slug}/booking).</li>
<li><strong>Booking Page</strong> means the Tenant's public page created within the Service.</li>
<li><strong>Plan</strong> means the scope of features and limits of the Service (Free or Pro).</li>
</ul>
<p>The <a href="{{ $links['dpa'] }}">Data Processing Agreement</a> (DPA), the <a href="{{ $links['aup'] }}">Acceptable Use Policy</a>, the <a href="{{ $links['refunds'] }}">Refund Policy</a> and the <a href="{{ $links['privacy'] }}">Privacy Policy</a> form an integral part of the contract between the Operator and the Tenant.</p>

<h2>2. Who may use the Service</h2>
<p>The Service may be used only by persons over 18 years of age. The Tenant declares that they use the Service in the course of their business or other professional activity (B2B relationship). The provisions of Slovak Act No. 108/2024 Coll. on Consumer Protection apply to the Tenant only to the extent that they qualify as a consumer under the mandatory law of their own state.</p>
<p>If the Tenant is a natural person operating a business under Polish law and the contract is not of a professional nature for them (Article 38a of the Polish Consumer Rights Act), they have the right to withdraw from the contract within 14 days of its conclusion without giving a reason. Details and a model form are set out in the <a href="{{ $links['refunds'] }}">Refund Policy</a>. Beyond the statutory requirements, the Operator offers all Tenants a commercial 14-day money-back guarantee on the first payment.</p>
{{-- [LAWYER] Verify the current wording of Article 38a of the Polish ustawa o prawach konsumenta and the scope of quasi-consumer protection for digital services. --}}

<h2>3. Account and security</h2>
<p>Registration creates an account for the Tenant. The Tenant must provide truthful details and keep them up to date. The Tenant is responsible for the confidentiality of their login credentials and for all activity carried out through their account. The Service allows two-factor authentication (2FA) to be enabled; the Operator recommends it. The Tenant must report any suspected misuse of the account without delay to {{ $operator['email'] }}.</p>

<h2>4. Plans, prices and payments</h2>
<ul>
<li><strong>Free</strong>: a free plan limited to 50 bookings per calendar month.</li>
<li><strong>Pro</strong>: EUR 5 or USD 5 per month, or EUR 50 or USD 50 per year, depending on the selected currency.</li>
</ul>
<p>Payments are made exclusively via PayPal. The subscription renews automatically for the next period until cancelled by the Tenant. The Operator charges no commission on bookings or on payments between the Tenant and the Customer; the Service is not a payment intermediary. The Operator will announce any price change by e-mail at least 30 days in advance; the change applies only to the following period and the Tenant may cancel the subscription before it takes effect.</p>
<p>Prices are stated exclusive of taxes. The Tenant is responsible for the taxes and charges that apply to them in their state.</p>
{{-- [LAWYER] Verify the Operator's VAT position (private individual, registration threshold, OSS for services supplied to other EU Member States) and the tax wording. --}}

<h2>5. Tenant obligations and content</h2>
<p>The Tenant is responsible for all content of their Booking Page (name, service descriptions, prices, images) and for its compliance with the law. The Tenant undertakes to use the Service in accordance with the <a href="{{ $links['aup'] }}">Acceptable Use Policy</a> and not to interfere with the security or operation of the Service.</p>
<p>With respect to Customers' personal data, the Tenant is the controller and the Operator is the processor within the meaning of Article 28 GDPR. The <a href="{{ $links['dpa'] }}">Data Processing Agreement</a> forms part of the contract. The Tenant must provide Customers with their own privacy notice; the Service offers a template for this purpose, but the Tenant remains responsible for its content.</p>

<h2>6. Relationship with Customers</h2>
<p>The contract for the provision of a service (e.g. a haircut, treatment or lesson) is concluded exclusively between the Tenant and the Customer. The Operator is not a party to it, does not guarantee that the booked service will be provided or its quality, and does not handle Customers' complaints against the Tenant. The Operator processes Customer data only on the Tenant's instructions, except for data required for platform security, for which it acts as an independent controller (see the <a href="{{ $links['privacy'] }}">Privacy Policy</a>).</p>

<h2>7. Availability and changes to the Service</h2>
<p>The Operator provides the Service with professional care but without any guarantee of uninterrupted availability (no SLA). Planned maintenance will be announced in advance where possible. The Operator may develop or change the features of the Service; any material restriction of paid-plan features will be announced at least 30 days in advance.</p>

<h2>8. Intellectual property</h2>
<p>The software, design and brand of the Service remain the property of the Operator. The Tenant receives a non-exclusive, non-transferable licence to use the Service for the duration of the contract. Content and data entered by the Tenant remain the Tenant's property; the Tenant grants the Operator only the licence necessary to operate the Service.</p>

<h2>9. Data portability, switching and exit (Regulation (EU) 2023/2854 – Data Act)</h2>
<ul>
<li>The Tenant may export their data or switch to another provider or to their own infrastructure at any time.</li>
<li>The notice period for switching is no more than 2 months from the Tenant's notification.</li>
<li>After the notice period, a 30-day transition period begins during which the Operator provides reasonable assistance. If this is technically unfeasible, the Operator will notify and justify this within 14 working days.</li>
<li>After termination of the contract, the data remain available for download for a further 30 days (retrieval window); they are then deleted in accordance with the DPA.</li>
<li>Exportable data (exhaustive list): bookings, customers, services, categories, staff, account settings. Format: CSV and JSON, directly from the dashboard.</li>
<li>No fee is charged for switching, export or transition (EUR 0).</li>
<li>ICT infrastructure: data are stored on the Operator's server in the Slovak Republic; traffic passes through the global Cloudflare network (CDN, WAF, DNS), which processes network traffic outside the EU as well.</li>
<li>Measures against unlawful access by third-country authorities: data storage in the EU, encryption in transit (TLS 1.2+), contractual safeguards with Cloudflare (DPF and standard contractual clauses), assessment of every third-country authority request and its refusal where it has no basis in EU or Member State law, and notification of the Tenant where permitted by law.</li>
</ul>
{{-- [LAWYER] Verify compliance with Articles 25, 28 and 30 of the Data Act (in particular the 14-day deadline for notifying technical unfeasibility and the scope of "functional equivalence" for SaaS). --}}

<h2>10. Term and termination</h2>
<p>The contract is concluded for an indefinite period. The Tenant may terminate it at any time using the "Cancel subscription" button in the dashboard or by e-mail; cancellation takes effect at the end of the paid period and amounts already paid are not refunded except in the cases set out in the <a href="{{ $links['refunds'] }}">Refund Policy</a>. The account may be deleted at any time.</p>
<p>The Operator may restrict or suspend an account or Booking Page, or terminate the contract, in the event of a material breach of these Terms, the Acceptable Use Policy or the law, or in the event of non-payment. The Operator will inform the Tenant of any restriction together with a statement of reasons pursuant to Article 17 of Regulation (EU) 2022/2065 (DSA) and advise them of the possibility to object.</p>

<h2>11. Reporting illegal content (DSA)</h2>
<p>Anyone may report a Booking Page or content they consider illegal by e-mail to {{ $operator['email'] }}, stating the page address, the reason and their contact details. The Operator will assess the notice without undue delay, inform the notifier of the outcome and inform the affected Tenant of the measure taken together with a statement of reasons. Objections to a decision may be submitted to the same address within 6 months; the Operator handles them free of charge and not solely by automated means. The point of contact for authorities and users is the e-mail {{ $operator['email'] }}.</p>

<h2>12. Liability</h2>
<p>The Operator is liable for damage caused to the Tenant up to the amount of the fees paid by the Tenant for the Service in the 12 months preceding the damage. The Operator is not liable for lost profit, loss of business opportunities or indirect damage, for unavailability caused by third parties (PayPal, Cloudflare, connectivity providers) or force majeure, or for services provided by the Tenant to Customers. These limitations do not apply in cases of intent or gross negligence, or to the extent excluded by mandatory law.</p>

<h2>13. Governing law and dispute resolution</h2>
<p>The contract is governed by the law of the Slovak Republic; the courts of the Slovak Republic have jurisdiction. If the Tenant qualifies as a consumer or quasi-consumer under the mandatory law of the state of their habitual residence, that law remains unaffected. A consumer may contact the Slovak Trade Inspection (Slovenská obchodná inšpekcia) or another alternative dispute resolution body under Slovak Act No. 391/2015 Coll. The European Online Dispute Resolution (ODR) platform was discontinued on 20 July 2025 and is no longer available.</p>

<h2>14. Changes to the Terms</h2>
<p>The Operator may amend these Terms. Changes will be announced by e-mail at least 30 days before they take effect. If the Tenant does not agree with a change, they may terminate the contract free of charge before the effective date; continued use of the Service after that date is deemed acceptance.</p>

<h2>15. Final provisions and contact</h2>
<p>The contract is concluded in the Slovak language within the meaning of Section 5 of Slovak Act No. 22/2004 Coll.; the Czech and English versions are for information only and the Slovak version prevails in case of conflict. If any provision is invalid, the remaining provisions remain in force. Contact: {{ $operator['name'] }}, {{ $operator['email'] }}.</p>
