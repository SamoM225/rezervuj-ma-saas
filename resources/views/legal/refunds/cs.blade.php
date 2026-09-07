<h1>Zásady vracení plateb a zrušení předplatného</h1>
<p class="legal-meta">Verze {{ $version }} · Účinnost od {{ $effective }}</p>

<p>Tyto zásady doplňují <a href="{{ $links['terms'] }}">Obchodní podmínky</a> služby {{ $siteUrl }} (dále „Služba“), kterou provozuje {{ $operator['name'] }} (dále „Provozovatel“). Vysvětlují, jak funguje platba za plán Pro, kdy vracíme peníze a jak zrušit předplatné.</p>

<h2>1. Plány a způsob platby</h2>
<ul>
    <li><strong>Free</strong> – bezplatný plán, nejvýše 50 rezervací měsíčně.</li>
    <li><strong>Pro</strong> – 5 EUR nebo 5 USD měsíčně, případně 50 EUR nebo 50 USD ročně. Cena je uvedena při objednávce a během již zaplaceného období se nemění.</li>
</ul>
<p>Jediným způsobem platby je PayPal (předplatné s automatickým obnovováním). Provozovatel nemá přístup k údajům o vaší kartě ani k bankovnímu účtu; ty zpracovává výhradně společnost PayPal jako samostatný správce. Z rezervací vašich zákazníků neúčtujeme žádnou provizi.</p>

<h2>2. Podnikatelé: bez zákonného práva na odstoupení</h2>
<p>Služba je určena podnikatelům (salony, studia, ordinace a podobné provozy). Uzavíráte-li smlouvu v rámci své podnikatelské činnosti, nejste spotřebitelem a zákonné právo odstoupit od smlouvy bez udání důvodu se na vás nevztahuje. Namísto toho vám poskytujeme dobrovolnou záruku vrácení peněz podle bodu 3.</p>

<h2>3. Dobrovolná 14denní záruka vrácení peněz</h2>
<ul>
    <li>Vztahuje se na <strong>první platbu</strong> každého předplatného Pro (měsíčního i ročního), nikoli na následná obnovení.</li>
    <li>Stačí do 14 dnů od první platby napsat na {{ $operator['email'] }} z e-mailové adresy účtu. Důvod uvádět nemusíte.</li>
    <li>Peníze vrátíme přes PayPal na původní způsob platby nejpozději do 14 dnů od přijetí žádosti. Předplatné se zároveň zruší a účet přejde na plán Free.</li>
    <li>Záruku lze uplatnit jednou na jeden účet. Při zjevném zneužívání (opakované registrace) ji můžeme odmítnout.</li>
</ul>

<h2>4. Zrušení předplatného</h2>
<p>Předplatné můžete zrušit kdykoli tlačítkem <strong>„Zrušit předplatné“</strong> v nastavení účtu, případně přímo ve svém účtu PayPal. Zrušení nabývá účinnosti na konci právě zaplaceného období; do tohoto dne máte plán Pro nadále k dispozici. Poplatek za zrušení neúčtujeme.</p>
<p>Poměrné vrácení zbytku zaplaceného období neposkytujeme. Po skončení období účet přejde na plán Free; vaše údaje zůstávají zachovány, uplatňují se však limity plánu Free. Export údajů (rezervace, zákazníci, služby, kategorie, personál, nastavení) ve formátu CSV/JSON je bezplatný a dostupný kdykoli, jak popisuje část o změně poskytovatele v <a href="{{ $links['terms'] }}">Obchodních podmínkách</a>.</p>

<h2>5. Neúspěšné a duplicitní platby, spory PayPal</h2>
<p>Pokud se platba nezdaří, PayPal ji zopakuje; pokud neuspěje ani opakování, účet přejde na plán Free. Duplicitní platbu (např. dvojí předplatné pro jeden účet) vrátíme v plné výši po ověření. Před otevřením sporu nebo reklamace v PayPal nás prosím nejprve kontaktujte na {{ $operator['email'] }} – většinu případů vyřešíme rychleji přímo.</p>

<h2>6. Měna a kurzové rozdíly</h2>
<p>Vracíme vždy částku v měně původní platby (EUR nebo USD). Kurzové rozdíly, poplatky vašeho poskytovatele platební karty nebo PayPal při konverzi nekompenzujeme.</p>

<h2>7. Zvláštní informace pro polské podnikatele – fyzické osoby</h2>
<p>Jste-li podnikatel – fyzická osoba se sídlem v Polsku a smlouva se Službou pro vás nemá odborný charakter (zejména nevyplývá z předmětu vaší činnosti zapsané v CEIDG), vztahují se na vás podle čl. 38a polského zákona o právech spotřebitele ze 30. května 2014 vybraná spotřebitelská práva, včetně práva odstoupit od smlouvy:</p>
<ul>
    <li>Od smlouvy můžete odstoupit do <strong>14 dnů od jejího uzavření</strong> bez udání důvodu.</li>
    <li>Pokud jste výslovně požádali, abychom Službu začali poskytovat ihned (aktivací plánu Pro), a poté odstoupíte, uhradíte poměrnou část ceny za období do odstoupení; zbytek vrátíme.</li>
    <li>K odstoupení stačí jednoznačné prohlášení zaslané e-mailem na {{ $operator['email'] }}. Můžete použít tento vzor: <em>„Adresát: {{ $operator['name'] }}, {{ $operator['email'] }}. Tímto odstupuji od smlouvy o poskytování služby rezervuj-ma.online uzavřené dne … . Jméno a příjmení, adresa, datum, podpis (pouze v listinné podobě).“</em></li>
</ul>
{{-- [LAWYER] Ověřit rozsah čl. 38a–38c polského zákona o právech spotřebitele (kvazi-spotřebitel) a zda postačuje poměrná úhrada při odstoupení po výslovné žádosti o okamžité plnění. --}}

<h2>8. Poznámka pro Rakousko</h2>
<p>Osoby, které zakládají podnikání v Rakousku a smlouvu uzavírají před zahájením provozu (Gründer), mohou být podle § 1 odst. 3 KSchG považovány za spotřebitele. Preventivně jim poskytujeme stejné 14denní právo na odstoupení a informace jako v bodě 7.</p>
{{-- [LAWYER] Ověřit aplikaci § 1 odst. 3 KSchG a FAGG na digitální služby poskytované zakladatelům před zahájením podnikání. --}}

<h2>9. Kogentní spotřebitelské právo jako minimum</h2>
<p>Pokud se na vás přes podnikatelský charakter Služby vztahuje kogentní spotřebitelské právo státu vašeho obvyklého bydliště (ve Slovenské republice zákon č. 108/2024 Z. z. o ochraně spotřebitele), má přednost před těmito zásadami. Dobrovolná 14denní záruka podle bodu 3 platí v každém případě jako minimum.</p>
<p>Spotřebitelé se sídlem ve Slovenské republice se mohou obrátit na Slovenskou obchodní inspekci (Slovenská obchodná inšpekcia) nebo na jiný subjekt alternativního řešení sporů zapsaný v seznamu podle zákona č. 391/2015 Z. z. Spotřebitelé z jiných členských států se mohou obrátit na subjekt ADR ve svém státě.</p>

<h2>10. Kontakt</h2>
<p>Žádosti o vrácení platby, odstoupení a dotazy k fakturaci: {{ $operator['email'] }}. Provozovatel: {{ $operator['name'] }}, {{ $operator['address'] }}. Další údaje najdete v <a href="{{ $links['imprint'] }}">Identifikaci provozovatele</a>.</p>
