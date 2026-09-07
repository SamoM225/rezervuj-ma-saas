<h1>Všeobecné obchodní podmínky služby rezervuj-ma.online</h1>
<p class="legal-meta">Verze {{ $version }} · Účinnost od {{ $effective }}</p>

<h2>1. Úvodní ustanovení a definice</h2>
<p>Tyto všeobecné obchodní podmínky (dále „Podmínky“) upravují používání online rezervační platformy dostupné na adrese {{ $siteUrl }} (dále „Služba“). Provozovatel: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }} (dále „Provozovatel“).</p>
{{-- [LAWYER] Provozovatel je zatím fyzická osoba bez živnostenského oprávnění. Ověřit, zda rozsah a úplatnost Služby nezakládá povinnost registrace podnikání podle slovenského práva a zda je formulace „Provozovatel“ dostačující. --}}
<ul>
<li><strong>Nájemce</strong> (angl. tenant) je podnikatel nebo jiná osoba, která si vytvoří účet a používá Službu ke správě rezervací svých klientů.</li>
<li><strong>Zákazník</strong> je koncová osoba, která si na veřejné rezervační stránce Nájemce ({{ $siteUrl }}/{slug}/booking) rezervuje termín.</li>
<li><strong>Rezervační stránka</strong> je veřejná stránka Nájemce vytvořená v rámci Služby.</li>
<li><strong>Plán</strong> je rozsah funkcí a limitů Služby (Free nebo Pro).</li>
</ul>
<p>Nedílnou součástí smlouvy mezi Provozovatelem a Nájemcem jsou <a href="{{ $links['dpa'] }}">Smlouva o zpracování osobních údajů</a> (DPA), <a href="{{ $links['aup'] }}">Pravidla přijatelného používání</a>, <a href="{{ $links['refunds'] }}">Podmínky vracení plateb</a> a <a href="{{ $links['privacy'] }}">Zásady ochrany osobních údajů</a>.</p>

<h2>2. Kdo může Službu používat</h2>
<p>Službu mohou používat pouze osoby starší 18 let. Nájemce prohlašuje, že Službu používá v rámci své podnikatelské nebo jiné profesní činnosti (vztah B2B). Ustanovení slovenského zákona č. 108/2024 Z. z. o ochraně spotřebitele se na Nájemce vztahují pouze v rozsahu, v jakém má postavení spotřebitele podle kogentních předpisů svého státu.</p>
<p>Je-li Nájemce fyzickou osobou podnikající podle polského práva a smlouva pro něj nemá profesní charakter (čl. 38a polského zákona o právech spotřebitele), má právo odstoupit od smlouvy do 14 dnů od uzavření bez uvedení důvodu. Podrobnosti a vzorový formulář jsou uvedeny v <a href="{{ $links['refunds'] }}">Podmínkách vracení plateb</a>. Nad rámec zákona poskytuje Provozovatel všem Nájemcům obchodní záruku vrácení první platby do 14 dnů.</p>
{{-- [LAWYER] Ověřit aktuální znění čl. 38a polského ustawa o prawach konsumenta a rozsah kvazi-spotřebitelské ochrany u digitálních služeb. --}}

<h2>3. Účet a bezpečnost</h2>
<p>Registrací vzniká Nájemci účet. Nájemce je povinen uvádět pravdivé údaje a udržovat je aktuální. Nájemce odpovídá za důvěrnost přihlašovacích údajů a za veškerou činnost provedenou prostřednictvím svého účtu. Služba umožňuje zapnout dvoufaktorové ověření (2FA); Provozovatel je doporučuje. Podezření na zneužití účtu je Nájemce povinen neprodleně oznámit na {{ $operator['email'] }}.</p>

<h2>4. Plány, ceny a platby</h2>
<ul>
<li><strong>Free</strong>: bezplatný plán s limitem 50 rezervací za kalendářní měsíc.</li>
<li><strong>Pro</strong>: 5 EUR nebo 5 USD měsíčně, případně 50 EUR nebo 50 USD ročně, podle zvolené měny.</li>
</ul>
<p>Platby se provádějí výhradně prostřednictvím služby PayPal. Předplatné se automaticky obnovuje na další období, dokud jej Nájemce nezruší. Provozovatel neúčtuje žádnou provizi z rezervací ani z plateb mezi Nájemcem a Zákazníkem; Služba není platebním zprostředkovatelem. Změnu cen oznámí Provozovatel e-mailem nejméně 30 dnů předem; změna se uplatní až na následující období a Nájemce může předplatné před její účinností zrušit.</p>
<p>Ceny jsou uvedeny bez daní. Nájemce odpovídá za daně a poplatky, které se na něj vztahují v jeho státě.</p>
{{-- [LAWYER] Ověřit režim DPH Provozovatele (fyzická osoba, prah registrace, OSS u služeb do jiných členských států EU) a formulaci o daních. --}}

