// 用户信息
var userInfo
// 充值方式列表
var rechargeTypeList
// 当前激活充值方式
var activatedRechargeType
// 充值渠道列表
var rechargeChannelList
// 当前激活充值渠道
var activatedRechargeChannel
// 访问终端
var isMobile = /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)
// 二维码信息
var qrInfo
// 接口等待中
var isLoading = false

function changeActiveNav(left, width) {
    $(".item_actived").css({
        "width": width,
        "left": left,
        "display": "inline-block"
    })
}

function hideAvtive() {
    $(".item_actived").css({
        "left": 0,
        "display": "none"
    })
}

/**
 * 打开dialog对话框
 * @param {String} type login-form | confirm-online | confirm-offline | success
 * @param {String} txt 弹出框提示文字
 */
function openDialog(type, txt) {
    $(".dialog-content>div").hide()
    $(".err-message").css("opacity", "0")
    if (type === "login-form") {
        $(".login-form").show()
    } else if (type === "confirm-online") {
        $(".confirm").show()
    } else if (type === "confirm-offline") {
        $(".offline").show()
    } else if(type === "success"){
        $(".success-confirm").show()
    }

    if (type === "login-form") {
        $(".dialog-title-main").text("请输入会员账号")
        $(".dialog-title-sub").text("Please enter your membership account")
    } else if (type === "confirm-online") {
        $(".dialog-title-main").text("澳亚快捷充值中心")
        $(".dialog-title-sub").text("AoYa fast recharge center")
        if (activatedRechargeChannel.deposit_range.isRange) {
            // 输入金额范围
            var conHtml = '<div class="form-item">' +
                '<label>会员账号：</label>' +
                '<input type="text" name="con-account" disabled class="input disabled"/>' +
                '</div>' +
                '<div class="form-item">' +
                '<label>充值渠道：</label>' +
                '<input type="text" name="con-channel" disabled class="input disabled"/>' +
                '</div>' +
                '<div class="form-item money">' +
                '<label>充值金额：</label>' +
                '<input type="text" class="input" name="recharge-money" placeholder="请输入充值金额"/>' +
                '</div>'
            $(".confirm-form").html(conHtml)
            $("input[name='con-account']").val(userInfo.username)
            $("input[name='con-channel']").val(activatedRechargeChannel.display_name + "(限额" + activatedRechargeChannel.deposit_range.data[0] + "-" + activatedRechargeChannel.deposit_range.data[1] + "元）")
        } else {
            // 输入固定金额
            var liList = ""
            for (var i = 0; i < activatedRechargeChannel.deposit_range.data.length; i++) {
                liList += '<li class="money-item" data-money="' + activatedRechargeChannel.deposit_range.data[i] + '">' + activatedRechargeChannel.deposit_range.data[i] + '</li>'
            }

            var conHtml = '<div class="form-item">' +
                '<label>会员账号：</label>' +
                '<input type="text" name="con-account" disabled class="input disabled"/>' +
                '</div>' +
                '<div class="form-item">' +
                '<label>充值渠道：</label>' +
                '<input type="text" name="con-channel" disabled class="input disabled"/>' +
                '</div>' +
                '<div class="form-item money">' +
                '<label>充值金额：</label>' +
                '<ul class="money-list">' + liList + '</ul>'
            '</div>'
            $(".confirm-form").html(conHtml)

            $("input[name='con-account']").val(userInfo.username)
            $("input[name='con-channel']").val(activatedRechargeChannel.display_name)

            $(".confirm-form .money-item:first").addClass("money-item_actived")
            $(".confirm-form .money-item").on("click", function () {
                $(".confirm-form .money-item").removeClass("money-item_actived")
                $(this).addClass("money-item_actived")
            })
        }
    } else if (type === "confirm-offline") {
        $(".dialog-title-main").text("澳亚快捷充值中心")
        $(".dialog-title-sub").text("AoYa fast recharge center")
        if(activatedRechargeChannel.display_name.indexOf("云闪付") > -1){
            if (!isLoading) {
                isLoading = true
                $(".offline").html("")
                $.ajax({
                    type: "GET",
                    url: "/api/getQrcode.php?type=4",
                    success: function (res) {
                        isLoading = false
                        res = JSON.parse(res)
                        qrInfo = res.data
                        if (!res.status) {
                            var conHtml ='<div class="yunWaper">'+
                                        '<div class="qr-form">'+
                                            '<div class="form-item">' +
                                                '<label>会员账号：</label>' +
                                                '<input type="text" name="con-account" disabled class="input disabled"/>' +
                                            '</div>' +
                                            '<div class="form-item">' +
                                                '<label>充值渠道：</label>' +
                                                '<input type="text" name="con-channel" disabled class="input disabled"/>' +
                                            '</div>' +
                                            '<div class="form-item money">' +
                                                '<label>充值金额：</label>' +
                                                '<input type="text" class="input" name="recharge-money" placeholder="请输入充值金额"/>' +
                                            '</div>'+
                                            '<div class="form-item money">' +
                                                '<label>存款姓名：</label>' +
                                                '<input type="text" class="input" name="recharge-name" placeholder="请输入存款姓名"/>' +
                                            '</div>'+
                                            '<div>扫码支付完成，请点击确认按钮提交</div>'+
                                        '</div>'+
                                        '<div class="qr-waper">'+
                                            '<img src="'+ qrInfo.url +'"/>'+
                                        '</div>'+
                                        '</div>'+
                                        '<div class="err-message">&nbsp;</div>' +
                                        '<div class="btn-waper">'+
                                            '<button class="btn sub-qrInfo">确认提交</button>' +
                                            '<button class="btn close">关闭窗口</button>' +
                                        '</div>'
                            $(".offline").html(conHtml)
                            $("input[name='con-account']").val(userInfo.username)
                            $("input[name='con-channel']").val(activatedRechargeChannel.display_name)
                        } else {
                            return $(".err-message").html(res.msg).css("opacity", "1")
                        }
                    },
                    error: function (err) {
                        isLoading = false
                    }
                })
            }
        }else{
            if (activatedRechargeChannel.position.offLineCategory === "scanCode") {
                // 下线扫码
                var stepInfo
                var payType
                if (activatedRechargeType.name.indexOf("微信") > -1) {
                    $(".dialog-title-main").text("微信扫码支付")
                    $(".dialog-title-sub").text("Wechat Code Payment")
                    payType = "微信"
                } else if (activatedRechargeType.name.indexOf("支付宝") > -1) {
                    $(".dialog-title-main").text("支付宝扫码支付")
                    $(".dialog-title-sub").text("Alipay Code Payment")
                    payType = "支付宝"
                }
                qrInfo = activatedRechargeChannel.position.data
                stepInfo = "<span>1.打开手机"+ payType +"</span>"+
                            "<span>2.点击扫一扫</span>"+
                            "<span>3.对准屏幕二维码，扫码进入支付</span>"+
                            "<span>4.支付完成</span>"
                            var conHtml = '<div class="scan-code">'+
                            '<div class="scan-code-waper">'+
                                '<div class="qr-form">'+
                                    '<div class="form-item">' +
                                        '<label>会员账号：</label>' +
                                        '<input type="text" name="con-account" disabled class="input disabled"/>' +
                                    '</div>' +
                                    '<div class="form-item">' +
                                        '<label>充值渠道：</label>' +
                                        '<input type="text" name="con-channel" disabled class="input disabled"/>' +
                                    '</div>' +
                                    '<div class="form-item money">' +
                                        '<label>充值金额：</label>' +
                                        '<input type="text" class="input" name="recharge-money" placeholder="请输入充值金额"/>' +
                                    '</div>'+
                                    '<div class="form-item money">' +
                                        '<label>支付昵称：</label>' +
                                        '<input type="text" class="input" name="recharge-name" placeholder="请输入'+ payType +'昵称"/>' +
                                    '</div>'+
                                '</div>'+
                                '<div class="img-waper">' +
                                    '<div class="img"><img style="width: 100%" src="' + activatedRechargeChannel.position.data.url + '"/></div>' +
                                '</div>'+
                            '</div>'+
                            '<div class="img-msg">'+ stepInfo +'</div>' +
                            '<div class="err-message">&nbsp;</div>' +
                            '<div class="btn-waper">'+
                                '<button class="btn sub-qrInfo">确认提交</button>' +
                                '<button class="btn close">关闭窗口</button>' +
                            '</div>'+
                            '</div>'

                $(".offline").html(conHtml)
                $("input[name='con-account']").val(userInfo.username)
                $("input[name='con-channel']").val(activatedRechargeChannel.display_name)
                // if (!isLoading) {
                //     isLoading = true
                //     $(".offline").html("")
                //     $.ajax({
                //         type: "GET",
                //         url: "/api/getQrcode.php?type=4",
                //         success: function (res) {
                //             isLoading = false
                //             res = JSON.parse(res)
                //             qrInfo = res.data
                //             if (!res.status) {
                //                 var conHtml = '<div class="scan-code">'+
                //                             '<div class="scan-code-waper">'+
                //                                 '<div class="qr-form">'+
                //                                     '<div class="form-item">' +
                //                                         '<label>会员账号：</label>' +
                //                                         '<input type="text" name="con-account" disabled class="input disabled"/>' +
                //                                     '</div>' +
                //                                     '<div class="form-item">' +
                //                                         '<label>充值渠道：</label>' +
                //                                         '<input type="text" name="con-channel" disabled class="input disabled"/>' +
                //                                     '</div>' +
                //                                     '<div class="form-item money">' +
                //                                         '<label>充值金额：</label>' +
                //                                         '<input type="text" class="input" name="recharge-money" placeholder="请输入充值金额"/>' +
                //                                     '</div>'+
                //                                     '<div class="form-item money">' +
                //                                         '<label>账号：</label>' +
                //                                         '<input type="text" class="input" name="recharge-name" placeholder="请输入'+ payType +'账号"/>' +
                //                                     '</div>'+
                //                                 '</div>'+
                //                                 '<div class="img-waper">' +
                //                                     '<div class="img"><img style="width: 100%" src="' + qrInfo.url + '"/></div>' +
                //                                 '</div>'+
                //                             '</div>'+
                //                             '<div class="img-msg">'+ stepInfo +'</div>' +
                //                             '<div class="err-message">&nbsp;</div>' +
                //                             '<div class="btn-waper">'+
                //                                 '<button class="btn sub-qrInfo">确认提交</button>' +
                //                                 '<button class="btn close back">关闭窗口</button>' +
                //                             '</div>'+
                //                             '</div>'

                //                 $(".offline").html(conHtml)
                //                 $("input[name='con-account']").val(userInfo.username)
                //                 $("input[name='con-channel']").val(activatedRechargeChannel.display_name)
                //             } else {
                //                 return $(".err-message").html(res.msg).css("opacity", "1")
                //             }
                //         },
                //         error: function (err) {
                //             isLoading = false
                //         }
                //     })
                // }
            } else if (activatedRechargeChannel.position.offLineCategory === "addFriend") {
                // 下线加好友
                var stepInfo
                if (activatedRechargeType.name.indexOf("微信") > -1) {
                    $(".dialog-title-main").text("微信扫码加好友")
                    $(".dialog-title-sub").text("Wechat Code Add Friend")
                    stepInfo = "<div>1.打开手机微信</div>"+
                                "<div>2.点击屏幕右上角加好，进入添加朋友</div>"+
                                "<div>3.点击扫一扫，对准屏幕二维码识别</div>"+
                                "<div>4.添加完成</div>"
                } else if (activatedRechargeType.name.indexOf("支付宝") > -1) {
                    $(".dialog-title-main").text("支付宝扫码加好友")
                    $(".dialog-title-sub").text("Alipay Code Add Friend")
                    stepInfo = "<div>1.打开手机支付宝</div>"+
                                "<div>2.点击屏幕右上角加好，进入添加朋友</div>"+
                                "<div>3.点击扫一扫，对准屏幕二维码识别</div>"+
                                "<div>4.添加完成</div>"
                }
                var conHtml = '<div class="img-waper">' +
                    '<div class="img"><img style="width: 100%" src="' + activatedRechargeChannel.position.data + '"/></div>' +
                    '<div class="img-msg">'+ stepInfo +'</div>' +
                    '<button class="btn close">关闭窗口</button>' +
                    '</div>'
                $(".offline").html(conHtml)
            } else if (activatedRechargeChannel.position.offLineCategory === "transfer") {
                // 下线银行转账
                var conHtml = '<div class="bank-waper">' +
                    '<div class="bank-item">' +
                    '<label>开户银行:</label>' +
                    '<div class="input disabled">' + activatedRechargeChannel.position.data.bankName + '</div>' +
                    '</div>' +
                    '<div class="bank-item">' +
                    '<label>银行卡号:</label>' +
                    '<div class="input disabled">' + activatedRechargeChannel.position.data.bankCard + '</div>' +
                    '</div>' +
                    '<div class="bank-item">' +
                    '<label>收款人:</label>' +
                    '<div class="input disabled">' + activatedRechargeChannel.position.data.bankAccount + '</div>' +
                    '</div>' +
                    '<div class="bank-item">' +
                    '<label>存款金额:</label>' +
                    '<input class="input" type="text" name="transferMoney"/>' +
                    '</div>' +
                    '<div class="bank-item">' +
                    '<label>存款姓名:</label>' +
                    '<input class="input" type="text" name="transferName"/>' +
                    '</div>' +
                    '</div>' +
                    '<div class="err-message">&nbsp;</div>' +
                    '<div class="btn-waper">' +
                    '<button class="btn sub-transfer">确认</button>' +
                    '<button class="btn close">关闭窗口</button>' +
                    '</div>'
                $(".offline").html(conHtml)

                $(".sub-transfer").on("click", transfer)
            }
        }

    } else if(type === "success"){
        $(".dialog-title-main").text("提交成功")
        $(".dialog-title-sub").text("Submitted successfully")
        $(".success-confirm .msg-success").html(txt)
    }
    $(".page").css("filter", "blur(4px)")
    $(".dialog").show()
}



