<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
function current_customer(): ?array {
 $id=$_SESSION['customer_id']??null;if(!is_int($id)&&!(is_string($id)&&ctype_digit($id)))return null;
 $s=db()->prepare('SELECT id,full_name,username,email,phone,profile_photo,auth_version FROM customers WHERE id=:id AND is_active=1 LIMIT 1');$s->execute([':id'=>(int)$id]);$c=$s->fetch();
 if(!$c){unset($_SESSION['customer_id'],$_SESSION['customer_auth_version']);return null;}
 if(!isset($_SESSION['customer_auth_version'])||(int)$_SESSION['customer_auth_version']!==(int)$c['auth_version']){unset($_SESSION['customer_id'],$_SESSION['customer_auth_version']);return null;}
 unset($c['auth_version']);return $c;
}
function require_customer(string $returnTo='/order.php'): array {
 $c=current_customer();if($c===null)redirect_local('/login.php?next='.rawurlencode(safe_return_path($returnTo,'/order.php')));return $c;
}
function customer_sign_in(int $id): void {
 session_regenerate_id(true);unset($_SESSION['admin_id']);
 $s=db()->prepare('SELECT auth_version FROM customers WHERE id=:id AND is_active=1 LIMIT 1');$s->execute([':id'=>$id]);
 $version=$s->fetchColumn();if($version===false)throw new RuntimeException('Customer account is unavailable.');
 $_SESSION['customer_id']=$id;$_SESSION['customer_auth_version']=(int)$version;
}
function customer_sign_out(): void {unset($_SESSION['customer_id'],$_SESSION['customer_auth_version']);session_regenerate_id(true);}
function auth_rate_key(string $scope,string $identity=''): string {
 $salt=(string)app_config('app.security_salt','');
 if(strlen($salt)<32||str_starts_with($salt,'REPLACE_'))throw new RuntimeException('Set a private random app.security_salt in config.local.php before using customer or admin authentication.');
 return hash_hmac('sha256',$scope.'|'.strtolower(trim($identity)).'|'.(string)($_SERVER['REMOTE_ADDR']??'unknown'),$salt);
}
function auth_rate_limited(string $key,int $limit,int $minutes): bool {
 $s=db()->prepare('SELECT attempts,window_started_at,blocked_until FROM auth_rate_limits WHERE rate_key=:key LIMIT 1');$s->execute([':key'=>$key]);$r=$s->fetch();
 if(!$r)return false;if(!empty($r['blocked_until'])&&strtotime((string)$r['blocked_until'])>time())return true;
 if(strtotime((string)$r['window_started_at'])<time()-$minutes*60)return false;return (int)$r['attempts'] >= $limit;
}
function auth_rate_failure(string $key,int $limit,int $minutes): void {
 $pdo=db();$pdo->beginTransaction();try{
  $s=$pdo->prepare('SELECT attempts,window_started_at FROM auth_rate_limits WHERE rate_key=:key FOR UPDATE');$s->execute([':key'=>$key]);$r=$s->fetch();
  if(!$r){$pdo->prepare('INSERT INTO auth_rate_limits(rate_key,attempts,window_started_at) VALUES(:key,1,NOW())')->execute([':key'=>$key]);}
  elseif(strtotime((string)$r['window_started_at'])<time()-$minutes*60){$pdo->prepare('UPDATE auth_rate_limits SET attempts=1,window_started_at=NOW(),blocked_until=NULL WHERE rate_key=:key')->execute([':key'=>$key]);}
  else{$n=(int)$r['attempts']+1;$blocked=$n >= $limit ? 'DATE_ADD(NOW(), INTERVAL '.max(1,$minutes).' MINUTE)' : 'NULL';$pdo->prepare('UPDATE auth_rate_limits SET attempts=:n,blocked_until='.$blocked.' WHERE rate_key=:key')->execute([':n'=>$n,':key'=>$key]);}
  $pdo->commit();
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function auth_rate_clear(string $key): void {db()->prepare('DELETE FROM auth_rate_limits WHERE rate_key=:key')->execute([':key'=>$key]);}
