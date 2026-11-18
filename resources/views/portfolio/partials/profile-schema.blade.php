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

    $worksFor = collect($portfolio['experiences'] ?? [])
        ->pluck('organization')
        ->filter()
        ->unique()
        ->map(fn (string $organization): array => ['@type' => 'Organization', 'name' => $organization])
        ->values()
        ->all();

    if ($worksFor !== []) {
        $person['worksFor'] = count($worksFor) === 1 ? $worksFor[0] : $worksFor;
    }

    $alumniOf = collect($portfolio['education'] ?? [])
        ->filter(fn (array $item): bool => filled($item['institution'] ?? null))
        ->map(function (array $item): array {
            $entry = ['@type' => 'EducationalOrganization', 'name' => $item['institution']];

            if (filled($item['url'] ?? null)) {
                $entry['sameAs'] = $item['url'];
            }

            return $entry;
        })
        ->values()
        ->all();

    if ($alumniOf !== []) {
        $person['alumniOf'] = $alumniOf;
    }

    $credentials = collect($portfolio['certifications'] ?? [])
        ->filter(fn (array $item): bool => filled($item['name'] ?? null))
        ->map(fn (array $item): array => [
            '@type' => 'EducationalOccupationalCredential',
            'name' => $item['name'],
            'credentialCategory' => 'certificate',
        ])
        ->values()
        ->all();

    if ($credentials !== []) {
        $person['hasCredential'] = $credentials;
    }

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
