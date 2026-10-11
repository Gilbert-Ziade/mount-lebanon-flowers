<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? config('site_title')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<?php foreach (['base', 'layout', 'components'] as $css): ?>
<link rel="stylesheet" href="<?= e(asset("css/$css.css")) ?>">
<?php endforeach; ?>
<?php foreach ($styles ?? [] as $css): /* page-specific files: css/pages/<name>.css */ ?>
<link rel="stylesheet" href="<?= e(asset("css/pages/$css.css")) ?>">
<?php endforeach; ?>
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main id="app"><?= $content ?></main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