<h2>5. Povinnosti Nájemce a obsah</h2>
<p>Nájemce odpovídá za veškerý obsah své Rezervační stránky (název, popisy služeb, ceny, obrázky) a za jeho soulad s právem. Nájemce se zavazuje používat Službu v souladu s <a href="{{ $links['aup'] }}">Pravidly přijatelného používání</a> a nezasahovat do bezpečnosti ani provozu Služby.</p>
<p>Ve vztahu k osobním údajům Zákazníků je Nájemce správcem (controller) a Provozovatel zpracovatelem (processor) ve smyslu čl. 28 GDPR. <a href="{{ $links['dpa'] }}">Smlouva o zpracování osobních údajů</a> je součástí smlouvy. Nájemce je povinen Zákazníkům poskytnout vlastní informaci o zpracování osobních údajů; Služba k tomu nabízí šablonu, za její obsah však odpovídá Nájemce.</p>

<h2>6. Vztah k Zákazníkům</h2>
<p>Smlouva o poskytnutí služby (např. střihu, ošetření, lekce) vzniká výhradně mezi Nájemcem a Zákazníkem. Provozovatel není její smluvní stranou, negarantuje poskytnutí rezervované služby ani její kvalitu a nevyřizuje reklamace Zákazníků vůči Nájemci. Provozovatel zpracovává údaje Zákazníků pouze podle pokynů Nájemce, s výjimkou údajů potřebných pro bezpečnost platformy, u nichž vystupuje jako samostatný správce (viz <a href="{{ $links['privacy'] }}">Zásady ochrany osobních údajů</a>).</p>

<h2>7. Dostupnost a změny Služby</h2>
<p>Provozovatel poskytuje Službu s odbornou péčí, avšak bez záruky nepřetržité dostupnosti (bez SLA). Plánovanou údržbu oznámí, je-li to možné, předem. Provozovatel může funkce Služby rozvíjet nebo měnit; podstatné omezení funkcí placených plánů oznámí nejméně 30 dnů předem.</p>

<h2>8. Duševní vlastnictví</h2>
<p>Software, design a značka Služby zůstávají majetkem Provozovatele. Nájemce získává nevýhradní, nepřenosnou licenci k používání Služby po dobu trvání smlouvy. Obsah a údaje vložené Nájemcem zůstávají jeho majetkem; Nájemce uděluje Provozovateli pouze licenci nezbytnou pro provoz Služby.</p>

<h2>9. Přenositelnost údajů, změna poskytovatele a ukončení (Nařízení (EU) 2023/2854 – Data Act)</h2>
<ul>
<li>Nájemce může kdykoli exportovat své údaje nebo přejít k jinému poskytovateli, resp. do vlastní infrastruktury.</li>
<li>Výpovědní doba při změně poskytovatele činí nejvýše 2 měsíce od oznámení Nájemce.</li>
<li>Po uplynutí výpovědní doby začíná přechodné období 30 dnů, během něhož Provozovatel poskytuje přiměřenou součinnost. Není-li to technicky možné, Provozovatel to do 14 pracovních dnů oznámí a odůvodní.</li>
<li>Po ukončení smlouvy jsou údaje dostupné ke stažení ještě 30 dnů (retrieval window); poté se vymažou podle DPA.</li>
<li>Exportovatelné údaje (úplný seznam): rezervace, zákazníci, služby, kategorie, personál, nastavení účtu. Formát: CSV a JSON, přímo z dashboardu.</li>
<li>Za změnu poskytovatele, export ani přechod se neúčtuje žádný poplatek (0 €).</li>
<li>Infrastruktura IKT: údaje jsou uloženy na serveru Provozovatele ve Slovenské republice; provoz probíhá přes globální síť Cloudflare (CDN, WAF, DNS), která zpracovává síťový provoz i mimo EU.</li>
<li>Opatření proti protiprávnímu přístupu orgánů třetích zemí: uložení údajů v EU, šifrování přenosu (TLS 1.2+), smluvní záruky s Cloudflare (DPF a standardní smluvní doložky), posouzení každé žádosti orgánu třetí země a její odmítnutí, nemá-li základ v právu EU nebo členského státu, a informování Nájemce, pokud to zákon dovoluje.</li>
</ul>
{{-- [LAWYER] Ověřit soulad s čl. 25, 28 a 30 Data Act (zejména 14denní lhůta pro oznámení technické neuskutečnitelnosti a rozsah „functional equivalence“ u SaaS). --}}

