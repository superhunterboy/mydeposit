<?php
/**
 * @api {get} /api/getAffiche.php 获取公告信息
 * @apiName getAffiche
 * @apiGroup Affiche
 * @apiVersion 1.0.0
 * @apiPermission none
 *
 *
 * @apiSuccessExample {json} Success-Response:
 *   HTTP/1.1 200 OK
 *   {
 *       "status":0,
 *       "msg":"okey",
 *       "data":[
 *           {
 *               "title":"like you",
 *               "content":"what are you donig?"
 *           }
 *       ]
 *   }
 */
require_once '../wmpay/conf.php';
require_once '../wmpay/common.php';

if (!IS_AJAX) {
    exit(json_encode(['status' => 1, 'msg' => '必须是AJAX!']));
}

$uri    = '/affiche';
$result = sendHttpRequest(PAYMENT_API_DOMAIN . $uri, [], 'get');
if ($result) {
    echo $result;
} else {
    echo json_encode(['status' => 1, 'msg' => '网路错误!']);
}
