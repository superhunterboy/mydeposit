<?php
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/getBankCard', [], 'get');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}