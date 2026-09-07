<h1>Zásady ochrany osobných údajov</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinné od {{ $effective }}</p>

<h2>1. Kto je prevádzkovateľom</h2>
<p>Prevádzkovateľ: {{ $operator['name'] }}<br>E-mail: {{ $operator['email'] }}</p>
<p>Prevádzkovateľ služby {{ $siteUrl }} (ďalej „platforma“) je fyzická osoba so sídlom v Slovenskej republike. Zodpovedná osoba (DPO) nebola určená, pretože prevádzkovateľ nespĺňa podmienky článku 37 GDPR; vo všetkých otázkach ochrany osobných údajov nás kontaktujte na uvedenom e-maile.</p>
{{-- [LAWYER] Overiť, či rozsah spracúvania po rozbehu služby nezaloží povinnosť určiť zodpovednú osobu podľa čl. 37 GDPR / § 44 zákona č. 18/2018 Z. z. --}}

<h2>2. Na čo sa tieto zásady vzťahujú</h2>
<p>Tieto zásady sa vzťahujú na spracúvanie osobných údajov, pri ktorom je prevádzkovateľom platformy prevádzkovateľ v zmysle článku 4 bodu 7 GDPR: účty poskytovateľov služieb (ďalej „nájomcovia“), fakturácia, podpora, návštevníci webu a bezpečnosť platformy.</p>
<p>Osobné údaje, ktoré zadáte na verejnej rezervačnej stránke konkrétneho nájomcu (napr. <code>{{ $siteUrl }}/nazov-prevadzky/booking</code>), spracúva ako prevádzkovateľ tento nájomca. My ich spracúvame len ako sprostredkovateľ na základe zmluvy o spracúvaní osobných údajov (<a href="{{ $links['dpa'] }}">DPA</a>). Informácie o spracúvaní vašej rezervácie nájdete v oznámení o ochrane osobných údajov, ktoré nájomca zobrazuje na svojej rezervačnej stránke; so žiadosťami o výkon práv sa v takom prípade obracajte primárne na nájomcu.</p>

<h2>3. Aké údaje spracúvame, prečo a na akom právnom základe</h2>

<h3>3.1 Účty nájomcov a administračné rozhranie</h3>
<p><strong>Údaje:</strong> meno, e-mail, telefón, heslo (ukladá sa len ako hash), tajný kľúč dvojfaktorového overenia (2FA), jazykové nastavenie, názov a nastavenia prevádzky, záznamy o prihlásení.<br><strong>Účel:</strong> zriadenie a vedenie účtu, poskytovanie služby, komunikácia o službe.<br><strong>Právny základ:</strong> plnenie zmluvy – článok 6 ods. 1 písm. b) GDPR.</p>

<h3>3.2 Fakturácia a platby cez PayPal</h3>
<p><strong>Údaje:</strong> e-mail platiteľa, suma, mena, identifikátor predplatného a transakcie PayPal, dátum platby, fakturačné údaje.<br><strong>Účel:</strong> spracovanie platby za plán Pro, vystavenie účtovných dokladov, plnenie účtovných a daňových povinností.<br><strong>Právny základ:</strong> článok 6 ods. 1 písm. b) GDPR (zmluva) a písm. c) GDPR (zákonná povinnosť, najmä zákon č. 431/2002 Z. z. o účtovníctve).</p>
<p>Platby spracúva spoločnosť PayPal (Europe) S.à r.l. et Cie, S.C.A., 22–24 Boulevard Royal, L-2449 Luxemburg, ktorá je pri spracúvaní platobných údajov samostatným prevádzkovateľom a riadi sa vlastnými zásadami ochrany osobných údajov. Prevádzkovateľ nikdy nedostáva ani neuchováva čísla platobných kariet.</p>

<h3>3.3 Podpora a komunikácia</h3>
<p><strong>Údaje:</strong> meno, e-mail, obsah správy, údaje o účte potrebné na vyriešenie požiadavky.<br><strong>Účel:</strong> odpovedanie na otázky, riešenie technických problémov a reklamácií.<br><strong>Právny základ:</strong> článok 6 ods. 1 písm. b) GDPR, ak ste nájomcom; inak článok 6 ods. 1 písm. f) GDPR – oprávnený záujem odpovedať na doručené správy.</p>

<h3>3.4 Návštevníci webu a bezpečnostné záznamy</h3>
<p><strong>Údaje:</strong> IP adresa, typ prehliadača a zariadenia (user agent), časová pečiatka, požadovaná adresa (URL), stavový kód odpovede, identifikátor relácie.<br><strong>Účel:</strong> zabezpečenie prevádzky, odhaľovanie a vyšetrovanie útokov, zneužitia a chýb, obrana právnych nárokov.<br><strong>Právny základ:</strong> článok 6 ods. 1 písm. f) GDPR – oprávnený záujem na bezpečnosti a funkčnosti platformy.<br><strong>Doba uchovávania:</strong> 12 mesiacov.</p>

