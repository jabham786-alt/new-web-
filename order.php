<?php
declare(strict_types=1); require_once __DIR__.'/app/bootstrap.php'; require_once APP_ROOT.'/app/services.php';
$metaTitle='Order Graphic Design or Website Services | Hammad Graphic';$metaDescription='Submit a project request for logo design, social media graphics, website design and other services. Receive a custom quotation from Hammad Graphic.';$canonicalPath='/order.php';
$services=service_catalogue();$selectedService=(string)($_GET['service']??'');if(!isset($services[$selectedService]))$selectedService='';$flashError=$_SESSION['order_error']??'';unset($_SESSION['order_error']);
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Start a project</p><h1>Tell us what you <span class="text-gradient">want to create.</span></h1><p>Share a few details. We'll review your request and confirm the scope and price before you make a payment.</p></div></section>
<section class="section section-white"><div class="container order-layout"><div class="order-form-wrap">
<?php if($flashError!==''):?><div class="alert alert-error" role="alert"><?=e($flashError)?></div><?php endif;?>
<form class="order-form" method="post" action="/submit-order.php">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-row"><label for="customer_name">Your name *</label><input id="customer_name" name="customer_name" type="text" maxlength="100" autocomplete="name" required></div>
<div class="form-row two-cols"><div><label for="email">Email address *</label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" required></div><div><label for="phone">WhatsApp / Phone *</label><input id="phone" name="phone" type="tel" maxlength="30" autocomplete="tel" required></div></div>
<div class="form-row"><label for="service">Select a service *</label><select id="service" name="service" required><option value="">Choose a service</option><?php foreach($services as $slug=>$service):?><option value="<?=e($slug)?>" <?=$selectedService===$slug?'selected':''?>><?=e($service['title'])?></option><?php endforeach;?></select></div>
<div class="form-row"><label for="project_details">Project details *</label><textarea id="project_details" name="project_details" rows="6" maxlength="5000" required placeholder="Describe what you need, your preferred style, size, pages or features, and deadline."></textarea></div>
<div class="form-row"><label for="budget">Estimated budget (optional)</label><select id="budget" name="budget"><option value="">Prefer to discuss</option><option>Under PKR 5,000</option><option>PKR 5,000–15,000</option><option>PKR 15,000–30,000</option><option>PKR 30,000+</option></select></div>
<div class="honeypot" aria-hidden="true"><label for="website">Leave this field empty</label><input id="website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
<label class="checkbox-row"><input type="checkbox" name="consent" value="1" required><span>I agree that Hammad Graphic may contact me about this project request.</span></label>
<button class="button button-primary button-full" type="submit">Submit Project Request <span aria-hidden="true">→</span></button>
<p class="form-note">No payment is collected on this form. We'll confirm your quotation first, then provide the configured EasyPaisa / JazzCash payment instructions.</p>
</form></div>
<aside class="order-aside"><p class="eyebrow">What happens next?</p><ol class="steps-list"><li><strong>Submit your brief</strong><span>Tell us what you need and how to reach you.</span></li><li><strong>Receive a quotation</strong><span>We'll confirm scope, timeline and project price.</span></li><li><strong>Pay by wallet</strong><span>After approval, you'll receive the configured payment account details.</span></li><li><strong>Work begins</strong><span>We'll proceed once payment is verified and the project is agreed.</span></li></ol><div class="wallet-note"><strong>Payment options</strong><p>EasyPaisa and JazzCash will be available after the account numbers are configured by the owner.</p></div></aside>
</div></section><?php require APP_ROOT.'/partials/footer.php'; ?>
