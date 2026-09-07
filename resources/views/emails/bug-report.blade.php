<p><strong>Nové nahlásenie chyby</strong></p>

<ul>
    <li><strong>Od:</strong> {{ $data['name'] }} ({{ $data['email'] }})</li>
    <li><strong>Predmet:</strong> {{ $data['summary'] }}</li>
    <li><strong>Dopad:</strong> {{ $data['impact'] ?? 'neuvedené' }}</li>
    <li><strong>Prehliadač / zariadenie:</strong> {{ $data['browser'] ?? 'nezistené' }}</li>
    <li><strong>Nahlásené:</strong> {{ $data['reported_at'] }}</li>
</ul>

<p><strong>Popis problému:</strong></p>
<p>{!! nl2br(e($data['description'])) !!}</p>

@if(!empty($data['steps']))
    <p><strong>Postup reprodukcie:</strong></p>
    <p>{!! nl2br(e($data['steps'])) !!}</p>
@endif

<p>Správa bola odoslaná z verejného formulára. Prílohy nájdete v emailovej správe.</p>
