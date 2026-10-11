<?php
$active = $active ?? '';
$links = [
    'home'    => ['/',        'Home'],
    'catalog' => ['/flowers', 'All flowers'],
    'about'   => ['/about',   'About'],
    'missing' => ['/missing', "Can't find a flower?"],
];
?>
<header>
  <div class="bar">
    <a class="logo" href="<?= e(url('/')) ?>">🌸 <?= e(config('site_name')) ?></a>
    <button class="burger" id="burger" aria-label="Open menu" aria-expanded="false" aria-controls="nav">
      <span></span><span></span><span></span>
    </button>
    <nav id="nav">
      <?php foreach ($links as $key => [$path, $label]): ?>
        <a href="<?= e(url($path)) ?>" class="<?= $active === $key ? 'on' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
