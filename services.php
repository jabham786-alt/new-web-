<?php
declare(strict_types=1); require_once __DIR__.'/app/bootstrap.php'; require_once APP_ROOT.'/app/services.php';
$metaTitle='Graphic Design & Website Services | Hammad Graphic';$metaDescription='Explore logo design, branding, social media graphics, print design, website design, PHP development and SEO setup services by Hammad Graphic.';$canonicalPath='/services.php';$services=service_catalogue();
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Our services</p><h1>Creative services for <span class="text-gradient">ambitious businesses.</span></h1><p>Choose a service to get started. Every project is reviewed individually and priced through a clear quotation.</p></div></section>
<section class="section section-white"><div class="container"><div class="service-grid service-grid-wide">
<?php foreach($services as $slug=>$service): ?><article class="service-card"><p class="service-category"><?=e($service['category'])?></p><h2><?=e($service['title'])?></h2><p><?=e($service['description'])?></p><a class="text-link" href="/order.php?service=<?=e(rawurlencode($slug))?>">Request a quote <span aria-hidden="true">↗</span></a></article><?php endforeach; ?>
</div></div></section><?php require APP_ROOT.'/partials/footer.php'; ?>
