<?php
//获取微信或者支付宝加好友二维码
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$type = '1';
if ($_GET['type'] == '2') {
    $type = '2';
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/getEnabledPicture/' . $type, [], 'get');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}