/**
 * 提交线下转账信息
 */
function transfer() {
    if (activatedRechargeChannel.position.offLineCategory === "transfer") {
        var transferMoney = $("input[name='transferMoney']").val()
        var transferName = $("input[name='transferName']").val()
        if (!transferMoney || isNaN(transferMoney)) {
            return $(".err-message").html("*请输入正确的存款金额!").css("opacity", "1")
        }
        if (!transferName || /[^\u4e00-\u9fa5]/.test(transferName)) {
            return $(".err-message").html("*请输入正确的存款姓名!").css("opacity", "1")
        }
        var params = {
            account: userInfo.username,
            amount: transferMoney,
            depositor: transferName,
            card_id: activatedRechargeChannel.position.data.id
        }
        if (!isLoading) {
            isLoading = true
            $.ajax({
                type: "POST",
                url: "/api/addBankTransfer.php",
                data: 'account=' + userInfo.username + "&amount=" + transferMoney + '&depositor=' + transferName + "&card_id=" + activatedRechargeChannel.position.data.id,
                success: function (res) {
                    isLoading = false
                    res = JSON.parse(res)
                    if (!res.status) {
                        openDialog("success", "您的转账信息已经提交，稍后请前往个人中心查询。")
                    } else {
                        return $(".err-message").html(res.msg).css("opacity", "1")
                    }
                },
                error: function (err) {
                    isLoading = false
                }
            })
        }
    }
}

