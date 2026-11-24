<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
if(current_customer()!==null)redirect_local('/account.php');
$next=safe_return_path($_GET['next']??$_POST['next']??'/order.php','/order.php');$metaTitle='Customer Login | Hammad Graphic';$metaDescription='Log in to your Hammad Graphic customer profile.';$canonicalPath='/login.php';$metaRobots='noindex,nofollow';$error='';$success=(($_GET['reset']??'')==='success')?'Your password has been reset. Please log in with your new password.':'';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$identity=strtolower(trim((string)($_POST['identity']??'')));$password=(string)($_POST['password']??'');$key=auth_rate_key('login',$identity);
 if(auth_rate_limited($key,5,15))$error='Too many login attempts. Please wait 15 minutes.';
 else{$s=db()->prepare('SELECT id,password_hash FROM customers WHERE (email=:email OR username=:username) AND is_active=1 LIMIT 1');$s->execute([':email'=>$identity,':username'=>$identity]);$c=$s->fetch();
 if($c&&password_verify($password,(string)$c['password_hash'])){auth_rate_clear($key);if(password_needs_rehash((string)$c['password_hash'],PASSWORD_DEFAULT))db()->prepare('UPDATE customers SET password_hash=:hash WHERE id=:id')->execute([':hash'=>password_hash($password,PASSWORD_DEFAULT),':id'=>(int)$c['id']]);db()->prepare('UPDATE customers SET last_login_at=NOW() WHERE id=:id')->execute([':id'=>(int)$c['id']]);customer_sign_in((int)$c['id']);redirect_local($next);}
 auth_rate_failure($key,5,15);$error='Username/email or password is incorrect.';}
}
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Welcome back</p><h1>Log in to your <span class="text-gradient">profile.</span></h1><p>Use your username or email address and password.</p></div></section>
<section class="section section-white"><div class="container auth-wrap"><form class="order-form auth-form" method="post" action="/login.php">
<?php if($success!==''):?><div class="alert alert-success" role="status"><?=e($success)?></div><?php endif;?><?php if($error!==''):?><div class="alert alert-error" role="alert"><?=e($error)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="next" value="<?=e($next)?>">
<div class="form-row"><label for="identity">Username or email *</label><input id="identity" name="identity" maxlength="190" autocomplete="username" required></div>
<div class="form-row"><label for="password">Password *</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
<button class="button button-primary button-full" type="submit">Log In</button><p class="form-note"><a href="/forgot-password.php">Forgot your password?</a></p><p class="form-note">New here? <a href="/register.php">Create your profile</a>.</p></form></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>