<?php
header("Accept: application/json;");
header("Content-Type: application/json; charset=utf-8");

require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$acc = preg_match('/^[a-zA-Z0-9_]{1,}$/', $_POST['account']);
if($acc != 1) exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/memberIsExists/' . $_POST['account'], [], 'get');
if($result)
{
    $resArr = json_decode($result, true);
    if($resArr['status'] != 0)
    {
        exit($result); //json_encode(['status'=>1, 'msg'=>'无效用户名!'])
    }
}

//防止重复提交
//if(empty($_SESSION['_token']) || empty($token)) exit(json_encode(['status'=>1, 'msg'=>'操作异常请重试!']));

//if($token != $_SESSION['_token']) exit(json_encode(['status'=>1, 'msg'=>'请勿重复操作!']));

$amo = preg_match('/^[0-9]+\.{0,1}[0-9]{0,2}$/', $_POST['amount']);
if($amo != 1) exit(json_encode(['status'=>1, 'msg'=>'非法金额格式!']));

$postData['account']    = $_POST['account'];
$postData['amount']     = $_POST['amount'];
$postData['depositor']  = $_POST['depositor'];
$postData['type']       = 1;
$postData['card_id']    = $_POST['card_id'];

$result = sendHttpRequest(PAYMENT_API_DOMAIN . '/addOfflinePay', $postData);
if($result)
    echo $result;
else
    echo json_decode(['status'=>1, 'msg'=>'网络异常!']);

