<h1>Všeobecné obchodné podmienky služby rezervuj-ma.online</h1>
<p class="legal-meta">Verzia {{ $version }} · Účinnosť od {{ $effective }}</p>

<h2>1. Úvodné ustanovenia a definície</h2>
<p>Tieto všeobecné obchodné podmienky (ďalej „Podmienky“) upravujú používanie online rezervačnej platformy dostupnej na adrese {{ $siteUrl }} (ďalej „Služba“). Prevádzkovateľ: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }} (ďalej „Prevádzkovateľ“).</p>
{{-- [LAWYER] Prevádzkovateľ je zatiaľ fyzická osoba bez živnostenského oprávnenia. Overiť, či rozsah a odplatnosť Služby nezakladá povinnosť registrácie podnikania podľa slovenského práva a či je formulácia „Prevádzkovateľ“ postačujúca. --}}
<ul>
<li><strong>Nájomca</strong> (angl. tenant) je podnikateľ alebo iná osoba, ktorá si vytvorí účet a používa Službu na správu rezervácií svojich klientov.</li>
<li><strong>Zákazník</strong> je koncová osoba, ktorá si na verejnej rezervačnej stránke Nájomcu ({{ $siteUrl }}/{slug}/booking) rezervuje termín.</li>
<li><strong>Rezervačná stránka</strong> je verejná stránka Nájomcu vytvorená v rámci Služby.</li>
<li><strong>Plán</strong> je rozsah funkcií a limitov Služby (Free alebo Pro).</li>
</ul>
<p>Neoddeliteľnou súčasťou zmluvy medzi Prevádzkovateľom a Nájomcom sú <a href="{{ $links['dpa'] }}">Zmluva o spracúvaní osobných údajov</a> (DPA), <a href="{{ $links['aup'] }}">Pravidlá prijateľného používania</a>, <a href="{{ $links['refunds'] }}">Podmienky vrátenia platieb</a> a <a href="{{ $links['privacy'] }}">Zásady ochrany osobných údajov</a>.</p>

<h2>2. Kto môže Službu používať</h2>
<p>Službu môžu používať len osoby staršie ako 18 rokov. Nájomca vyhlasuje, že Službu používa v rámci svojej podnikateľskej alebo inej profesijnej činnosti (vzťah B2B). Ustanovenia zákona č. 108/2024 Z. z. o ochrane spotrebiteľa sa na Nájomcu vzťahujú len v rozsahu, v akom má postavenie spotrebiteľa podľa kogentných predpisov svojho štátu.</p>
<p>Ak je Nájomca fyzickou osobou podnikajúcou podľa poľského práva a zmluva nemá pre neho profesijný charakter (čl. 38a poľského zákona o právach spotrebiteľa), má právo odstúpiť od zmluvy do 14 dní od uzavretia bez uvedenia dôvodu. Podrobnosti a vzorový formulár sú uvedené v <a href="{{ $links['refunds'] }}">Podmienkách vrátenia platieb</a>. Nad rámec zákona Prevádzkovateľ poskytuje všetkým Nájomcom obchodnú záruku vrátenia prvej platby do 14 dní.</p>
{{-- [LAWYER] Overiť aktuálne znenie čl. 38a poľského ustawa o prawach konsumenta a rozsah kvázi-spotrebiteľskej ochrany pri digitálnych službách. --}}

<h2>3. Účet a bezpečnosť</h2>
<p>Registráciou vzniká Nájomcovi účet. Nájomca je povinný uvádzať pravdivé údaje a udržiavať ich aktuálne. Nájomca zodpovedá za dôvernosť prihlasovacích údajov a za všetku činnosť vykonanú prostredníctvom svojho účtu. Služba umožňuje zapnúť dvojfaktorové overenie (2FA); Prevádzkovateľ ho odporúča. Podozrenie na zneužitie účtu je Nájomca povinný bezodkladne oznámiť na {{ $operator['email'] }}.</p>

<h2>4. Plány, ceny a platby</h2>
<ul>
<li><strong>Free</strong>: bezplatný plán s limitom 50 rezervácií za kalendárny mesiac.</li>
<li><strong>Pro</strong>: 5 EUR alebo 5 USD mesačne, prípadne 50 EUR alebo 50 USD ročne, podľa zvolenej meny.</li>
</ul>
<p>Platby sa uskutočňujú výlučne prostredníctvom služby PayPal. Predplatné sa automaticky obnovuje na ďalšie obdobie, kým ho Nájomca nezruší. Prevádzkovateľ neúčtuje žiadnu províziu z rezervácií ani z platieb medzi Nájomcom a Zákazníkom; Služba nie je platobným sprostredkovateľom. Zmenu cien oznámi Prevádzkovateľ e-mailom najmenej 30 dní vopred; zmena sa uplatní až na nasledujúce obdobie a Nájomca môže predplatné pred jej účinnosťou zrušiť.</p>
<p>Ceny sú uvedené bez daní. Nájomca zodpovedá za dane a poplatky, ktoré sa na neho vzťahujú v jeho štáte.</p>
{{-- [LAWYER] Overiť DPH režim Prevádzkovateľa (fyzická osoba, prah registrácie, OSS pri službách do iných členských štátov EÚ) a formuláciu o daniach. --}}

