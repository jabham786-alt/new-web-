<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/admin_auth.php';
$admin=require_admin();$orderId=filter_var($_GET['id']??$_POST['order_id']??null,FILTER_VALIDATE_INT);
if(!$orderId||$orderId<1){http_response_code(404);exit('Order not found.');}
$allowed=['new','reviewing','quoted','in_progress','completed','cancelled'];
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$action=(string)($_POST['action']??'');
 if($action==='status'){
  $status=(string)($_POST['status']??'');if(!in_array($status,$allowed,true)){http_response_code(400);exit('Invalid order status.');}
  db()->prepare('UPDATE orders SET status=:status,updated_at=NOW() WHERE id=:id')->execute([':status'=>$status,':id'=>$orderId]);
  redirect_local('/admin/order.php?id='.$orderId.'#conversation');
 }
 if($action==='message'){
  $message=trim((string)($_POST['message']??''));if($message===''||mb_strlen($message)>3000){$_SESSION['admin_flash_error']='Message must contain 1 to 3000 characters.';redirect_local('/admin/order.php?id='.$orderId.'#conversation');}
  $exists=db()->prepare('SELECT id FROM orders WHERE id=:id');$exists->execute([':id'=>$orderId]);if(!$exists->fetchColumn()){http_response_code(404);exit('Order not found.');}
  db()->prepare("INSERT INTO order_messages(order_id,sender_type,sender_id,message,created_at) VALUES(:order,'admin',:sender,:message,NOW())")->execute([':order'=>$orderId,':sender'=>(int)$admin['id'],':message'=>$message]);
  redirect_local('/admin/order.php?id='.$orderId.'#conversation');
 }
 http_response_code(400);exit('Invalid action.');
}
$s=db()->prepare('SELECT * FROM orders WHERE id=:id LIMIT 1');$s->execute([':id'=>$orderId]);$order=$s->fetch();if(!$order){http_response_code(404);exit('Order not found.');}
$m=db()->prepare('SELECT sender_type,message,created_at FROM order_messages WHERE order_id=:id ORDER BY id ASC');$m->execute([':id'=>$orderId]);$messages=$m->fetchAll();
$error=$_SESSION['admin_flash_error']??'';unset($_SESSION['admin_flash_error']);
$metaTitle='Manage '.$order['order_reference'].' | Hammad Graphic';$metaDescription='Manage customer order and conversation.';$canonicalPath='/admin/order.php';$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Order management</p><h1><?=e($order['order_reference'])?></h1><p>Customer: <?=e($order['customer_name'])?> · <?=e($order['email'])?></p><a class="button button-secondary" href="/admin/">Back to Admin Dashboard</a></div></section>
<section class="section section-white"><div class="container order-detail-layout"><div class="order-detail-card"><h2>Customer & Project Details</h2><p><strong>Name:</strong> <?=e($order['customer_name'])?></p><p><strong>Email:</strong> <a href="mailto:<?=e($order['email'])?>"><?=e($order['email'])?></a></p><p><strong>Phone:</strong> <?=e($order['phone'])?></p><p><strong>Service:</strong> <?=e($order['service_title'])?></p><p><strong>Project brief:</strong></p><p><?=nl2br(e($order['project_details']))?></p><p><strong>Budget:</strong> <?=e($order['budget']?:'Not specified')?></p><p><strong>Payment status:</strong> <?=e($order['payment_status'])?></p><p><strong>Received:</strong> <?=e($order['created_at'])?></p>
<h2>Update Order Status</h2><form method="post" class="order-form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="order_id" value="<?=e((string)$orderId)?>"><input type="hidden" name="action" value="status"><div class="form-row"><label for="status">Order status</label><select id="status" name="status"><?php foreach($allowed as $value):?><option value="<?=e($value)?>" <?=$order['status']===$value?'selected':''?>><?=e(ucwords(str_replace('_',' ',$value)))?></option><?php endforeach;?></select></div><button class="button button-primary" type="submit">Save Status</button></form></div>
<div id="conversation" class="conversation-panel"><h2>Customer Conversation</h2><p class="form-note">Messages sent here appear in this customer's dashboard under this order.</p><?php if($error!==''):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?><?php if(!$messages):?><p>No messages yet.</p><?php else:?><div class="message-list"><?php foreach($messages as $msg):?><article class="message-bubble <?=e($msg['sender_type']==='admin'?'message-admin':'message-customer')?>"><strong><?=e($msg['sender_type']==='admin'?'Hammad Graphic':'Customer')?></strong><p><?=nl2br(e($msg['message']))?></p><time><?=e($msg['created_at'])?></time></article><?php endforeach;?></div><?php endif;?>
<form method="post" class="order-form message-form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="order_id" value="<?=e((string)$orderId)?>"><input type="hidden" name="action" value="message"><div class="form-row"><label for="message">Reply to customer</label><textarea id="message" name="message" rows="4" maxlength="3000" required placeholder="Your order has been received. We will contact you soon."></textarea></div><button class="button button-primary" type="submit">Send Reply</button></form></div></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
