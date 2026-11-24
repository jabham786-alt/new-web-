<?php
declare(strict_types=1);
require_once __DIR__.'/app/bootstrap.php';
$metaTitle='Terms of Service | Hammad Graphic';
$metaDescription='Read the basic service, quotation and payment terms for projects requested from Hammad Graphic.';
$canonicalPath='/terms.php';
require APP_ROOT.'/partials/header.php';
?>
<section class="page-hero"><div class="container"><p class="eyebrow">Terms of service</p><h1>Clear expectations make <span class="text-gradient">better projects.</span></h1><p>These starter terms describe the general process for requesting services from Hammad Graphic.</p></div></section>
<section class="section section-white"><div class="container legal-copy">
<h2>Project requests and quotations</h2><p>Submitting a request does not automatically create a paid order. Scope, price, delivery estimate and included revisions should be agreed with Hammad Graphic before work begins.</p>
<h2>Payments</h2><p>Payment instructions are provided after a quotation is accepted. Customers should verify the recipient details before sending money. Work starts after the payment is confirmed or another written arrangement is agreed.</p>
<h2>Revisions and delivery</h2><p>Deliverables, file formats, revision rounds and delivery dates are agreed per project. Additional work outside the agreed scope may require a revised quotation.</p>
<h2>Customer materials</h2><p>Customers are responsible for having permission to use logos, images, text and other materials they provide for a project.</p>
<h2>Changes</h2><p>Any project-specific cancellation, refund, ownership or licensing conditions should be confirmed in writing before payment.</p>
<h2>Contact</h2><p>Questions about these terms can be sent to <a href="mailto:<?=e(app_config('business.email'))?>"><?=e(app_config('business.email'))?></a>.</p>
<p class="form-note">This is a starter terms page, not legal advice. Review and customise it for your actual service policies before launch.</p>
</div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
