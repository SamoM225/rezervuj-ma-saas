<h1>Zásady ochrany osobních údajů</h1>
<p class="legal-meta">Verze {{ $version }} · Účinné od {{ $effective }}</p>

<h2>1. Kdo je správcem</h2>
<p>Provozovatel: {{ $operator['name'] }}<br>E-mail: {{ $operator['email'] }}</p>
<p>Provozovatel služby {{ $siteUrl }} (dále „platforma“) je fyzická osoba se sídlem ve Slovenské republice. Pověřenec pro ochranu osobních údajů (DPO) nebyl jmenován, protože provozovatel nesplňuje podmínky článku 37 GDPR; ve všech otázkách ochrany osobních údajů nás kontaktujte na uvedeném e-mailu.</p>
{{-- [LAWYER] Ověřit, zda rozsah zpracování po rozjezdu služby nezaloží povinnost jmenovat pověřence podle čl. 37 GDPR / § 44 slovenského zákona č. 18/2018 Z. z. --}}

<h2>2. Na co se tyto zásady vztahují</h2>
<p>Tyto zásady se vztahují na zpracování osobních údajů, při němž je provozovatel platformy správcem ve smyslu článku 4 bodu 7 GDPR: účty poskytovatelů služeb (dále „nájemci“), fakturace, podpora, návštěvníci webu a bezpečnost platformy.</p>
<p>Osobní údaje, které zadáte na veřejné rezervační stránce konkrétního nájemce (např. <code>{{ $siteUrl }}/nazev-provozovny/booking</code>), zpracovává jako správce tento nájemce. My je zpracováváme pouze jako zpracovatel na základě smlouvy o zpracování osobních údajů (<a href="{{ $links['dpa'] }}">DPA</a>). Informace o zpracování vaší rezervace najdete v oznámení o ochraně osobních údajů, které nájemce zobrazuje na své rezervační stránce; se žádostmi o výkon práv se v takovém případě obracejte primárně na nájemce.</p>

<h2>3. Jaké údaje zpracováváme, proč a na jakém právním základě</h2>

<h3>3.1 Účty nájemců a administrační rozhraní</h3>
<p><strong>Údaje:</strong> jméno, e-mail, telefon, heslo (ukládá se pouze jako hash), tajný klíč dvoufaktorového ověření (2FA), jazykové nastavení, název a nastavení provozovny, záznamy o přihlášení.<br><strong>Účel:</strong> zřízení a vedení účtu, poskytování služby, komunikace o službě.<br><strong>Právní základ:</strong> plnění smlouvy – článek 6 odst. 1 písm. b) GDPR.</p>

<h3>3.2 Fakturace a platby přes PayPal</h3>
<p><strong>Údaje:</strong> e-mail plátce, částka, měna, identifikátor předplatného a transakce PayPal, datum platby, fakturační údaje.<br><strong>Účel:</strong> zpracování platby za plán Pro, vystavení účetních dokladů, plnění účetních a daňových povinností.<br><strong>Právní základ:</strong> článek 6 odst. 1 písm. b) GDPR (smlouva) a písm. c) GDPR (zákonná povinnost, zejména slovenský zákon č. 431/2002 Z. z. o účetnictví).</p>
<p>Platby zpracovává společnost PayPal (Europe) S.à r.l. et Cie, S.C.A., 22–24 Boulevard Royal, L-2449 Lucemburk, která je při zpracování platebních údajů samostatným správcem a řídí se vlastními zásadami ochrany osobních údajů. Provozovatel nikdy nedostává ani neuchovává čísla platebních karet.</p>

<h3>3.3 Podpora a komunikace</h3>
<p><strong>Údaje:</strong> jméno, e-mail, obsah zprávy, údaje o účtu potřebné k vyřešení požadavku.<br><strong>Účel:</strong> odpovídání na dotazy, řešení technických problémů a reklamací.<br><strong>Právní základ:</strong> článek 6 odst. 1 písm. b) GDPR, pokud jste nájemcem; jinak článek 6 odst. 1 písm. f) GDPR – oprávněný zájem odpovídat na doručené zprávy.</p>

<h3>3.4 Návštěvníci webu a bezpečnostní záznamy</h3>
<p><strong>Údaje:</strong> IP adresa, typ prohlížeče a zařízení (user agent), časové razítko, požadovaná adresa (URL), stavový kód odpovědi, identifikátor relace.<br><strong>Účel:</strong> zabezpečení provozu, odhalování a vyšetřování útoků, zneužití a chyb, obrana právních nároků.<br><strong>Právní základ:</strong> článek 6 odst. 1 písm. f) GDPR – oprávněný zájem na bezpečnosti a funkčnosti platformy.<br><strong>Doba uchování:</strong> 12 měsíců.</p>