<h3>3.5 Ochrana pred botmi a zneužitím na rezervačných stránkach</h3>
<p>Verejné rezervačné stránky nájomcov sú chránené pred automatizovaným spamom a zneužitím (obmedzenie počtu požiadaviek, bezpečnostné hodnotenie požiadaviek službou Cloudflare). Pri tomto spracúvaní IP adresy a technických signálov požiadavky vystupuje prevádzkovateľ ako samostatný prevádzkovateľ, pretože ide o bezpečnosť celej platformy, nie o rezerváciu u konkrétneho nájomcu. Právny základ: článok 6 ods. 1 písm. f) GDPR. Nevytvárame žiadne profily návštevníkov.</p>

<h3>3.6 Transakčné e-maily</h3>
<p><strong>Údaje:</strong> e-mailová adresa, obsah správy (potvrdenie rezervácie, jednorazový overovací kód, upozornenie na zmenu v účte, doklad o platbe).<br><strong>Účel:</strong> plnenie služby a bezpečnosť účtu.<br><strong>Právny základ:</strong> článok 6 ods. 1 písm. b) GDPR. Tieto správy nie sú marketingom a nie je možné ich odhlásiť, pokým je účet aktívny.</p>

<h3>3.7 Marketingové e-maily</h3>
<p>Novinky a obchodné ponuky vám zasielame výlučne na základe vášho predchádzajúceho súhlasu (článok 6 ods. 1 písm. a) GDPR a § 116 zákona č. 452/2021 Z. z. o elektronických komunikáciách). Súhlas môžete kedykoľvek odvolať kliknutím na odkaz v každej správe alebo e-mailom na {{ $operator['email'] }}; odvolanie nemá vplyv na zákonnosť spracúvania pred odvolaním.</p>
{{-- [LAWYER] Ak by sa využívala výnimka pre existujúcich zákazníkov (§ 116 ods. 15 zákona č. 452/2021 Z. z.), doplniť podmienky a možnosť odmietnutia už pri získaní kontaktu. --}}

<h2>4. Príjemcovia osobných údajov</h2>
<ul>
    <li><strong>Cloudflare, Inc.</strong> (USA) – sieť na doručovanie obsahu, DNS, ochrana pred útokmi (WAF). Prenos zabezpečený rámcom EU-U.S. Data Privacy Framework a štandardnými zmluvnými doložkami Komisie (rozhodnutie 2021/914).</li>
    <li><strong>{{ $emailProvider }}</strong> – doručovanie transakčných e-mailov, servery v EÚ; sprostredkovateľ.</li>
    <li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong> – platby; samostatný prevádzkovateľ.</li>
    <li><strong>Orgány verejnej moci</strong> – len ak nám to ukladá zákon alebo vykonateľné rozhodnutie.</li>
</ul>
<p>Hosting platformy zabezpečuje prevádzkovateľ na vlastnom serveri umiestnenom v Slovenskej republike. Osobné údaje nepredávame, nevytvárame profily a neprijímame automatizované rozhodnutia s právnymi účinkami v zmysle článku 22 GDPR. Aktuálny zoznam sprostredkovateľov: <a href="{{ $links['subprocessors'] }}">Subdodávatelia</a>.</p>

<h2>5. Prenos do tretích krajín</h2>
<p>Údaje sú primárne spracúvané v EÚ. Pri službe Cloudflare môže dôjsť k prenosu do USA; opierame sa o rozhodnutie Komisie o primeranosti pre EU-U.S. Data Privacy Framework a subsidiárne o štandardné zmluvné doložky (2021/914) s doplňujúcimi opatreniami (TLS šifrovanie prenosu, minimalizácia údajov na okrajovej sieti). Kópiu záruk si môžete vyžiadať na {{ $operator['email'] }}.</p>

<h2>6. Doba uchovávania</h2>
<table>
    <thead>
        <tr><th>Kategória</th><th>Doba uchovávania</th><th>Dôvod</th></tr>
    </thead>
    <tbody>
        <tr><td>Faktúry a záznamy o platbách</td><td>10 rokov od konca účtovného roka</td><td>§ 35 zákona č. 431/2002 Z. z. o účtovníctve</td></tr>
        <tr><td>Údaje účtu nájomcu</td><td>Počas trvania účtu + 30 dní</td><td>Plnenie zmluvy, lehota na export údajov</td></tr>
        <tr><td>Bezpečnostné záznamy (logy)</td><td>12 mesiacov</td><td>Oprávnený záujem – bezpečnosť</td></tr>
        <tr><td>Jednorazové overovacie kódy</td><td>24 hodín</td><td>Bezpečnosť účtu</td></tr>
        <tr><td>Komunikácia s podporou</td><td>3 roky od uzavretia požiadavky</td><td>Obrana právnych nárokov</td></tr>
        <tr><td>Záznamy o súhlase (marketing)</td><td>Do odvolania + 3 roky</td><td>Preukázanie súhlasu (čl. 7 ods. 1 GDPR)</td></tr>
    </tbody>
