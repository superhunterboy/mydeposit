<?php
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$type       = $_GET['type'] ?? '1';
if (!in_array($type, ['1', '2', '3'])) {
    exit(json_encode(['status'=>1, 'msg'=>'错误支付方式']));
}
$payType    = $_GET['payType'] ?? '2';
if (!in_array($payType, ['3', '2'])) {
    exit(json_encode(['status'=>1, 'msg'=>'错误支付平台']));
}
if ($type == '1') {
    $uri = '/getSingleWechat';
} elseif ($type == '3') {
    $uri = '/getSingleQQ';
} else {
    $uri = '/getSingleAlipay/' . $payType;
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . $uri, [], 'get');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}
