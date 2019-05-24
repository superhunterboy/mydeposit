<?php

require_once('./wmpay/conf.php');
require_once('./wmpay/dictionaries.php');

// 调取支付接口，获取支付方式信息
$output = sendHttpRequest(PAYMENT_API_DOMAIN . '/payment/getPaymentInfo/' . COMPANY_NO, array(), 'get');
$payTypes = json_decode($output, true);
$wechatPayType = '';
$alipayType = '';
$bankPayType = '';
$qqPayType = '';
$jdPayType = '';
$baiduPayType ='';
$unionPayType ='';
$yunPayType ='';

if ($payTypes) {
    // type： 0 系统未配置、1 雅付、2 闪付、3 讯宝、4 乐盈
    $wechatPayType = key($payTypes['data'][1]);
    $alipayType = key($payTypes['data'][2]);
    $bankPayType = $payTypes['data'][3];
    $qqPayType = key($payTypes['data'][4]);
    $jdPayType = key($payTypes['data'][5]);
    $baiduPayType = key($payTypes['data'][6]);
    $unionPayType = key($payTypes['data'][7]);
    $yunPayType = key($payTypes['data'][14]);
}
//var_dump($payTypes, '************************', $bankPayType);exit;
$_token = md5(uniqid(rand(), true));
$_SESSION['_token'] = $_token;

