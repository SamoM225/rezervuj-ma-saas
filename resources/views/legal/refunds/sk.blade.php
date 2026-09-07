<h1>Zásady vrátenia platieb a zrušenia predplatného</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinnosť od {{ $effective }}</p>

<p>Tieto zásady dopĺňajú <a href="{{ $links['terms'] }}">Obchodné podmienky</a> služby {{ $siteUrl }} (ďalej „Služba“), ktorú prevádzkuje {{ $operator['name'] }} (ďalej „Prevádzkovateľ“). Vysvetľujú, ako funguje platba za plán Pro, kedy vraciame peniaze a ako zrušiť predplatné.</p>

<h2>1. Plány a spôsob platby</h2>
<ul>
    <li><strong>Free</strong> – bezplatný plán, najviac 50 rezervácií mesačne.</li>
    <li><strong>Pro</strong> – 5 EUR alebo 5 USD mesačne, prípadne 50 EUR alebo 50 USD ročne. Cena je uvedená pri objednávke a nemení sa počas už zaplateného obdobia.</li>
</ul>
<p>Jediným spôsobom platby je PayPal (predplatné s automatickým obnovovaním). Prevádzkovateľ nemá prístup k údajom o vašej karte ani k bankovému účtu; tie spracúva výlučne spoločnosť PayPal ako samostatný prevádzkovateľ. Z rezervácií vašich zákazníkov neúčtujeme žiadnu províziu.</p>

<h2>2. Podnikatelia: bez zákonného práva na odstúpenie</h2>
<p>Služba je určená podnikateľom (salóny, štúdiá, ambulancie a podobné prevádzky). Ak uzatvárate zmluvu v rámci svojej podnikateľskej činnosti, nie ste spotrebiteľom a zákonné právo odstúpiť od zmluvy bez udania dôvodu sa na vás nevzťahuje. Namiesto toho vám poskytujeme dobrovoľnú záruku vrátenia peňazí podľa bodu 3.</p>

<h2>3. Dobrovoľná 14-dňová záruka vrátenia peňazí</h2>
<ul>
    <li>Vzťahuje sa na <strong>prvú platbu</strong> každého predplatného Pro (mesačného aj ročného), nie na následné obnovenia.</li>
    <li>Stačí do 14 dní od prvej platby napísať na {{ $operator['email'] }} z e-mailovej adresy účtu. Dôvod uvádzať nemusíte.</li>
    <li>Peniaze vrátime cez PayPal na pôvodný spôsob platby najneskôr do 14 dní od prijatia žiadosti. Predplatné sa zároveň zruší a účet prejde na plán Free.</li>
    <li>Záruku možno uplatniť raz na jeden účet. Pri zjavnom zneužívaní (opakované registrácie) ju môžeme odmietnuť.</li>
</ul>

<h2>4. Zrušenie predplatného</h2>
<p>Predplatné môžete zrušiť kedykoľvek tlačidlom <strong>„Zrušiť predplatné“</strong> v nastaveniach účtu, prípadne priamo vo svojom účte PayPal. Zrušenie nadobúda účinnosť na konci práve zaplateného obdobia; do tohto dňa máte plán Pro naďalej k dispozícii. Poplatok za zrušenie neúčtujeme.</p>
<p>Pomerné vrátenie zvyšku zaplateného obdobia neposkytujeme. Po skončení obdobia účet prejde na plán Free; vaše údaje zostávajú zachované, uplatňujú sa však limity plánu Free. Export údajov (rezervácie, zákazníci, služby, kategórie, personál, nastavenia) vo formáte CSV/JSON je bezplatný a dostupný kedykoľvek, ako opisuje časť o zmene poskytovateľa v <a href="{{ $links['terms'] }}">Obchodných podmienkach</a>.</p>

<h2>5. Neúspešné a duplicitné platby, spory PayPal</h2>
<p>Ak sa platba nepodarí, PayPal ju zopakuje; ak neuspeje ani opakovanie, účet prejde na plán Free. Duplicitnú platbu (napr. dvojité predplatné pre jeden účet) vrátime v plnej výške po overení. Pred otvorením sporu alebo reklamácie v PayPal nás prosím najprv kontaktujte na {{ $operator['email'] }} – väčšinu prípadov vyriešime rýchlejšie priamo.</p>

