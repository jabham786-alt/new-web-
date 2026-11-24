<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/admin_auth.php';
if(current_admin()!==null)redirect_local('/admin/');
$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$identity=strtolower(trim((string)($_POST['identity']??'')));$password=(string)($_POST['password']??'');$key=auth_rate_key('admin-login',$identity);
 if(auth_rate_limited($key,5,15))$error='Too many attempts. Please wait 15 minutes.';
 else{$s=db()->prepare('SELECT id,password_hash FROM admin_users WHERE (username=:u OR email=:e) AND is_active=1 LIMIT 1');$s->execute([':u'=>$identity,':e'=>$identity]);$a=$s->fetch();
 if($a&&password_verify($password,(string)$a['password_hash'])){auth_rate_clear($key);if(password_needs_rehash((string)$a['password_hash'],PASSWORD_DEFAULT))db()->prepare('UPDATE admin_users SET password_hash=:p WHERE id=:id')->execute([':p'=>password_hash($password,PASSWORD_DEFAULT),':id'=>(int)$a['id']]);db()->prepare('UPDATE admin_users SET last_login_at=NOW() WHERE id=:id')->execute([':id'=>(int)$a['id']]);admin_sign_in((int)$a['id']);redirect_local('/admin/');}
 auth_rate_failure($key,5,15);$error='Username/email or password is incorrect.';}
}
$metaTitle='Admin Login | Hammad Graphic';$metaDescription='Private administrator sign in.';$canonicalPath='/admin/login.php';$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php'; ?>
<section class="section section-white"><div class="container auth-wrap"><form class="order-form auth-form" method="post">
<h1>Admin Login</h1><?php if($error!==''):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-row"><label>Username or email</label><input name="identity" required autocomplete="username"></div>
<div class="form-row"><label>Password</label><input name="password" type="password" required autocomplete="current-password"></div>
<button class="button button-primary button-full">Log In</button></form></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
