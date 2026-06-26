<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= eXSS($pageTitle ?? 'Auth') ?> | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card__header">
            <a href="<?= url('') ?>" class="auth-card__logo"><?= APP_NAME ?></a>
        </div>

        <?php if (!empty($flash)): ?>
            <?php foreach ($flash as $type => $msg): ?>
                <div class="alert alert--<?= eXSS($type) ?>">
                    <?= $msg ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?= ($content ?? '') ?>
    </div>
</div>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
