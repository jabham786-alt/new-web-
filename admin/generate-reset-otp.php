<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/app/admin_auth.php';require_admin();
if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){http_response_code(405);header('Allow: POST');exit('Method not allowed.');}
require_valid_csrf_post();$id=filter_var($_POST['request_id']??null,FILTER_VALIDATE_INT);
if(!$id||$id<1){http_response_code(400);exit('Invalid request.');}
$otp=str_pad((string)random_int(0,999999),6,'0',STR_PAD_LEFT);
$s=db()->prepare("UPDATE password_reset_requests SET status='otp_issued',otp_hash=:hash,expires_at=DATE_ADD(NOW(),INTERVAL 10 MINUTE),attempts=0,issued_at=NOW() WHERE id=:id AND status IN ('pending','otp_issued')");
$s->execute([':hash'=>password_hash($otp,PASSWORD_DEFAULT),':id'=>$id]);
if($s->rowCount()!==1){$_SESSION['admin_flash_error']='That reset request is no longer active.';redirect_local('/admin/');}
$emailStmt=db()->prepare('SELECT email FROM password_reset_requests WHERE id=:id');$emailStmt->execute([':id'=>$id]);$email=(string)$emailStmt->fetchColumn();
$_SESSION['issued_otp_flash']=['otp'=>$otp,'email'=>$email];redirect_local('/admin/');
