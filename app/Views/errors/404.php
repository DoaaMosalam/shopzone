<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 – Page Not Found</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .error-page { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:60vh; text-align:center; }
        .error-page h1 { font-size:6rem; margin:0; color:var(--color-primary); }
    </style>
</head>
<body>
    <div class="error-page">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you are looking for doesn't exist or has been moved.</p>
        <a href="<?= url('') ?>" class="btn btn--primary">Go Home</a>
    </div>
</body>
</html>
