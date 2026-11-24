<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/admin_auth.php';
if((int)db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn()>0){http_response_code(404);exit('Not found.');}
$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$provided=(string)($_POST['setup_key']??'');$expected=(string)app_config('admin.setup_key','');
 $username=trim((string)($_POST['username']??''));$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');$confirm=(string)($_POST['confirm_password']??'');
 if($expected===''||str_starts_with($expected,'CHANGE_ME_')||!hash_equals($expected,$provided))$error='Setup key is not configured correctly.';
 elseif(!preg_match('/^[A-Za-z0-9_.-]{4,60}$/',$username)||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<14||strlen($password)>200||!hash_equals($password,$confirm))$error='Enter valid admin details. Password must be at least 14 characters.';
 else{$s=db()->prepare('INSERT INTO admin_users(username,email,password_hash,created_at) VALUES(:u,:e,:p,NOW())');$s->execute([':u'=>$username,':e'=>$email,':p'=>password_hash($password,PASSWORD_DEFAULT)]);admin_sign_in((int)db()->lastInsertId());redirect_local('/admin/');}
}
$metaTitle='Admin Setup | Hammad Graphic';$metaDescription='One-time protected administrator setup.';$canonicalPath='/admin/setup.php';$metaRobots='noindex,nofollow';
require APP_ROOT.'/partials/header.php'; ?>
<section class="section section-white"><div class="container auth-wrap"><form class="order-form auth-form" method="post">
<h1>One-time Admin Setup</h1><p class="form-note">Only use this page after adding your private setup key in app/config/config.local.php. This page locks automatically after the first admin is created.</p>
<?php if($error!==''):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-row"><label>One-time setup key</label><input name="setup_key" type="password" required autocomplete="off"></div>
<div class="form-row"><label>Admin username</label><input name="username" required minlength="4" maxlength="60"></div>
<div class="form-row"><label>Admin email</label><input name="email" type="email" required></div>
<div class="form-row"><label>Password (14+ characters)</label><input name="password" type="password" minlength="14" required autocomplete="new-password"></div>
<div class="form-row"><label>Confirm password</label><input name="confirm_password" type="password" minlength="14" required autocomplete="new-password"></div>
<button class="button button-primary button-full">Create Administrator</button></form></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
