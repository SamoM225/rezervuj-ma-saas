<h1>Podmínky online rezervace</h1>
<p class="legal-meta">Účinné od {{ $effective }}</p>

<h2>1. Poskytovatel služby</h2>
<p>Termíny na této stránce poskytuje <strong>{{ $tenant->name }}</strong> (dále „poskytovatel“).
    @if($tenant->address)<br>Adresa: {{ $tenant->address }}@endif
    @foreach($locations as $location)<br>Provozovna {{ $location->name }}{{ $location->address ? ': '.$location->address : '' }}@endforeach
    @if($tenant->email)<br>E-mail: <a href="mailto:{{ $tenant->email }}">{{ $tenant->email }}</a>@endif
    @if($tenant->phone)<br>Telefon: <a href="tel:{{ preg_replace('/\s+/', '', $tenant->phone) }}">{{ $tenant->phone }}</a>@endif
</p>
<p>Rezervační stránku technicky provozuje platforma rezervuj-ma.online jako zpracovatel poskytovatele. Platforma není smluvní stranou rezervace ani poskytované služby.</p>

<h2>2. Jak rezervace vzniká</h2>
<p>Termín si vyberete v online formuláři na této stránce. Rezervaci lze vytvořit nejdříve {{ $advanceHours }} hodin před jejím začátkem.
    @if($requireConfirmation)
        Odesláním formuláře vzniká žádost o termín, kterou poskytovatel potvrdí e-mailem. Závaznou se rezervace stává až po potvrzení.
    @else
        Po odeslání vám přijde potvrzení e-mailem a rezervace je závazná pro obě strany.
    @endif
    Vybraný čas je během vyplňování údajů na několik minut podržen, aby ho mezitím nezabral někdo jiný.</p>

<h2>3. Ceny a platba</h2>
<p>Ceny uvedené u služeb jsou konečné ceny za jedno poskytnutí služby, není-li u služby uvedeno jinak. Je-li cena označena jako orientační, přesnou výši určí poskytovatel po konzultaci podle rozsahu služby. Platí se poskytovateli na místě po poskytnutí služby; rezervační stránka nevyžaduje ani nepřijímá online platbu.</p>

<h2>4. Změna a zrušení termínu</h2>
<p>Termín můžete zrušit odkazem v potvrzovacím e-mailu
    @if($cancelHours > 0)
        nejpozději {{ $cancelHours }} hodin před jeho začátkem. Později prosím kontaktujte poskytovatele telefonicky nebo e-mailem.
    @else
        kdykoli před jeho začátkem.
    @endif
    Změnu termínu vyřídí poskytovatel telefonicky nebo e-mailem. Poskytovatel může termín zrušit nebo přesunout z vážných provozních důvodů; v takovém případě vás neprodleně informuje a nabídne náhradní termín. Opakované nedostavení se na rezervovaný termín bez zrušení opravňuje poskytovatele další online rezervace odmítnout.</p>

<h2>5. Poskytnutí služby</h2>
<p>Poskytovatel může před poskytnutím služby požádat o informace potřebné k jejímu bezpečnému provedení (například o alergiích nebo kontraindikacích) a službu odmítnout nebo navrhnout alternativu, pokud by její provedení nebylo vhodné. Rezervace termínu není příslibem provedení konkrétního úkonu, závisí-li jeho vhodnost na posouzení na místě.</p>

<h2>6. Osobní údaje</h2>
<p>Údaje z rezervace zpracovává poskytovatel jako správce podle <a href="{{ $privacyUrl }}">informací o zpracování osobních údajů</a>. Používá je k vyřízení termínu, jeho potvrzení a připomínce.</p>

<h2>7. Reklamace a řešení sporů</h2>
<p>Reklamace a podněty řeší poskytovatel přednostně osobně nebo e-mailem na kontaktech uvedených v bodě 1. Jste-li spotřebitel, máte právo obrátit se na {{ $adrBody }}. Rezervace se řídí právem země, ve které má poskytovatel sídlo; kogentní ustanovení na ochranu spotřebitele v zemi vašeho bydliště tím nejsou dotčena.</p>
