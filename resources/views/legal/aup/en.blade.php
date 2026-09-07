<h1>Acceptable Use Policy</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<p>This Acceptable Use Policy (the "Policy") supplements the <a href="{{ $links['terms'] }}">Terms of Service</a> of the platform {{ $siteUrl }} (the "Platform"), operated by {{ $operator['name'] }} (the "Operator"). It applies to customers of the Platform (the "Tenants"), their employees and collaborators, as well as to anyone who uses the Tenants' public booking pages. The purpose of the Policy is to protect end customers, other Tenants and the Platform itself.</p>

<h2>1. Prohibited conduct and content</h2>
<p>When using the Platform, it is prohibited in particular to:</p>
<ul>
  <li>publish or offer illegal content or services, or use the Platform for any activity that violates the laws of the Slovak Republic, the European Union or the country in which the Tenant operates;</li>
  <li>create misleading or fraudulent booking pages, impersonate another person or business, or use the Platform for phishing or other collection of data under false pretences;</li>
  <li>distribute malicious code, links to malicious sites or content that endangers the security of users;</li>
  <li>send unsolicited messages (spam), bulk e-mails or SMS without the recipients' consent through the Platform, or misuse confirmation and reminder messages for marketing;</li>
  <li>collect, extract or automatically download (scrape) personal data or other content from the Platform or from the booking pages of other Tenants;</li>
  <li>burden the Platform with an unreasonable number of requests, circumvent rate limits, plan quotas (for example the limit of 50 bookings per month in the Free plan) or other technical restrictions, including creating multiple free accounts to bypass quotas;</li>
  <li>carry out security testing, penetration tests or vulnerability scanning of the Platform without the Operator's prior written permission;</li>
  <li>store special categories of personal data (for example health data) in notes or other fields without a lawful basis under Article 9 GDPR and without adequately informing the data subjects;</li>
  <li>publish content infringing third-party intellectual property rights, hate speech, content inciting violence or any sexual content depicting minors;</li>
  <li>offer services whose provision requires an authorisation or licence (for example healthcare or other regulated services) without the Tenant holding such authorisation.</li>
</ul>
{{-- [LAWYER] Assess whether the scope of prohibited content matches the Operator's obligations as a hosting service provider under the DSA and whether specific categories need to be added for the target markets. --}}

<h2>2. Obligations of Tenants</h2>
<p>The Tenant is the controller of its customers' personal data and is obliged to:</p>
<ul>
  <li>publish its own privacy information on its booking page; the Platform provides a template for this purpose which the Tenant completes with its own details;</li>
  <li>handle its customers' requests to exercise their rights under the GDPR (access, rectification, erasure and others) and ensure that employees and collaborators with access to the data comply with this Policy;</li>
  <li>collect only the data it needs to handle the booking, and instruct customers not to enter sensitive information in the notes unless necessary;</li>
  <li>provide truthful information about its business, services, prices and opening hours.</li>
</ul>

<h2>3. Fair use and quotas</h2>
<p>The Platform is intended for managing bookings of the ordinary operational volume of small and medium-sized businesses. The Operator applies technical limits (number of requests per unit of time, attachment size, number of bookings according to the plan) that protect the availability of the service for everyone. If a Tenant's usage significantly exceeds the ordinary volume and threatens the stability of the Platform, the Operator will notify the Tenant and propose a solution; restrictions without notice are applied only in the event of an acute threat.</p>

<h2>4. Reporting illegal content (notice and action)</h2>
<p>Anyone may notify the Operator that a particular booking page or part of it contains illegal content or violates this Policy. Send the notice to {{ $operator['email'] }} and include:</p>
<ol>
  <li>the exact address (URL) of the page or a description of where the content is located;</li>
  <li>a sufficiently substantiated explanation of why you consider the content illegal or contrary to the Policy;</li>
  <li>your name and e-mail address (except for notices concerning child sexual abuse, where identification is not required);</li>
  <li>a statement that you are submitting the notice in good faith and that the information provided is accurate and complete.</li>
</ol>
<p>The Operator will review the notice without undue delay, in a diligent, non-arbitrary and objective manner, confirm receipt to the notifier and communicate the outcome. If additional information is needed, the Operator will request it.</p>

<h2>5. Measures and statement of reasons</h2>
<p>In the event of a violation of the Policy or of the law, the Operator may, proportionately to the severity, take in particular the following measures: remove or disable access to specific content, restrict the visibility of the booking page, temporarily suspend the Tenant's account or, in the case of a serious or repeated violation, terminate the account. Before taking a measure the Operator will normally warn the Tenant and give them a period to remedy the situation; it acts immediately only in the case of manifestly illegal content or an imminent security threat.</p>
<p>For every restriction the Operator will provide the affected Tenant with a clear and specific statement of reasons within the meaning of Article 17 of Regulation (EU) 2022/2065 on a Single Market for Digital Services (DSA), containing:</p>
<ul>
  <li>a description of the measure taken and its duration;</li>
  <li>the facts and circumstances relied on, including whether the Operator acted on the basis of a notice or on its own initiative;</li>
  <li>the legal ground (provision of law) or contractual ground (provision of this Policy or of the Terms) for the decision;</li>
  <li>information on the possibilities of redress: an internal complaint to {{ $operator['email'] }}, which the Operator will assess within 14 days, as well as the possibility of out-of-court dispute settlement or judicial redress.</li>
</ul>
{{-- [LAWYER] Verify whether the Operator, as a small enterprise under the DSA, is subject to further obligations (e.g. Art. 20 internal complaint-handling system applies only to online platforms; assess whether Tenants' booking pages create online-platform status). --}}
<p>Repeated or intentional misuse of the Platform, as well as the repeated submission of manifestly unfounded notices, may lead to termination of access to the Platform. In all measures the Operator takes into account the fundamental rights of those affected, including freedom of expression and the freedom to conduct a business.</p>

<h2>6. Requests from public authorities</h2>
<p>If the Operator receives an order from a court or another competent authority of a Member State to remove content or to provide information, it proceeds in accordance with Articles 9 and 10 DSA and applicable national law. The Operator informs the affected Tenant of the order received unless prohibited by law or by the order itself.</p>

<h2>7. Point of contact</h2>
<p>The point of contact for notices, complaints and communication with public authorities under Articles 11 and 12 DSA is {{ $operator['email'] }}. Communication is possible in Slovak, Czech and English. Operator: {{ $operator['name'] }}.</p>
