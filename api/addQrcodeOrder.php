<?php
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$data['id']         = $_POST['id'] ?? '';
$data['type']       = $_POST['type'] ?? '';
$data['member']     = $_POST['member'] ?? '';
$data['money']      = $_POST['money'] ?? '';
$data['drawee']     = $_POST['drawee'] ?? '';

$number = preg_match('/^[0-9]+$/', $data['id']);
if ($number != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法id值!']));
}
$account = preg_match('/^[a-zA-Z0-9_]{1,}$/', $data['member']);
if ($account != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));
}
$amount = preg_match('/^[0-9]+\.{0,1}[0-9]{0,2}$/', $data['money']);
if ($amount != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法金额格式!']));
}
$drawee = preg_match("/^([\x{4e00}-\x{9fa5}]|[a-zA-Z0-9_-])+$/u", $data['drawee']);
if($drawee != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法姓名格式!']));
}
if (empty($data['type']) || !in_array($data['type'], ['1', '2', '3' ,'4'])) {
    exit(json_encode(['status'=>1, 'msg'=>'未知支付类型!']));
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/addSinglePay', $data, 'post');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}
