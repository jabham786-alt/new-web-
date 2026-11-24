<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
$metaTitle='Reset Password | Hammad Graphic';$metaDescription='Request a one-time password reset code for your Hammad Graphic customer account.';$canonicalPath='/forgot-password.php';$metaRobots='noindex,nofollow';
$error='';$notice='';$showOtp=(bool)($_SESSION['password_reset_flow_started']??false);
if(isset($_GET['new'])){unset($_SESSION['password_reset_request_id'],$_SESSION['password_reset_flow_started']);$showOtp=false;}
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$action=(string)($_POST['action']??'request');
 if($action==='request'){
  $email=strtolower(trim((string)($_POST['email']??'')));$key=auth_rate_key('password-reset',$email);
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190){$error='Please enter a valid email address.';}
  elseif(auth_rate_limited($key,3,30)){$error='Too many requests. Please wait before trying again.';}
  else{
   $s=db()->prepare('SELECT id FROM customers WHERE email=:email AND is_active=1 LIMIT 1');$s->execute([':email'=>$email]);$customer=$s->fetch();
   if($customer){
    $pdo=db();$pdo->beginTransaction();
    try{
     $pdo->prepare("UPDATE password_reset_requests SET status='cancelled',otp_hash=NULL WHERE customer_id=:id AND status IN ('pending','otp_issued')")->execute([':id'=>(int)$customer['id']]);
     $ipHash=hash_hmac('sha256',(string)($_SERVER['REMOTE_ADDR']??'unknown'),(string)app_config('app.security_salt',''));
     $insert=$pdo->prepare("INSERT INTO password_reset_requests(customer_id,email,status,requested_ip_hash,created_at) VALUES(:id,:email,'pending',:ip,NOW())");
     $insert->execute([':id'=>(int)$customer['id'],':email'=>$email,':ip'=>$ipHash]);$_SESSION['password_reset_request_id']=(int)$pdo->lastInsertId();$pdo->commit();
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
   }else{unset($_SESSION['password_reset_request_id']);}
   auth_rate_failure($key,3,30);$_SESSION['password_reset_flow_started']=true;$showOtp=true;
   $notice='If an active account matches that email, a reset request has been queued. The OTP field is ready below; enter the code after the administrator sends it to your registered email.';
  }
 }elseif($action==='reset'){
  $requestId=$_SESSION['password_reset_request_id']??0;$otp=trim((string)($_POST['otp']??''));$password=(string)($_POST['new_password']??'');$confirm=(string)($_POST['confirm_password']??'');
  if(!preg_match('/^[0-9]{6}$/',$otp)||strlen($password)<12||strlen($password)>200||!preg_match('/[a-z]/',$password)||!preg_match('/[A-Z]/',$password)||!preg_match('/[0-9]/',$password)||!hash_equals($password,$confirm))$error='Enter the six-digit code and a valid matching password (12+ characters, uppercase, lowercase and number).';
  else{
   $pdo=db();$pdo->beginTransaction();
   try{
    $s=$pdo->prepare('SELECT id,customer_id,otp_hash,expires_at,attempts,status FROM password_reset_requests WHERE id=:id FOR UPDATE');$s->execute([':id'=>(int)$requestId]);$r=$s->fetch();
    $valid=$r&&$r['status']==='otp_issued'&&!empty($r['otp_hash'])&&!empty($r['expires_at'])&&strtotime((string)$r['expires_at'])>time()&&(int)$r['attempts']<5&&password_verify($otp,(string)$r['otp_hash']);
    if(!$valid){
     if($r&&$r['status']==='otp_issued'){$n=(int)$r['attempts']+1;$status=$n>=5?'expired':'otp_issued';$pdo->prepare('UPDATE password_reset_requests SET attempts=:n,status=:status WHERE id=:id')->execute([':n'=>$n,':status'=>$status,':id'=>(int)$r['id']]);}
     $pdo->commit();$error='The code is invalid, expired or has already been used. Request a new code if needed.';
    }else{
     $pdo->prepare('UPDATE customers SET password_hash=:hash,auth_version=auth_version+1,updated_at=NOW() WHERE id=:id')->execute([':hash'=>password_hash($password,PASSWORD_DEFAULT),':id'=>(int)$r['customer_id']]);
     $pdo->prepare("UPDATE password_reset_requests SET status='used',otp_hash=NULL,used_at=NOW() WHERE id=:id")->execute([':id'=>(int)$r['id']]);$pdo->commit();
     unset($_SESSION['password_reset_request_id'],$_SESSION['password_reset_flow_started']);session_regenerate_id(true);redirect_local('/login.php?reset=success');
    }
   }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
  }
 }
}
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Account recovery</p><h1>Reset your <span class="text-gradient">password.</span></h1><p>Enter your registered email. An administrator will issue a one-time code and send it to you.</p></div></section>
<section class="section section-white"><div class="container auth-wrap">
<form class="order-form auth-form" method="post" action="/forgot-password.php" novalidate>
<?php if($error!==''):?><div class="alert alert-error" role="alert"><?=e($error)?></div><?php endif;?>
<?php if($notice!==''):?><div class="alert alert-success" role="status"><?=e($notice)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="form-row"><label for="email">Registered email address *</label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" required></div>
<button class="button button-primary button-full" type="submit" name="action" value="request">Continue</button>
</form>
<?php if($showOtp):?><form class="order-form auth-form otp-form" method="post" action="/forgot-password.php" novalidate>
<h2>Enter your one-time code</h2><p class="form-note">This section appears on this same page. Check your registered email for the code sent by the administrator.</p>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="reset">
<div class="form-row"><label for="otp">6-digit OTP *</label><input id="otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required></div>
<div class="form-row"><label for="new_password">New password *</label><input id="new_password" name="new_password" type="password" minlength="12" maxlength="200" autocomplete="new-password" required></div>
<div class="form-row"><label for="confirm_password">Confirm new password *</label><input id="confirm_password" name="confirm_password" type="password" minlength="12" maxlength="200" autocomplete="new-password" required></div>
<button class="button button-primary button-full" type="submit">Reset Password</button>
</form><?php endif;?>
<p class="form-note auth-foot"><a href="/login.php">Back to login</a></p></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>