/**
 * 关闭dialog对话框
 */
function closeDialog() {
    $(".page").css("filter", "blur(0)")
    $(".dialog").hide()
}

$(".dialog").delegate(".close", "click", function () {
    closeDialog()
})

$(".dialog").delegate(".back", "click", function () {
    location.href = "https://ay210.com/"
})

/**
 * 绑定用户登录事件
 */
$(".btn-login").on("click", function () {
    var userInput = $("input[name='account']").val()
    if (!userInput) {
        return $(".err-message").html("*请先输入会员账号").css("opacity", "1")
    }
    userInfo = {
        username: userInput
    }
    $(".user-info").text(userInput).attr("title", userInput)
    getRechargeType(userInput)
})

$("input[name='account']").bind("keypress", function(event){
    if(event.keyCode == "13"){
        $(".btn-login").click()
    }
})

$(".dialog").delegate("input", "focus", function () {
    $(".err-message").html("&nbsp;").css("opacity", "0")
})

/**
 * 绑定支付方式切换事件
 */
$(".pay-list").delegate(".pay-item", "click", function () {
    $(".pay-item").removeClass("pay-item_actived")
    $(this).addClass("pay-item_actived")
    var payTypeId = $(this).data("typeid")
    // 切换当前激活方式
    for (var i = 0; i < rechargeTypeList.length; i++) {
        if (rechargeTypeList[i].id == payTypeId) {
            activatedRechargeType = rechargeTypeList[i]
        }
    }
    getRechargeChannel(payTypeId)
})

