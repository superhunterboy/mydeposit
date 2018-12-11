<?php
/**
 * @api {get} /api/getOffBank.php 根据会员层级获取银行卡
 * @apiName getOffBank
 * @apiGroup OffBank
 * @apiVersion 1.0.0
 * @apiPermission none
 *
 * @apiParam {String} account 会员账号
 *
 * @apiSuccessExample {json} Success-Response:
 *   HTTP/1.1 200 OK
 *   {
 *       "status": 0,
 *       "msg": "",
 *       "data": {
 *           "bank_name": "中国银行",
 *           "user_name": "范德萨",
 *           "bank_number": "456446464"
 *       }
 *   }
 */
require_once('../wmpay/conf.php');
require_once('../wmpay/common.php');

if (!IS_AJAX) {
    exit(json_encode(['status'=>1, 'msg'=>'必须是AJAX!']));
}

$account = $_GET['account'] ?? '';
$acc = preg_match('/^[a-zA-Z0-9_]{1,}$/', $account);
if ($acc != 1) {
    exit(json_encode(['status'=>1, 'msg'=>'非法会员账号格式!']));
}

$uri = '/getPayBankByMemberLevel/'. $account;

$result = sendHttpRequest(PAYMENT_API_DOMAIN . $uri, [], 'get');
if ($result) {
    echo $result;
}else{
    echo json_encode(['status'=>1, 'msg'=>'网路错误!']);
}