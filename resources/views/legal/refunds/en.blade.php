<h1>Refund and Subscription Cancellation Policy</h1>
<p class="legal-meta">Version {{ $version }} · Effective from {{ $effective }}</p>

<p>This policy supplements the <a href="{{ $links['terms'] }}">Terms of Service</a> of {{ $siteUrl }} (the "Service"), operated by {{ $operator['name'] }} (the "Operator"). It explains how payment for the Pro plan works, when we refund money and how to cancel a subscription.</p>

<h2>1. Plans and payment method</h2>
<ul>
    <li><strong>Free</strong> – free plan, up to 50 bookings per month.</li>
    <li><strong>Pro</strong> – 5 EUR or 5 USD per month, or 50 EUR or 50 USD per year. The price is shown at checkout and does not change during a period that has already been paid for.</li>
</ul>
<p>PayPal is the only payment method (a subscription with automatic renewal). The Operator has no access to your card or bank account details; these are processed exclusively by PayPal as an independent controller. We charge no commission on your customers' bookings.</p>

<h2>2. Businesses: no statutory right of withdrawal</h2>
<p>The Service is intended for businesses (salons, studios, clinics and similar establishments). If you conclude the contract in the course of your business activity, you are not a consumer and the statutory right to withdraw from the contract without giving a reason does not apply to you. Instead, we offer you the voluntary money-back guarantee described in section 3.</p>

<h2>3. Voluntary 14-day money-back guarantee</h2>
<ul>
    <li>It applies to the <strong>first payment</strong> of each Pro subscription (monthly or yearly), not to subsequent renewals.</li>
    <li>Simply write to {{ $operator['email'] }} from the account's e-mail address within 14 days of the first payment. You do not need to give a reason.</li>
    <li>We refund via PayPal to the original payment method no later than 14 days after receiving the request. The subscription is cancelled at the same time and the account moves to the Free plan.</li>
    <li>The guarantee may be used once per account. We may refuse it in cases of obvious abuse (repeated registrations).</li>
</ul>

<h2>4. Cancelling a subscription</h2>
<p>You can cancel your subscription at any time using the <strong>"Cancel subscription"</strong> button in your account settings, or directly in your PayPal account. Cancellation takes effect at the end of the period already paid for; until that day the Pro plan remains available to you. We charge no cancellation fee.</p>
<p>We do not provide pro-rata refunds for the remainder of a paid period. After the period ends, the account moves to the Free plan; your data is retained, but the Free plan limits apply. Data export (bookings, customers, services, categories, staff, settings) in CSV/JSON format is free of charge and available at any time, as described in the switching section of the <a href="{{ $links['terms'] }}">Terms of Service</a>.</p>

<h2>5. Failed and duplicate payments, PayPal disputes</h2>
<p>If a payment fails, PayPal retries it; if the retry also fails, the account moves to the Free plan. A duplicate payment (e.g. two subscriptions for one account) is refunded in full after verification. Before opening a dispute or claim in PayPal, please contact us first at {{ $operator['email'] }} – we resolve most cases faster directly.</p>

<h2>6. Currency and exchange differences</h2>
<p>We always refund the amount in the currency of the original payment (EUR or USD). We do not compensate exchange rate differences or conversion fees charged by your card issuer or PayPal.</p>

<h2>7. Special information for Polish sole traders</h2>
<p>If you are a sole trader (natural person) established in Poland and the contract with the Service is not of a professional character for you (in particular, it does not follow from the subject of your activity registered in CEIDG), selected consumer rights apply to you under Article 38a of the Polish Consumer Rights Act of 30 May 2014, including the right to withdraw from the contract:</p>
<ul>
    <li>You may withdraw from the contract within <strong>14 days of its conclusion</strong> without giving a reason.</li>
    <li>If you expressly requested that we start providing the Service immediately (by activating the Pro plan) and then withdraw, you pay a proportionate part of the price for the period up to withdrawal; we refund the rest.</li>
    <li>An unequivocal statement sent by e-mail to {{ $operator['email'] }} is sufficient to withdraw. You may use this model: <em>"To: {{ $operator['name'] }}, {{ $operator['email'] }}. I hereby withdraw from the contract for the provision of the rezervuj-ma.online service concluded on … . Name and surname, address, date, signature (only if on paper)."</em></li>
</ul>
{{-- [LAWYER] Verify the scope of Articles 38a–38c of the Polish Consumer Rights Act (quasi-consumer) and whether proportionate payment suffices on withdrawal after an express request for immediate performance. --}}

<h2>8. Note for Austria</h2>
<p>Persons setting up a business in Austria who conclude the contract before commencing operations (Gründer) may be treated as consumers under § 1(3) KSchG. As a precaution, we grant them the same 14-day right of withdrawal and information as in section 7.</p>
{{-- [LAWYER] Verify the application of § 1(3) KSchG and FAGG to digital services provided to founders before commencement of business. --}}

<h2>9. Mandatory consumer law as the minimum</h2>
<p>If, despite the business nature of the Service, mandatory consumer law of the country of your habitual residence applies to you (in the Slovak Republic, Act No. 108/2024 Coll. on Consumer Protection), it prevails over this policy. The voluntary 14-day guarantee under section 3 applies in every case as a minimum.</p>
<p>Consumers resident in the Slovak Republic may contact the Slovak Trade Inspection (Slovenská obchodná inšpekcia) or another alternative dispute resolution body listed under Act No. 391/2015 Coll. Consumers from other Member States may contact the ADR body in their own country.</p>

<h2>10. Contact</h2>
<p>Refund requests, withdrawals and billing questions: {{ $operator['email'] }}. Operator: {{ $operator['name'] }}, {{ $operator['address'] }}. Further details are in the <a href="{{ $links['imprint'] }}">Legal Notice</a>.</p>
