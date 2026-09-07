<h1>Seznam dílčích zpracovatelů</h1>
<p class="legal-meta">Verze {{ $version }} · Účinnost od {{ $effective }}</p>

<h2>1. Účel tohoto seznamu</h2>
<p>Tento seznam tvoří Přílohu III ke Smlouvě o zpracování osobních údajů (dále „DPA“) dostupné na adrese <a href="{{ $links['dpa'] }}">{{ $links['dpa'] }}</a>. Provozovatel platformy rezervuj-ma.online ({{ $siteUrl }}) vystupuje vůči svým zákazníkům (tenantům) jako zpracovatel a při poskytování služby využívá další zpracovatele (dílčí zpracovatele) uvedené níže. Zpracovatel: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }}.</p>

<h2>2. Obecné povolení a oznamování změn</h2>
<p>Uzavřením DPA uděluje tenant obecné povolení k zapojení dílčích zpracovatelů uvedených v tomto seznamu. Každé plánované doplnění nebo nahrazení dílčího zpracovatele oznámí zpracovatel nejméně 30 dní předem e-mailem na adresu spojenou s účtem tenanta a současně aktualizací této stránky.</p>
<p>Tenant má právo proti změně písemně vznést námitku na adrese {{ $operator['email'] }} ve lhůtě 30 dnů od oznámení. Nelze-li námitku přiměřeně vyřešit (například poskytnutím služby bez daného dílčího zpracovatele), má tenant právo DPA a smlouvu o poskytování služby vypovědět s účinností ke dni nasazení změny bez jakýchkoli poplatků; data si může exportovat podle {{ $links['terms'] }}.</p>
{{-- [LAWYER] Ověřit, zda 30denní lhůta pro námitku a způsob oznámení (e-mail + web) postačuje podle čl. 28 odst. 2 GDPR a doložky 7.7 rozhodnutí 2021/915. --}}

<h2>3. Aktuální dílčí zpracovatelé</h2>
<table>
  <thead>
    <tr>
      <th>Dílčí zpracovatel</th>
      <th>Služba</th>
      <th>Místo zpracování</th>
      <th>Mechanismus předání</th>
      <th>Účel</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Cloudflare, Inc., 101 Townsend St, San Francisco, CA 94107, USA</td>
      <td>CDN, WAF (firewall webových aplikací), DNS, správa botů</td>
      <td>Globální síť okrajových serverů (přednostně uzly v EU, technicky však kterákoli lokalita)</td>
      <td>Rámec EU – USA pro ochranu osobních údajů (Data Privacy Framework) a standardní smluvní doložky 2021/914, modul 3</td>
      <td>Bezpečnost a doručování platformy (ochrana před útoky, šifrované spojení, prevence zneužití)</td>
    </tr>
    <tr>
      <td>{{ $emailProvider }}</td>
      <td>Doručování transakčních e-mailů (potvrzení rezervací, připomínky, jednorázové kódy)</td>
      <td>Evropská unie</td>
      <td>Bez předání do třetí země</td>
      <td>Odesílání e-mailů zákazníkům a personálu tenanta</td>
    </tr>
  </tbody>
</table>
<p>Cloudflare zpracovává zejména síťové údaje (IP adresa, hlavičky požadavku, bezpečnostní cookies) a přechodně obsah požadavků směřujících na platformu. Poskytovatel e-mailových služeb zpracovává e-mailovou adresu adresáta a obsah zprávy po dobu nezbytnou k doručení.</p>
{{-- [LAWYER] Zkontrolovat aktuální stav certifikace Cloudflare v rámci DPF a to, zda konkrétní e-mailový poskytovatel ({{ $emailProvider }}) skutečně zpracovává výlučně v EU. --}}

<h2>4. Infrastruktura, která není dílčím zpracovatelem</h2>
<ul>
  <li><strong>Hosting.</strong> Aplikace a databáze běží na vlastním serveru zpracovatele umístěném na území Slovenské republiky. Nejedná se o třetí osobu – zpracovatel provozuje infrastrukturu sám, a proto se hosting v tomto seznamu neuvádí jako dílčí zpracovatel.</li>
  <li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong>, 22-24 Boulevard Royal, L-2449 Lucemburk. PayPal zpracovává platby za předplatné tenantů jako samostatný správce podle vlastních podmínek. Nedostává žádné údaje o zákaznících tenanta a není dílčím zpracovatelem podle DPA. Bližší informace jsou v Zásadách ochrany osobních údajů: <a href="{{ $links['privacy'] }}">{{ $links['privacy'] }}</a>.</li>
</ul>

<h2>5. Jurisdikce IKT infrastruktury (nařízení (EU) 2023/2854)</h2>
<p>V souladu s kapitolou VI Aktu o datech uvádíme, že IKT infrastruktura používaná k poskytování služby se nachází ve Slovenské republice (aplikační a databázový server) a v globální okrajové síti společnosti Cloudflare (CDN a bezpečnostní vrstva). Data v klidu jsou uložena výlučně ve Slovenské republice; okrajová síť Cloudflare data tranzituje a krátkodobě ukládá do vyrovnávací paměti. Opatření proti nezákonnému přístupu orgánů třetích zemí jsou popsána v DPA a v {{ $links['terms'] }}.</p>

<h2>6. Historie změn</h2>
<ul>
  <li>{{ $effective }} – první verze</li>
</ul>