<h3>3.5 Ochrana před boty a zneužitím na rezervačních stránkách</h3>
<p>Veřejné rezervační stránky nájemců jsou chráněny před automatizovaným spamem a zneužitím (omezení počtu požadavků, bezpečnostní hodnocení požadavků službou Cloudflare). Při tomto zpracování IP adresy a technických signálů požadavku vystupuje provozovatel jako samostatný správce, protože jde o bezpečnost celé platformy, nikoli o rezervaci u konkrétního nájemce. Právní základ: článek 6 odst. 1 písm. f) GDPR. Nevytváříme žádné profily návštěvníků.</p>

<h3>3.6 Transakční e-maily</h3>
<p><strong>Údaje:</strong> e-mailová adresa, obsah zprávy (potvrzení rezervace, jednorázový ověřovací kód, upozornění na změnu v účtu, doklad o platbě).<br><strong>Účel:</strong> plnění služby a bezpečnost účtu.<br><strong>Právní základ:</strong> článek 6 odst. 1 písm. b) GDPR. Tyto zprávy nejsou marketingem a nelze je odhlásit, dokud je účet aktivní.</p>

<h3>3.7 Marketingové e-maily</h3>
<p>Novinky a obchodní nabídky vám zasíláme výhradně na základě vašeho předchozího souhlasu (článek 6 odst. 1 písm. a) GDPR a § 116 slovenského zákona č. 452/2021 Z. z. o elektronických komunikacích). Souhlas můžete kdykoli odvolat kliknutím na odkaz v každé zprávě nebo e-mailem na {{ $operator['email'] }}; odvolání nemá vliv na zákonnost zpracování před odvoláním.</p>
{{-- [LAWYER] Pokud by se využívala výjimka pro stávající zákazníky (§ 116 odst. 15 zákona č. 452/2021 Z. z.), doplnit podmínky a možnost odmítnutí již při získání kontaktu. --}}

<h2>4. Příjemci osobních údajů</h2>
<ul>
    <li><strong>Cloudflare, Inc.</strong> (USA) – síť pro doručování obsahu, DNS, ochrana před útoky (WAF). Předání zajištěno rámcem EU-U.S. Data Privacy Framework a standardními smluvními doložkami Komise (rozhodnutí 2021/914).</li>
    <li><strong>{{ $emailProvider }}</strong> – doručování transakčních e-mailů, servery v EU; zpracovatel.</li>
    <li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong> – platby; samostatný správce.</li>
    <li><strong>Orgány veřejné moci</strong> – pouze pokud nám to ukládá zákon nebo vykonatelné rozhodnutí.</li>
</ul>
<p>Hosting platformy zajišťuje provozovatel na vlastním serveru umístěném ve Slovenské republice. Osobní údaje neprodáváme, nevytváříme profily a nepřijímáme automatizovaná rozhodnutí s právními účinky ve smyslu článku 22 GDPR. Aktuální seznam zpracovatelů: <a href="{{ $links['subprocessors'] }}">Subdodavatelé</a>.</p>

<h2>5. Předání do třetích zemí</h2>
<p>Údaje jsou primárně zpracovávány v EU. U služby Cloudflare může dojít k předání do USA; opíráme se o rozhodnutí Komise o odpovídající ochraně pro EU-U.S. Data Privacy Framework a podpůrně o standardní smluvní doložky (2021/914) s doplňujícími opatřeními (šifrování přenosu TLS, minimalizace údajů na okrajové síti). Kopii záruk si můžete vyžádat na {{ $operator['email'] }}.</p>

<h2>6. Doba uchování</h2>
<table>
    <thead>
        <tr><th>Kategorie</th><th>Doba uchování</th><th>Důvod</th></tr>
    </thead>
    <tbody>
        <tr><td>Faktury a záznamy o platbách</td><td>10 let od konce účetního roku</td><td>§ 35 slovenského zákona č. 431/2002 Z. z. o účetnictví</td></tr>
        <tr><td>Údaje účtu nájemce</td><td>Po dobu trvání účtu + 30 dní</td><td>Plnění smlouvy, lhůta pro export údajů</td></tr>
        <tr><td>Bezpečnostní záznamy (logy)</td><td>12 měsíců</td><td>Oprávněný zájem – bezpečnost</td></tr>
        <tr><td>Jednorázové ověřovací kódy</td><td>24 hodin</td><td>Bezpečnost účtu</td></tr>
        <tr><td>Komunikace s podporou</td><td>3 roky od uzavření požadavku</td><td>Obrana právních nároků</td></tr>
        <tr><td>Záznamy o souhlasu (marketing)</td><td>Do odvolání + 3 roky</td><td>Prokázání souhlasu (čl. 7 odst. 1 GDPR)</td></tr>
    </tbody>
