<h1>Informácie o spracúvaní osobných údajov – rezervačná stránka</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinnosť od {{ $effective }}</p>

<p>Tieto informácie sa vzťahujú na osobné údaje, ktoré zadávate pri rezervácii termínu na tejto rezervačnej stránke. Poskytujú sa podľa článkov 13 a 14 nariadenia (EÚ) 2016/679 (GDPR).</p>

<h2>1. Prevádzkovateľ</h2>
<p>Prevádzkovateľom vašich osobných údajov je poskytovateľ služby, u ktorého si rezervujete termín:</p>
<ul>
  <li>Názov: {{ $tenant->name }}</li>
  <li>Adresa: {{ $tenant->address }}</li>
  <li>E-mail: {{ $tenant->email }}</li>
  <li>Telefón: {{ $tenant->phone }}</li>
</ul>

<h2>2. Sprostredkovateľ a technická platforma</h2>
<p>Rezervačná stránka beží na platforme rezervuj-ma.online ({{ $siteUrl }}). Jej prevádzkovateľ spracúva vaše údaje v mene prevádzkovateľa ako sprostredkovateľ na základe zmluvy o spracúvaní osobných údajov a nepoužíva ich na vlastné účely, s výnimkou zabezpečenia platformy. Platforma využíva týchto subdodávateľov: Cloudflare, Inc. (bezpečnosť, ochrana pred útokmi a doručovanie obsahu; prenos do USA je zabezpečený Rámcom EÚ – USA na ochranu osobných údajov a štandardnými zmluvnými doložkami) a poskytovateľa transakčných e-mailov so sídlom v Európskej únii. Informácie o tom, ako platforma spracúva údaje vo vlastnom mene (napr. bezpečnostné záznamy), nájdete v jej zásadách ochrany osobných údajov: <a href="{{ $links['privacy'] }}">{{ $links['privacy'] }}</a>.</p>

<h2>3. Aké údaje spracúvame</h2>
<ul>
  <li>meno a priezvisko,</li>
  <li>e-mailová adresa,</li>
  <li>telefónne číslo,</li>
  <li>zvolená služba, dátum a čas termínu, prípadne zvolený člen personálu,</li>
  <li>poznámka, ktorú do rezervácie sami napíšete.</li>
</ul>
<p>Do poznámky prosím neuvádzajte údaje o zdravotnom stave ani iné citlivé údaje, pokiaľ to charakter služby nevyžaduje a prevádzkovateľ vás o to výslovne nepožiadal.</p>
{{-- [LAWYER] Ak tenant poskytuje zdravotné alebo kozmetické služby, pri ktorých sa v poznámke očakávajú údaje o zdraví, musí mať vlastný právny základ podľa čl. 9 ods. 2 GDPR (napr. výslovný súhlas) a túto vetu upraviť. --}}

<h2>4. Účely a právne základy</h2>
<ul>
  <li><strong>Vytvorenie a správa rezervácie</strong> vrátane jej zmeny alebo zrušenia – plnenie zmluvy, resp. kroky pred jej uzavretím (čl. 6 ods. 1 písm. b) GDPR).</li>
  <li><strong>Potvrdenie rezervácie a pripomienka termínu</strong> e-mailom – súčasť poskytovanej služby (čl. 6 ods. 1 písm. b) GDPR).</li>
  <li><strong>Plnenie právnych povinností</strong>, napríklad vedenie účtovníctva (čl. 6 ods. 1 písm. c) GDPR).</li>
  <li><strong>Bezpečnosť, predchádzanie zneužitiu a uplatňovanie alebo obrana právnych nárokov</strong> – oprávnený záujem (čl. 6 ods. 1 písm. f) GDPR).</li>
  <li><strong>Marketingové správy</strong> (novinky, ponuky) – výlučne na základe vášho súhlasu (čl. 6 ods. 1 písm. a) GDPR), ktorý môžete kedykoľvek odvolať bez vplyvu na už vykonané spracúvanie.</li>
</ul>
{{-- [LAWYER] V niektorých krajinách (napr. DE, AT) môže byť pre pripomienky termínu vhodnejšie vyžiadať súhlas alebo ich výslovne uviesť ako súčasť služby v podmienkach tenanta. --}}

<h2>5. Doba uchovávania</h2>
<p>Údaje o rezervácii uchovávame {{ $retentionDays }} dní od uskutočnenia (alebo zrušenia) termínu; potom sa vymažú alebo anonymizujú. Dlhšie uchovávame údaje iba vtedy, ak to vyžaduje právny predpis (napr. účtovné doklady) alebo ak je to potrebné na uplatnenie právnych nárokov. Súhlas s marketingom platí do jeho odvolania.</p>

<h2>6. Príjemcovia údajov</h2>
<p>Údaje sprístupňujeme sprostredkovateľovi (platforme rezervuj-ma.online) a jeho subdodávateľom uvedeným v bode 2, a orgánom verejnej moci, ak nám to ukladá zákon. Údaje nepredávame ani neposkytujeme na marketingové účely tretím stranám.</p>

<h2>7. Prenos do tretích krajín</h2>
<p>Údaje sú uložené v Európskej únii (Slovenská republika). Pri technickom doručovaní stránky môže spoločnosť Cloudflare, Inc. (USA) spracúvať sieťové údaje mimo EÚ; tento prenos je zabezpečený Rámcom EÚ – USA na ochranu osobných údajov a štandardnými zmluvnými doložkami Európskej komisie.</p>

<h2>8. Profilovanie a automatizované rozhodovanie</h2>
<p>Nevykonávame profilovanie ani automatizované rozhodovanie s právnymi účinkami voči vám.</p>

<h2>9. Vaše práva</h2>
<p>Podľa článkov 15 až 22 GDPR máte právo na prístup k údajom, ich opravu, vymazanie, obmedzenie spracúvania, prenosnosť, právo namietať proti spracúvaniu založenému na oprávnenom záujme a právo kedykoľvek odvolať súhlas. Práva si uplatníte e-mailom na adrese {{ $tenant->email }} alebo prostredníctvom kontaktov uvedených v bode 1. Odpovieme vám najneskôr do jedného mesiaca.</p>
<p>Ak sa domnievate, že spracúvanie porušuje právne predpisy, máte právo podať sťažnosť dozornému orgánu: {{ $authority }}, prípadne dozornému orgánu v členskom štáte vášho pobytu.</p>

<h2>10. Deti</h2>
<p>Rezervačnú stránku môžu samostatne používať osoby vo veku najmenej {{ $minAge }} rokov. Rezerváciu pre mladšiu osobu môže vytvoriť iba jej rodič alebo zákonný zástupca.</p>

<h2>11. Cookies</h2>
<p>Rezervačná stránka používa iba nevyhnutné cookies potrebné na jej fungovanie a bezpečnosť; nepoužíva analytické ani marketingové cookies, a preto sa nevyžaduje váš súhlas. Podrobnosti: <a href="{{ $links['cookies'] }}">{{ $links['cookies'] }}</a>.</p>
