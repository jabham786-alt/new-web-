<?php
declare(strict_types=1);require_once __DIR__.'/app/auth.php';$customer=require_customer('/account.php');
$metaTitle='My Profile | Hammad Graphic';$metaDescription='Manage your Hammad Graphic customer profile and project order history.';$canonicalPath='/account.php';$metaRobots='noindex,nofollow';$error='';$success=(($_GET['welcome']??'')==='1')?'Your customer profile has been created successfully.':'';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$name=trim((string)($_POST['full_name']??''));$phone=trim((string)($_POST['phone']??''));$newPhoto=null;
 try{
  if($name===''||mb_strlen($name)>100||!preg_match('/^[0-9+().\-\s]{7,30}$/',$phone))throw new RuntimeException('Please enter a valid name and mobile number.');
  if(isset($_FILES['profile_photo'])&&($_FILES['profile_photo']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){
   if(($_FILES['profile_photo']['error']??UPLOAD_ERR_OK)!==UPLOAD_ERR_OK||($_FILES['profile_photo']['size']??0)>2*1024*1024)throw new RuntimeException('Profile photo must be no larger than 2 MB.');
   $tmp=(string)$_FILES['profile_photo']['tmp_name'];$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($tmp);$extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
   if(!isset($extensions[$mime])||@getimagesize($tmp)===false)throw new RuntimeException('Only valid JPG, PNG and WebP photos are allowed.');
   $dir=APP_ROOT.'/uploads/profile';if(!is_dir($dir)&&!mkdir($dir,0755,true)&&!is_dir($dir))throw new RuntimeException('Profile photo storage is unavailable.');
   $filename=bin2hex(random_bytes(20)).'.'.$extensions[$mime];if(!move_uploaded_file($tmp,$dir.'/'.$filename))throw new RuntimeException('Could not save profile photo.');$newPhoto='uploads/profile/'.$filename;
  }
  if($newPhoto!==null){db()->prepare('UPDATE customers SET full_name=:name,phone=:phone,profile_photo=:photo,updated_at=NOW() WHERE id=:id')->execute([':name'=>$name,':phone'=>$phone,':photo'=>$newPhoto,':id'=>(int)$customer['id']]);if(!empty($customer['profile_photo'])&&is_file(APP_ROOT.'/'.$customer['profile_photo']))@unlink(APP_ROOT.'/'.$customer['profile_photo']);}
  else db()->prepare('UPDATE customers SET full_name=:name,phone=:phone,updated_at=NOW() WHERE id=:id')->execute([':name'=>$name,':phone'=>$phone,':id'=>(int)$customer['id']]);
  redirect_local('/account.php?updated=1');
 }catch(Throwable $e){if($newPhoto!==null&&is_file(APP_ROOT.'/'.$newPhoto))@unlink(APP_ROOT.'/'.$newPhoto);$error=$e instanceof RuntimeException?$e->getMessage():'Profile update failed. Please try again.';}
 $customer=current_customer()??$customer;
}
if(($_GET['updated']??'')==='1')$success='Your profile has been updated.';
$s=db()->prepare("SELECT o.id,o.order_reference,o.service_title,o.status,o.payment_status,o.created_at,(SELECT COUNT(*) FROM order_messages m WHERE m.order_id=o.id AND m.sender_type='admin') AS replies FROM orders o WHERE o.customer_id=:id ORDER BY o.id DESC LIMIT 50");$s->execute([':id'=>(int)$customer['id']]);$orders=$s->fetchAll();
$blogPosts=db()->query("SELECT title,slug,excerpt,published_at FROM blog_posts WHERE status='published' AND published_at<=NOW() ORDER BY published_at DESC LIMIT 6")->fetchAll();
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Your customer profile</p><div class="profile-heading"><?php if(!empty($customer['profile_photo'])):?><img class="profile-avatar" src="/<?=e($customer['profile_photo'])?>" alt="<?=e($customer['full_name'])?> profile photo"><?php else:?><div class="profile-avatar profile-avatar-fallback" aria-hidden="true"><?=e(strtoupper(substr($customer['full_name'],0,1)))?></div><?php endif;?><div><h1>Hello, <span class="text-gradient"><?=e($customer['full_name'])?>.</span></h1><p>@<?=e($customer['username'])?> · <?=e($customer['email'])?></p></div></div></div></section>
<section class="section section-white"><div class="container account-layout">
<div><h2 class="account-heading">Edit your profile</h2><?php if($success!==''):?><div class="alert alert-success" role="status"><?=e($success)?></div><?php endif;?><?php if($error!==''):?><div class="alert alert-error" role="alert"><?=e($error)?></div><?php endif;?>
<form class="order-form profile-edit-form" method="post" action="/account.php" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-row"><label for="full_name">Full name</label><input id="full_name" name="full_name" maxlength="100" value="<?=e($customer['full_name'])?>" required></div>
<div class="form-row"><label for="username_display">Username</label><input id="username_display" value="<?=e($customer['username'])?>" readonly><p class="form-note">Your unique username cannot currently be changed.</p></div>
<div class="form-row"><label for="email_display">Email address</label><input id="email_display" value="<?=e($customer['email'])?>" readonly></div>
<div class="form-row"><label for="phone">Mobile / WhatsApp number</label><input id="phone" name="phone" type="tel" maxlength="30" value="<?=e($customer['phone'])?>" required></div>
<div class="form-row"><label for="profile_photo">Change profile photo</label><input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"><p class="form-note">JPG, PNG or WebP, maximum 2 MB. Leave empty to keep current photo.</p></div>
<button class="button button-primary" type="submit">Save Profile</button></form></div>
<div><div class="account-actions"><a class="button button-primary" href="/order.php">Place a New Order</a><form method="post" action="/logout.php"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><button class="button button-secondary" type="submit">Log Out</button></form></div><h2 class="account-heading">Latest from Hammad Graphics Blog</h2><p class="form-note">Read helpful design tips and creative updates published by our team.</p><?php if(!$blogPosts):?><p class="form-note">New articles will appear here soon.</p><?php else:?><div class="blog-mini-list"><?php foreach($blogPosts as $bp):?><article><h3><a href="/blog-detail.php?slug=<?=rawurlencode($bp['slug'])?>"><?=e($bp['title'])?></a></h3><p><?=e($bp['excerpt'])?></p></article><?php endforeach;?></div><?php endif;?><p><a class="button button-secondary" href="/blog.php">View All Blogs</a></p><h2 class="account-heading">Your Orders & Messages</h2><p class="form-note">Open any order to view its progress and message Hammad Graphic.</p>
<?php if(!$orders):?><p class="form-note">You haven't placed an order yet.</p><?php else:?><div class="table-scroll"><table class="orders-table"><thead><tr><th>Reference</th><th>Service</th><th>Order status</th><th>Payment</th><th>Messages</th><th>Date</th></tr></thead><tbody><?php foreach($orders as $o):?><tr><td><a href="/customer-order.php?id=<?=e((string)$o['id'])?>"><?=e($o['order_reference'])?></a></td><td><?=e($o['service_title'])?></td><td><?=e(str_replace('_',' ',$o['status']))?></td><td><?=e(str_replace('_',' ',$o['payment_status']))?></td><td><a href="/customer-order.php?id=<?=e((string)$o['id'])?>">Open conversation (<?=e((string)$o['replies'])?>)</a></td><td><?=e($o['created_at'])?></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></div>
</div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>