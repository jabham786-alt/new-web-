<?php
declare(strict_types=1);
require_once __DIR__.'/app/bootstrap.php';
$metaTitle='Privacy Policy | Hammad Graphic';
$metaDescription='Learn how Hammad Graphic uses the contact and project information submitted through this website.';
$canonicalPath='/privacy.php';
require APP_ROOT.'/partials/header.php';
?>
<section class="page-hero"><div class="container"><p class="eyebrow">Privacy</p><h1>Your information should be handled <span class="text-gradient">with care.</span></h1><p>This page explains the basic information collected when you contact Hammad Graphic or submit a project request.</p></div></section>
<section class="section section-white"><div class="container legal-copy">
<h2>Information we collect</h2><p>When you submit an order request, we collect the name, email address, phone number, selected service and project details that you provide.</p>
<h2>How we use it</h2><p>We use these details to review your request, prepare a quotation, communicate about the project and manage agreed work. We do not ask for your wallet MPIN, password or one-time password.</p>
<h2>Payments</h2><p>If you pay through EasyPaisa or JazzCash, payment instructions will be shared after the quotation is agreed. Transaction details or a receipt may be requested to help reconcile your payment. A receipt is not treated as proof until the funds are verified.</p>
<h2>Data protection</h2><p>We take reasonable steps to restrict access to project information. Do not send passwords, OTPs or other secret authentication details through the website or chat.</p>
<h2>Contact</h2><p>For a privacy question, email <a href="mailto:<?=e(app_config('business.email'))?>"><?=e(app_config('business.email'))?></a>.</p>
<p class="form-note">This is a starter privacy notice and should be reviewed against the actual business process and applicable legal requirements before launch.</p>
</div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
