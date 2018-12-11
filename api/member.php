<?php

header("Accept: application/json;");
header("Content-Type: application/json; charset=utf-8");

require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$member = $_GET['member'] ?? '';
if($member == '') exit(json_encode(['status'=>1, 'msg'=>'会员账号不能为空!']));

$number = preg_match('/^[A-Za-z0-9_@]{1,}$/', $member);
if($number != 1) exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/memberIsExists/' . $member, [], 'get');
if($result){
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}