<h2>5. Povinnosti Nájomcu a obsah</h2>
<p>Nájomca zodpovedá za celý obsah svojej Rezervačnej stránky (názov, popisy služieb, ceny, obrázky) a za jeho súlad s právom. Nájomca sa zaväzuje používať Službu v súlade s <a href="{{ $links['aup'] }}">Pravidlami prijateľného používania</a> a nezasahovať do bezpečnosti ani prevádzky Služby.</p>
<p>Vo vzťahu k osobným údajom Zákazníkov je Nájomca prevádzkovateľom (controller) a Prevádzkovateľ sprostredkovateľom (processor) v zmysle čl. 28 GDPR. <a href="{{ $links['dpa'] }}">Zmluva o spracúvaní osobných údajov</a> je súčasťou zmluvy. Nájomca je povinný Zákazníkom poskytnúť vlastnú informáciu o spracúvaní osobných údajov; Služba na tento účel ponúka šablónu, za jej obsah však zodpovedá Nájomca.</p>

<h2>6. Vzťah k Zákazníkom</h2>
<p>Zmluva o poskytnutí služby (napr. strihu, ošetrenia, lekcie) vzniká výlučne medzi Nájomcom a Zákazníkom. Prevádzkovateľ nie je jej zmluvnou stranou, negarantuje poskytnutie rezervovanej služby ani jej kvalitu a nevybavuje reklamácie Zákazníkov voči Nájomcovi. Prevádzkovateľ spracúva údaje Zákazníkov len podľa pokynov Nájomcu, s výnimkou údajov potrebných na bezpečnosť platformy, pri ktorých vystupuje ako samostatný prevádzkovateľ (pozri <a href="{{ $links['privacy'] }}">Zásady ochrany osobných údajov</a>).</p>

<h2>7. Dostupnosť a zmeny Služby</h2>
<p>Prevádzkovateľ poskytuje Službu s odbornou starostlivosťou, avšak bez záruky nepretržitej dostupnosti (bez SLA). Plánovanú údržbu oznámi, ak je to možné, vopred. Prevádzkovateľ môže funkcie Služby rozvíjať alebo meniť; podstatné obmedzenie funkcií platených plánov oznámi najmenej 30 dní vopred.</p>

<h2>8. Duševné vlastníctvo</h2>
<p>Softvér, dizajn a značka Služby zostávajú majetkom Prevádzkovateľa. Nájomca získava nevýhradnú, neprenosnú licenciu na používanie Služby počas trvania zmluvy. Obsah a údaje vložené Nájomcom zostávajú jeho majetkom; Nájomca udeľuje Prevádzkovateľovi len licenciu potrebnú na prevádzku Služby.</p>

<h2>9. Prenositeľnosť údajov, zmena poskytovateľa a ukončenie (Nariadenie (EÚ) 2023/2854 – Data Act)</h2>
<ul>
<li>Nájomca môže kedykoľvek exportovať svoje údaje alebo prejsť k inému poskytovateľovi, resp. do vlastnej infraštruktúry.</li>
<li>Výpovedná lehota pri zmene poskytovateľa je najviac 2 mesiace od oznámenia Nájomcu.</li>
<li>Po uplynutí výpovednej lehoty začína prechodné obdobie 30 dní, počas ktorého Prevádzkovateľ poskytuje primeranú súčinnosť. Ak to nie je technicky možné, Prevádzkovateľ to do 14 pracovných dní oznámi a odôvodní.</li>
<li>Po ukončení zmluvy sú údaje dostupné na stiahnutie ešte 30 dní (retrieval window); potom sa vymažú podľa DPA.</li>
<li>Exportovateľné údaje (úplný zoznam): rezervácie, zákazníci, služby, kategórie, personál, nastavenia účtu. Formát: CSV a JSON, priamo z dashboardu.</li>
<li>Za zmenu poskytovateľa, export ani prechod sa neúčtuje žiadny poplatok (0 €).</li>
<li>Infraštruktúra IKT: údaje sú uložené na serveri Prevádzkovateľa v Slovenskej republike; prevádzka prebieha cez globálnu sieť Cloudflare (CDN, WAF, DNS), ktorá spracúva sieťovú prevádzku aj mimo EÚ.</li>
<li>Opatrenia proti protiprávnemu prístupu orgánov tretích krajín: uloženie údajov v EÚ, šifrovanie prenosu (TLS 1.2+), zmluvné záruky s Cloudflare (DPF a štandardné zmluvné doložky), posúdenie každej žiadosti orgánu tretej krajiny a jej odmietnutie, ak nemá základ v práve EÚ alebo členského štátu, a informovanie Nájomcu, ak to zákon dovoľuje.</li>
</ul>
{{-- [LAWYER] Overiť súlad s čl. 25, 28 a 30 Data Act (najmä 14-dňová lehota na oznámenie technickej neuskutočniteľnosti a rozsah „functional equivalence“ pri SaaS). --}}