<h2>10. Trvání a ukončení smlouvy</h2>
<p>Smlouva se uzavírá na dobu neurčitou. Nájemce ji může kdykoli ukončit tlačítkem „Zrušit předplatné“ v dashboardu nebo e-mailem; zrušení je účinné ke konci zaplaceného období a již zaplacené částky se nevracejí s výjimkou případů uvedených v <a href="{{ $links['refunds'] }}">Podmínkách vracení plateb</a>. Účet lze smazat kdykoli.</p>
<p>Provozovatel může účet nebo Rezervační stránku omezit, pozastavit nebo smlouvu vypovědět při podstatném porušení těchto Podmínek, Pravidel přijatelného používání nebo právních předpisů, anebo při neuhrazení platby. O každém omezení Nájemce informuje spolu s odůvodněním (statement of reasons) podle čl. 17 Nařízení (EU) 2022/2065 (DSA) a poučí jej o možnosti podat námitku.</p>

<h2>11. Nahlašování protiprávního obsahu (DSA)</h2>
<p>Kdokoli může nahlásit Rezervační stránku nebo obsah, který považuje za protiprávní, na e-mail {{ $operator['email'] }} s uvedením adresy stránky, důvodu a kontaktu. Provozovatel oznámení posoudí bez zbytečného odkladu, informuje oznamovatele o výsledku a dotčeného Nájemce o přijatém opatření s odůvodněním. Námitky proti rozhodnutí lze podat na téže adrese do 6 měsíců; Provozovatel je vyřídí bezplatně a nikoli výlučně automatizovanými prostředky. Kontaktním místem pro orgány a uživatele je e-mail {{ $operator['email'] }}.</p>

<h2>12. Odpovědnost</h2>
<p>Provozovatel odpovídá za škodu způsobenou Nájemci nejvýše do výše poplatků, které Nájemce zaplatil za Službu v posledních 12 měsících před vznikem škody. Provozovatel neodpovídá za ušlý zisk, ztrátu obchodních příležitostí ani nepřímou škodu, za nedostupnost způsobenou třetími stranami (PayPal, Cloudflare, poskytovatel připojení) nebo vyšší mocí, ani za služby poskytované Nájemcem Zákazníkům. Tato omezení neplatí při úmyslu, hrubé nedbalosti a v rozsahu, v jakém je kogentní právo vylučuje.</p>

<h2>13. Rozhodné právo a řešení sporů</h2>
<p>Smlouva se řídí právem Slovenské republiky; příslušné jsou soudy Slovenské republiky. Má-li Nájemce postavení spotřebitele nebo kvazi-spotřebitele podle kogentních předpisů státu svého obvyklého bydliště, zůstávají tyto předpisy nedotčeny. Spotřebitel se může obrátit na Slovenskou obchodní inspekci (Slovenská obchodná inšpekcia) nebo jiný subjekt alternativního řešení sporů podle slovenského zákona č. 391/2015 Z. z. Evropská platforma pro řešení sporů online (ODR) byla k 20. červenci 2025 ukončena a již není dostupná.</p>

<h2>14. Změny Podmínek</h2>
<p>Provozovatel může tyto Podmínky měnit. Změnu oznámí e-mailem nejméně 30 dnů před účinností. Pokud Nájemce se změnou nesouhlasí, může smlouvu do dne účinnosti bezplatně ukončit; pokračování v používání Služby po tomto dni se považuje za souhlas.</p>

<h2>15. Závěrečná ustanovení a kontakt</h2>
<p>Smlouva se uzavírá ve slovenském jazyce ve smyslu § 5 slovenského zákona č. 22/2004 Z. z.; české a anglické znění slouží pro informaci, při rozporu má přednost slovenské znění. Je-li některé ustanovení neplatné, ostatní zůstávají v platnosti. Kontakt: {{ $operator['name'] }}, {{ $operator['email'] }}.</p>
