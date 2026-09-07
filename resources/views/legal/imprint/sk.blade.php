<h1>Identifikácia prevádzkovateľa</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinnosť od {{ $effective }}</p>

<p>Údaje podľa § 4 zákona č. 22/2004 Z. z. o elektronickom obchode, čl. 11 a 12 nariadenia (EÚ) 2022/2065 o jednotnom trhu s digitálnymi službami (DSA) a čl. 28 nariadenia (EÚ) 2023/2854 o údajoch (Data Act).</p>

<h2>1. Poskytovateľ služby</h2>
<ul>
    <li>Prevádzkovateľ: {{ $operator['name'] }}</li>
    <li>Adresa: {{ $operator['address'] }}</li>
    <li>E-mail: {{ $operator['email'] }}</li>
    <li>Webové sídlo: {{ $siteUrl }}</li>
</ul>
<p>Prevádzkovateľ je fyzická osoba, ktorá službu poskytuje pod vlastným menom. Identifikačné údaje podnikateľa (napríklad identifikačné číslo a registrový orgán) budú do tejto stránky doplnené, keď bude prevádzkovateľ zapísaný v príslušnom registri.</p>
{{-- [LAWYER] Posúdiť, či rozsah a odplatnosť služby (plán Pro) zakladá povinnosť získať živnostenské oprávnenie podľa zákona č. 455/1991 Zb. a registráciu podľa daňových predpisov; po registrácii doplniť IČO, DIČ a registrový orgán. --}}

<h2>2. Kontaktné miesta podľa DSA</h2>
<p>Jednotné kontaktné miesto pre orgány členských štátov, Komisiu a Európsky výbor pre digitálne služby (čl. 11 DSA) a zároveň kontaktné miesto pre príjemcov služby (čl. 12 DSA): {{ $operator['email'] }}. Komunikovať možno v slovenskom, českom a anglickom jazyku.</p>
<p>Oznámenia o nezákonnom obsahu na rezervačných stránkach nájomcov (mechanizmus oznámení a opatrení podľa čl. 16 DSA) zasielajte na tú istú adresu. Postup a náležitosti oznámenia opisujú <a href="{{ $links['aup'] }}">Pravidlá prijateľného používania</a>.</p>

<h2>3. Informácie o infraštruktúre IKT (čl. 28 Data Act)</h2>
<ul>
    <li>Aplikácia a databáza bežia na vlastnom serveri prevádzkovateľa umiestnenom v Slovenskej republike (EÚ).</li>
    <li>Sieťová vrstva (CDN, firewall webových aplikácií, DNS) je zabezpečená spoločnosťou Cloudflare, Inc., ktorej okrajové servery sa nachádzajú v dátových centrách po celom svete.</li>
    <li>Transakčné e-maily odosiela poskytovateľ {{ $emailProvider }} (EÚ).</li>
</ul>
<p>Opatrenia proti nezákonnému prístupu orgánov verejnej moci tretích krajín k údajom:</p>
<ul>
    <li>údaje sú trvalo uložené výlučne v EÚ; Cloudflare pôsobí iba ako prenosová a vyrovnávacia vrstva na základe rámca EÚ – USA pre ochranu údajov (DPF) a štandardných zmluvných doložiek podľa rozhodnutia (EÚ) 2021/914 s vykonaným posúdením vplyvu prenosu;</li>
    <li>prenos je šifrovaný protokolom TLS 1.2 alebo vyšším;</li>
    <li>žiadosti orgánov o sprístupnenie údajov posudzujeme individuálne a údaje sprístupňujeme len na základe právne záväzného rozhodnutia; nájomcov o tom informujeme, ak to právo umožňuje.</li>
</ul>
<p>Zoznam subdodávateľov je v dokumente <a href="{{ $links['subprocessors'] }}">Subdodávatelia</a>.</p>

<h2>4. Príjemca platieb</h2>
<p>Platby za plán Pro spracúva PayPal (Europe) S.à r.l. et Cie, S.C.A., 22-24 Boulevard Royal, L-2449 Luxembourg, ako samostatný prevádzkovateľ platobných údajov. Prevádzkovateľ nemá prístup k údajom o kartách ani bankových účtoch.</p>

<h2>5. Orgány dohľadu</h2>
<ul>
    <li>Elektronický obchod a ochrana spotrebiteľa: Slovenská obchodná inšpekcia, Ústredný inšpektorát, Bajkalská 21/A, 827 99 Bratislava.</li>
    <li>Cookies a elektronický marketing (zákon č. 452/2021 Z. z. o elektronických komunikáciách): Úrad pre reguláciu elektronických komunikácií a poštových služieb, Továrenská 7, 828 55 Bratislava.</li>
    <li>Ochrana osobných údajov: Úrad na ochranu osobných údajov Slovenskej republiky, Hraničná 12, 820 07 Bratislava.</li>
</ul>

<h2>6. Jazyk a záväzná verzia</h2>
<p>Podľa § 5 zákona č. 22/2004 Z. z. sú informácie poskytované v štátnom jazyku. Slovenské znenie všetkých právnych dokumentov je záväzné; české a anglické preklady slúžia na uľahčenie porozumenia.</p>

<h2>7. Právne dokumenty</h2>
<ul>
    <li><a href="{{ $links['terms'] }}">Obchodné podmienky</a></li>
    <li><a href="{{ $links['privacy'] }}">Zásady ochrany osobných údajov</a></li>
    <li><a href="{{ $links['dpa'] }}">Zmluva o spracúvaní osobných údajov</a></li>
    <li><a href="{{ $links['cookies'] }}">Zásady používania cookies</a></li>
    <li><a href="{{ $links['aup'] }}">Pravidlá prijateľného používania</a></li>
    <li><a href="{{ $links['refunds'] }}">Zásady vrátenia platieb</a></li>
    <li><a href="{{ $links['subprocessors'] }}">Subdodávatelia</a></li>
</ul>
