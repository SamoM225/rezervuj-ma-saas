<h1>Informace o zpracování osobních údajů – rezervační stránka</h1>
<p class="legal-meta">Verze {{ $version }} · Účinnost od {{ $effective }}</p>

<p>Tyto informace se vztahují na osobní údaje, které zadáváte při rezervaci termínu na této rezervační stránce. Poskytují se podle článků 13 a 14 nařízení (EU) 2016/679 (GDPR).</p>

<h2>1. Správce</h2>
<p>Správcem vašich osobních údajů je poskytovatel služby, u kterého si rezervujete termín:</p>
<ul>
  <li>Název: {{ $tenant->name }}</li>
  <li>Adresa: {{ $tenant->address }}</li>
  <li>E-mail: {{ $tenant->email }}</li>
  <li>Telefon: {{ $tenant->phone }}</li>
</ul>

<h2>2. Zpracovatel a technická platforma</h2>
<p>Rezervační stránka běží na platformě rezervuj-ma.online ({{ $siteUrl }}). Její provozovatel zpracovává vaše údaje jménem správce jako zpracovatel na základě smlouvy o zpracování osobních údajů a nepoužívá je k vlastním účelům, s výjimkou zabezpečení platformy. Platforma využívá tyto dílčí zpracovatele: Cloudflare, Inc. (bezpečnost, ochrana před útoky a doručování obsahu; předání do USA je zajištěno Rámcem EU – USA pro ochranu osobních údajů a standardními smluvními doložkami) a poskytovatele transakčních e-mailů se sídlem v Evropské unii. Informace o tom, jak platforma zpracovává údaje vlastním jménem (např. bezpečnostní záznamy), najdete v jejích zásadách ochrany osobních údajů: <a href="{{ $links['privacy'] }}">{{ $links['privacy'] }}</a>.</p>

<h2>3. Jaké údaje zpracováváme</h2>
<ul>
  <li>jméno a příjmení,</li>
  <li>e-mailová adresa,</li>
  <li>telefonní číslo,</li>
  <li>zvolená služba, datum a čas termínu, případně zvolený člen personálu,</li>
  <li>poznámka, kterou do rezervace sami napíšete.</li>
</ul>
<p>Do poznámky prosím neuvádějte údaje o zdravotním stavu ani jiné citlivé údaje, pokud to charakter služby nevyžaduje a správce vás o to výslovně nepožádal.</p>
{{-- [LAWYER] Pokud tenant poskytuje zdravotní nebo kosmetické služby, u kterých se v poznámce očekávají údaje o zdraví, musí mít vlastní právní základ podle čl. 9 odst. 2 GDPR (např. výslovný souhlas) a tuto větu upravit. --}}

<h2>4. Účely a právní základy</h2>
<ul>
  <li><strong>Vytvoření a správa rezervace</strong> včetně její změny nebo zrušení – plnění smlouvy, resp. kroky před jejím uzavřením (čl. 6 odst. 1 písm. b) GDPR).</li>
  <li><strong>Potvrzení rezervace a připomínka termínu</strong> e-mailem – součást poskytované služby (čl. 6 odst. 1 písm. b) GDPR).</li>
  <li><strong>Plnění právních povinností</strong>, například vedení účetnictví (čl. 6 odst. 1 písm. c) GDPR).</li>
  <li><strong>Bezpečnost, předcházení zneužití a uplatňování nebo obrana právních nároků</strong> – oprávněný zájem (čl. 6 odst. 1 písm. f) GDPR).</li>
  <li><strong>Marketingová sdělení</strong> (novinky, nabídky) – výlučně na základě vašeho souhlasu (čl. 6 odst. 1 písm. a) GDPR), který můžete kdykoli odvolat bez vlivu na již provedené zpracování.</li>
</ul>
{{-- [LAWYER] V některých zemích (např. DE, AT) může být pro připomínky termínu vhodnější vyžádat souhlas nebo je výslovně uvést jako součást služby v podmínkách tenanta. --}}

<h2>5. Doba uchování</h2>
<p>Údaje o rezervaci uchováváme {{ $retentionDays }} dní od uskutečnění (nebo zrušení) termínu; poté se vymažou nebo anonymizují. Déle uchováváme údaje pouze tehdy, vyžaduje-li to právní předpis (např. účetní doklady) nebo je-li to nutné k uplatnění právních nároků. Souhlas s marketingem platí do jeho odvolání.</p>

<h2>6. Příjemci údajů</h2>
<p>Údaje zpřístupňujeme zpracovateli (platformě rezervuj-ma.online) a jeho dílčím zpracovatelům uvedeným v bodě 2 a orgánům veřejné moci, ukládá-li nám to zákon. Údaje neprodáváme ani neposkytujeme třetím stranám k marketingovým účelům.</p>

<h2>7. Předání do třetích zemí</h2>
<p>Údaje jsou uloženy v Evropské unii (Slovenská republika). Při technickém doručování stránky může společnost Cloudflare, Inc. (USA) zpracovávat síťové údaje mimo EU; toto předání je zajištěno Rámcem EU – USA pro ochranu osobních údajů a standardními smluvními doložkami Evropské komise.</p>

<h2>8. Profilování a automatizované rozhodování</h2>
<p>Neprovádíme profilování ani automatizované rozhodování s právními účinky vůči vám.</p>

<h2>9. Vaše práva</h2>
<p>Podle článků 15 až 22 GDPR máte právo na přístup k údajům, jejich opravu, výmaz, omezení zpracování, přenositelnost, právo vznést námitku proti zpracování založenému na oprávněném zájmu a právo kdykoli odvolat souhlas. Práva uplatníte e-mailem na adrese {{ $tenant->email }} nebo prostřednictvím kontaktů uvedených v bodě 1. Odpovíme vám nejpozději do jednoho měsíce.</p>
<p>Domníváte-li se, že zpracování porušuje právní předpisy, máte právo podat stížnost dozorovému úřadu: {{ $authority }}, případně dozorovému úřadu v členském státě vašeho pobytu.</p>

<h2>10. Děti</h2>
<p>Rezervační stránku mohou samostatně používat osoby ve věku nejméně {{ $minAge }} let. Rezervaci pro mladší osobu může vytvořit pouze její rodič nebo zákonný zástupce.</p>

<h2>11. Cookies</h2>
<p>Rezervační stránka používá pouze nezbytné cookies potřebné pro její fungování a bezpečnost; nepoužívá analytické ani marketingové cookies, a proto se nevyžaduje váš souhlas. Podrobnosti: <a href="{{ $links['cookies'] }}">{{ $links['cookies'] }}</a>.</p>
