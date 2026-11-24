<?php
declare(strict_types=1);
http_response_code(404);
require_once __DIR__.'/app/bootstrap.php';
$metaTitle='Page Not Found | Hammad Graphic';
$metaDescription='The page you requested could not be found.';
$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php';
?>
<section class="section section-white confirmation-section"><div class="container confirmation-card"><p class="eyebrow">Error 404</p><h1>This page went off the canvas.</h1><p>The link may be outdated or the page may have moved.</p><a class="button button-primary" href="/">Return to Hammad Graphic</a></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
