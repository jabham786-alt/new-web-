<?php
declare(strict_types=1);require_once __DIR__.'/app/auth.php';
if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){http_response_code(405);header('Allow: POST');exit('Method not allowed.');}
require_valid_csrf_post();customer_sign_out();redirect_local('/');
