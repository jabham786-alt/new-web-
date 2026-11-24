<?php
declare(strict_types=1);
require_once __DIR__.'/app/bootstrap.php';
require_once APP_ROOT.'/app/services.php';
$metaTitle='Hammad Graphic | Graphic Design & Website Design in Pakistan';
$metaDescription='Grow your business with professional logo design, social media graphics, print design, responsive websites and SEO setup from Hammad Graphic.';
$canonicalPath='/';$services=service_catalogue();
require APP_ROOT.'/partials/header.php'; ?>
<section class="hero-section"><div class="container hero-grid"><div class="hero-copy">
<p class="eyebrow">Design • Websites • Digital Growth</p><h1>Make your business <span class="text-gradient">impossible to ignore.</span></h1>
<p class="hero-lead">Hammad Graphic helps businesses look professional and grow online with creative graphic design, responsive websites and practical digital solutions.</p>
<div class="hero-actions"><a class="button button-primary" href="/order.php">Start Your Project <span aria-hidden="true">→</span></a><a class="button button-secondary" href="/services.php">Explore Services</a></div>
<div class="trust-line"><span class="trust-dot"></span> Personal service • Clear communication • Custom quotations</div></div>
<div class="hero-art" aria-label="Graphic design and digital creativity illustration"><div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
<div class="art-card art-card-main"><span class="art-label">YOUR BRAND</span><strong>Stand out.<br>Grow forward.</strong><span class="art-swatch-row"><i></i><i></i><i></i><i></i></span></div>
<div class="art-card art-card-small"><span class="mini-icon">✦</span><strong>Creative<br>ideas</strong></div><div class="art-card art-card-bottom"><span class="mini-bars"><i></i><i></i><i></i></span><strong>Digital presence</strong></div><span class="art-spark spark-one">✳</span><span class="art-spark spark-two">✦</span></div></div></section>
<section class="section section-white"><div class="container"><div class="section-heading"><p class="eyebrow">What we do</p><h2>Everything your brand needs to move forward.</h2><p>From the first logo concept to your business website, get creative and digital services in one place.</p></div><div class="service-grid">
<?php $i=0;foreach(array_slice($services,0,6,true) as $slug=>$service):$i++;?><article class="service-card"><span class="service-number"><?=e(str_pad((string)$i,2,'0',STR_PAD_LEFT))?></span><p class="service-category"><?=e($service['category'])?></p><h3><?=e($service['title'])?></h3><p><?=e($service['description'])?></p><a class="text-link" href="/order.php?service=<?=e(rawurlencode($slug))?>">Order this service <span aria-hidden="true">↗</span></a></article><?php endforeach;?>
</div><div class="center-action"><a class="button button-secondary" href="/services.php">View all services</a></div></div></section>
<section class="section section-dark"><div class="container split-section"><div><p class="eyebrow eyebrow-light">Why Hammad Graphic</p><h2>Good design is more than looking good.</h2></div><div class="benefit-list">
<div><span>01</span><p><strong>Built around your goals</strong><br>Every project starts with understanding your business and audience.</p></div>
<div><span>02</span><p><strong>Clear project process</strong><br>Share your requirements, receive a quotation and track your order.</p></div>
<div><span>03</span><p><strong>Digital-ready delivery</strong><br>Designs and websites prepared for real-world business use.</p></div></div></div></section>
<section class="section section-white"><div class="container cta-panel"><div><p class="eyebrow">Have a project in mind?</p><h2>Let's build something your customers remember.</h2><p>Tell us what you need. We'll review your brief and get back to you with the next steps.</p></div><a class="button button-primary" href="/order.php">Get a Quote <span aria-hidden="true">→</span></a></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