?>
    <!DOCTYPE html>
    <html>

    <head lang="en">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="./css/pay.css">
        <link rel="stylesheet" href="css/reset.css">
        <title>澳亚国际快速充值中心</title>
    </head>

    <body>
        <div class="box">
            <div class="head clearfix">
                <div class="head-left">
                    <div class="desc desc-2" style="margin-left:2px;" onclick=window.location='http://ay210.com/'>返回首页</div>
                    <div class="desc desc-3" onclick=window.location='https://xingmim.com/'>活动申请大厅</div>
                    <div class="desc desc-4" onclick=window.location='http://ay210.com/cl/?module=System&method=MAdvertis&other=MemberExclusiveII&ExclusiveNo=#%23%23'>优惠活动</div>
                    <div class="desc desc-5" onclick=window.location='http://ay210.com/?aff='>注册页面</div>
                </div>
                <div class="desc-1" onclick=window.location="https://static.meiqia.com/dist/standalone.html?_=t&eid=93887"></div>
                <a href="./index.html" style="position: absolute;top: 180px;color: white;right: 32px;font-size: 17px;padding: 6px 12px;border-radius: 12px;background: linear-gradient(to bottom, #e30000, #710000);">切换新版</a>
            </div>

            <div class="content">
                <div class="notice">
                    <div class="news-text"></div>
                </div>
                <div class="shell">
                    <div class="tab">
                        <?php if($payTypes['onlinepay'] == 2){?>
                        <div class="tab-item tab-active" m-div="online-pay">在线支付</div>
                        <?php } ?>
                        <?php if($payTypes['yunpay'] == 2){?>
                        <div class="tab-item" m-div="qq-pay" style="color:yellow;">云闪付</div>
                        <?php } ?>
                        <?php if($payTypes['trance'] == 2){?>
                        <div class="tab-item" m-div="transfer-pay">转账汇款</div>
                        <?php } ?>
                        <?php if($payTypes['weixin'] == 2){?>
                            <div class="tab-item" m-div="qrcode-pay">微信支付</div>
                        <?php } ?>
                        <?php if($payTypes['alipay'] == 2){?>
                        <div class="tab-item ali-tab-item" m-div="ali-pay">支付宝支付</div>
                        <?php } ?>
                        <!--<div class="tab-item" m-div="qq-pay">QQ支付</div>-->

                    </div>

                    <div class="content-inner">
                        <div class="online-pay">
                            <div class="online-pay-form">
                                <form method="post" action="./wmpay/payment.php" id="pay_form">
                                    <input type="hidden" name="companyNo" value="<?php echo COMPANY_NO; ?>">
                                    <input type="hidden" name="vendorType" id="vendor_type" value="<?php echo $payTypes['type']['wechat']; ?>">
                                    <input type="hidden" name="device" value="1">
                                    <input type="hidden" id="token" name="token" value="<?php echo $_token; ?>">
                                    <input type="hidden" name="pay_type" id="pay_type" value="<?php echo $wechatPayType; ?>" />
                                    <input type="hidden" name="paytype2" id="paytype2" value="" />

                                    <div class="online-pay-left"  style="height:238px;">
                                        <div class="input-row"  style="margin-bottom:15px;">
                                            <span>会员账号：</span>
                                            <input type="text" class="input" name="account" placeholder="请输入会员账号">
                                        </div>
                                        <div class="input-row" style="margin-bottom:15px;">
                                            <span>存款金额：</span>
                                            <input type="text" class="input" name="money" placeholder="请输入存款金额">
                                        </div>
                                        <input type="hidden" id="gftinputhid" value="<?= $payTypes['type']['netpay'];?>" />
                                        <?php
                                        if ($payTypes['type']['netpay'] == 69 && !empty($payTypes['data'][3])) {
                                            ?>
                                            <div class="input-row" id="gftbanknum" style="margin-bottom:15px;">
                                                <span>银行卡号：</span>
                                                <input type="text" class="input" name="banknumber" id="banknumber" placeholder="请输入银行卡号">
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="online-pay-right">
                                        <?php if ($payTypes['payStatus']['netpay'] == 1) { ?>
                                            <?php if ($payTypes['type']['netpay'] != 5) { ?>
                                                <span class="txt">点击图标选择银行</span>
                                                <div class="bank-list">
                                                    <?php $bankType = $payTypes['type']['netpay']; ?>
                                                    <?php foreach ($switch_img[$bankType] as $key => $value) { ?>
                                                        <div data-type="3" data-value="01" data-vendor="<?php echo $bankType; ?>" data-id="<?php echo $key; ?>" class="bank-item">
                                                            <img src="./img/bank/<?php echo $value; ?>" />
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>

                                    <div class="btn-wraper">
                                        <input type="button" class="btn-submit" onclick="doSubmit('pay_form')" value="下一步" />
                                    </div>

                                    <div class="online-pay-bottom">
                                        <div class="btn-pay-box">
                                            <?php if ($payTypes['payStatus']['wechat'] == 1) { ?>
                                            <a data-type="1" data-value="<?php echo $wechatPayType; ?>" data-vendor="<?php echo $payTypes['type']['wechat']; ?>" class="btn-pay bwechat">
                                                <img class="gou" src="./img/pay/gou.png" />
                                                <img class="img" src="./img/pay/wechat-pay-active.png" />
                                            </a>
                                            <?php } ?>
                                            <?php if ($payTypes['payStatus']['alipay'] == 1) { ?>
                                            <a data-type="2" data-value="<?php echo $alipayType; ?>" data-vendor="<?php echo $payTypes['type']['alipay']; ?>" class="btn-pay ali">
                                                <img class="gou" src="./img/pay/gou.png" />
                                                <img class="img" src="./img/pay/ali-pay-active.png" />
                                            </a>
                                            <?php } ?>
                                            <?php if ($payTypes['payStatus']['qq'] == 1) { ?>
                                            <a data-type="4" data-value="<?php echo $qqPayType; ?>" data-vendor="<?php echo $payTypes['type']['qq']; ?>" class="btn-pay qq">
                                                <img class="gou" src="./img/pay/gou.png" />
                                                <img class="img" src="./img/pay/qq-pay-active.png" />
                                            </a>
                                            <?php } ?>
                                            <?php if ($payTypes['payStatus']['jd'] == 1) { ?>
                                            <a data-type="5" data-value="<?php echo $jdPayType; ?>" data-vendor="<?php echo $payTypes['type']['jd']; ?>" class="btn-pay jd">
                                                <img class="gou" src="./img/pay/gou.png" />
                                                <img class="img" src="./img/pay/jd-pay-active.png" />
                                            </a>
                                            <?php } ?>
                                            <?php if ($payTypes['payStatus']['baidu'] == 1) { ?>
                                            <a data-type="6" data-value="<?php echo $baiduPayType; ?>" data-vendor="<?php echo $payTypes['type']['baidu']; ?>" class="btn-pay baidu">
                                                <img class="gou" src="./img/pay/gou.png" />
                                                <img class="img" src="./img/pay/baidu-pay-active.png" />
                                            </a>
                                            <?php } ?>
                                            <?php if ($payTypes['payStatus']['union'] == 1) { ?>
                                            <a data-type="7" data-value="<?php echo $unionPayType; ?>" data-vendor="<?php echo $payTypes['type']['union']; ?>" class="btn-pay union">
                                                <img class="gou" src="./img/pay/gou.png" />
                                                <img class="img" src="./img/pay/union-pay-active.png" />
                                            </a>
                                            <?php } ?>
                                            <?php if ($payTypes['payStatus']['yun'] == 1) { ?>
                                                <a data-type="14" data-value="<?php echo $yunPayType; ?>" data-vendor="<?php echo $payTypes['type']['yun']; ?>" class="btn-pay union">
                                                    <img class="gou" src="./img/pay/gou.png" />
                                                    <img class="img" src="./img/pay/yun-pay-active.png" />
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <div class="btm-txt">
                                            <span>其他在线支付</span>
                                            <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="qrcode-pay">
                            <!-- <div class="part1 pps">
                                <div class="wp-1 wshow">
                                    <div class="transfer-pay-left">
                                        <div style="margin-bottom:15px;border-left:4px solid white;color:white;padding-left:5px;height:16px;line-height:16px;">请输入会员账号</div>
                                        <div class="input-row">
                                            <input type="text" placeholder="请输入需要存款的会员账号" id="wep-account" class="input" style="width:250px;text-indent:10px;">
                                        </div>
                                    </div>

                                    <img class="transfer-pay-right" src="./img/pay/transfer-bg.jpg" />

                                    <div class="qr-pay-bottom" style="margin-top:0;">
                                        <div class="btm-txt">
                                            <span style="color:red;">温馨提示：</span>
                                            <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                        </div>
                                        <img class="step-txt-img" src="./img/pay/step-txt.jpg" />
                                    </div>
                                    <input type="button" class="btn-submit" onclick="wechatPayNext()" value="下一步" />
                                </div>
                                <div class="wp-2">
                                    <div class="wf-box">
                                        <div class="personal">
                                            <div class="input-row">
                                                <span>存款金额：</span>
                                                <input id="pesonal-money" type="text" class="input" name="account" placeholder="请确认您的存款金额">
                                            </div>
                                            <div class="input-row">
                                                <span>微信昵称：</span>
                                                <input id="pesonal-name" type="text" class="input" name="account" placeholder="请输入您的微信账户昵称">
                                            </div>
                                        </div>
                                        <div class="merchant" style="padding-top:40px;">
                                            <div class="input-row">
                                                <span>商户单号：</span>
                                                <input id="merchant-order" maxlength="7" type="text" class="input" name="account" placeholder="请输入商户单号后七位">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="qr-box">
                                        <div class="step">
                                            <img class="step-img" src="./img/pay/pay-tip-new.png" />
                                            <img id="wtcode-img" class="qr-img" src="" />
                                            <div id="wtcode"></div>
                                        </div>
                                    </div>

                                    <div class="qr-pay-bottom" style="padding-bottom:47px;">
                                        <div class="btm-txt">
                                            <span style="color:red;">温馨提示：</span>
                                            <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                        </div>

                                        <div class="step-txt">
                                            <div class="step-txt1">1.打开手机微信，请点击右上角"+",在弹出下拉框中选择“扫一扫”。</div>
                                            <div class="step-txt2">2.将扫描窗口对准界面中的付款二维码。</div>
                                            <div class="step-txt3">3.输入充值金额,点击<span style="color:red;">"添加备注"</span>并填写您的澳亚国际<span style="color:red;">"会员账号"</span>再进行充值。</div>
                                        </div>
                                    </div>
                                    <p class="imp-tips" style="right:155px;">转账成功后，输入存款信息，点击“提交”按钮。</p>
                                    <input type="button" class="btn-submit" onclick="wechatPaySubmit()" value="提交" />
                                </div>
                            </div>

                            <div class="part2">
                                <div class="p2box">
                                    <div class="p2box-step">
                                        <img class="p2boxstep-img" src="./img/pay/phone.png" />
                                        <img class="personl-img" src="" />
                                        <img class="transfer-pay-right" src="./img/pay/transfer-bg.jpg" />
                                    </div>
                                </div>

                                <div class="qr-pay-bottom" style="padding-bottom:47px;">
                                    <div class="btm-txt">
                                        <span style="color:red;">温馨提示：</span>
                                        <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                    </div>

                                    <div class="step-txt">
                                        <div class="step-txt1">1.打开手机微信，请点击右上角"+",在弹出下拉框中选择“扫一扫”。</div>
                                        <div class="step-txt2">2.将扫描窗口对准界面中的二维码。</div>
                                        <div class="step-txt3">3.添加专员微信好友，通过微信转账进行入款。</div>
                                    </div>
                                </div>
                                <input type="button" class="btn-submit" onclick=" backWf();" value="完成" />
                            </div> -->
                            <?php $sAvailable = date('H:i:s');?>
                            <?php if($sAvailable >= '09:00:00' && $sAvailable <= '22:00:00' ){?>
                            <div class="online-pay-form">
                                <form method="post" action="./wmpay/payment.php" id="wechat_pay_form">
                                    <input type="hidden" name="companyNo" value="<?php echo COMPANY_NO; ?>">
                                    <input type="hidden" name="vendorType" id="vendor_type" value="48">
                                    <input type="hidden" name="device" value="1">
                                    <input type="hidden" id="token" name="token" value="<?php echo $_token; ?>">
                                    <input type="hidden" name="pay_type" id="pay_type" value="0101" />
                                    <input type="hidden" name="paytype2" id="paytype2" value="67" />


                                    <div class="online-pay-left">
                                        <div class="input-row">
                                            <span>会员账号：</span>
                                            <input type="text" class="input" name="account" placeholder="请输入需要存款的会员账号">
                                        </div>
                                        <div style="color:#FFF;font-size: 18px;">
                                            <style>
                                                .moneyItem{
                                                    display: flex;align-items: center;float: right; margin-left: 12px;
                                                }
                                                .moneyItem input{ margin-left: 6px;width:15px;height:15px; }
                                            </style>
                                            <span>存款金额：</span>
                                            <span class="moneyItem">
                                                200元 <input type="radio" value="200" name="money">
                                            </span>
                                            <span class="moneyItem">
                                                100元 <input type="radio" value="100" name="money" checked>&nbsp;&nbsp;
                                            </span>
                                            <span class="moneyItem">
                                               50元 <input type="radio" value="50" name="money">&nbsp;&nbsp;
                                           </span>
                                            <span class="moneyItem">
                                                30元 <input type="radio" value="30" name="money">&nbsp;&nbsp;
                                            </span>
                                            <span class="moneyItem">
                                                 20元 <input type="radio" value="20" name="money">&nbsp;&nbsp;
                                            </span>
                                            <span class="moneyItem">
                                                10元 <input type="radio" value="10" name="money">&nbsp;&nbsp;
                                            </span>
                                        </div>
                                    </div>
                                    <style>
                                        .no-pointer{
                                            background: #ccc;
                                            pointer-events: none;
                                            outline: none;
                                        }
                                    </style>
                                    <div class="online-pay-bottom" style="margin-top:100px;">
                                        <div class="btn-wraper">
                                            <input type="button" class="btn-submit marginLeft0" onclick="doSubmit('wechat_pay_form')" value="下一步" />
                                        </div>
                                        <div class="btm-txt">
                                            <span>其他在线支付</span>
                                            <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php } else echo "<div style='margin-top:100px;color:#fff;text-align:center;'>微信支付维护时间： 晚上10点至隔日早上9点！</div>";?>
                        </div>

                        <div class="transfer-pay">
                            <div class="p1 ps">
                                <div class="transfer-pay-left">
                                    <div style="margin-bottom:15px;border-left:4px solid white;color:white;padding-left:5px;height:16px;line-height:16px;">请输入会员账号</div>
                                    <div class="input-row">
                                        <input type="text" placeholder="请输入需要存款的会员账号" id="tf-account" class="input" style="width:250px;text-indent:10px;">
                                    </div>
                                </div>

                                <img class="transfer-pay-right" src="./img/pay/transfer-bg.jpg" />
                            </div>
                            <div class="p2">
                                <div class="account-info">
                                <input id="bankId1" type="hidden" value="" />
                                    <div class="col">
                                        <div>
                                            <span>开户银行：</span>
                                            <span id="bank-name"></span>
                                        </div>
                                        <div>
                                            <span>银行卡号：</span>
                                            <span id="cardno"></span>
                                            <a class="copy" style="margin-left: 20px; z-index: 30000;" id="d_clip_button">复制</a>
                                        </div>
                                        <span>银行户名：
                                            <span id="account-name"></span>
                                        </span>
                                    </div>
                                    <div class="col" style="float:right;margin-right:50px;">
                                        <div class="input-row" style="border-radius:3px;margin:5px 0 20px 0;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="tf-money" class="input" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')">
                                        </div>
                                        <div class="input-row" style="border-radius:3px;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="tf-name" class="input" placeholder="请确认您的存款人姓名">
                                        </div>
                                    </div>
                                </div>
                                <p class="ac-tip">※复制账号户名至银行界面转账</p>
                            </div>
                            <div class="transfer-pay-bottom">
                                <div class="btn-wraper">
                                    <input type="button" class="btn-submit transfer-button" onclick="showTransferPanel()" value="下一步" />
                                </div>
                                <div class="btm-txt" style="margin-left:100px;">
                                    <span style="color:red;">温馨提示：</span>
                                    <span> ※ 为了不影响您的体验游戏，建议选择实时到账。</span>
                                </div>
                                <p class="imp-tips">转账成功后，输入存款信息，点击“提交”按钮。</p>
                            </div>
                        </div>

                        <div class="ali-pay">
                            <div class="ali-step1 show">
                                <div class="alipay-form">
                                    <div class="alipay-row" style="margin-top:40px;">
                                        <span>会员账号：</span>
                                        <input type="text" id="alipay-account" placeholder="请输入需要存款的会员账号" />
                                    </div>
                                    <div class="alipay-row">
                                        <span>付款方式：</span>
                                        <select id="alipayType" onchange="alipayTypeChange(this)">
                                            <option value="0" selected="selected">请选择付款方式</option>
                                        </select>
                                    </div>
                                    <div class="alipay-row bank-select-box">
                                        <span>付款银行：</span>
                                        <select id="bank-selector">
                                            <option value="0">请选择银行</option>
                                        </select>
                                    </div>
                                </div>

                                <img class="transfer-pay-right" src="./img/pay/transfer-bg.jpg" />
                                <div class="btn-wraper">
                                    <input type="button" class="btn-submit" onclick="alipayNext()" value="下一步" />
                                </div>
                                <div class="btm-txt">
                                    <span style="color:red;">温馨提示:</span>
                                    <span>请您存款后，提交存款信息，存款5-10分钟到账，建议您使用在线支付存款。
                                        <br/> 支付宝在 22:30 - 1:00 是银行结算时间，存款会延迟到账，为了您的存款能及时到账，这个时间段请用在线存款。</span>
                                </div>
                            </div>


                            <div class="alipay-bank-transfer">
                                <div class="account-info" style="margin-top:55px;">
                                    <input id="bankId" type="hidden" value="" />
                                    <div class="col">
                                        <div>
                                            <span>开户银行：</span>
                                            <span id="ali-bank-name"></span>
                                        </div>
                                        <div>
                                            <span>银行卡号：</span>
                                            <span id="ali-cardno"></span>
                                            <a class="copy" style="margin-left: 20px; z-index: 30000;" id="ali_clip_button">复制</a>
                                        </div>
                                        <span>银行户名：
                                            <span id="ali-account-name"></span>
                                        </span>
                                    </div>
                                    <div class="col" style="float:right;margin-right:50px;">
                                        <div class="input-row" style="border-radius:3px;margin:5px 0 20px 0;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="ab-money" class="input" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')">
                                        </div>
                                        <div class="input-row" style="border-radius:3px;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="ab-name" class="input" placeholder="请确认您的存款人姓名">
                                        </div>
                                    </div>
                                </div>
                                <p class="ac-tip">※复制账号户名至银行界面转账</p>
                                <div class="transfer-pay-bottom">
                                    <div class="btm-txt" style="left:85px;bottom:80px;">
                                        <span style="color:red;">温馨提示：</span>
                                        <span> ※ 为了不影响您的体验游戏，建议选择实时到账。</span>
                                    </div>
                                    <p class="imp-tips" style="display:block;right:260px;">转账成功后，输入存款信息，点击“提交”按钮。</p>
                                    <input type="button" class="btn-cancel" onclick="back()" value="上一步" />
                                    <input type="button" class="btn-submit" onclick="alipaySubmit(1)" value="提交" />
                                </div>
                            </div>

                            <div class="alipay-account">
                                <div class="account-info" style="margin-top:55px;padding-top:10px;">
                                    <div class="col">
                                        <img class="ali-qrcode" src="" />
                                        <div class="ali-tip">
                                            <h3 style="color:red;margin-bottom:10px;">操作提示：</h3>
                                            <p>1. 打开支付宝，点击“扫一扫”；</p>
                                            <p>2. 对准二维码进行扫描；</p>
                                            <p>3. 输入充值金额进行充值。</p>
                                        </div>
                                    </div>
                                    <div class="col" style="float:right;margin-right:50px;">
                                        <p style="color:#D76D6D;margin-bottom:10px;">转账后填写下方信息确保快速到账</p>
                                        <div class="input-row" style="border-radius:3px;margin:5px 0 15px 0;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="ac-money" class="input" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')">
                                        </div>
                                        <div class="input-row" style="border-radius:3px;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="ac-name" class="input" placeholder="请输入您的支付宝真实姓名">
                                        </div>
                                    </div>
                                </div>
                                <div class="transfer-pay-bottom">

                                    <input type="button" class="btn-submit" onclick="alipaySubmit(2)" value="提交" />
                                    <input type="button" class="btn-cancel" onclick="back()" value="上一步" />
                                </div>
                            </div>

                            <div class="alipay-qrcode">
                                <div class="account-info" style="margin-top:55px;padding-top:10px;">
                                    <div class="col">
                                        <div id="mcode" class="ali-qrcode"></div>
                                        <div class="ali-tip">
                                            <h3 style="color:red;margin-bottom:10px;">操作提示：</h3>
                                            <p>1. 打开支付宝，点击“扫一扫”；</p>
                                            <p>2. 对准二维码进行扫描；</p>
                                            <p>3. 输入充值金额进行充值。</p>
                                        </div>
                                    </div>
                                    <div class="col" style="float:right;margin-right:50px;">
                                        <p style="color:#D76D6D;margin-bottom:10px;">转账后填写下方信息确保快速到账</p>
                                        <div class="input-row" style="border-radius:3px;margin:5px 0 15px 0;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="aliqr-money" class="input" placeholder="请确认您的存款金额" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"
                                                onafterpaste="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')">
                                        </div>
                                        <div class="input-row" style="border-radius:3px;">
                                            <input style="width:250px;text-indent:10px;" type="text" id="order-no" maxlength="7" class="input" placeholder="请输入交易单号后七位">
                                        </div>
                                    </div>
                                </div>
                                <div class="transfer-pay-bottom">
                                    <input type="button" class="btn-cancel" onclick="back()" value="上一步" />
                                    <input type="button" class="btn-submit" onclick="alipaySubmit(3)" value="提交" />
                                </div>
                            </div>
                        </div>

                    <!--
                        <div class="qq-pay">
                            <div class="part1 pps">
                                <div class="qp-1 wshow">
                                    <div class="transfer-pay-left">
                                        <div style="margin-bottom:15px;border-left:4px solid white;color:white;padding-left:5px;height:16px;line-height:16px;">请输入会员账号</div>
                                        <div class="input-row">
                                            <input type="text" placeholder="请输入需要存款的会员账号" id="qq-account" class="input" style="width:250px;text-indent:10px;">
                                        </div>
                                    </div>

                                    <img class="transfer-pay-right" src="./img/pay/transfer-bg.jpg" />

                                    <div class="qr-pay-bottom" style="margin-top:0;">
                                        <div class="btm-txt">
                                            <span style="color:red;">温馨提示：</span>
                                            <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                        </div>
                                    </div>
                                    <input type="button" class="btn-submit" onclick="qqPayNext()" value="下一步" />
                                </div>
                                <div class="qp-2">
                                    <div class="wf-box">
                                        <div class="qq-personal">
                                            <div class="input-row">
                                                <span style="width:110px;">存款金额：</span>
                                                <input style="width:220px;" id="qq-pesonal-money" type="text" class="input" name="account" placeholder="请确认您的存款金额">
                                            </div>
                                            <div class="input-row">
                                                <span style="width:110px;">QQ账户昵称：</span>
                                                <input style="width:220px;" id="qq-pesonal-name" type="text" class="input" name="account" placeholder="请输入您的QQ账户昵称">
                                            </div>
                                        </div>
                                        <div class="merchant" style="padding-top:40px;">
                                            <div class="input-row">
                                                <span>商户单号：</span>
                                                <input id="qq-merchant-order" maxlength="7" type="text" class="input" name="account" placeholder="请输入商户单号后七位">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="qr-box">
                                        <div class="step">
                                            <img class="step-img" src="./img/pay/pay-tip-new-qq.png" />
                                            <img id="qqcode-img" class="qq-qr-img" src="" />
                                            <div id="qqcode"></div>
                                        </div>
                                    </div>

                                    <div class="qr-pay-bottom" style="padding-bottom:47px;">
                                        <div class="btm-txt">
                                            <span style="color:red;">温馨提示：</span>
                                            <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                        </div>

                                        <div class="step-txt">
                                            <div class="step-txt1">1.打开手机QQ，请点击右上角"+",在弹出下拉框中选择“扫一扫”。</div>
                                            <div class="step-txt2">2.将扫描窗口对准界面中的付款二维码。</div>
                                            <div class="step-txt3">3.输入充值金额,点击<span style="color:red;">"转账留言"</span>并填写您的澳亚国际<span style="color:red;">"会员账号"</span>再进行充值。</div>
                                        </div>
                                    </div>
                                    <p class="imp-tips" style="right:155px;">转账成功后，输入存款信息，点击“提交”按钮。</p>
                                    <input type="button" class="btn-submit" onclick="qqPaySubmit()" value="提交" />
                                </div>
                            </div>
                        </div>
                    -->
                    <!-- 云闪付 -->
                    <div class="qq-pay">
                        <div class="part1 pps">
                            <div class="qp-1 wshow">
                                <div class="transfer-pay-left">
                                    <div style="margin-bottom:15px;border-left:4px solid white;color:white;padding-left:5px;height:16px;line-height:16px;">请输入会员账号</div>
                                    <div class="input-row">
                                        <input type="text" placeholder="请输入需要存款的会员账号" id="qq-account" class="input" style="width:250px;text-indent:10px;">
                                    </div>
                                </div>

                                <img class="transfer-pay-right" src="./img/pay/transfer-bg.jpg" />

                                <div class="btn-wraper">
                                    <input type="button" class="btn-submit" onclick="qqPayNext()" value="下一步" />
                                </div>
                                <div class="yun_span_1"><span  style="font-size: 18px; color: red; font-weight: bold ; display: inline-block;">温馨提示</span><span style=" color: red; font-weight: bold ; font-size: 18px; display: inline-block;"> : <a href="https://static.95516.com/static/product/detail_210.html" style="color:#1200ff;" target="_blank">点击下载 云闪付</a></span></div>

                                <div class="qr-pay-bottom" style="margin-top:0;">
                                    <div class="btm-txt">
                                        <span style="color:red;">温馨提示：</span>
                                        <span> ※ 转帐完成后请保留单据作为核对证明。</span>
                                    </div>
                                </div>
                            </div>
                            <div class="qp-2">
                                <div class="wf-box">
                                    <div class="qq-personal">
                                        <div class="input-row">
                                            <span style="width:110px;">存款金额：</span>
                                            <input style="width:220px;" id="qq-pesonal-money" type="text" class="input" name="account" placeholder="请确认您的存款金额">
                                        </div>
                                        <div class="input-row">
                                            <span style="width:110px;">账户姓名：</span>
                                            <input style="width:220px;" id="qq-pesonal-name" type="text" class="input" name="account" placeholder="请输入云闪付绑定卡真实姓名">
                                        </div>
                                    </div>
                                    <div class="merchant" style="padding-top:40px;">
                                        <div class="input-row">
                                            <span>商户单号：</span>
                                            <input id="qq-merchant-order" maxlength="7" type="text" class="input" name="account" placeholder="请输入商户单号后七位">
                                        </div>
                                    </div>
                                </div>
                                <div class="qr-box">
                                    <div class="step">
                                        <!--<img class="step-img" src="./img/pay/pay-tip-new-qq.png" />-->
                                        <img id="qqcode-img" class="qq-qr-img" src="" />
                                        <div id="qqcode"></div>
                                    </div>
                                </div>

                                <div class="qr-pay-bottom" style="padding-bottom:47px;">
                                    <div class="btm-txt">

                                        <span> <input type="button" class="btn-submit_yun" onclick="qqPaySubmit()" value="提交" /></span>
                                        <span><span  style="font-size: 18px; margin-top: 15px; color: red; font-weight:bold ; display: inline-block;">温馨提示</span><span style=" color: red; font-weight:bold ; font-size: 18px; margin-top: 15px; display: inline-block;"> : <a href="https://static.95516.com/static/product/detail_210.html" style="color:#1200ff;" target="_blank">点击下载 云闪付</a></span></span>
                                    </div>
                                    <div class="step-txt">
                                        <div class="step-txt1">1.打开云闪付，请点击左上角“扫一扫”</div>
                                        <div class="step-txt2">2.将扫描窗口对准界面中的收款二维码。</div>
                                        <div class="step-txt3">3.支付成功后请填写云闪付绑定卡真实姓名后点击下方提交即可</div>
                                    </div>

                                </div>
                                <p class="imp-tips" style="right:155px;">转账成功后，输入存款信息，点击“提交”按钮。</p>

                            </div>
                        </div>
                    </div>

                    </div>

                    <div id="mask">
                        <span class="alert_words green">错误信息</span>
                    </div>
                    <script type="text/javascript" src="./js/jquery-1.11.0.min.js"></script>
                    <script type="text/javascript" src="./js/zeroClipboard.js"></script>
                    <script type="text/javascript" src="./js/jQuery.browser.mobile.js"></script>
                    <script type="text/javascript" src="./js/qrcode.min.js"></script>
                    <script type="text/javascript" src="./js/pay.js?v=5646234567487878"></script>
                    <script type="text/javascript">
                        if (jQuery.browser.mobile) {
                            window.location = "./mobile/";
                        }
                    </script>
                    <script>
                    var _hmt = _hmt || [];
                    (function() {
                      var hm = document.createElement("script");
                      hm.src = "https://hm.baidu.com/hm.js?66b60f37dcdd95821feeb6962a9ab34a";
                      var s = document.getElementsByTagName("script")[0];
                      s.parentNode.insertBefore(hm, s);
                    })();
                    </script>

    </body>

    </html>
