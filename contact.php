<?php
declare(strict_types=1); require_once __DIR__.'/app/bootstrap.php';
$metaTitle='Contact Hammad Graphic | Get in Touch';$metaDescription='Contact Hammad Graphic in Pakistan for graphic design, website design and digital service enquiries.';$canonicalPath='/contact.php';
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Contact</p><h1>Let's talk about <span class="text-gradient">your next project.</span></h1><p>Send your project brief or contact us directly. We'll guide you through the next steps.</p></div></section>
<section class="section section-white"><div class="container contact-grid">
<div class="contact-card"><span class="contact-icon">✉</span><h2>Email</h2><a href="mailto:<?=e(app_config('business.email'))?>"><?=e(app_config('business.email'))?></a></div>
<div class="contact-card"><span class="contact-icon">◉</span><h2>WhatsApp</h2><a href="<?=e(whatsapp_url('Hello Hammad Graphic, I have a project enquiry.'))?>" target="_blank" rel="noopener noreferrer">Chat with Hammad Graphic</a></div>
<div class="contact-card"><span class="contact-icon">⌖</span><h2>Location</h2><p>Pakistan • Serving clients online</p></div>
</div><div class="container contact-bottom"><p>Ready to share project details?</p><a class="button button-primary" href="/order.php">Submit an Order Request</a></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
