<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
$customer=require_customer('/account.php');
$orderId=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
if(!$orderId||$orderId<1){http_response_code(404);exit('Order not found.');}
$s=db()->prepare('SELECT id,order_reference,service_title,project_details,budget,status,payment_status,created_at FROM orders WHERE id=:id AND customer_id=:customer LIMIT 1');
$s->execute([':id'=>$orderId,':customer'=>(int)$customer['id']]);$order=$s->fetch();
if(!$order){http_response_code(404);exit('Order not found.');}
$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$message=trim((string)($_POST['message']??''));
 if($message===''||mb_strlen($message)>3000)$error='Message must contain 1 to 3000 characters.';
 else{db()->prepare("INSERT INTO order_messages(order_id,sender_type,sender_id,message,created_at) VALUES(:order,'customer',:sender,:message,NOW())")->execute([':order'=>$orderId,':sender'=>(int)$customer['id'],':message'=>$message]);redirect_local('/customer-order.php?id='.$orderId.'#conversation');}
}
$m=db()->prepare('SELECT sender_type,message,created_at FROM order_messages WHERE order_id=:id ORDER BY id ASC');$m->execute([':id'=>$orderId]);$messages=$m->fetchAll();
$metaTitle='Order '.$order['order_reference'].' | Hammad Graphic';$metaDescription='View your order progress and conversation.';$canonicalPath='/customer-order.php';$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Customer order</p><h1><?=e($order['order_reference'])?></h1><p><?=e($order['service_title'])?> · <?=e(str_replace('_',' ',$order['status']))?></p><a class="button button-secondary" href="/account.php">Back to Dashboard</a></div></section>
<section class="section section-white"><div class="container order-detail-layout"><div class="order-detail-card"><h2>Order Details</h2><p><strong>Project brief</strong></p><p><?=nl2br(e($order['project_details']))?></p><p><strong>Budget:</strong> <?=e($order['budget']?:'To be discussed')?></p><p><strong>Payment:</strong> <?=e(str_replace('_',' ',$order['payment_status']))?></p><p><strong>Submitted:</strong> <?=e($order['created_at'])?></p></div>
<div id="conversation" class="conversation-panel"><h2>Messages with Hammad Graphic</h2><p class="form-note">Send a message about this order. Replies from the admin will appear here.</p><?php if(!$messages):?><p>No messages yet.</p><?php else:?><div class="message-list"><?php foreach($messages as $msg):?><article class="message-bubble <?=e($msg['sender_type']==='admin'?'message-admin':'message-customer')?>"><strong><?=e($msg['sender_type']==='admin'?'Hammad Graphic':'You')?></strong><p><?=nl2br(e($msg['message']))?></p><time><?=e($msg['created_at'])?></time></article><?php endforeach;?></div><?php endif;?>
<?php if($error!==''):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?><form class="order-form message-form" method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="form-row"><label for="message">Your message</label><textarea id="message" name="message" rows="4" maxlength="3000" required></textarea></div><button class="button button-primary" type="submit">Send Message</button></form></div></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