<h2>6. Mena a kurzové rozdiely</h2>
<p>Vraciame vždy sumu v mene pôvodnej platby (EUR alebo USD). Kurzové rozdiely, poplatky vášho poskytovateľa platobnej karty alebo PayPal pri konverzii nekompenzujeme.</p>

<h2>7. Osobitné informácie pre poľských podnikateľov – fyzické osoby</h2>
<p>Ak ste podnikateľ – fyzická osoba so sídlom v Poľsku a zmluva so Službou nemá pre vás odborný charakter (najmä vyplýva z predmetu vašej činnosti zapísanej v CEIDG), vzťahujú sa na vás podľa čl. 38a poľského zákona o právach spotrebiteľa z 30. mája 2014 vybrané spotrebiteľské práva, vrátane práva odstúpiť od zmluvy:</p>
<ul>
    <li>Od zmluvy môžete odstúpiť do <strong>14 dní od jej uzavretia</strong> bez udania dôvodu.</li>
    <li>Ak ste výslovne požiadali, aby sme Službu začali poskytovať ihneď (aktiváciou plánu Pro), a potom odstúpite, uhradíte pomernú časť ceny za obdobie do odstúpenia; zvyšok vrátime.</li>
    <li>Na odstúpenie stačí jednoznačné vyhlásenie zaslané e-mailom na {{ $operator['email'] }}. Môžete použiť tento vzor: <em>„Adresát: {{ $operator['name'] }}, {{ $operator['email'] }}. Týmto odstupujem od zmluvy o poskytovaní služby rezervuj-ma.online uzavretej dňa … . Meno a priezvisko, adresa, dátum, podpis (iba pri listinnej forme).“</em></li>
</ul>
{{-- [LAWYER] Overiť rozsah čl. 38a–38c poľského zákona o právach spotrebiteľa (kvázi-spotrebiteľ) a či postačuje pomerná úhrada pri odstúpení po výslovnej žiadosti o okamžité plnenie. --}}

<h2>8. Poznámka pre Rakúsko</h2>
<p>Osoby, ktoré si zakladajú podnikanie v Rakúsku a zmluvu uzatvárajú pred začatím prevádzky (Gründer), môžu byť podľa § 1 ods. 3 KSchG považované za spotrebiteľov. Preventívne im poskytujeme rovnaké 14-dňové právo na odstúpenie a informácie ako v bode 7.</p>
{{-- [LAWYER] Overiť aplikáciu § 1 ods. 3 KSchG a FAGG na digitálne služby poskytované zakladateľom pred začatím podnikania. --}}

<h2>9. Kogentné spotrebiteľské právo ako minimum</h2>
<p>Ak sa na vás napriek podnikateľskému charakteru Služby vzťahuje kogentné spotrebiteľské právo štátu vášho obvyklého pobytu (v Slovenskej republike zákon č. 108/2024 Z. z. o ochrane spotrebiteľa), má prednosť pred týmito zásadami. Dobrovoľná 14-dňová záruka podľa bodu 3 platí v každom prípade ako minimum.</p>
<p>Spotrebitelia so sídlom v Slovenskej republike sa môžu obrátiť na Slovenskú obchodnú inšpekciu alebo na iný subjekt alternatívneho riešenia sporov zapísaný v zozname podľa zákona č. 391/2015 Z. z. Spotrebitelia z iných členských štátov sa môžu obrátiť na subjekt ARS vo svojom štáte.</p>

<h2>10. Kontakt</h2>
<p>Žiadosti o vrátenie platby, odstúpenie a otázky k fakturácii: {{ $operator['email'] }}. Prevádzkovateľ: {{ $operator['name'] }}, {{ $operator['address'] }}. Ďalšie údaje nájdete v <a href="{{ $links['imprint'] }}">Identifikácii prevádzkovateľa</a>.</p>
