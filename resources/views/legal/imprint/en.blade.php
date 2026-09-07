<h1>Legal Notice</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<p>Information provided under § 4 of Slovak Act No. 22/2004 Coll. on Electronic Commerce, Articles 11 and 12 of Regulation (EU) 2022/2065 on a Single Market for Digital Services (DSA) and Article 28 of Regulation (EU) 2023/2854 on harmonised rules on fair access to and use of data (Data Act).</p>

<h2>1. Service provider</h2>
<ul>
    <li>Operator: {{ $operator['name'] }}</li>
    <li>Address: {{ $operator['address'] }}</li>
    <li>E-mail: {{ $operator['email'] }}</li>
    <li>Website: {{ $siteUrl }}</li>
</ul>
<p>The Operator is a natural person providing the service under their own name. Business identification details (such as a registration number and the registering authority) will be added to this page once the Operator is entered in the relevant register.</p>
{{-- [LAWYER] Assess whether the scope and paid nature of the service (Pro plan) triggers an obligation to obtain a trade licence under Slovak Act No. 455/1991 Coll. and tax registration; add IČO, DIČ and the registering authority after registration. --}}

<h2>2. Points of contact under the DSA</h2>
<p>Single point of contact for Member State authorities, the Commission and the European Board for Digital Services (Article 11 DSA) and, at the same time, point of contact for recipients of the service (Article 12 DSA): {{ $operator['email'] }}. Communication is possible in Slovak, Czech and English.</p>
<p>Notices of illegal content on tenants' booking pages (notice-and-action mechanism under Article 16 DSA) should be sent to the same address. The procedure and required elements of a notice are described in the <a href="{{ $links['aup'] }}">Acceptable Use Policy</a>.</p>

<h2>3. ICT infrastructure information (Article 28 Data Act)</h2>
<ul>
    <li>The application and database run on the Operator's own server located in the Slovak Republic (EU).</li>
    <li>The network layer (CDN, web application firewall, DNS) is provided by Cloudflare, Inc., whose edge servers are located in data centres around the world.</li>
    <li>Transactional e-mails are sent through the provider {{ $emailProvider }} (EU).</li>
</ul>
<p>Measures against unlawful access to data by public authorities of third countries:</p>
<ul>
    <li>data is stored at rest exclusively in the EU; Cloudflare acts only as a pass-through and caching layer under the EU–US Data Privacy Framework (DPF) and the standard contractual clauses under Decision (EU) 2021/914, with a transfer impact assessment carried out;</li>
    <li>transmission is encrypted using TLS 1.2 or higher;</li>
    <li>requests from authorities for disclosure of data are assessed individually and data is disclosed only on the basis of a legally binding decision; tenants are informed where the law allows.</li>
</ul>
<p>The list of sub-processors is in the <a href="{{ $links['subprocessors'] }}">Sub-processors</a> document.</p>

<h2>4. Payment recipient</h2>
<p>Payments for the Pro plan are processed by PayPal (Europe) S.à r.l. et Cie, S.C.A., 22-24 Boulevard Royal, L-2449 Luxembourg, as an independent controller of payment data. The Operator has no access to card or bank account details.</p>

<h2>5. Supervisory authorities</h2>
<ul>
    <li>Electronic commerce and consumer protection: Slovak Trade Inspection (Slovenská obchodná inšpekcia), Central Inspectorate, Bajkalská 21/A, 827 99 Bratislava.</li>
    <li>Cookies and electronic marketing (Slovak Act No. 452/2021 Coll. on Electronic Communications): Regulatory Authority for Electronic Communications and Postal Services (Úrad pre reguláciu elektronických komunikácií a poštových služieb), Továrenská 7, 828 55 Bratislava.</li>
    <li>Personal data protection: Office for Personal Data Protection of the Slovak Republic (Úrad na ochranu osobných údajov Slovenskej republiky), Hraničná 12, 820 07 Bratislava.</li>
</ul>

<h2>6. Language and binding version</h2>
<p>Under § 5 of Slovak Act No. 22/2004 Coll., information is provided in the state language. The Slovak version of all legal documents is binding; the Czech and English translations are provided for convenience.</p>

<h2>7. Legal documents</h2>
<ul>
    <li><a href="{{ $links['terms'] }}">Terms of Service</a></li>
    <li><a href="{{ $links['privacy'] }}">Privacy Policy</a></li>
    <li><a href="{{ $links['dpa'] }}">Data Processing Agreement</a></li>
    <li><a href="{{ $links['cookies'] }}">Cookie Policy</a></li>
    <li><a href="{{ $links['aup'] }}">Acceptable Use Policy</a></li>
    <li><a href="{{ $links['refunds'] }}">Refund Policy</a></li>
    <li><a href="{{ $links['subprocessors'] }}">Sub-processors</a></li>
</ul>
