<?php
declare(strict_types=1);
/* Copy to config.local.php and edit values. Never commit credentials or wallet numbers. */
return [
 'app'=>['environment'=>'production','debug'=>false,'base_url'=>'','timezone'=>'Asia/Karachi','security_salt'=>'REPLACE_WITH_A_LONG_RANDOM_SECRET'],
 'business'=>['name'=>'Hammad Graphic','owner'=>'Hammad Ali','tagline'=>'Creative Design. Professional Digital Presence.','email'=>'jabham786@gmail.com','phone'=>'+923067115462','whatsapp'=>'923067115462','address'=>'Pakistan'],
 'database'=>['host'=>'localhost','name'=>'CHANGE_ME_DATABASE','username'=>'CHANGE_ME_USERNAME','password'=>'CHANGE_ME_PASSWORD','charset'=>'utf8mb4'],
 'admin'=>['setup_key'=>'CHANGE_ME_USE_A_LONG_RANDOM_ONE_TIME_SETUP_KEY'],
 'payments'=>[
  'easypaisa'=>['enabled'=>false,'account_name'=>'Hammad Ali','account_number'=>''],
  'jazzcash'=>['enabled'=>false,'account_name'=>'Hammad Ali','account_number'=>''],
  'instructions'=>'Submit your order first. Wallet payment details will be shared after your quotation is approved.',
 ],
];
