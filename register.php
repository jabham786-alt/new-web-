<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
if(current_customer()!==null)redirect_local('/account.php');
$metaTitle='Create Customer Profile | Hammad Graphic';$metaDescription='Create your Hammad Graphic customer profile to manage project orders.';$canonicalPath='/register.php';$error='';
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();
 $name=trim((string)($_POST['full_name']??''));$username=strtolower(trim((string)($_POST['username']??'')));$email=strtolower(trim((string)($_POST['email']??'')));$phone=trim((string)($_POST['phone']??''));$password=(string)($_POST['password']??'');$confirm=(string)($_POST['confirm_password']??'');
 $key=auth_rate_key('register',$email);
 $imagePath=null;
 if(auth_rate_limited($key,5,15))$error='Too many registration attempts. Please wait 15 minutes.';
 elseif($name===''||mb_strlen($name)>100||!preg_match('/^[a-z0-9_]{4,30}$/',$username)||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190||!preg_match('/^[0-9+().\-\s]{7,30}$/',$phone)||strlen($password)<12||strlen($password)>200||!preg_match('/[a-z]/',$password)||!preg_match('/[A-Z]/',$password)||!preg_match('/[0-9]/',$password)||!hash_equals($password,$confirm)){$error='Please check all fields. Username must be 4–30 letters/numbers/underscores. Password needs 12+ characters with uppercase, lowercase and a number.';auth_rate_failure($key,5,15);}
 else {
  try {
   if(isset($_FILES['profile_photo'])&&($_FILES['profile_photo']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){
    if(($_FILES['profile_photo']['error']??UPLOAD_ERR_OK)!==UPLOAD_ERR_OK||($_FILES['profile_photo']['size']??0)>2*1024*1024)throw new RuntimeException('Profile photo must be an image no larger than 2 MB.');
    $tmp=(string)$_FILES['profile_photo']['tmp_name'];$finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($tmp);
    $extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if(!isset($extensions[$mime])||@getimagesize($tmp)===false)throw new RuntimeException('Please upload a valid JPG, PNG or WebP profile photo.');
    $dir=APP_ROOT.'/uploads/profile';if(!is_dir($dir)&&!mkdir($dir,0755,true)&&!is_dir($dir))throw new RuntimeException('Profile photo storage is unavailable.');
    $filename=bin2hex(random_bytes(20)).'.'.$extensions[$mime];
    if(!move_uploaded_file($tmp,$dir.'/'.$filename))throw new RuntimeException('Profile photo could not be saved.');
    $imagePath='uploads/profile/'.$filename;
   }
   $s=db()->prepare('INSERT INTO customers(full_name,username,email,phone,profile_photo,password_hash,created_at) VALUES(:name,:username,:email,:phone,:photo,:hash,NOW())');
   $s->execute([':name'=>$name,':username'=>$username,':email'=>$email,':phone'=>$phone,':photo'=>$imagePath,':hash'=>password_hash($password,PASSWORD_DEFAULT)]);
   $id=(int)db()->lastInsertId();auth_rate_clear($key);customer_sign_in($id);redirect_local('/account.php?welcome=1');
  } catch(PDOException $e){if($imagePath!==null&&is_file(APP_ROOT.'/'.$imagePath))@unlink(APP_ROOT.'/'.$imagePath);if((string)$e->getCode()==='23000')$error='This username or email is already registered. Please choose another or log in.';else{error_log('Customer registration failed: '.$e->getMessage());$error='Registration could not be completed. Please try again later.';}}
  catch(Throwable $e){if($imagePath!==null&&is_file(APP_ROOT.'/'.$imagePath))@unlink(APP_ROOT.'/'.$imagePath);$error=$e instanceof RuntimeException?$e->getMessage():'Registration could not be completed. Please try again later.';}
 }
}
require APP_ROOT.'/partials/header.php'; ?>
<section class="page-hero"><div class="container"><p class="eyebrow">Customer profile</p><h1>Create your <span class="text-gradient">profile.</span></h1><p>Set up your personal profile to place orders and manage your projects.</p></div></section>
<section class="section section-white"><div class="container auth-wrap"><form class="order-form auth-form" method="post" action="/register.php" enctype="multipart/form-data">
<?php if($error!==''):?><div class="alert alert-error" role="alert"><?=e($error)?></div><?php endif;?>
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="profile-photo-picker"><div class="profile-photo-placeholder" aria-hidden="true">HG</div><div class="form-row"><label for="profile_photo">Profile photo (optional)</label><input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"><p class="form-note">JPG, PNG or WebP. Maximum 2 MB.</p></div></div>
<div class="form-row"><label for="full_name">Full name *</label><input id="full_name" name="full_name" maxlength="100" autocomplete="name" required></div>
<div class="form-row"><label for="username">Username *</label><input id="username" name="username" minlength="4" maxlength="30" pattern="[A-Za-z0-9_]{4,30}" autocomplete="username" required><p class="form-note">Unique profile name. Use 4–30 English letters, numbers or underscore.</p></div>
<div class="form-row"><label for="phone">Personal mobile / WhatsApp number *</label><input id="phone" name="phone" type="tel" maxlength="30" autocomplete="tel" placeholder="+92 3XX XXXXXXX" required></div>
<div class="form-row"><label for="email">Email address *</label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" required></div>
<div class="form-row"><label for="password">Create password *</label><input id="password" name="password" type="password" minlength="12" maxlength="200" autocomplete="new-password" required><p class="form-note">At least 12 characters, including uppercase, lowercase and a number.</p></div>
<div class="form-row"><label for="confirm_password">Confirm password *</label><input id="confirm_password" name="confirm_password" type="password" minlength="12" maxlength="200" autocomplete="new-password" required></div>
<button class="button button-primary button-full" type="submit">Create My Profile</button><p class="form-note">Already registered? <a href="/login.php">Log in here</a>.</p></form></div></section>
<?php require APP_ROOT.'/partials/footer.php'; ?>