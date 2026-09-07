<h1>Data Processing Agreement (DPA)</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<p>This Data Processing Agreement (the "<strong>DPA</strong>") forms part of the Terms of Service of rezervuj-ma.online available at {{ $links['terms'] }} and is concluded between the <strong>controller</strong> (within the meaning of Art. 4(7) GDPR – the user of the service who has created an account and operates a booking page, the "<strong>Controller</strong>") and the <strong>processor</strong> (within the meaning of Art. 4(8) GDPR – the service provider: {{ $operator['name'] }}, e-mail {{ $operator['email'] }}, the "<strong>Processor</strong>"). The DPA is accepted by accepting the Terms of Service when creating an account; no separate signature is required.</p>

<p>The text of the DPA is based on the standard contractual clauses between controllers and processors under Commission Implementing Decision (EU) 2021/915 and meets the requirements of Art. 28(3) and (4) GDPR and Section 34 of Slovak Act No. 18/2018 Coll. on the protection of personal data.</p>

<h2>Section I – Introductory provisions</h2>

<h3>Clause 1 – Purpose and scope</h3>
<ul>
    <li>The purpose of this DPA is to ensure compliance with Art. 28(3) and (4) GDPR in the processing of personal data that the Processor processes on behalf of the Controller in the course of operating the booking system on the domain {{ $siteUrl }} (the "<strong>Service</strong>").</li>
    <li>The DPA applies to the processing described in Annex I.</li>
    <li>Annexes I to III form an integral part of the DPA.</li>
    <li>The DPA does not relieve the Controller of its own obligations under the GDPR (in particular providing information to data subjects under Art. 13 GDPR on the booking page).</li>
</ul>

<h3>Clause 2 – Invariability of the clauses</h3>
<p>The parties undertake not to modify the clauses of this DPA except to add information to the annexes or to update information contained therein. This does not prevent the parties from agreeing other clauses or additional safeguards, provided they do not contradict the DPA or prejudice the fundamental rights and freedoms of data subjects.</p>

<h3>Clause 3 – Interpretation</h3>
<p>Terms defined in the GDPR have the same meaning in this DPA. The DPA shall be read and interpreted in the light of the provisions of the GDPR and shall not be interpreted in a way that conflicts with the rights and obligations provided for in the GDPR.</p>

<h3>Clause 4 – Hierarchy</h3>
<p>In the event of a conflict between this DPA and the provisions of the Terms of Service or other agreements between the parties, this DPA shall prevail.</p>

<h3>Clause 5 – Docking clause</h3>
<p>An entity that is not a party to this DPA may, with the agreement of both parties, accede to it as a controller or processor by completing the annexes; upon accession it acquires the rights and obligations of a party in the corresponding role.</p>

<h2>Section II – Obligations of the parties</h2>

<h3>Clause 6 – Description of the processing</h3>
<p>The details of the processing, in particular the categories of personal data and the purposes for which they are processed on behalf of the Controller, are set out in Annex I.</p>

<h3>Clause 7 – Obligations of the parties</h3>

<h4>7.1 Instructions</h4>
<ul>
    <li>The Processor processes personal data only on documented instructions from the Controller, unless required to do so by Union or Member State law to which the Processor is subject; in such a case the Processor informs the Controller of that legal requirement before processing, unless the law prohibits this.</li>
    <li>Documented instructions are the Terms of Service, this DPA and the settings the Controller makes in the administration interface of the Service (e.g. retention period, mandatory fields of the booking form, enabling reminders, export or deletion of data). Further instructions may be given by the Controller in writing to {{ $operator['email'] }}.</li>
    <li>The Processor shall immediately inform the Controller if, in its opinion, an instruction infringes the GDPR or other data protection law.</li>
</ul>

<h4>7.2 Purpose limitation</h4>
<p>The Processor processes personal data only for the specific purpose(s) set out in Annex I, unless it receives further instructions from the Controller. The Processor does not use the Controller's end-customer data for its own marketing purposes or for profiling.</p>

<h4>7.3 Duration of the processing</h4>
<p>Processing continues for the duration of the contractual relationship established by the Terms of Service and thereafter for the data return and deletion period under Clause 12.</p>

<h4>7.4 Security of processing</h4>
<ul>
    <li>The Processor shall implement at least the technical and organisational measures specified in Annex II to ensure the security of personal data, including protection against a personal data breach. In assessing the appropriate level of security, the parties take into account the state of the art, the costs of implementation, the nature, scope, context and purposes of processing and the risks to data subjects.</li>
    <li>The Processor grants access to personal data only to persons who need it to perform the contract and who are bound by a duty of confidentiality (contractual or statutory).</li>
</ul>