/**
 * 绑定支付渠道切换事件
 */
$(".page").delegate(".channel-item", "click", function () {
    $(".channel-item").removeClass("channel-item_selected")
    $(this).addClass("channel-item_selected")
    var payChannelId = $(this).data("channelid")
    activatedRechargeChannel = rechargeChannelList.filter(item => item.id === payChannelId)[0]

    // // 移动端点击直接弹出dialog框
    // if (isMobile) {
    //     if (activatedRechargeChannel.position.type === 1) {
    //         // 线上充值
    //         openDialog("confirm-online")
    //     } else {
    //         // 线下充值
    //         openDialog("confirm-offline")
    //     }
    // }

    if (activatedRechargeChannel.position.type === 1) {
        // 线上充值
        openDialog("confirm-online")
    } else {
        // 线下充值
        openDialog("confirm-offline")
    }
})


/**
 * 绑定弹出充值对话框事件
 */
$(".next").on("click", function () {
    if (activatedRechargeChannel.position.type === 1) {
        // 线上充值
        openDialog("confirm-online")
    } else {
        // 线下充值
        openDialog("confirm-offline")
    }
})

/**
 * 绑定提交充值信息事件
 */
$(".sub-recharge").on("click", function () {
    if (activatedRechargeChannel.position.type === 1) {
        // 线上
        var rechargeMoney
        if (activatedRechargeChannel.deposit_range.isRange) {
            // 输入金额
            rechargeMoney = $("input[name='recharge-money']").val()
            var rangeMin = activatedRechargeChannel.deposit_range.data[0] * 1;
            var rangeMax = activatedRechargeChannel.deposit_range.data[activatedRechargeChannel.deposit_range.data.length - 1] * 1;
            if (rechargeMoney < rangeMin || rechargeMoney > rangeMax) {
                return $(".err-message").html("*金额与充值渠道限额不符，请重新填写金额或者重新选择充值渠道").css("opacity", "1")
            }
        } else {
            // 固定金额
            rechargeMoney = $(".money-item_actived").data("money")
        }
        var device
        if (isMobile) {
            device = 2
        } else {
            device = 1
        }
        if (!isLoading) {
            isLoading = true
            var temWidow = window.open()
            $.ajax({
                type: "POST",
                url: "/wmpay/doPayment.php",
                data: 'id=' + activatedRechargeChannel.id + "&member=" + userInfo.username + "&money=" + rechargeMoney + "&device=" + device,
                success: function (res) {
                    isLoading = false
                    res = JSON.parse(res)
                    if (!res.status) {
                        if (res.type === "http") {
                            temWidow.location.href = res.data
                        } else if (res.type === "html") {
                            temWidow.document.write(res.data)
                        }
                        openDialog("success", "订单已经生成，请在新页面中完成充值")
                    } else {
                        temWidow.close()
                        return $(".err-message").html(res.msg).css("opacity", "1")
                    }

                },
                error: function (err) {
                    isLoading = false
                    temWidow.close()
                }
            })
        }
    } else if (activatedRechargeChannel.position.type === 2) {
        // 线下
        // 银行转账
        alert("银行转账")
    }
})

