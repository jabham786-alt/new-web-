<?php
declare(strict_types=1);require_once dirname(__DIR__).'/app/admin_auth.php';require_admin();
$id=filter_var($_GET['id']??$_POST['id']??0,FILTER_VALIDATE_INT)?:0;$s=db()->prepare('SELECT id,full_name,username,email,phone,is_active,created_at FROM customers WHERE id=:id');$s->execute([':id'=>$id]);$customer=$s->fetch();if(!$customer){http_response_code(404);exit('Customer not found.');}
$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$name=trim((string)($_POST['full_name']??''));$email=strtolower(trim((string)($_POST['email']??'')));$phone=trim((string)($_POST['phone']??''));$active=($_POST['is_active']??'0')==='1'?1:0;
 if($name===''||mb_strlen($name)>100||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190||!preg_match('/^[0-9+().\-\s]{7,30}$/',$phone))$error='Enter a valid name, email and phone number.';
 else{try{$q=db()->prepare('UPDATE customers SET full_name=:name,email=:email,phone=:phone,is_active=:active,updated_at=NOW() WHERE id=:id');$q->execute([':name'=>$name,':email'=>$email,':phone'=>$phone,':active'=>$active,':id'=>$id]);redirect_local('/admin/customers.php?saved=1');}catch(PDOException $e){$error='Could not save. This email may already belong to another customer.';}}
 $customer=array_merge($customer,['full_name'=>$name,'email'=>$email,'phone'=>$phone,'is_active'=>$active]);
}
$metaTitle='Edit Customer | Hammad Graphics';$metaDescription='Edit customer account details.';$canonicalPath='/admin/customer-edit.php';$metaRobots='noindex,nofollow';require APP_ROOT.'/partials/header.php';?>
<section class="page-hero"><div class="container"><p class="eyebrow">Customer editor</p><h1>Edit <span class="text-gradient">Customer.</span></h1><a class="button button-secondary" href="/admin/customers.php">Back to Customers</a></div></section>
<section class="section section-white"><div class="container auth-wrap"><?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<form class="order-form" method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$id?>">
<div class="form-row"><label>Full name</label><input name="full_name" maxlength="100" value="<?=e($customer['full_name'])?>" required></div>
<div class="form-row"><label>Username (read only)</label><input value="<?=e($customer['username'])?>" readonly></div>
<div class="form-row"><label>Email</label><input type="email" name="email" maxlength="190" value="<?=e($customer['email'])?>" required></div>
<div class="form-row"><label>Phone</label><input name="phone" maxlength="30" value="<?=e($customer['phone'])?>" required></div>
<div class="form-row"><label>Account status</label><select name="is_active"><option value="1" <?=(int)$customer['is_active']===1?'selected':''?>>Active</option><option value="0" <?=(int)$customer['is_active']===0?'selected':''?>>Disabled</option></select></div>
<button class="button button-primary">Save Customer</button></form></div></section><?php require APP_ROOT.'/partials/footer.php';?>