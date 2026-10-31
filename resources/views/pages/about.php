<?php

/**
 * About page.
 * Expects: $flowerCount (int), $regionCount (int)
 *
 * IMAGES: drop real photos in public/assets/img/about/ using these names and they
 * replace the generated illustrations automatically: TODO
 *   hero.jpg    (main photo, landscape, e.g. the village or a field of flowers) 
 *   field.jpg   (second photo, portrait or square, e.g. the team in the field)
 *   team-1.jpg, team-2.jpg, team-3.jpg (square portraits)
 */

/** <img> if public/assets/img/about/<file> exists, otherwise a generated flower illustration. */
$aboutImg = function (string $file, array $colors, string $alt, int $size = 220): string {
  $path = BASE_PATH . '/public/assets/img/about/' . $file;
  if (is_file($path)) {
    return '<img src="' . e(asset('img/about/' . $file)) . '" alt="' . e($alt) . '" loading="lazy" decoding="async">';
  }
  return flower_art($colors, $size, 0);
};

$team = [
  ['name' => 'Team Member One',   'role' => 'Founder & Field Botanist',  'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.', 'colors' => ['#d2342b', '#f5d547']],
  ['name' => 'Team Member Two',   'role' => 'Photographer',              'bio' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.',   'colors' => ['#e8a1c4', '#ffffff']],
  ['name' => 'Team Member Three', 'role' => 'Web & Community',           'bio' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla.',        'colors' => ['#8f6bd1', '#f5d547']],
];

$steps = [
  ['01', 'We walk the land', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. We visit meadows, roadsides and forests across the villages.'],
  ['02', 'We identify and record', 'Sed do eiusmod tempor incididunt ut labore. Every flower gets its names, colors, season and where to find it.'],
  ['03', 'We share it with you', 'Ut enim ad minim veniam. Browse the catalog, follow us for photos, or send us a flower we missed.'],
];

$socials = [
  'instagram' => ['Instagram', '@yourpage', 'Daily photos from the field'],
  'facebook'  => ['Facebook',  'Your page',  'News, events and community'],
  'youtube'   => ['YouTube',   'Your channel', 'Walks and flower stories'],
];
$icons = [
  'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.2" cy="6.8" r="1.2" fill="currentColor"/></svg>',
  'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.3H8v3h2.5V21h3z"/></svg>',
  'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3L10 15z"/></svg>',
];
?>

<!-- HERO -->
<section class="about-hero">
  <div class="about-hero__text">
    <span class="eyebrow">About us</span>
    <h1>People who love the wild flowers of Mount Lebanon.</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. We are a small team of neighbours, nature lovers and photographers building a living catalog of the flowers that grow around our villages.</p>
    <div class="row">
      <a class="btn" href="<?= e(url('/flowers')) ?>">Browse the catalog</a>
      <a class="btn alt" href="#team">Meet the team</a>
    </div>
  </div>

  <div class="about-hero__media">
    <figure class="photo photo--main" style="<?= e(flower_bg(['colors' => ['#d2342b']])) ?>">
      <?= $aboutImg('hero.jpg', ['#d2342b', '#f5d547'], 'Wild flowers growing in the mountains', 260) ?>
    </figure>
    <figure class="photo photo--small" style="<?= e(flower_bg(['colors' => ['#8f6bd1']])) ?>">
      <?= $aboutImg('field.jpg', ['#8f6bd1', '#f5d547'], 'The team in the field', 140) ?>
    </figure>
  </div>
</section>

<!-- STATS -->
<section class="stats" aria-label="The catalog in numbers">
  <div><b><?= (int) ($flowerCount ?? 0) ?></b><span>flowers catalogued</span></div>
  <!--<div><b><?/*= /*(int) ($regionCount ?? 0)*/ ?></b><span>villages covered</span></div> TODO to remove later when multiple cities-->
  <div><b>100%</b><span>free to browse</span></div>
</section>

<!-- STORY -->
<section class="story">
  <div>
    <span class="eyebrow">Our story</span>
    <h2>A catalog born from long walks.</h2>
  </div>
  <div class="story__body">
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
  </div>
</section>

<!-- HOW WE WORK -->
<section>
  <span class="eyebrow">How it works</span>
  <h2>From the field to your screen</h2>
  <div class="steps">
    <?php foreach ($steps as [$num, $title, $text]): ?>
      <article class="step">
        <span class="step__num"><?= e($num) ?></span>
        <h3><?= e($title) ?></h3>
        <p><?= e($text) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- TEAM -->
<section id="team">
  <span class="eyebrow">The team</span>
  <h2>Meet the people behind the catalog</h2>
  <div class="team">
    <?php foreach ($team as $i => $m): ?>
      <article class="member">
        <div class="member__avatar" style="<?= e(flower_bg(['colors' => $m['colors']])) ?>">
          <?= $aboutImg('team-' . ($i + 1) . '.jpg', $m['colors'], $m['name'], 90) ?>
        </div>
        <h3><?= e($m['name']) ?></h3>
        <div class="member__role"><?= e($m['role']) ?></div>
        <p><?= e($m['bio']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- SOCIAL -->
<section class="follow">
  <div class="follow__head">
    <span class="eyebrow">Stay in touch</span>
    <h2>Follow the blooms</h2>
    <p>New photos and discoveries are posted on our pages first.</p>
  </div>
  <div class="social-cards">
    <?php foreach ($socials as $key => [$label, $handle, $tagline]): ?>
      <a class="social social--<?= e($key) ?>" href="<?= e(config('social.' . $key)) ?>" target="_blank" rel="noopener">
        <span class="social__icon"><?= $icons[$key] ?></span>
        <span class="social__text">
          <b><?= e($label) ?></b>
          <small><?= e($tagline) ?></small>
        </span>
        <span class="social__arrow" aria-hidden="true">↗</span>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- CTA -->
<section class="about-cta">
  <h2>Found a flower we don't have?</h2>
  <p>Send us a photo and the exact place on WhatsApp, and we will add it to the catalog.</p>
  <a class="btn wa" href="<?= e(url('/missing')) ?>">Tell us about it</a>
</section>