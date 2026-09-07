<h1>Zásady používania cookies</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinnosť od {{ $effective }}</p>

<p>Tieto zásady vysvetľujú, aké súbory cookie a podobné technológie používa platforma {{ $siteUrl }} (ďalej len „Platforma“), ktorú prevádzkuje {{ $operator['name'] }} (ďalej len „Prevádzkovateľ“), a prečo pri jej používaní nezobrazujeme lištu so žiadosťou o súhlas. Informácie o spracúvaní osobných údajov nájdete v <a href="{{ $links['privacy'] }}">Zásadách ochrany osobných údajov</a>.</p>

<h2>1. Používame iba nevyhnutné cookies</h2>
<p>Platforma používa výlučne cookies, ktoré sú nevyhnutne potrebné na poskytnutie služby, ktorú ste si výslovne vyžiadali – prihlásenie do účtu, ochranu formulárov, zapamätanie jazyka a bezpečnostnú ochranu pred automatizovanými útokmi. Nepoužívame žiadne analytické, reklamné ani marketingové cookies a nesledujeme vaše správanie na iných webových stránkach.</p>
<p>Podľa § 109 ods. 8 zákona č. 452/2021 Z. z. o elektronických komunikáciách sa súhlas nevyžaduje pri ukladaní alebo získavaní prístupu k informáciám, ktoré sú nevyhnutne potrebné na poskytnutie služby informačnej spoločnosti výslovne vyžiadanej používateľom. Rovnaká výnimka platí aj v ďalších krajinách, v ktorých pôsobia naši zákazníci: v Českej republike § 89 ods. 3 zákona č. 127/2005 Sb., v Poľsku čl. 399 zákona Prawo komunikacji elektronicznej, v Nemecku § 25 TDDDG a v Rakúsku § 165 ods. 3 TKG 2021. Z tohto dôvodu Platforma nezobrazuje lištu so žiadosťou o súhlas s cookies; zobrazuje iba krátke informačné upozornenie.</p>
{{-- [LAWYER] Overiť, že všetky uvedené cookies (najmä cookie „locale“ s platnosťou 1 rok) spadajú pod výnimku „nevyhnutne potrebné“ podľa výkladu ÚREKPS a stanoviska EDPB. --}}

<h2>2. Prehľad používaných cookies</h2>
<table>
  <thead>
    <tr>
      <th>Názov</th>
      <th>Účel</th>
      <th>Platnosť</th>
      <th>Poskytovateľ</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>laravel_session</td>
      <td>Identifikácia relácie prihláseného používateľa a udržanie stavu formulárov.</td>
      <td>Relácia (session)</td>
      <td>Platforma (prvá strana)</td>
    </tr>
    <tr>
      <td>XSRF-TOKEN</td>
      <td>Ochrana pred útokmi typu cross-site request forgery pri odosielaní formulárov.</td>
      <td>Relácia (session)</td>
      <td>Platforma (prvá strana)</td>
    </tr>
    <tr>
      <td>locale</td>
      <td>Zapamätanie zvoleného jazyka rozhrania.</td>
      <td>1 rok</td>
      <td>Platforma (prvá strana)</td>
    </tr>
    <tr>
      <td>otp_trust</td>
      <td>Označenie dôveryhodného zariadenia po úspešnom dvojfaktorovom overení, aby sa kód nevyžadoval pri každom prihlásení.</td>
      <td>30 dní</td>
      <td>Platforma (prvá strana)</td>
    </tr>
    <tr>
      <td>__cf_bm</td>
      <td>Rozpoznávanie automatizovanej prevádzky (botov) a ochrana Platformy pred zneužitím.</td>
      <td>30 minút</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>cf_clearance</td>
      <td>Uloženie výsledku bezpečnostnej výzvy, aby ju návštevník nemusel opakovať.</td>
      <td>Až 1 rok (podľa nastavenia Cloudflare)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>__cfruid</td>
      <td>Vyrovnávanie zaťaženia a bezpečnostné smerovanie požiadaviek.</td>
      <td>Relácia (session)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>_cfuvid</td>
      <td>Obmedzenie počtu požiadaviek (rate limiting) bez identifikácie konkrétnej osoby.</td>
      <td>Relácia (session)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
  </tbody>
</table>
{{-- [LAWYER][TECH] Overiť skutočnú platnosť cookie „locale“ a „otp_trust“ v konfigurácii aplikácie a aktuálne platnosti cookies Cloudflare podľa ich dokumentácie. --}}
<p>Cookies označené ako „relácia“ sa vymažú po zatvorení prehliadača alebo po uplynutí doby nečinnosti nastavenej Platformou. Cookies spoločnosti Cloudflare sa nastavujú preto, že Platforma je prevádzkovaná za sieťou Cloudflare, ktorá zabezpečuje ochranu pred útokmi a filtrovanie škodlivej prevádzky. Cloudflare tieto cookies nepoužíva na sledovanie naprieč webovými stránkami.</p>

<h2>3. Lokálne úložisko prehliadača</h2>
<p>Okrem cookies ukladá Platforma do lokálneho úložiska prehliadača (localStorage) jedinú položku, ktorá si pamätá, že ste zavreli informačné upozornenie o cookies. Táto položka nie je cookie, neodosiela sa na server a neobsahuje žiadne osobné údaje.</p>

<h2>4. Vložený obsah tretích strán na rezervačných stránkach</h2>
<p>Samotná Platforma nevkladá obsah tretích strán. Ak si však zákazník Platformy (prevádzkovateľ rezervačnej stránky, ďalej len „Tenant“) na svojej verejnej rezervačnej stránke zapne vložený obsah tretích strán – napríklad mapu, video alebo externý widget – môžu tieto služby ukladať vlastné cookies alebo podobné identifikátory. Za takýto obsah a za získanie prípadného súhlasu návštevníkov zodpovedá Tenant, ktorý o ňom musí informovať vo svojich vlastných zásadách. Cookies Platformy sa tým nemenia a zostávajú nevyhnutne potrebné.</p>

<h2>5. Ako spravovať cookies</h2>
<p>Cookies môžete kedykoľvek vymazať alebo zablokovať v nastaveniach svojho prehliadača (napríklad v sekcii „Súkromie a zabezpečenie“). Upozorňujeme, že pri zablokovaní nevyhnutných cookies nebude možné prihlásiť sa do účtu, odoslať rezervačný formulár ani prejsť bezpečnostnou kontrolou Cloudflare. Vymazanie cookie <code>otp_trust</code> spôsobí, že pri ďalšom prihlásení sa opäť vyžiada dvojfaktorový kód.</p>

<h2>6. Zmeny týchto zásad</h2>
<p>Ak by sme v budúcnosti zaviedli cookies, ktoré nie sú nevyhnutne potrebné, tieto zásady aktualizujeme a pred ich použitím si vyžiadame váš súhlas v súlade s platnými predpismi. Aktuálna verzia je vždy dostupná na {{ $siteUrl }}.</p>

<h2>7. Kontakt</h2>
<p>Prevádzkovateľ: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }}. Otázky týkajúce sa cookies a ochrany osobných údajov nám môžete zaslať na uvedenú adresu.</p>
