<?php
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$data['member']      = $_POST['member'] ?? '';
$data['order']       = $_POST['order'] ?? '';
$data['merchant_id'] = $_POST['merchant_id'] ?? '';
$data['type']        = $_POST['type'] ?? '';

$account = preg_match('/^[a-zA-Z0-9_]{1,}$/', $data['member']);
if ($account != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));
}
$number = preg_match('/^[0-9]+$/', $data['merchant_id']);
if ($number != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法id值!']));
}
$order = preg_match('/^[0-9]+$/', $data['order']);
if ($order != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'订单号!']));
}
if (!in_array($data['type'], ['1', '2'])) {
    exit(json_encode(['status'=>1, 'msg'=>'错误支付方式!']));
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/addOrder', $data, 'post');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}