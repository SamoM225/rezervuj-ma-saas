<h1>Zoznam subdodávateľov spracúvania</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinnosť od {{ $effective }}</p>

<h2>1. Účel tohto zoznamu</h2>
<p>Tento zoznam tvorí Prílohu III k Zmluve o spracúvaní osobných údajov (ďalej „DPA“) dostupnej na adrese <a href="{{ $links['dpa'] }}">{{ $links['dpa'] }}</a>. Prevádzkovateľ platformy rezervuj-ma.online ({{ $siteUrl }}) vystupuje voči svojim zákazníkom (tenantom) ako sprostredkovateľ a pri poskytovaní služby využíva ďalších sprostredkovateľov (subdodávateľov) uvedených nižšie. Sprostredkovateľ: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }}.</p>

<h2>2. Všeobecné poverenie a oznamovanie zmien</h2>
<p>Uzavretím DPA udeľuje tenant všeobecné poverenie na zapojenie subdodávateľov uvedených v tomto zozname. Každé plánované doplnenie alebo nahradenie subdodávateľa oznámi sprostredkovateľ najmenej 30 dní vopred e-mailom na adresu spojenú s účtom tenanta a súčasne aktualizáciou tejto stránky.</p>
<p>Tenant má právo voči zmene písomne namietať na adrese {{ $operator['email'] }} v lehote 30 dní od oznámenia. Ak námietku nemožno primerane vyriešiť (napríklad poskytnutím služby bez daného subdodávateľa), má tenant právo DPA a zmluvu o poskytovaní služby vypovedať s účinnosťou ku dňu nasadenia zmeny bez akýchkoľvek poplatkov; údaje si môže vyexportovať podľa {{ $links['terms'] }}.</p>
{{-- [LAWYER] Overiť, či 30-dňová lehota na námietku a spôsob oznámenia (e-mail + web) postačuje podľa čl. 28 ods. 2 GDPR a doložky 7.7 rozhodnutia 2021/915. --}}

<h2>3. Aktuálni subdodávatelia</h2>
<table>
  <thead>
    <tr>
      <th>Subdodávateľ</th>
      <th>Služba</th>
      <th>Miesto spracúvania</th>
      <th>Mechanizmus prenosu</th>
      <th>Účel</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Cloudflare, Inc., 101 Townsend St, San Francisco, CA 94107, USA</td>
      <td>CDN, WAF (firewall webových aplikácií), DNS, správa botov</td>
      <td>Globálna sieť okrajových serverov (prednostne uzly v EÚ, technicky však ktorákoľvek lokalita)</td>
      <td>Rámec EÚ – USA na ochranu osobných údajov (Data Privacy Framework) a štandardné zmluvné doložky 2021/914, modul 3</td>
      <td>Bezpečnosť a doručovanie platformy (ochrana pred útokmi, šifrované spojenie, prevencia zneužitia)</td>
    </tr>
    <tr>
      <td>{{ $emailProvider }}</td>
      <td>Doručovanie transakčných e-mailov (potvrdenia rezervácií, pripomienky, jednorazové kódy)</td>
      <td>Európska únia</td>
      <td>Bez prenosu do tretej krajiny</td>
      <td>Odosielanie e-mailov zákazníkom a personálu tenanta</td>
    </tr>
  </tbody>
</table>
<p>Cloudflare spracúva najmä sieťové údaje (IP adresa, hlavičky požiadavky, bezpečnostné cookies) a prechodne obsah požiadaviek, ktoré smerujú na platformu. Poskytovateľ e-mailových služieb spracúva e-mailovú adresu adresáta a obsah správy počas doby potrebnej na doručenie.</p>
{{-- [LAWYER] Skontrolovať aktuálny stav certifikácie Cloudflare v rámci DPF a to, či konkrétny e-mailový poskytovateľ ({{ $emailProvider }}) skutočne spracúva výlučne v EÚ. --}}

<h2>4. Infraštruktúra, ktorá nie je subdodávateľom</h2>
<ul>
  <li><strong>Hosting.</strong> Aplikácia a databáza bežia na vlastnom serveri sprostredkovateľa umiestnenom na území Slovenskej republiky. Nejde o tretiu osobu – sprostredkovateľ prevádzkuje infraštruktúru sám, a preto sa hosting v tomto zozname neuvádza ako subdodávateľ.</li>
  <li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong>, 22-24 Boulevard Royal, L-2449 Luxemburg. PayPal spracúva platby za predplatné tenantov ako samostatný prevádzkovateľ podľa vlastných podmienok. Nedostáva žiadne údaje o zákazníkoch tenanta a nie je subdodávateľom podľa DPA. Bližšie informácie sú v Zásadách ochrany osobných údajov: <a href="{{ $links['privacy'] }}">{{ $links['privacy'] }}</a>.</li>
</ul>

<h2>5. Jurisdikcia IKT infraštruktúry (nariadenie (EÚ) 2023/2854)</h2>
<p>V súlade s kapitolou VI Aktu o údajoch uvádzame, že IKT infraštruktúra používaná na poskytovanie služby sa nachádza v Slovenskej republike (aplikačný a databázový server) a v globálnej okrajovej sieti spoločnosti Cloudflare (CDN a bezpečnostná vrstva). Údaje v pokoji sú uložené výlučne v Slovenskej republike; okrajová sieť Cloudflare údaje tranzituje a krátkodobo ukladá do vyrovnávacej pamäte. Opatrenia proti nezákonnému prístupu orgánov tretích krajín sú opísané v DPA a v {{ $links['terms'] }}.</p>

<h2>6. História zmien</h2>
<ul>
  <li>{{ $effective }} – prvá verzia</li>
</ul>