</table>
{{-- [LAWYER] Ověřit 3leté lhůty u podpory a záznamů o souhlasu ve vazbě na obecnou promlčecí dobu podle slovenského občanského zákoníku (§ 101). --}}
<p>Zálohy se automaticky přepisují do 30 dnů, takže údaje smazané z produkční databáze zmizí i ze záloh nejpozději po této lhůtě.</p>

<h2>7. Cookies</h2>
<p>Používáme výhradně nezbytné cookies (relace, ochrana CSRF, jazyk, důvěryhodné zařízení, bezpečnostní cookies Cloudflare), u kterých se podle § 109 odst. 8 slovenského zákona č. 452/2021 Z. z. (v ČR § 89 odst. 3 zákona č. 127/2005 Sb.) souhlas nevyžaduje. Analytické ani marketingové cookies nepoužíváme. Podrobnosti: <a href="{{ $links['cookies'] }}">Zásady používání cookies</a>.</p>

<h2>8. Vaše práva</h2>
<p>Podle článků 15 až 22 GDPR máte právo:</p>
<ul>
    <li>na přístup ke svým osobním údajům a na jejich kopii (čl. 15),</li>
    <li>na opravu nesprávných nebo neúplných údajů (čl. 16),</li>
    <li>na výmaz („právo být zapomenut“), pokud neexistuje důvod k dalšímu uchování (čl. 17),</li>
    <li>na omezení zpracování (čl. 18),</li>
    <li>na přenositelnost údajů ve strukturovaném, běžně používaném formátu (čl. 20),</li>
    <li>vznést námitku proti zpracování založenému na oprávněném zájmu podle článku 6 odst. 1 písm. f) GDPR (čl. 21),</li>
    <li>kdykoli odvolat souhlas, je-li zpracování založeno na souhlasu (čl. 7 odst. 3),</li>
    <li>nebýt předmětem automatizovaného individuálního rozhodování (čl. 22) – takové rozhodování neprovádíme.</li>
</ul>
<p>Práva uplatníte e-mailem na {{ $operator['email'] }} nebo přímo v nastavení účtu (export a smazání účtu). Odpovíme bez zbytečného odkladu, nejpozději do jednoho měsíce; u složitých žádostí můžeme lhůtu prodloužit o další dva měsíce, o čemž vás budeme informovat. K ověření identity vás můžeme požádat o potvrzení z e-mailové adresy přiřazené k účtu.</p>
<p>Pokud se domníváte, že zpracování porušuje právní předpisy, máte právo podat stížnost dozorovému úřadu: <strong>Úrad na ochranu osobných údajov Slovenskej republiky</strong>, Hraničná 12, 820 07 Bratislava 27, Slovenská republika, <a href="https://dataprotection.gov.sk">dataprotection.gov.sk</a>, nebo dozorovému úřadu členského státu vašeho obvyklého bydliště, místa výkonu práce nebo místa údajného porušení (v ČR Úřad pro ochranu osobních údajů).</p>

<h2>9. Děti</h2>
<p>Účet nájemce si může zřídit pouze osoba, která dovršila 18 let. Vědomě nezpracováváme údaje dětí jako správce; věkové omezení na rezervačních stránkách určuje příslušný nájemce ve svém oznámení.</p>

<h2>10. Zabezpečení</h2>
<p>Přenos údajů je šifrován protokolem TLS 1.2 a vyšším, hesla se ukládají pouze ve formě odolného hashe, účty lze chránit dvoufaktorovým ověřením, přístup k údajům je omezen zásadou nejmenších oprávnění, platforma je chráněna firewallem Cloudflare a omezením počtu požadavků. Denní zálohy jsou šifrované a uchovávané 30 dní. Údaje o platebních kartách neuchováváme. Podrobnější technická a organizační opatření jsou uvedena v <a href="{{ $links['dpa'] }}">DPA</a>.</p>

<h2>11. Změny těchto zásad</h2>
<p>Zásady můžeme aktualizovat při změně služby nebo právních předpisů. Novou verzi zveřejníme na této stránce s uvedením verze a data účinnosti; o podstatných změnách informujeme nájemce e-mailem nebo oznámením v administračním rozhraní nejméně 14 dní předem.</p>

<h2>12. Právní předpisy</h2>
<p>Nařízení (EU) 2016/679 (GDPR); slovenský zákon č. 18/2018 Z. z. o ochraně osobních údajů; slovenský zákon č. 452/2021 Z. z. o elektronických komunikacích; slovenský zákon č. 22/2004 Z. z. o elektronickém obchodu; slovenský zákon č. 431/2002 Z. z. o účetnictví. Související dokumenty: <a href="{{ $links['terms'] }}">Obchodní podmínky</a>, <a href="{{ $links['dpa'] }}">DPA</a>, <a href="{{ $links['cookies'] }}">Cookies</a>.</p>