<h4>7.5 Sensitive data</h4>
<p>The Service is not intended for processing special categories of personal data (Art. 9 GDPR), data relating to criminal convictions (Art. 10 GDPR) or data of children without the consent of a legal guardian. If the Controller (e.g. a medical or cosmetic facility) records health data in booking notes or custom fields, it must (i) have a legal basis under Art. 9(2) GDPR, (ii) inform the Processor in writing before commencing such processing and (iii) limit the recorded data to the strict minimum. {{-- [LAWYER] Verify whether tenants from the healthcare sector (clinics) need a separate addendum with stricter measures or an express exclusion of health data from notes. --}}</p>

<h4>7.6 Documentation and compliance</h4>
<ul>
    <li>The parties shall be able to demonstrate compliance with this DPA.</li>
    <li>The Processor shall deal promptly and adequately with enquiries from the Controller about information needed to demonstrate compliance with the obligations under this DPA.</li>
    <li>The Processor shall make available to the Controller all information necessary to demonstrate compliance with the obligations under this DPA and Art. 28 GDPR and shall, on request, allow for and contribute to audits, including inspections (procedure under Clause 13).</li>
</ul>

<h4>7.7 Use of sub-processors</h4>
<ul>
    <li><strong>General written authorisation.</strong> The Controller grants the Processor a general authorisation to engage the sub-processors listed in Annex III. The current list is also published at {{ $links['subprocessors'] }}.</li>
    <li><strong>Notice of changes.</strong> The Processor shall inform the Controller of any intended addition or replacement of a sub-processor at least <strong>30 days</strong> in advance (by e-mail to the account address and by updating the list at the address above).</li>
    <li><strong>Right to object.</strong> The Controller may raise a reasoned objection within that period. If the parties cannot find a solution, the Controller has the right to terminate the Service contract with effect before the new sub-processor is deployed; any unused part of a prepaid period is refunded in that case.</li>
    <li>The Processor shall impose on the sub-processor, by written contract, substantially the same data protection obligations as those binding the Processor under this DPA and remains fully responsible to the Controller for the performance of the sub-processor's obligations.</li>
    <li>At the Controller's request, the Processor shall provide a copy of the sub-processor agreement (commercial terms may be redacted before disclosure).</li>
</ul>

