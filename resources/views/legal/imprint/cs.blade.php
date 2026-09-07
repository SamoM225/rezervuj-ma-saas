<h1>Identifikace provozovatele</h1>
<p class="legal-meta">Verze {{ $version }} · Účinnost od {{ $effective }}</p>

<p>Údaje podle § 4 slovenského zákona č. 22/2004 Z. z. o elektronickém obchodu, čl. 11 a 12 nařízení (EU) 2022/2065 o jednotném trhu pro digitální služby (DSA) a čl. 28 nařízení (EU) 2023/2854 o datech (Data Act).</p>

<h2>1. Poskytovatel služby</h2>
<ul>
    <li>Provozovatel: {{ $operator['name'] }}</li>
    <li>Adresa: {{ $operator['address'] }}</li>
    <li>E-mail: {{ $operator['email'] }}</li>
    <li>Webové sídlo: {{ $siteUrl }}</li>
</ul>
<p>Provozovatel je fyzická osoba, která službu poskytuje pod vlastním jménem. Identifikační údaje podnikatele (například identifikační číslo a registrový orgán) budou na tuto stránku doplněny, jakmile bude provozovatel zapsán v příslušném registru.</p>
{{-- [LAWYER] Posoudit, zda rozsah a úplatnost služby (plán Pro) zakládá povinnost získat živnostenské oprávnění podle slovenského zákona č. 455/1991 Zb. a registraci podle daňových předpisů; po registraci doplnit IČO, DIČ a registrový orgán. --}}

<h2>2. Kontaktní místa podle DSA</h2>
<p>Jednotné kontaktní místo pro orgány členských států, Komisi a Evropský sbor pro digitální služby (čl. 11 DSA) a zároveň kontaktní místo pro příjemce služby (čl. 12 DSA): {{ $operator['email'] }}. Komunikovat lze ve slovenském, českém a anglickém jazyce.</p>
<p>Oznámení o nezákonném obsahu na rezervačních stránkách nájemců (mechanismus oznámení a opatření podle čl. 16 DSA) zasílejte na tutéž adresu. Postup a náležitosti oznámení popisují <a href="{{ $links['aup'] }}">Pravidla přijatelného používání</a>.</p>

<h2>3. Informace o infrastruktuře IKT (čl. 28 Data Act)</h2>
<ul>
    <li>Aplikace a databáze běží na vlastním serveru provozovatele umístěném ve Slovenské republice (EU).</li>
    <li>Síťová vrstva (CDN, firewall webových aplikací, DNS) je zajištěna společností Cloudflare, Inc., jejíž okrajové servery se nacházejí v datových centrech po celém světě.</li>
    <li>Transakční e-maily odesílá poskytovatel {{ $emailProvider }} (EU).</li>
</ul>
<p>Opatření proti nezákonnému přístupu orgánů veřejné moci třetích zemí k údajům:</p>
<ul>
    <li>údaje jsou trvale uloženy výhradně v EU; Cloudflare působí pouze jako přenosová a vyrovnávací vrstva na základě rámce EU–USA pro ochranu údajů (DPF) a standardních smluvních doložek podle rozhodnutí (EU) 2021/914 s provedeným posouzením dopadu předání;</li>
    <li>přenos je šifrován protokolem TLS 1.2 nebo vyšším;</li>
    <li>žádosti orgánů o zpřístupnění údajů posuzujeme individuálně a údaje zpřístupňujeme pouze na základě právně závazného rozhodnutí; nájemce o tom informujeme, pokud to právo umožňuje.</li>
</ul>
<p>Seznam subdodavatelů je v dokumentu <a href="{{ $links['subprocessors'] }}">Subdodavatelé</a>.</p>

<h2>4. Příjemce plateb</h2>
<p>Platby za plán Pro zpracovává PayPal (Europe) S.à r.l. et Cie, S.C.A., 22-24 Boulevard Royal, L-2449 Luxembourg, jako samostatný správce platebních údajů. Provozovatel nemá přístup k údajům o kartách ani bankovních účtech.</p>

<h2>5. Orgány dohledu</h2>
<ul>
    <li>Elektronický obchod a ochrana spotřebitele: Slovenská obchodní inspekce (Slovenská obchodná inšpekcia), Ústredný inšpektorát, Bajkalská 21/A, 827 99 Bratislava.</li>
    <li>Cookies a elektronický marketing (slovenský zákon č. 452/2021 Z. z. o elektronických komunikacích): Úrad pre reguláciu elektronických komunikácií a poštových služieb, Továrenská 7, 828 55 Bratislava.</li>
    <li>Ochrana osobních údajů: Úrad na ochranu osobných údajov Slovenskej republiky, Hraničná 12, 820 07 Bratislava.</li>
</ul>

<h2>6. Jazyk a závazná verze</h2>
<p>Podle § 5 slovenského zákona č. 22/2004 Z. z. jsou informace poskytovány ve státním jazyce. Slovenské znění všech právních dokumentů je závazné; české a anglické překlady slouží k usnadnění porozumění.</p>

<h2>7. Právní dokumenty</h2>
<ul>
    <li><a href="{{ $links['terms'] }}">Obchodní podmínky</a></li>
    <li><a href="{{ $links['privacy'] }}">Zásady ochrany osobních údajů</a></li>
    <li><a href="{{ $links['dpa'] }}">Smlouva o zpracování osobních údajů</a></li>
    <li><a href="{{ $links['cookies'] }}">Zásady používání cookies</a></li>
    <li><a href="{{ $links['aup'] }}">Pravidla přijatelného používání</a></li>
    <li><a href="{{ $links['refunds'] }}">Zásady vracení plateb</a></li>
    <li><a href="{{ $links['subprocessors'] }}">Subdodavatelé</a></li>
</ul>
