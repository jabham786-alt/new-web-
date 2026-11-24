<?php
declare(strict_types=1);
require_once __DIR__.'/bootstrap.php';
function current_admin(): ?array {
 $id=$_SESSION['admin_id']??null;if(!is_int($id)&&!(is_string($id)&&ctype_digit($id)))return null;
 $s=db()->prepare('SELECT id,username,email FROM admin_users WHERE id=:id AND is_active=1 LIMIT 1');$s->execute([':id'=>(int)$id]);$a=$s->fetch();if(!$a)unset($_SESSION['admin_id']);return $a?:null;
}
function require_admin(): array { $a=current_admin();if($a===null)redirect_local('/admin/login.php');return $a; }
function admin_sign_in(int $id): void {session_regenerate_id(true);unset($_SESSION['customer_id']);$_SESSION['admin_id']=$id;}