<h2>10. Trvanie a ukončenie zmluvy</h2>
<p>Zmluva sa uzatvára na neurčitý čas. Nájomca ju môže kedykoľvek ukončiť tlačidlom „Zrušiť predplatné“ v dashboarde alebo e-mailom; zrušenie je účinné ku koncu zaplateného obdobia a už zaplatené sumy sa nevracajú s výnimkou prípadov uvedených v <a href="{{ $links['refunds'] }}">Podmienkach vrátenia platieb</a>. Účet je možné vymazať kedykoľvek.</p>
<p>Prevádzkovateľ môže účet alebo Rezervačnú stránku obmedziť, pozastaviť alebo zmluvu vypovedať pri podstatnom porušení týchto Podmienok, Pravidiel prijateľného používania alebo právnych predpisov, alebo pri neuhradení platby. O každom obmedzení Nájomcu informuje spolu s odôvodnením (statement of reasons) podľa čl. 17 Nariadenia (EÚ) 2022/2065 (DSA) a poučí ho o možnosti podať námietku.</p>

<h2>11. Nahlasovanie protiprávneho obsahu (DSA)</h2>
<p>Ktokoľvek môže nahlásiť Rezervačnú stránku alebo obsah, ktorý považuje za protiprávny, na e-mail {{ $operator['email'] }} s uvedením adresy stránky, dôvodu a kontaktu. Prevádzkovateľ oznámenie posúdi bez zbytočného odkladu, informuje oznamovateľa o výsledku a dotknutého Nájomcu o prijatom opatrení s odôvodnením. Námietky proti rozhodnutiu možno podať na tej istej adrese do 6 mesiacov; Prevádzkovateľ ich vybaví bezplatne a nie výlučne automatizovanými prostriedkami. Kontaktným miestom pre orgány a používateľov je e-mail {{ $operator['email'] }}.</p>

<h2>12. Zodpovednosť</h2>
<p>Prevádzkovateľ zodpovedá za škodu spôsobenú Nájomcovi najviac do výšky poplatkov, ktoré Nájomca zaplatil za Službu v posledných 12 mesiacoch pred vznikom škody. Prevádzkovateľ nezodpovedá za ušlý zisk, stratu obchodných príležitostí ani nepriamu škodu, za nedostupnosť spôsobenú tretími stranami (PayPal, Cloudflare, poskytovateľ pripojenia) alebo vyššou mocou, ani za služby poskytované Nájomcom Zákazníkom. Tieto obmedzenia neplatia pri úmysle, hrubej nedbanlivosti a v rozsahu, v akom ich kogentné právo vylučuje.</p>

<h2>13. Rozhodné právo a riešenie sporov</h2>
<p>Zmluva sa riadi právom Slovenskej republiky; príslušné sú súdy Slovenskej republiky. Ak má Nájomca postavenie spotrebiteľa alebo kvázi-spotrebiteľa podľa kogentných predpisov štátu svojho obvyklého pobytu, tieto predpisy zostávajú nedotknuté. Spotrebiteľ sa môže obrátiť na Slovenskú obchodnú inšpekciu alebo iný subjekt alternatívneho riešenia sporov podľa zákona č. 391/2015 Z. z. Európska platforma riešenia sporov online (ODR) bola k 20. júlu 2025 ukončená a už nie je dostupná.</p>

<h2>14. Zmeny Podmienok</h2>
<p>Prevádzkovateľ môže tieto Podmienky meniť. Zmenu oznámi e-mailom najmenej 30 dní pred účinnosťou. Ak Nájomca so zmenou nesúhlasí, môže zmluvu do dňa účinnosti bezplatne ukončiť; pokračovanie v používaní Služby po tomto dni sa považuje za súhlas.</p>

<h2>15. Záverečné ustanovenia a kontakt</h2>
<p>Zmluva sa uzatvára v slovenskom jazyku v zmysle § 5 zákona č. 22/2004 Z. z.; české a anglické znenie slúži na informáciu, pri rozpore má prednosť slovenské znenie. Ak je niektoré ustanovenie neplatné, ostatné zostávajú v platnosti. Kontakt: {{ $operator['name'] }}, {{ $operator['email'] }}.</p>