<h4>7.8 International transfers</h4>
<ul>
    <li>Personal data are primarily stored on the Processor's server in the Slovak Republic. A transfer to a third country takes place only on documented instructions from the Controller or to the extent necessary for the sub-processors in Annex III, and only in compliance with Chapter V GDPR.</li>
    <li>For transfers to the USA (Cloudflare) the Processor relies primarily on the adequacy decision for the <strong>EU-US Data Privacy Framework</strong>; as a fallback mechanism the standard contractual clauses under Commission Implementing Decision (EU) 2021/914 (Module 3: processor to sub-processor) apply, including a transfer impact assessment and supplementary measures. {{-- [LAWYER] Verify the current validity of the EU-US DPF and Cloudflare's certification as of the effective date. --}}</li>
    <li>If the Controller is established outside the EEA and the processing of its customers' data is subject to the GDPR (e.g. under Art. 3(2)), the parties agree that the transfer of data from the Processor (in the EEA) to the Controller (outside the EEA) is governed by <strong>Module 4</strong> (processor to controller) of the 2021/914 standard contractual clauses, which are hereby deemed incorporated into this DPA with annexes corresponding to Annexes I and II hereof. {{-- [LAWYER] Verify the applicability of Module 4 for third-country tenants (e.g. UK, CH, USA) and any need for the UK Addendum / Swiss finish. --}}</li>
</ul>

<h3>Clause 8 – Assistance to the Controller</h3>
<ul>
    <li>The Processor shall promptly notify the Controller of any request from a data subject that it receives directly (e.g. by e-mail from the Controller's end customer); it shall not respond to the request itself unless authorised to do so by the Controller.</li>
    <li>The Processor assists the Controller in fulfilling its obligation to respond to data subject requests (Arts. 15 to 22 GDPR), in particular by providing search, export, rectification and deletion functions for customer records in the administration interface.</li>
    <li>Taking into account the nature of the processing and the information available to it, the Processor assists the Controller in complying with the obligations under Arts. 32 to 36 GDPR: (a) security of processing, (b) notification of a personal data breach to the supervisory authority, (c) communication to data subjects, (d) data protection impact assessment, (e) prior consultation with the supervisory authority.</li>
    <li>The parties set out in Annex II the appropriate technical and organisational measures by which the Processor provides assistance, as well as the scope and extent of the assistance required.</li>
</ul>

<h3>Clause 9 – Notification of a personal data breach</h3>
<ul>
    <li>In the event of a personal data breach concerning data processed on behalf of the Controller, the Processor shall notify the Controller <strong>without undue delay and no later than 48 hours</strong> after becoming aware of it, to the e-mail address of the Controller's account.</li>
    <li>The notification shall contain at least: (a) a description of the nature of the breach (where possible, the categories and approximate number of data subjects and records concerned), (b) contact details for obtaining further information, (c) the likely consequences of the breach, (d) the measures taken or proposed to address the breach and mitigate its effects. Where it is not possible to provide all information at the same time, it shall be provided in phases without undue delay.</li>
    <li>The Processor cooperates with the Controller and assists it in complying with the obligations under Arts. 33 and 34 GDPR, taking into account the nature of the processing and the information available to it.</li>
    <li>The Processor keeps records of breaches, including the facts, their effects and the remedial action taken.</li>
</ul>

<h2>Section III – Final provisions</h2>

<h3>Clause 10 – Non-compliance and termination</h3>
<ul>
    <li>If the Processor is in breach of its obligations under this DPA, the Controller may suspend the processing of personal data (e.g. by deactivating the booking page) until the breach is remedied or the contract is terminated.</li>
    <li>The Controller is entitled to terminate the contract insofar as it concerns the processing of personal data if (a) processing has been suspended and compliance is not restored within a reasonable time and in any event within one month, (b) the Processor is in substantial or persistent breach of this DPA or of its obligations under the GDPR, (c) the Processor fails to comply with a binding decision of a competent court or supervisory authority.</li>
    <li>The Processor is entitled to terminate the contract if, after being warned, the Controller insists on instructions that infringe the law, or if the Controller enters data into the Service contrary to Clause 7.5 or the Acceptable Use Policy ({{ $links['aup'] }}).</li>
</ul>

<h3>Clause 11 – Liability</h3>
<p>The parties' liability for damage caused to data subjects is governed by Art. 82 GDPR. The Processor's liability towards the Controller is limited to the extent agreed in the Terms of Service; this limitation does not apply to claims of data subjects under Art. 82 GDPR or to damage caused intentionally. {{-- [LAWYER] Check that the limitation of liability in the Terms of Service is consistent with Sections 379 et seq. of the Slovak Commercial Code and with Art. 82 GDPR. --}}</p>

<h3>Clause 12 – Deletion and return of data</h3>
<ul>
    <li>During the term of the Service the Controller may at any time export customer, booking, service, category, staff and settings data in CSV or JSON format.</li>
    <li>After termination of the Service (for any reason) the export remains available for <strong>30 days</strong> (the "data retrieval period"). Within that period the Controller chooses between the return of the data by export or its deletion; if the Controller does not respond, the data are deleted.</li>
    <li>After the data retrieval period the Processor deletes all personal data processed on behalf of the Controller from production systems <strong>within 30 days</strong>. Backup copies are automatically overwritten no later than <strong>30 days</strong> after deletion from production; until overwritten, backups are used for no purpose other than disaster recovery.</li>
    <li>The Processor may retain data longer only to the extent required by Union or Member State law (in particular accounting records relating to the Controller as a customer of the Service, which are however not subject to this DPA).</li>
    <li>On request, the Processor confirms deletion in writing.</li>
</ul>

<h3>Clause 13 – Audits</h3>
<ul>
    <li>The Processor demonstrates compliance primarily through documentation: this DPA, the description of measures in Annex II, the list of sub-processors, breach records and written answers to the Controller's questions. Answers are provided within 30 days of receipt of the request.</li>
    <li>Where documentation is reasonably insufficient, the Controller (or an independent auditor mandated by it and bound by confidentiality) may carry out an on-site audit <strong>at most once per calendar year</strong>, with at least 30 days' written notice, during normal business hours and in a manner that does not jeopardise security or the data of other controllers (tenants). The Controller bears the costs of the audit.</li>
    <li>The frequency limitation does not apply to an audit required by a supervisory authority or an audit following a personal data breach.</li>
</ul>

<h3>Clause 14 – Governing law and amendments</h3>
<p>This DPA is governed by the law of the Slovak Republic and the GDPR. The Processor may update the DPA in connection with changes in legislation or the Service; it informs the Controller of material changes at least 30 days in advance in the same manner as for changes to the Terms of Service. The current version is always available at {{ $links['dpa'] }}. Information on the processing of the Controller's own data as a customer of the Service (not on its behalf) is set out in the Privacy Policy at {{ $links['privacy'] }}.</p>

<h2>Annex I – Description of the processing</h2>
<table>
    <tbody>
        <tr><th>Subject matter</th><td>Operation of an online booking system (booking page, calendar, customer and staff management, transactional e-mails) for the Controller.</td></tr>
        <tr><th>Duration</th><td>For the lifetime of the Controller's account and thereafter for the periods under Clause 12.</td></tr>
        <tr><th>Nature and purpose</th><td>Collection, storage, display, sending of confirmations and reminders, export and deletion of data for the purpose of recording and managing the Controller's bookings. No profiling and no automated decision-making.</td></tr>
        <tr><th>Categories of data subjects</th><td>(a) The Controller's customers (persons booking an appointment); (b) the Controller's staff (employees and collaborators kept in the system).</td></tr>
        <tr><th>Categories of personal data</th><td>Customers: identification data (first and last name), contact data (e-mail, phone), booking data (service, date and time, price, status, history), notes entered by the customer or the Controller, technical data about the form submission. Staff: name, contact data, work schedule, assigned services, login credentials (password in hashed form only).</td></tr>
        <tr><th>Special categories of data</th><td>Excluded. Processed only if the Controller demonstrates a legal basis under Art. 9(2) GDPR and follows Clause 7.5.</td></tr>
        <tr><th>Frequency</th><td>Continuous, for the duration of the Service.</td></tr>
        <tr><th>Retention period</th><td>As configured by the Controller in the administration interface (number of days after the booking date); on expiry customer data are anonymised or deleted.</td></tr>
        <tr><th>Sub-processors</th><td>As set out in Annex III.</td></tr>
    </tbody>
</table>

<h2>Annex II – Technical and organisational measures</h2>
<p>The Processor has implemented and maintains the following measures. The list is exhaustive; the Processor does not claim ISO/IEC 27001 certification or a SOC 2 report.</p>
<ul>
    <li><strong>Encryption in transit:</strong> all communication with the Service takes place over HTTPS with TLS 1.2 or later; unencrypted connections are redirected.</li>
    <li><strong>Credential protection:</strong> passwords are stored exclusively in hashed form (modern adaptive algorithm); two-factor authentication (2FA) is available for Controller and staff accounts.</li>
    <li><strong>Rate limiting:</strong> login, password reset, the booking form and the API are protected by limits against password guessing and automated abuse.</li>
    <li><strong>Web application firewall:</strong> traffic is routed through the Cloudflare WAF with protection against common attacks and bots.</li>
    <li><strong>Access control:</strong> principle of least privilege; administrative access to the server is held only by the Processor, via key-authenticated remote access; access of the Controller's staff is role-based within the tenant.</li>
    <li><strong>Logical tenant separation:</strong> each Controller's data are bound in the application to a tenant identifier and every query is filtered by it; cross-tenant access is blocked at the application level.</li>
    <li><strong>Backups:</strong> daily encrypted database backups, retained for 30 days and then automatically overwritten; restoration is tested regularly.</li>
    <li><strong>Security logs:</strong> records of logins, permission changes and security events are retained for 6 to 12 months and used exclusively for detecting and investigating incidents.</li>
    <li><strong>Payment data:</strong> the Service does not store payment card or bank account numbers; payments are processed by PayPal as an independent controller.</li>
    <li><strong>Organisational measures:</strong> dependency and operating system updates, separate development and production environments, an incident response procedure with notification under Clause 9, regular review of this list.</li>
</ul>
<p><strong>Assistance to the Controller (Clause 8):</strong> search, export (CSV/JSON), rectification and deletion of a customer record in the administration interface; export of the whole tenant; written information for a DPIA on request.</p>

<h2>Annex III – List of sub-processors</h2>
<p>The Controller has authorised the use of the following sub-processors. Current list and change history: {{ $links['subprocessors'] }}.</p>
<table>
    <thead>
        <tr>
            <th>Entity</th>
            <th>Purpose of processing</th>
            <th>Location of processing</th>
            <th>Transfer mechanism</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Cloudflare, Inc., 101 Townsend St., San Francisco, CA 94107, USA</td>
            <td>CDN, DNS, DDoS protection, web application firewall (WAF), bot management; transiting traffic (IP address, headers, request content in encrypted form at edge servers)</td>
            <td>Global edge network; traffic from the EU is generally served within the EU</td>
            <td>EU-US Data Privacy Framework; 2021/914 standard contractual clauses (fallback)</td>
        </tr>
        <tr>
            <td>{{ $emailProvider }}</td>
            <td>Delivery of transactional e-mails (booking confirmations and reminders, verification codes); processes e-mail address, name and message content</td>
            <td>European Union</td>
            <td>Processing in the EU; no transfer to a third country takes place</td>
        </tr>
    </tbody>
</table>
<p><strong>Note on hosting:</strong> the application and database run on the Processor's own server located in the Slovak Republic. Hosting is therefore not a sub-processor. {{-- [LAWYER] If the server is located in a rented data centre / colocation, consider listing the data centre provider as a sub-processor (physical access). --}}</p>
