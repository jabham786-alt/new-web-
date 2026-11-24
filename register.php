<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
if(current_customer()!==null)redirect_local('/account.php');
$metaTitle='Create Customer Account | Hammad Graphic';$metaDescription='Register securely to submit and manage your Hammad Graphic project orders.';$canonicalPath='/register.php';$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$name=trim((string)($_POST['full_name']??''));$email=strtolower(trim((string)($_POST['email']??'')));$phone=trim((string)($_POST['phone']??''));$password=(string)($_POST['password']??'');$confirm=(string)($_POST['confirm_password']??'');$key=auth_rate_key('register',$email);
 if(auth_rate_limited($key,5,15))$error='Too many registration attempts. Please wait 15 minutes.';
 elseif($name===''||mb_strlen($name)>100||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190||!preg_match('/^[0-9+().\-\s]{7,30}$/',$phone)||strlen($password)<12||strlen($password)>200||!preg_match('/[a-z]/',$password)||!preg_match('/[A-Z]/',$password)||!preg_match('/[0-9]/',$password)||!hash_equals($password,$confirm)){auth_rate_failure($key,5,15);$error='Please enter valid details. Password must be at least 12 characters and include uppercase, lowercase and a number.';}
 else{try{$s=db()->prepare('INSERT INTO customers(full_name,email,phone,password_hash,created_at) VALUES(:name,:email,:phone,:hash,NOW())');$s->execute([':name'=>$name,':email'=>$email,':phone'=>$phone,':hash'=>password_hash($password,PASSWORD_DEFAULT)]);$id=(int)db()->lastInsertId();auth_rate_clear($key);customer_sign_in($id);redirect_local('/order.php');}
 catch(PDOException $e){if((string)$e->getCode()==='23000')$error='An account with this email already exists. Please log in instead.';else{error_log('Customer registration failed: '.$e->getMessage());$error='Registration could not be completed. Please try again later.';}}}
}
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Customer account</p><h1>Create your <span class="text-gradient">account.</span></h1><p>Register once to submit project orders and keep your details together.</p></div></section>
<section class="section section-white"><div class="container auth-wrap"><form class="order-form auth-form" method="post" action="/register.php" novalidate>
<?php if($error!==''):?><div class="alert alert-error" role="alert"><?=e($error)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-row"><label for="full_name">Full name *</label><input id="full_name" name="full_name" maxlength="100" autocomplete="name" required></div>
<div class="form-row"><label for="email">Email address *</label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" required></div>
<div class="form-row"><label for="phone">WhatsApp / Phone *</label><input id="phone" name="phone" type="tel" maxlength="30" autocomplete="tel" required></div>
<div class="form-row"><label for="password">Password *</label><input id="password" name="password" type="password" minlength="12" maxlength="200" autocomplete="new-password" required><p class="form-note">At least 12 characters, including uppercase, lowercase and a number.</p></div>
<div class="form-row"><label for="confirm_password">Confirm password *</label><input id="confirm_password" name="confirm_password" type="password" minlength="12" maxlength="200" autocomplete="new-password" required></div>
<button class="button button-primary button-full" type="submit">Create Account</button><p class="form-note">Already registered? <a href="/login.php">Log in here</a>.</p></form></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