</table>
{{-- [LAWYER] Overiť 3-ročné lehoty pri podpore a záznamoch o súhlase vo väzbe na všeobecnú premlčaciu dobu (§ 101 Občianskeho zákonníka) a § 100 a nasl. --}}
<p>Zálohy sa automaticky prepisujú do 30 dní, takže údaje vymazané z produkčnej databázy zmiznú aj zo záloh najneskôr po tejto lehote.</p>

<h2>7. Cookies</h2>
<p>Používame výlučne nevyhnutné cookies (relácia, ochrana CSRF, jazyk, dôveryhodné zariadenie, bezpečnostné cookies Cloudflare), na ktoré sa podľa § 109 ods. 8 zákona č. 452/2021 Z. z. súhlas nevyžaduje. Analytické ani marketingové cookies nepoužívame. Podrobnosti: <a href="{{ $links['cookies'] }}">Zásady používania cookies</a>.</p>

<h2>8. Vaše práva</h2>
<p>Podľa článkov 15 až 22 GDPR máte právo:</p>
<ul>
    <li>na prístup k svojim osobným údajom a na ich kópiu (čl. 15),</li>
    <li>na opravu nesprávnych alebo neúplných údajov (čl. 16),</li>
    <li>na vymazanie („právo na zabudnutie“), ak neexistuje dôvod na ďalšie uchovávanie (čl. 17),</li>
    <li>na obmedzenie spracúvania (čl. 18),</li>
    <li>na prenosnosť údajov v štruktúrovanom, bežne používanom formáte (čl. 20),</li>
    <li>namietať proti spracúvaniu založenému na oprávnenom záujme podľa článku 6 ods. 1 písm. f) GDPR (čl. 21),</li>
    <li>kedykoľvek odvolať súhlas, ak je spracúvanie založené na súhlase (čl. 7 ods. 3),</li>
    <li>nebyť predmetom automatizovaného individuálneho rozhodovania (čl. 22) – takéto rozhodovanie nevykonávame.</li>
</ul>
<p>Práva uplatníte e-mailom na {{ $operator['email'] }} alebo priamo v nastaveniach účtu (export a vymazanie účtu). Odpovieme bez zbytočného odkladu, najneskôr do jedného mesiaca; pri zložitých žiadostiach môžeme lehotu predĺžiť o ďalšie dva mesiace, o čom vás budeme informovať. Na overenie identity vás môžeme požiadať o potvrdenie z e-mailovej adresy priradenej k účtu.</p>
<p>Ak sa domnievate, že spracúvanie porušuje právne predpisy, máte právo podať návrh na začatie konania dozornému orgánu: <strong>Úrad na ochranu osobných údajov Slovenskej republiky</strong>, Hraničná 12, 820 07 Bratislava 27, Slovenská republika, <a href="https://dataprotection.gov.sk">dataprotection.gov.sk</a>, alebo dozornému orgánu členského štátu vášho obvyklého pobytu, miesta výkonu práce alebo miesta údajného porušenia.</p>

<h2>9. Deti</h2>
<p>Účet nájomcu si môže zriadiť len osoba, ktorá dovŕšila 18 rokov. Vedome nespracúvame údaje detí ako prevádzkovateľ; vekové obmedzenie na rezervačných stránkach určuje príslušný nájomca vo svojom oznámení.</p>

<h2>10. Zabezpečenie</h2>
<p>Prenos údajov je šifrovaný protokolom TLS 1.2 a vyšším, heslá sa ukladajú len vo forme odolného hashu, účty možno chrániť dvojfaktorovým overením, prístup k údajom je obmedzený zásadou najmenších oprávnení, platforma je chránená firewallom Cloudflare a obmedzením počtu požiadaviek. Denné zálohy sú šifrované a uchovávané 30 dní. Údaje o platobných kartách neuchovávame. Podrobnejšie technické a organizačné opatrenia sú uvedené v <a href="{{ $links['dpa'] }}">DPA</a>.</p>

<h2>11. Zmeny týchto zásad</h2>
<p>Zásady môžeme aktualizovať pri zmene služby alebo právnych predpisov. Novú verziu zverejníme na tejto stránke s uvedením verzie a dátumu účinnosti; o podstatných zmenách informujeme nájomcov e-mailom alebo oznámením v administračnom rozhraní najmenej 14 dní vopred.</p>

<h2>12. Právne predpisy</h2>
<p>Nariadenie (EÚ) 2016/679 (GDPR); zákon č. 18/2018 Z. z. o ochrane osobných údajov; zákon č. 452/2021 Z. z. o elektronických komunikáciách; zákon č. 22/2004 Z. z. o elektronickom obchode; zákon č. 431/2002 Z. z. o účtovníctve. Súvisiace dokumenty: <a href="{{ $links['terms'] }}">Obchodné podmienky</a>, <a href="{{ $links['dpa'] }}">DPA</a>, <a href="{{ $links['cookies'] }}">Cookies</a>.</p>
