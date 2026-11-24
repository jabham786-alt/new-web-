<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Business Website';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A secure, professional business website.">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="/" aria-label="Company home">Your Company</a>
    </header>

    <main>
        <section class="hero" aria-labelledby="hero-title">
            <p class="eyebrow">Business / Company Website</p>
            <h1 id="hero-title">A professional online presence starts here.</h1>
            <p class="lead">
                This is the secure foundation of your new company website.
                We will tailor the pages, services, and contact details after
                confirming your business information.
            </p>
            <div class="notice">
                <strong>Setup phase:</strong>
                The website foundation is being prepared. Business-specific
                content and customer-facing forms will be added in the next phase.
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> Your Company. All rights reserved.</p>
    </footer>
</body>
</html>
