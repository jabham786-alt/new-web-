<?php
declare(strict_types=1);require_once dirname(__DIR__).'/app/admin_auth.php';
if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){http_response_code(405);header('Allow: POST');exit('Method not allowed.');}
require_valid_csrf_post();unset($_SESSION['admin_id']);session_regenerate_id(true);redirect_local('/admin/login.php');
