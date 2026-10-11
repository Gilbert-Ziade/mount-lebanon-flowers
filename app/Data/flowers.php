<?php
// MOCK DATA. Later, replace Flower::all() with a database query; the array shape stays the same.
// images: file names inside public/assets/img/flowers/ (or full URLs). Empty = generated placeholders.
return [
    [
        'id' => 1, 'slug' => 'cyclamen', 'name' => 'Cyclamen', 'scientific_name' => 'Cyclamen persicum',
        'regions' => ['Baskinta', 'Chahtoul'], 'colors' => ['#e8a1c4', '#ffffff'], 'edible' => false,
        'description' => 'A low woodland flower with swept-back petals and marbled leaves, found in rocky shade between rocks and pines.',
        'benefits' => 'Valued as an ornamental. Traditionally used in small amounts in folk medicine, but the tuber is toxic.',
        'season' => 'Winter – Early spring', 'location' => 'Shaded rocky slopes near Baskinta village',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/', 'facebook' => 'https://facebook.com/'],
    ],
    [
        'id' => 2, 'slug' => 'red-poppy', 'name' => 'Red poppy', 'scientific_name' => 'Papaver rhoeas',
        'regions' => ['Rechmaya', 'Faraya'], 'colors' => ['#d2342b'], 'edible' => true,
        'description' => 'Bright red petals with a dark centre, covering fields and roadsides in spring.',
        'benefits' => 'Petals are used in teas and syrups for their mild calming effect. Seeds are edible; the rest of the plant is not.',
        'season' => 'April – June', 'location' => 'Open terraces and field edges',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/', 'youtube' => 'https://youtube.com/'],
    ],
    [
        'id' => 3, 'slug' => 'wild-thyme', 'name' => "Wild thyme (Za'atar)", 'scientific_name' => 'Origanum syriacum',
        'regions' => ['Chahtoul', 'Faraya'], 'colors' => ['#ffffff', '#d9c6ee'], 'edible' => true,
        'description' => "An aromatic shrub with small white flowers, the base of the Lebanese za'atar mix.",
        'benefits' => 'Rich in aromatic oils. Used fresh or dried in cooking and as an infusion for digestion.',
        'season' => 'May – August', 'location' => 'Dry, sunny hillsides',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/', 'facebook' => 'https://facebook.com/'],
    ],
    [
        'id' => 4, 'slug' => 'crown-anemone', 'name' => 'Crown anemone', 'scientific_name' => 'Anemone coronaria',
        'regions' => ['Baskinta'], 'colors' => ['#b3122f', '#6a3d9a', '#ffffff'], 'edible' => false,
        'description' => 'Large poppy-like blooms in red, purple or white, with a dark centre ring.',
        'benefits' => 'Mainly ornamental and a sign of the end of winter. The sap can irritate skin, so do not eat or handle often.',
        'season' => 'February – April', 'location' => 'Meadows below the pine forest',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/'],
    ],
    [
        'id' => 5, 'slug' => 'chamomile', 'name' => 'Chamomile', 'scientific_name' => 'Matricaria chamomilla',
        'regions' => ['Chahtoul', 'Faraya', 'Baskinta'], 'colors' => ['#ffffff', '#f5d547'], 'edible' => true,
        'description' => 'Small daisy-like flowers with a yellow cone and a sweet apple scent.',
        'benefits' => 'Flowers are dried for a soothing tea often used for sleep and upset stomach.',
        'season' => 'May – July', 'location' => 'Field margins and paths',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/', 'facebook' => 'https://facebook.com/'],
    ],
    [
        'id' => 6, 'slug' => 'cornflower', 'name' => 'Cornflower', 'scientific_name' => 'Centaurea cyanus',
        'regions' => ['Faraya'], 'colors' => ['#3e6ac4'], 'edible' => true,
        'description' => 'Fringed, intense blue flowers on slender stems.',
        'benefits' => 'The petals are edible and used as a colourful garnish in salads and teas.',
        'season' => 'May – July', 'location' => 'Edges of wheat and barley plots',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/'],
    ],
    [
        'id' => 7, 'slug' => 'borage', 'name' => 'Borage', 'scientific_name' => 'Borago officinalis',
        'regions' => ['Chahtoul'], 'colors' => ['#4d7ee0', '#c97bd6'], 'edible' => true,
        'description' => 'Star-shaped blue flowers on hairy stems that bees love.',
        'benefits' => 'Flowers and young leaves are edible with a light cucumber taste. Use in moderation.',
        'season' => 'April – June', 'location' => 'Gardens and disturbed ground',
        'images' => [], 'links' => ['facebook' => 'https://facebook.com/', 'youtube' => 'https://youtube.com/'],
    ],
    [
        'id' => 8, 'slug' => 'yellow-broom', 'name' => 'Yellow broom', 'scientific_name' => 'Spartium junceum',
        'regions' => ['Rechmaya', 'Faraya'], 'colors' => ['#f5c518'], 'edible' => false,
        'description' => 'A tall shrub with bright, fragrant yellow flowers and thin green stems.',
        'benefits' => 'Used for hedges and erosion control and as a source of nectar. Seeds and pods are toxic.',
        'season' => 'May – July', 'location' => 'Roadside slopes',
        'images' => [], 'links' => ['instagram' => 'https://instagram.com/'],
    ],
];
