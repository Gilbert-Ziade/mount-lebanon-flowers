<?php
$slides = flower_slides($flower);
$socials = ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube'];
?>
<a class="crumb" href="<?= e(url('/flowers')) ?>">← All flowers</a>
<div class="detail">
  <div class="gallery">
    <div class="big" id="gmain" style="<?= e(flower_bg($flower)) ?>">
      <?php foreach ($slides as $i => $_): ?>
        <div class="slide" <?= $i > 0 ? 'hidden' : '' ?>><?= flower_slide($flower, $i, 260) ?></div>
      <?php endforeach; ?>
    </div>
    <?php if (count($slides) > 1): ?>
      <div class="thumbs" id="gthumbs" role="group" aria-label="Flower images">
        <?php foreach ($slides as $i => $_): ?>
          <button type="button" data-i="<?= $i ?>" class="<?= $i === 0 ? 'on' : '' ?>" aria-label="Show image <?= $i + 1 ?>" style="<?= e(flower_bg($flower)) ?>">
            <?= flower_slide($flower, $i, 56) ?>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div>
    <h1><?= e($flower['name']) ?></h1>
    <div class="sci"><?= e($flower['scientific_name']) ?></div>
    <p style="margin-top:12px"><?= e($flower['description']) ?></p>
    <dl class="facts">
      <dt>Edible</dt><dd><?= $flower['edible'] ? 'Yes, see notes below' : 'No' ?></dd>
      <dt>Colors</dt><dd><?= color_dots($flower) ?></dd>
      <dt>Season</dt><dd><?= e($flower['season']) ?></dd>
      <dt>Regions</dt><dd><?= e(implode(', ', $flower['regions'])) ?></dd>
      <dt>Where to find it</dt><dd><?= e($flower['location']) ?></dd>
      <dt>Benefits</dt><dd><?= e($flower['benefits']) ?></dd>
    </dl>
    <div class="row">
      <?php foreach ($socials as $key => $label): ?>
        <?php if (!empty($flower['links'][$key])): ?>
          <a class="btn alt sm" href="<?= e($flower['links'][$key]) ?>" target="_blank" rel="noopener"><?= e($label) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <p class="sci" style="margin-top:14px">Never eat a wild plant unless an expert has confirmed it.</p>
  </div>
</div>
