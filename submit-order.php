<?php
declare(strict_types=1); require_once __DIR__.'/app/bootstrap.php'; require_once APP_ROOT.'/app/services.php';
if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){http_response_code(405);header('Allow: POST');exit('Method not allowed.');}
require_valid_csrf_post();
if(!empty($_POST['website']??'')){http_response_code(400);exit('Request rejected.');}
$now=time();$recent=array_filter($_SESSION['order_submissions']??[],static fn($stamp)=>is_int($stamp)&&$stamp>$now-600);
if(count($recent)>=3){$_SESSION['order_error']='Too many requests. Please wait a few minutes and try again.';redirect_local('/order.php');}
$name=trim((string)($_POST['customer_name']??''));$email=trim((string)($_POST['email']??''));$phone=trim((string)($_POST['phone']??''));$service=trim((string)($_POST['service']??''));$details=trim((string)($_POST['project_details']??''));$budget=trim((string)($_POST['budget']??''));$consent=($_POST['consent']??'')==='1';$catalogue=service_catalogue();
if($name===''||mb_strlen($name)>100||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190||!preg_match('/^[0-9+().\-\s]{7,30}$/',$phone)||!isset($catalogue[$service])||$details===''||mb_strlen($details)>5000||!$consent){$_SESSION['order_error']='Please check the required fields and submit the form again.';redirect_local('/order.php');}
try{
 $pdo=db();$stmt=$pdo->prepare('INSERT INTO orders (order_reference,customer_name,email,phone,service_key,service_title,project_details,budget,status,payment_status,created_at) VALUES (:reference,:name,:email,:phone,:service_key,:service_title,:details,:budget,:status,:payment_status,NOW())');
 $reference='HG-'.strtoupper(bin2hex(random_bytes(4)));
 $stmt->execute([':reference'=>$reference,':name'=>$name,':email'=>$email,':phone'=>$phone,':service_key'=>$service,':service_title'=>$catalogue[$service]['title'],':details'=>$details,':budget'=>$budget!==''?$budget:null,':status'=>'new',':payment_status'=>'unpaid']);
 $_SESSION['order_submissions']=array_values($recent);$_SESSION['order_submissions'][]=$now;$_SESSION['last_order_reference']=$reference;redirect_local('/order-confirmation.php');
}catch(Throwable $exception){error_log('Hammad Graphic order submission failed: '.$exception->getMessage());$_SESSION['order_error']='We could not save your request right now. Please try again later or contact us on WhatsApp.';redirect_local('/order.php');}
