<h1>Cookie Policy</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<p>This policy explains which cookies and similar technologies are used by the platform {{ $siteUrl }} (the "Platform"), operated by {{ $operator['name'] }} (the "Operator"), and why we do not display a consent banner when you use it. Information on the processing of personal data can be found in the <a href="{{ $links['privacy'] }}">Privacy Policy</a>.</p>

<h2>1. We use strictly necessary cookies only</h2>
<p>The Platform uses exclusively cookies that are strictly necessary to provide the service you have explicitly requested: signing in to your account, protecting forms, remembering your language and protecting the service against automated attacks. We do not use any analytics, advertising or marketing cookies, and we do not track your behaviour on other websites.</p>
<p>Under Section 109(8) of the Slovak Act No. 452/2021 Coll. on Electronic Communications, consent is not required for storing or gaining access to information that is strictly necessary to provide an information-society service explicitly requested by the user. The same exemption applies in the other countries where our customers operate: in the Czech Republic Section 89(3) of Act No. 127/2005 Coll., in Poland Article 399 of the Electronic Communications Law (Prawo komunikacji elektronicznej), in Germany Section 25 TDDDG and in Austria Section 165(3) TKG 2021. For this reason the Platform does not display a cookie consent banner; it only shows a short informational notice.</p>
{{-- [LAWYER] Verify that all listed cookies (in particular the "locale" cookie with a 1-year lifetime) fall under the "strictly necessary" exemption as interpreted by the Slovak Regulatory Authority (ÚREKPS) and the EDPB. --}}

<h2>2. Overview of cookies used</h2>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Purpose</th>
      <th>Duration</th>
      <th>Provider</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>laravel_session</td>
      <td>Identifies the session of a signed-in user and keeps the state of forms.</td>
      <td>Session</td>
      <td>Platform (first party)</td>
    </tr>
    <tr>
      <td>XSRF-TOKEN</td>
      <td>Protection against cross-site request forgery attacks when submitting forms.</td>
      <td>Session</td>
      <td>Platform (first party)</td>
    </tr>
    <tr>
      <td>locale</td>
      <td>Remembers the selected interface language.</td>
      <td>1 year</td>
      <td>Platform (first party)</td>
    </tr>
    <tr>
      <td>otp_trust</td>
      <td>Marks a trusted device after successful two-factor authentication so that the code is not required at every sign-in.</td>
      <td>30 days</td>
      <td>Platform (first party)</td>
    </tr>
    <tr>
      <td>__cf_bm</td>
      <td>Detects automated traffic (bots) and protects the Platform against abuse.</td>
      <td>30 minutes</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>cf_clearance</td>
      <td>Stores the result of a security challenge so that the visitor does not have to repeat it.</td>
      <td>Up to 1 year (as set by Cloudflare)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>__cfruid</td>
      <td>Load balancing and security routing of requests.</td>
      <td>Session</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>_cfuvid</td>
      <td>Rate limiting of requests without identifying a specific person.</td>
      <td>Session</td>
      <td>Cloudflare, Inc.</td>
    </tr>
  </tbody>
</table>
{{-- [LAWYER][TECH] Verify the actual lifetime of the "locale" and "otp_trust" cookies in the application configuration and the current lifetimes of Cloudflare cookies according to Cloudflare's documentation. --}}
<p>Cookies marked as "session" are deleted when you close your browser or after the inactivity period set by the Platform expires. Cloudflare cookies are set because the Platform is operated behind the Cloudflare network, which provides protection against attacks and filters malicious traffic. Cloudflare does not use these cookies for cross-site tracking.</p>

<h2>3. Browser local storage</h2>
<p>In addition to cookies, the Platform stores a single item in your browser's local storage (localStorage) that remembers that you have dismissed the cookie information notice. This item is not a cookie, is not sent to the server and does not contain any personal data.</p>

<h2>4. Third-party embedded content on booking pages</h2>
<p>The Platform itself does not embed third-party content. However, if a customer of the Platform (the operator of a booking page, the "Tenant") enables third-party embedded content on their public booking page, such as a map, a video or an external widget, those services may set their own cookies or similar identifiers. The Tenant is responsible for such content and for obtaining any consent from visitors, and must inform about it in their own policies. The Platform's cookies are not affected by this and remain strictly necessary.</p>

<h2>5. How to manage cookies</h2>
<p>You can delete or block cookies at any time in your browser settings (for example in the "Privacy and security" section). Please note that if you block strictly necessary cookies, you will not be able to sign in to your account, submit a booking form or pass the Cloudflare security check. Deleting the <code>otp_trust</code> cookie will cause the two-factor code to be requested again at your next sign-in.</p>

<h2>6. Changes to this policy</h2>
<p>If we were to introduce cookies that are not strictly necessary in the future, we will update this policy and ask for your consent before using them, in accordance with applicable law. The current version is always available at {{ $siteUrl }}.</p>

<h2>7. Contact</h2>
<p>Operator: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }}. Questions regarding cookies and data protection may be sent to the address above.</p>
