<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
if(current_customer()!==null)redirect_local('/account.php');
$next=safe_return_path($_GET['next']??$_POST['next']??'/order.php','/order.php');$metaTitle='Customer Login | Hammad Graphic';$metaDescription='Log in to your Hammad Graphic customer account.';$canonicalPath='/login.php';$metaRobots='noindex,nofollow';$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');$key=auth_rate_key('login',$email);
 if(auth_rate_limited($key,5,15))$error='Too many login attempts. Please wait 15 minutes.';
 else{$s=db()->prepare('SELECT id,password_hash FROM customers WHERE email=:email AND is_active=1 LIMIT 1');$s->execute([':email'=>$email]);$c=$s->fetch();
 if($c&&password_verify($password,(string)$c['password_hash'])){auth_rate_clear($key);if(password_needs_rehash((string)$c['password_hash'],PASSWORD_DEFAULT))db()->prepare('UPDATE customers SET password_hash=:hash WHERE id=:id')->execute([':hash'=>password_hash($password,PASSWORD_DEFAULT),':id'=>(int)$c['id']]);db()->prepare('UPDATE customers SET last_login_at=NOW() WHERE id=:id')->execute([':id'=>(int)$c['id']]);customer_sign_in((int)$c['id']);redirect_local($next);}
 auth_rate_failure($key,5,15);$error='Email or password is incorrect.';}
}
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Welcome back</p><h1>Log in to your <span class="text-gradient">account.</span></h1><p>Access your customer account before placing a new order.</p></div></section>
<section class="section section-white"><div class="container auth-wrap"><form class="order-form auth-form" method="post" action="/login.php" novalidate>
<?php if($error!==''):?><div class="alert alert-error" role="alert"><?=e($error)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="next" value="<?=e($next)?>">
<div class="form-row"><label for="email">Email address *</label><input id="email" name="email" type="email" maxlength="190" autocomplete="username" required></div>
<div class="form-row"><label for="password">Password *</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
<button class="button button-primary button-full" type="submit">Log In</button><p class="form-note"><a href="/forgot-password.php">Forgot your password?</a></p><p class="form-note">New here? <a href="/register.php">Create an account</a>.</p></form></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
