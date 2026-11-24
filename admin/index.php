<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/admin_auth.php';$admin=require_admin();
$orders=db()->query('SELECT id,order_reference,customer_name,email,service_title,status,payment_status,created_at FROM orders ORDER BY id DESC LIMIT 100')->fetchAll();
$requests=db()->query("SELECT r.id,r.email,r.created_at,r.status,c.full_name FROM password_reset_requests r INNER JOIN customers c ON c.id=r.customer_id WHERE r.status IN ('pending','otp_issued') ORDER BY r.created_at DESC LIMIT 50")->fetchAll();
$issued=$_SESSION['issued_otp_flash']??null;unset($_SESSION['issued_otp_flash']);$adminError=$_SESSION['admin_flash_error']??'';unset($_SESSION['admin_flash_error']);
$metaTitle='Admin Dashboard | Hammad Graphic';$metaDescription='Private Hammad Graphic order and password reset administration.';$canonicalPath='/admin/';$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Private admin area</p><h1>Admin <span class="text-gradient">Dashboard.</span></h1><p>Signed in as <?=e($admin['username'])?>.</p><form method="post" action="/admin/logout.php"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><button class="button button-secondary" type="submit">Log Out</button></form></div></section>
<section class="section section-white"><div class="container"><?php if($adminError!==''):?><div class="alert alert-error"><?=e($adminError)?></div><?php endif;?>
<?php if(is_array($issued)):?><div class="otp-issued"><h2>New one-time code — show once</h2><p>Send this code to <?=e($issued['email'])?> using your business email. It expires in 10 minutes. Do not share it with anyone else.</p><strong class="otp-code"><?=e($issued['otp'])?></strong><button class="button button-secondary" type="button" data-copy-otp="<?=e($issued['otp'])?>">Copy OTP</button><p class="form-note">This code will not be displayed again after leaving this page. Generate a new one if it is lost.</p></div><?php endif;?>
<h2>Password reset requests</h2><p class="form-note">Generate an OTP only after confirming the customer request. The code is hashed in the database and displayed to you once so you can email it manually.</p>
<?php if(!$requests):?><p>No active password reset requests.</p><?php else:?><div class="table-scroll"><table class="orders-table"><thead><tr><th>Customer</th><th>Email</th><th>Requested</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($requests as $r):?><tr><td><?=e($r['full_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['created_at'])?></td><td><?=e($r['status'])?></td><td><form method="post" action="/admin/generate-reset-otp.php"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="request_id" value="<?=e((string)$r['id'])?>"><button class="button button-primary" type="submit">Generate OTP</button></form></td></tr><?php endforeach;?>
</tbody></table></div><?php endif;?>
<h2 class="account-heading">Recent customer orders</h2>
<?php if(!$orders):?><p>No orders have been submitted.</p><?php else:?><div class="table-scroll"><table class="orders-table"><thead><tr><th>Reference</th><th>Customer</th><th>Service</th><th>Status</th><th>Payment</th><th>Created</th></tr></thead><tbody><?php foreach($orders as $o):?><tr><td><?=e($o['order_reference'])?></td><td><?=e($o['customer_name'])?><br><?=e($o['email'])?></td><td><?=e($o['service_title'])?></td><td><?=e($o['status'])?></td><td><?=e($o['payment_status'])?></td><td><?=e($o['created_at'])?></td></tr><?php endforeach;?></tbody></table></div><?php endif;?>
</div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
