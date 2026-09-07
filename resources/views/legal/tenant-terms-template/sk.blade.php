<h1>Podmienky online rezervácie</h1>
<p class="legal-meta">Účinné od {{ $effective }}</p>

<h2>1. Poskytovateľ služby</h2>
<p>Termíny na tejto stránke poskytuje <strong>{{ $tenant->name }}</strong> (ďalej „poskytovateľ“).
    @if($tenant->address)<br>Adresa: {{ $tenant->address }}@endif
    @foreach($locations as $location)<br>Prevádzka {{ $location->name }}{{ $location->address ? ': '.$location->address : '' }}@endforeach
    @if($tenant->email)<br>E-mail: <a href="mailto:{{ $tenant->email }}">{{ $tenant->email }}</a>@endif
    @if($tenant->phone)<br>Telefón: <a href="tel:{{ preg_replace('/\s+/', '', $tenant->phone) }}">{{ $tenant->phone }}</a>@endif
</p>
<p>Rezervačnú stránku technicky prevádzkuje platforma rezervuj-ma.online ako sprostredkovateľ poskytovateľa. Platforma nie je zmluvnou stranou rezervácie ani poskytovanej služby.</p>

<h2>2. Ako rezervácia vzniká</h2>
<p>Termín si vyberiete v online formulári na tejto stránke. Rezerváciu je možné vytvoriť najskôr {{ $advanceHours }} hodín pred jej začiatkom.
    @if($requireConfirmation)
        Odoslaním formulára vzniká žiadosť o termín, ktorú poskytovateľ potvrdí e-mailom. Záväznou sa rezervácia stáva až po potvrdení.
    @else
        Po odoslaní vám príde potvrdenie e-mailom a rezervácia je záväzná pre obe strany.
    @endif
    Vybraný čas je počas vypĺňania údajov na niekoľko minút podržaný, aby ho medzitým nezabral niekto iný.</p>

<h2>3. Ceny a platba</h2>
<p>Ceny uvedené pri službách sú konečné ceny za jedno poskytnutie služby, ak pri službe nie je uvedené inak. Ak je cena označená ako orientačná, presnú výšku určí poskytovateľ po konzultácii podľa rozsahu služby. Platí sa poskytovateľovi na mieste po poskytnutí služby; rezervačná stránka nevyžaduje ani neprijíma online platbu.</p>

<h2>4. Zmena a zrušenie termínu</h2>
<p>Termín môžete zrušiť odkazom v potvrdzujúcom e-maile
    @if($cancelHours > 0)
        najneskôr {{ $cancelHours }} hodín pred jeho začiatkom. Neskôr prosím kontaktujte poskytovateľa telefonicky alebo e-mailom.
    @else
        kedykoľvek pred jeho začiatkom.
    @endif
    Zmenu termínu vybaví poskytovateľ telefonicky alebo e-mailom. Poskytovateľ môže termín zrušiť alebo presunúť z vážnych prevádzkových dôvodov; v takom prípade vás bezodkladne informuje a ponúkne náhradný termín. Opakované nedostavenie sa na rezervovaný termín bez zrušenia oprávňuje poskytovateľa ďalšie online rezervácie odmietnuť.</p>

<h2>5. Poskytnutie služby</h2>
<p>Poskytovateľ môže pred poskytnutím služby požiadať o informácie potrebné na jej bezpečné vykonanie (napríklad o alergiách alebo kontraindikáciách) a službu odmietnuť alebo navrhnúť alternatívu, ak by jej vykonanie nebolo vhodné. Rezervácia termínu nie je prísľubom vykonania konkrétneho úkonu, ak jeho vhodnosť závisí od posúdenia na mieste.</p>

<h2>6. Osobné údaje</h2>
<p>Údaje z rezervácie spracúva poskytovateľ ako prevádzkovateľ podľa <a href="{{ $privacyUrl }}">informácií o spracúvaní osobných údajov</a>. Používa ich na vybavenie termínu, jeho potvrdenie a pripomienku.</p>

<h2>7. Reklamácie a riešenie sporov</h2>
<p>Reklamácie a podnety rieši poskytovateľ prednostne osobne alebo e-mailom na kontaktoch uvedených v bode 1. Ak ste spotrebiteľ, máte právo obrátiť sa na {{ $adrBody }}. Rezervácia sa riadi právom krajiny, v ktorej má poskytovateľ sídlo; kogentné ustanovenia na ochranu spotrebiteľa v krajine vášho bydliska tým nie sú dotknuté.</p>
