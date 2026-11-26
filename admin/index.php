<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/admin_auth.php';
$admin=require_admin();
$stats=[
 'orders'=>(int)db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
 'new'=>(int)db()->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn(),
 'customers'=>(int)db()->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
 'messages'=>(int)db()->query("SELECT COUNT(*) FROM order_messages WHERE sender_type='customer'")->fetchColumn(),
];
$orders=db()->query('SELECT o.id,o.order_reference,o.customer_name,o.email,o.phone,o.service_title,o.status,o.payment_status,o.created_at,(SELECT COUNT(*) FROM order_messages m WHERE m.order_id=o.id) AS message_count FROM orders o ORDER BY o.id DESC LIMIT 200')->fetchAll();
$requests=db()->query("SELECT r.id,r.email,r.created_at,r.status,c.full_name FROM password_reset_requests r INNER JOIN customers c ON c.id=r.customer_id WHERE r.status IN ('pending','otp_issued') ORDER BY r.created_at DESC LIMIT 50")->fetchAll();
$issued=$_SESSION['issued_otp_flash']??null;unset($_SESSION['issued_otp_flash']);$adminError=$_SESSION['admin_flash_error']??'';unset($_SESSION['admin_flash_error']);
$metaTitle='Admin Dashboard | Hammad Graphic';$metaDescription='Manage customer orders and messages.';$canonicalPath='/admin/';$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Business control center</p><h1>Admin <span class="text-gradient">Dashboard.</span></h1><p>Welcome, <?=e($admin['username'])?>. Review incoming work, update order progress and reply to customers.</p><form method="post" action="/admin/logout.php"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><button class="button button-secondary" type="submit">Log Out</button></form></div></section>
<section class="section section-white"><div class="container">
<?php if($adminError!==''):?><div class="alert alert-error"><?=e($adminError)?></div><?php endif;?>
<div class="admin-stat-grid"><article class="admin-stat"><span>Total orders</span><strong><?=e((string)$stats['orders'])?></strong></article><article class="admin-stat"><span>New orders</span><strong><?=e((string)$stats['new'])?></strong></article><article class="admin-stat"><span>Customers</span><strong><?=e((string)$stats['customers'])?></strong></article><article class="admin-stat"><span>Customer messages</span><strong><?=e((string)$stats['messages'])?></strong></article></div>
<div class="admin-actions"><a class="button button-primary" href="/admin/blogs.php">Manage Blogs</a><a class="button button-secondary" href="/admin/customers.php">Manage Customers</a><a class="button button-secondary" href="/blog.php" target="_blank" rel="noopener">View Public Blog</a></div><h2 class="account-heading">All Customer Orders</h2><p class="form-note">Select “Open order” to view the complete brief, update its status and send a reply visible in the customer's dashboard.</p>
<?php if(!$orders):?><p>No customer orders have been received yet.</p><?php else:?><div class="table-scroll"><table class="orders-table"><thead><tr><th>Order</th><th>Customer</th><th>Service</th><th>Status</th><th>Payment</th><th>Messages</th><th>Received</th><th>Action</th></tr></thead><tbody>
<?php foreach($orders as $o):?><tr><td><strong><?=e($o['order_reference'])?></strong></td><td><?=e($o['customer_name'])?><br><a href="mailto:<?=e($o['email'])?>"><?=e($o['email'])?></a><br><?=e($o['phone'])?></td><td><?=e($o['service_title'])?></td><td><?=e(str_replace('_',' ',$o['status']))?></td><td><?=e(str_replace('_',' ',$o['payment_status']))?></td><td><?=e((string)$o['message_count'])?></td><td><?=e($o['created_at'])?></td><td><a class="button button-primary" href="/admin/order.php?id=<?=e((string)$o['id'])?>">Open order</a></td></tr><?php endforeach;?>
</tbody></table></div><?php endif;?>
<?php if(is_array($issued)):?><div class="otp-issued"><h2>New one-time code — show once</h2><p>Send this code to <?=e($issued['email'])?> using your business email. It expires in 10 minutes.</p><strong class="otp-code"><?=e($issued['otp'])?></strong><button class="button button-secondary" type="button" data-copy-otp="<?=e($issued['otp'])?>">Copy OTP</button></div><?php endif;?>
<h2 class="account-heading">Customer Password Reset Requests</h2>
<?php if(!$requests):?><p>No active password reset requests.</p><?php else:?><div class="table-scroll"><table class="orders-table"><thead><tr><th>Customer</th><th>Email</th><th>Requested</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($requests as $r):?><tr><td><?=e($r['full_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['created_at'])?></td><td><?=e($r['status'])?></td><td><form method="post" action="/admin/generate-reset-otp.php"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="request_id" value="<?=e((string)$r['id'])?>"><button class="button button-secondary" type="submit">Generate OTP</button></form></td></tr><?php endforeach;?></tbody></table></div><?php endif;?>
</div></section><?php require APP_ROOT.'/partials/footer.php'; ?>
