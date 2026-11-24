<?php
declare(strict_types=1);
require_once APP_ROOT.'/app/seo.php';
require_once APP_ROOT.'/app/auth.php';
$navCustomer=current_customer();
$metaTitle=$metaTitle??'Hammad Graphic | Graphic & Website Design in Pakistan';
$metaDescription=$metaDescription??'Professional graphic design, website design and digital services for businesses in Pakistan.';
$canonicalPath=$canonicalPath??'/';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php render_seo_head($metaTitle,$metaDescription,$canonicalPath,$metaRobots??'index,follow,max-image-preview:large'); render_business_schema(); ?>
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/assets/css/style.css"><script src="/assets/js/site.js" defer></script></head><body>
<a class="skip-link" href="#main">Skip to content</a><header class="site-header"><div class="container nav-wrap">
<a class="brand" href="/" aria-label="Hammad Graphic home"><span class="brand-mark">HG</span><span>Hammad<span class="brand-accent">Graphic</span></span></a>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">Menu</button>
<nav id="primary-nav" class="primary-nav" aria-label="Main navigation"><a href="/">Home</a><a href="/services.php">Services</a><a href="/about.php">About</a><a href="/contact.php">Contact</a><?php if($navCustomer):?><a href="/account.php">My Profile</a><?php else:?><a href="/login.php">Log In</a><a href="/register.php">Register</a><?php endif;?><a class="nav-cta" href="/order.php">Start an Order</a></nav>
</div></header><main id="main">
