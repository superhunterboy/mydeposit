<?php
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

$data['username']   = $_POST['account'] ?? '';
$data['money']      = $_POST['money'] ?? '';
$data['companyNo']  = '00001';
$data['vendorType'] = 68;
$data['paytype']    = 'VPay_ZFB';
$data['paytype2']   = '';
$data['payTime'] = date('Y-m-d H:i:s');
$data['device'] = 1;
$data['ip'] = getIp();

$account = preg_match('/^[a-zA-Z0-9_]{1,}$/', $data['username']);
if ($account != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));
}
$amount = preg_match('/^[0-9]+\.{0,1}[0-9]{0,2}$/', $data['money']);
if ($amount != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法金额格式!']));
}

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/payment', $data, 'post');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}