/**
 * 提交扫码信息
 */
$(".dialog").delegate(".sub-qrInfo", "click", function (){
    var inputName = $("input[name='recharge-name']").val()
    var inputMoney = $("input[name='recharge-money']").val()
    if (!inputMoney || isNaN(inputMoney)) {
        return $(".err-message").html("*请输入正确的存款金额!").css("opacity", "1")
    }
    if (!inputName) {
        return $(".err-message").html("*请输入正确的存款账号!").css("opacity", "1")
    }
    if (!isLoading) {
        isLoading = true
        $.ajax({
            type: "POST",
            url: "/api/addQrcodeOrder.php",
            data: 'member=' + userInfo.username + "&type="+ activatedRechargeChannel.position.data.type + '&id=' + qrInfo.id + "&money=" + inputMoney + "&drawee=" + inputName,
            success: function (res) {
                isLoading = false
                res = JSON.parse(res)
                if (!res.status) {
                    openDialog("success", "您的充值信息已经提交，稍后请前往个人中心查询。")
                } else {
                    return $(".err-message").html(res.msg).css("opacity", "1")
                }
            },
            error: function (err) {
                isLoading = false
            }
        })
    }
})

$("input[name='recharge-money']").on("focus", function () {
    $(".err-message").css("opacity", "0")
})

/**
 * 获取充值类型
 */
