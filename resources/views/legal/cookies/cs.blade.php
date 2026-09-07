<h1>Zásady používání cookies</h1>
<p class="legal-meta">Verze {{ $version }} · Účinnost od {{ $effective }}</p>

<p>Tyto zásady vysvětlují, jaké soubory cookie a podobné technologie používá platforma {{ $siteUrl }} (dále jen „Platforma“), kterou provozuje {{ $operator['name'] }} (dále jen „Provozovatel“), a proč při jejím používání nezobrazujeme lištu se žádostí o souhlas. Informace o zpracování osobních údajů najdete v <a href="{{ $links['privacy'] }}">Zásadách ochrany osobních údajů</a>.</p>

<h2>1. Používáme pouze nezbytné cookies</h2>
<p>Platforma používá výhradně cookies, které jsou nezbytně nutné k poskytnutí služby, kterou jste si výslovně vyžádali – přihlášení do účtu, ochranu formulářů, zapamatování jazyka a bezpečnostní ochranu před automatizovanými útoky. Nepoužíváme žádné analytické, reklamní ani marketingové cookies a nesledujeme vaše chování na jiných webových stránkách.</p>
<p>Podle § 109 odst. 8 slovenského zákona č. 452/2021 Z. z. o elektronických komunikacích se souhlas nevyžaduje při ukládání nebo získávání přístupu k informacím, které jsou nezbytně nutné k poskytnutí služby informační společnosti výslovně vyžádané uživatelem. Stejná výjimka platí i v dalších zemích, ve kterých působí naši zákazníci: v České republice § 89 odst. 3 zákona č. 127/2005 Sb., v Polsku čl. 399 zákona Prawo komunikacji elektronicznej, v Německu § 25 TDDDG a v Rakousku § 165 odst. 3 TKG 2021. Z tohoto důvodu Platforma nezobrazuje lištu se žádostí o souhlas s cookies; zobrazuje pouze krátké informační upozornění.</p>
{{-- [LAWYER] Ověřit, že všechny uvedené cookies (zejména cookie „locale“ s platností 1 rok) spadají pod výjimku „nezbytně nutné“ podle výkladu ÚREKPS a stanoviska EDPB. --}}

<h2>2. Přehled používaných cookies</h2>
<table>
  <thead>
    <tr>
      <th>Název</th>
      <th>Účel</th>
      <th>Platnost</th>
      <th>Poskytovatel</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>laravel_session</td>
      <td>Identifikace relace přihlášeného uživatele a udržení stavu formulářů.</td>
      <td>Relace (session)</td>
      <td>Platforma (první strana)</td>
    </tr>
    <tr>
      <td>XSRF-TOKEN</td>
      <td>Ochrana před útoky typu cross-site request forgery při odesílání formulářů.</td>
      <td>Relace (session)</td>
      <td>Platforma (první strana)</td>
    </tr>
    <tr>
      <td>locale</td>
      <td>Zapamatování zvoleného jazyka rozhraní.</td>
      <td>1 rok</td>
      <td>Platforma (první strana)</td>
    </tr>
    <tr>
      <td>otp_trust</td>
      <td>Označení důvěryhodného zařízení po úspěšném dvoufaktorovém ověření, aby se kód nevyžadoval při každém přihlášení.</td>
      <td>30 dní</td>
      <td>Platforma (první strana)</td>
    </tr>
    <tr>
      <td>__cf_bm</td>
      <td>Rozpoznávání automatizovaného provozu (botů) a ochrana Platformy před zneužitím.</td>
      <td>30 minut</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>cf_clearance</td>
      <td>Uložení výsledku bezpečnostní výzvy, aby ji návštěvník nemusel opakovat.</td>
      <td>Až 1 rok (podle nastavení Cloudflare)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>__cfruid</td>
      <td>Vyrovnávání zátěže a bezpečnostní směrování požadavků.</td>
      <td>Relace (session)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
    <tr>
      <td>_cfuvid</td>
      <td>Omezení počtu požadavků (rate limiting) bez identifikace konkrétní osoby.</td>
      <td>Relace (session)</td>
      <td>Cloudflare, Inc.</td>
    </tr>
  </tbody>
</table>
{{-- [LAWYER][TECH] Ověřit skutečnou platnost cookie „locale“ a „otp_trust“ v konfiguraci aplikace a aktuální platnosti cookies Cloudflare podle jejich dokumentace. --}}
<p>Cookies označené jako „relace“ se vymažou po zavření prohlížeče nebo po uplynutí doby nečinnosti nastavené Platformou. Cookies společnosti Cloudflare se nastavují proto, že Platforma je provozována za sítí Cloudflare, která zajišťuje ochranu před útoky a filtrování škodlivého provozu. Cloudflare tyto cookies nepoužívá ke sledování napříč webovými stránkami.</p>

<h2>3. Lokální úložiště prohlížeče</h2>
<p>Kromě cookies ukládá Platforma do lokálního úložiště prohlížeče (localStorage) jedinou položku, která si pamatuje, že jste zavřeli informační upozornění o cookies. Tato položka není cookie, neodesílá se na server a neobsahuje žádné osobní údaje.</p>

<h2>4. Vložený obsah třetích stran na rezervačních stránkách</h2>
<p>Samotná Platforma nevkládá obsah třetích stran. Pokud si však zákazník Platformy (provozovatel rezervační stránky, dále jen „Tenant“) na své veřejné rezervační stránce zapne vložený obsah třetích stran – například mapu, video nebo externí widget – mohou tyto služby ukládat vlastní cookies nebo podobné identifikátory. Za takový obsah a za získání případného souhlasu návštěvníků odpovídá Tenant, který o něm musí informovat ve svých vlastních zásadách. Cookies Platformy se tím nemění a zůstávají nezbytně nutné.</p>

<h2>5. Jak spravovat cookies</h2>
<p>Cookies můžete kdykoli vymazat nebo zablokovat v nastavení svého prohlížeče (například v sekci „Soukromí a zabezpečení“). Upozorňujeme, že při zablokování nezbytných cookies nebude možné přihlásit se do účtu, odeslat rezervační formulář ani projít bezpečnostní kontrolou Cloudflare. Vymazání cookie <code>otp_trust</code> způsobí, že při dalším přihlášení bude opět vyžádán dvoufaktorový kód.</p>

<h2>6. Změny těchto zásad</h2>
<p>Pokud bychom v budoucnu zavedli cookies, které nejsou nezbytně nutné, tyto zásady aktualizujeme a před jejich použitím si vyžádáme váš souhlas v souladu s platnými předpisy. Aktuální verze je vždy dostupná na {{ $siteUrl }}.</p>

<h2>7. Kontakt</h2>
<p>Provozovatel: {{ $operator['name'] }}, e-mail: {{ $operator['email'] }}. Dotazy týkající se cookies a ochrany osobních údajů nám můžete zaslat na uvedenou adresu.</p>
