@php
    $person = [
        '@type' => 'Person',
        '@id' => route('home').'#person',
        'name' => $portfolio['name'],
        'jobTitle' => $portfolio['role'],
        'url' => route('home'),
        'description' => $portfolio['lead'],
        'homeLocation' => ['@type' => 'Place', 'name' => $portfolio['location']],
        'knowsAbout' => collect($portfolio['skills'])->pluck('items')->flatten()->unique()->values()->all(),
    ];

    $socialUrls = collect($portfolio['socials'])
        ->whereNotIn('platform', ['whatsapp', 'telegram'])
        ->pluck('url')->filter()->unique()->values()->all();

    if ($socialUrls !== []) {
        $person['sameAs'] = $socialUrls;
    }

    if ($profile->photo_path) {
        $person['image'] = $profile->photoUrl();
    }

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfilePage',
        '@id' => route('home').'#profile',
        'url' => route('home'),
        'name' => $portfolio['meta']['title'],
        'inLanguage' => 'id-ID',
        'mainEntity' => $person,
    ];
@endphp
<script type="application/ld+json">{!! \Illuminate\Support\Js::encode($structuredData) !!}</script>