function getRechargeType(account) {
    if (isMobile) {
        position = 2
    } else {
        position = 1
    }
    if (!isLoading) {
        isLoading = true
        $.ajax({
            type: "POST",
            url: "/api/getPaymentChannels.php",
            data: 'member=' + account + "&position=" + position,
            success: function (res) {
                isLoading = false
                res = JSON.parse(res)
                if (!res.status) {
                    if (!res.data.length) {
                        return $(".err-message").html("没有获取到可用充值方式").css("opacity", "1")
                    }
                    rechargeTypeList = []
                    // 过滤渠道为空的充值方式
                    for(var i = 0; i < res.data.length; i++){
                        if(res.data[i].channelList.length){
                            rechargeTypeList.push(res.data[i])
                        }
                    }

                    var liList = ''
                    if (isMobile) {
                        for (var i = 0; i < rechargeTypeList.length; i++) {
                            liList += '<li class="pay-item" data-typeId="' + rechargeTypeList[i].id + '"><div><span class="icon icon-' + rechargeTypeList[i].tag + '"></span><span class="pay-item-text">' +
                                rechargeTypeList[i].name + '</span></div><span class="icon icon-open"></span></li>'
                        }
                        $(".pay-list").html(liList)
                    } else {
                        for (var i = 0; i < rechargeTypeList.length; i++) {
                            liList += '<li class="pay-item" data-typeId="' + rechargeTypeList[i].id + '"><span class="icon icon-' + rechargeTypeList[i].tag + '"></span><span class="pay-item-text">' + rechargeTypeList[i].name + '</span></li>'
                        }
                        $(".pay-list").html(liList)
                    }
                    // 默认选择第一个充值方式
                    $(".pay-list .pay-item:nth-child(1)").addClass("pay-item_actived")
                    activatedRechargeType = rechargeTypeList[0]

                    if(!activatedRechargeType){
                        return $(".err-message").html("没有可用的充值渠道").css("opacity", "1")
                    }

                    // 获取支付渠道
                    getRechargeChannel(activatedRechargeType.id)

                    closeDialog()
                } else {
                    $(".err-message").html(res.msg).css("opacity", "1")
                }

            },
            error: function (err) {
                isLoading = false
            }
        })
    }
}

/**
 * 获取充值渠道
 * @param {Number} id   // 充值方式ID
 */
function getRechargeChannel(id) {
    $(".channel-waper").hide()
    $(".channel-no-list").removeClass("channel-no-list_show").addClass("channel-no-list_hide")
    $(".channel-loading").removeClass("channel-loading_hide").addClass("channel-loading_show")
    rechargeChannelList = getRechargeChannelData(id)
    var liList = ''
    if (rechargeChannelList && rechargeChannelList.length) {
        if (isMobile) {
            for (var i = 0; i < rechargeChannelList.length; i++) {
                liList += '<li class="channel-item" data-channelId="' + rechargeChannelList[i].id + '"><div class="channel-name">' + rechargeChannelList[i].display_name +
                    '</div><div class="channel-tip">' + rechargeChannelList[i].remark + '</div></li>'
            }
            var ulHtml = '<ul class="channel-list">' + liList + '</ul>'
            !$(".pay-item_actived .channel-list").length && $(".pay-item_actived").append(ulHtml)
        } else {
            for (var i = 0; i < rechargeChannelList.length; i++) {
                liList += '<li class="channel-item" data-channelId="' + rechargeChannelList[i].id + '"><div class="channel-name">' + rechargeChannelList[i].display_name +
                    '</div><div class="channel-tip">' + rechargeChannelList[i].remark + '</div></li>'
            }

            $(".channel-list").html(liList)
            $(".channel-waper").show()
            $(".channel-loading").removeClass("channel-loading_show").addClass("channel-loading_hide")
        }


        // 默认选择第一个渠道
        $(".channel-list .channel-item").removeClass("channel-item_selected")
        $(".channel-list .channel-item:nth-child(1)").addClass("channel-item_selected")
        activatedRechargeChannel = rechargeChannelList[0]
    } else {
        $(".channel-no-list").removeClass("channel-no-list_hide").addClass("channel-no-list_show")
        $(".channel-loading").removeClass("channel-loading_show").addClass("channel-loading_hide")
    }
}

/**
 * 获取充值渠道信息
 * @param {Number} id 充值方式ID
 * @return {Array}  渠道列表
 */
function getRechargeChannelData(id) {
    for (var i = 0; i < rechargeTypeList.length; i++) {
        if (rechargeTypeList[i].id === id) {
            return rechargeTypeList[i].channelList
        }
    }
}

function getAffiche(){
    $.ajax({
        type: "POST",
        url: "/api/getAffiche.php",
        success: function (res) {
            isLoading = false
            res = JSON.parse(res)
            if(!res.status){
                var marqueeHtml = ""
                for(var m = 0; m < res.data.length; m++){
                    marqueeHtml += "<span>"+ res.data[m].content +"</span>"
                }
                $("marquee").html(marqueeHtml)
            }else{

            }
        },
        error: function (err) {
            isLoading = false
        }
    })
}

function init() {
    getAffiche()
    openDialog("login-form")
}

init()
