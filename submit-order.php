<?php
declare(strict_types=1);
require_once __DIR__.'/app/auth.php';
require_once APP_ROOT.'/app/services.php';
if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){http_response_code(405);header('Allow: POST');exit('Method not allowed.');}
require_valid_csrf_post();
$customer=require_customer('/order.php');
if(!empty($_POST['website']??'')){http_response_code(400);exit('Request rejected.');}
$now=time();$recent=array_filter($_SESSION['order_submissions']??[],static fn($stamp)=>is_int($stamp)&&$stamp>$now-600);
if(count($recent)>=3){$_SESSION['order_error']='Too many requests. Please wait a few minutes and try again.';redirect_local('/order.php');}
$service=trim((string)($_POST['service']??''));$details=trim((string)($_POST['project_details']??''));$budget=trim((string)($_POST['budget']??''));$consent=($_POST['consent']??'')==='1';$catalogue=service_catalogue();
if(!isset($catalogue[$service])||$details===''||mb_strlen($details)>5000||!$consent){$_SESSION['order_error']='Please check the required fields and submit the form again.';redirect_local('/order.php');}
try{
 $pdo=db();$pdo->beginTransaction();
 $s=$pdo->prepare('INSERT INTO orders(customer_id,order_reference,customer_name,email,phone,service_key,service_title,project_details,budget,status,payment_status,created_at) VALUES(:customer_id,:reference,:name,:email,:phone,:service_key,:service_title,:details,:budget,:status,:payment_status,NOW())');
 $reference='HG-'.strtoupper(bin2hex(random_bytes(4)));
 $s->execute([':customer_id'=>(int)$customer['id'],':reference'=>$reference,':name'=>$customer['full_name'],':email'=>$customer['email'],':phone'=>$customer['phone'],':service_key'=>$service,':service_title'=>$catalogue[$service]['title'],':details'=>$details,':budget'=>$budget!==''?$budget:null,':status'=>'new',':payment_status'=>'unpaid']);
 $orderId=(int)$pdo->lastInsertId();
 $ack=$pdo->prepare("INSERT INTO order_messages(order_id,sender_type,sender_id,message,created_at) VALUES(:order_id,'admin',NULL,:message,NOW())");
 $ack->execute([':order_id'=>$orderId,':message'=>'Your order has been received successfully. Thank you for choosing Hammad Graphic. We will review your requirements and contact you soon.']);
 $pdo->commit();
 $_SESSION['order_submissions']=array_values($recent);$_SESSION['order_submissions'][]=$now;$_SESSION['last_order_reference']=$reference;redirect_local('/order-confirmation.php');
}catch(Throwable $e){error_log('Hammad Graphic order submission failed: '.$e->getMessage());$_SESSION['order_error']='We could not save your request right now. Please try again later or contact us on WhatsApp.';redirect_local('/order.php');}
