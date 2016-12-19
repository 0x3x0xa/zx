

/*弹出层*/
/*
 参数解释：
 title	标题
 url		请求的url
 id		需要操作的数据id
 w		弹出层宽度（缺省调默认值）
 h		弹出层高度（缺省调默认值）
 */
function layer_show(w, h, title, url) {
    if (title == null || title == '') {
        title = false;
    }
    ;
    if (url == null || url == '') {
        url = "404.html";
    }
    ;
    if (w == null || w == '') {
        w = 800;
    }
    ;
    if (h == null || h == '') {
        h = ($(window).height() - 50);
    }
    ;

    layer.open({
        type: 2,
        area: [w + 'px', h + 'px'],
        fix: false, //不固定
        shade: 0.4,
        title: title,
        content: url,
        skin: 'home-class',
        maxmin: false
    });

}


/*关闭弹出框口*/
function layer_close() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}

function showPage(w, h, title, url) {
    layer_show(w, h, title, url);
}


/*-留言提交-提交*/
function message_add_save()
{

    var type = $('.type').val();
    var token = $("input[name='token']").val();
    var subject = $(".subject").val();
    var content = $(".content").val();
    $.ajax({
        type: "post",
        url: "/Home/Message/messageadd",
        data: {token: token, subject: subject, content: content, type: type},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 1500}, function () {

                    location.replace(location.href);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {

                    location.replace(location.href);


                });
            }
        }
    });
}
function checktowpassword()
{
    var token = $("input[name='token']").val();
    var towpassword = $('.towpassword').val();
    $.ajax({
        type: "post",
        url: "/Home/Member/checktowpassword",
        data: {token: token, towpassword: towpassword},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    location.replace(location.href);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }
        }
    });
}

function passwordedit_save()
{
    layer.confirm('确定要修改密码？', function (index) {
        var type = $('.type').val();
        var token = $("input[name='token']").val();
        var oldpassword = $('.oldpassword').val();
        var newpassword = $('.newpassword').val();
        $.ajax({
            type: "post",
            url: "/Home/Member/userpasswordedit",
            data: {type: type, token: token, oldpassword: oldpassword, newpassword: newpassword},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 1500}, function () {

                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1500}, function () {

                        location.replace(location.href);


                    });
                }
            }
        });
    });
}

//会员升级
function upgrade_add()
{
    var level = $('.level').val();

    $.ajax({
        type: "post",
        url: "/Home/Member/upgrade",
        data: {level: level},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);


                });



            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}

//会员升级
function reupgrade_add()
{
    var level = $('.level').val();
    var username = $('.username').val();
    $.ajax({
        type: "post",
        url: "/Home/Member/reupgrade",
        data: {level: level, username: username},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);


                });



            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}

function password_add() {
    layer.confirm('确定要提交？', function (index) {
        var token = $("input[name='token']").val();
        var towpassword = $(".towpassword").val();
        var threepassword = $(".threepassword").val();
        $.ajax({
            type: "post",
            url: "/Home/Member/userpassword",
            data: {towpassword: towpassword, threepassword: threepassword, token: token},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 1500}, function () {

                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1500}, function () {

                        location.replace(location.href);


                    });
                }
            }
        });

    });
}


//购买商品
function product_add()
{
    layer.confirm('确定要提交订单？', function (index) {
        var token = $("input[name='token']").val();
        var receiver = $(".receiver").val();
        var mobile = $(".mobile").val();
        var post_code = $(".post_code").val();
        var province = $('.province').val();
        var message = $('.message').val();
        var city = $('.city').val();
        var area = $('.area').val();
        var detailed_address = $('.detailed_address').val();
        var gwj=$('.gwj').val();
        var gwjf=$('.gwjf').val();
        var dzjf=$('.dzjf').val();
        var xjjf=$('.xjjf').val();
        var product = [];
        var product_id = [];
        var product_num = [];
        $('input[name="product_id"]:checked').each(function () {
            product_id = $(this).val();
            product_num = $('.productNum_' + product_id).val();
            var jsons2 = {};
            jsons2.id = product_id;
            jsons2.num = product_num;
            product.push(jsons2);
            i++;
        });

        $.ajax({
            type: "post",
            url: "/Home/Orders/productadd",
            data: {gwj:gwj,gwjf:gwjf,dzjf:dzjf,xjjf:xjjf,receiver: receiver, token: token, mobile: mobile, post_code: post_code, province: province, city: city, area: area, detailed_address: detailed_address, product: product, message: message},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 1500}, function () {

                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1500}, function () {

                        location.replace(location.href);


                    });
                }
            }
        });
    });
}




function  add_register()
{

    var username = $('.rusername').val();
    var password = $('.password').val();
    var repwd = $('.repwd').val();
    var towpassword = $('.towpassword').val();
    var towrepwd = $('.towrepwd').val();
    var mobile = $('.mobile').val();
    var alipay = $('.alipay').val();
    var tname = $('.tname').val();
    var bankno = $('.bankno').val();
    var bankname = $('.bankname').val();
    var pin = $('.pin').val();
    var code = $('.code').val();
    var token = $("input[name='token']").val();

    $.ajax({
        type: "post",
        url: "/Home/Reg/register",
        data: {code: code, username: username, password: password, repwd: repwd, mobile: mobile, alipay: alipay, tname: tname, bankno: bankno, bankname: bankname, towpassword: towpassword, towrepwd: towrepwd, pin: pin, token: token},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {

            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    location.replace(location.href);
                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }

        }
    });

}

function  add_registers()
{
    var m = $('.m').val();
    var username = $('.rusername').val();
    var password = $('.password').val();
    var repwd = $('.repwd').val();
    var towpassword = $('.towpassword').val();
    var towrepwd = $('.towrepwd').val();
    var mobile = $('.mobile').val();
    var alipay = $('.alipay').val();
    var tname = $('.tname').val();
    var bankno = $('.bankno').val();
    var bankname = $('.bankname').val();
    var pin = $('.pin').val();
    var code = $('.code').val();
    var token = $("input[name='token']").val();

    $.ajax({
        type: "post",
        url: "/Home/Login/register",
        data: {m: m, code: code, username: username, password: password, repwd: repwd, mobile: mobile, alipay: alipay, tname: tname, bankno: bankno, bankname: bankname, towpassword: towpassword, towrepwd: towrepwd, pin: pin, token: token},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {

            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    location.replace(location.href);
                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }

        }
    });

}

function  add_message()
{

    var type = $('.type').val();
    var subject = $('.subject').val();
    var content = $('.content').val();
    $.ajax({
        type: "post",
        url: "/Home/Message/message",
        data: {type: type, subject: subject, content: content},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {

            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    location.replace(location.href);
                });


            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500});
            }

        }
    });

}

function user_add_save()
{
    var token = $("input[name='token']").val();
    var username = $('.username').val();
    var name = $('.name').val();
    var password = $('.password').val();
    var recommend = $('.recommend').val();
    var region = $('.region').val();
    var mobile = $('.mobile').val();

    $.ajax({
        type: "post",
        url: "/Home/Member/useradd",
        data: {token: token, username: username, name: name, password: password, recommend: recommend, region: region, mobile: mobile},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    location.reload();
                    window.location='/Home/Member/recommend';
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000}, function ()
                {
                    location.reload();

                });
            }

        }
    });
}

function  userinfo_save()
{

    var name = $('.tname').val();
    var alipay = $('.alipay').val();
    var bankno = $('.bankno').val();
    var bank = $('.bankname').val();
    var towpwd = $('.towlevelpassword').val();

    $.ajax({
        type: "post",
        url: "/Home/Member/userinfo",
        data: {name: name,  alipay: alipay, bankno: bankno, bank: bank, towpwd: towpwd},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {

            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2500}, function () {
                    $('.towlevelpassword').val('');
                });


            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2500});
            }

        }
    });

}

function password_save()
{

    var oldpassword = $('.oldpassword').val();
    var password = $('.password').val();
    var repwd = $('.repwd').val();
    $.ajax({
        type: "post",
        url: "/Home/Member/password_save",
        data: {oldpassword: oldpassword, password: password, repwd: repwd},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {

            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    $('.oldpassword').val('');
                    $('.password').val('');
                    $('.repwd').val('');
                });


            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500});
            }

        }
    });

}

//-奖金转电子货币
function onBonus2Coin() {
    layer.confirm('确认要转换吗？', function (index) {
        var token = $("input[name='token']").val();
        var threepassword = $('.threepassword').val();
        var bizhong = $('.bizhong').val();
        var sum = $('.sum').val();

        $.ajax({
            type: "post",
            url: "/Home/Report/currencyConversion",
            data: {threepassword: threepassword, bizhong: bizhong, sum: sum, token: token},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {

                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                       location.replace(location.href);
                    });


                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1500});
                }

            }
        });


    });
}
function getname(){
     var othername = $.trim($('.othername').val());
     if(othername==""){
            $('#tip_msg').html('<span style=\'color:red\'>请输入接收方账号</span>');
            return;
        }else{
            $.ajax({
            type: "post",
            url: "/Home/Report/getname",
            data: { othername: othername},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                $('#tip_msg').html('<span style=\'color:green\'>'+data.msg+'</span>');
                } else
                {
                    $('#tip_msg').html('<span style=\'color:red\'>'+data.msg+'</span>');
                }

            }
        });
            
        }

}
//会员间互转
function onMemberExchangeCoin() {
    layer.confirm('确认要转换吗？', function (index) {
        var token = $("input[name='token']").val();
        var othername = $.trim($('.othername').val());
        var type = $.trim($('.type').val());
        var money = $.trim($('.money').val());
        var password = $.trim($('.password').val());
        
        if(othername==""){
            $('#tip_msg').html('<span style=\'color:red\'>请输入接收方账号</span>');
            return;
        }

        $.ajax({
            type: "post",
            url: "/Home/Report/onMemberExchangeCoin",
            data: {token: token, othername: othername, type: type, money: money, password: password},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                     location.replace(location.href);
                    
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                       location.replace(location.href);
                 
                    });
                }

            }
        });
    });
}

//汇款通知
function remittance()
{
    var token = $("input[name='token']").val();
    var bankno = $("input[name='bankno']:checked").val();
    var remitter = $.trim($('.remitter').val());
    var money = $.trim($('.money').val());
    var remittance_date = $.trim($('.remittance_date').val());
    var message = $.trim($('.message').val());

    $.ajax({
        type: "post",
        url: "/Home/Report/addRemit",
        data: {token: token, bankno: bankno, remitter: remitter, money: money, remittance_date: remittance_date, message: message},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);

                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }

        }
    });

}

//充值
function pay()
{
    var token = $("input[name='token']").val();
    var bank = $.trim($('.bank').val());
    var money = $.trim($('.money').val());
    var message = $.trim($('.message').val());



    $.ajax({
        type: "post",
        url: "/Home/Report/pay",
        data: {token: token, bank: bank, money: money, message: message},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
                window.open(data.url+'/Home/Pays/newpay?billno=' + data.billno, "_blank");
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }

        }
    });

}

function userinfo()
{
    layer.confirm('确定要提交表单？', function (index) {
        var token = $("input[name='token']").val();
        var mobile = $.trim($('.mobile').val());
        var id_card = $.trim($('.id_card').val());
        var post_code = $.trim($('.post_code').val());
        var province = $.trim($('.province').val());
        var city = $.trim($('.city').val());
        var area = $.trim($('.area').val());
        var detailed_address = $.trim($('.detailed_address').val());
        var bank = $.trim($('.bank').val());
        var account_name = $.trim($('.account_name').val());
        var bankno = $.trim($('.bankno').val());
        var bank_outlets = $.trim($('.bank_outlets').val());
        $.ajax({
            type: "post",
            url: "/Home/Member/userinfo",
            data: {token: token, mobile: mobile, id_card: id_card, post_code: post_code, detailed_address: detailed_address,
                bank: bank, account_name: account_name, bankno: bankno, bank_outlets: bank_outlets, province: province, city: city, area: area},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                        location.replace(location.href);

                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                        location.replace(location.href);
                    });
                }

            }
        });
    });
}


function findpassword_save()
{
  
    var code = $.trim($('.code').val());
    var type = $.trim($('.type').val());
    var password = $.trim($('.newpassword').val());
    $.ajax({
        type: "post",
        url: "/Home/Member/finduserpassword",
        data: {code: code, password: password,type:type},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    location.replace(location.href);

                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }

        }
    });
}


function petpwd_save()
{
    var username = $.trim($('.rusername').val());
    var mobile = $.trim($('.mobile').val());
    var codes = $.trim($('.codes').val());
    var code = $.trim($('.code').val());
    var password = $.trim($('.password').val());
    $.ajax({
        type: "post",
        url: "/Home/Login/recover",
        data: {mobile: mobile, code: code, codes: codes, password: password, username: username},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    location.replace(location.href);

                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    location.replace(location.href);
                });
            }

        }
    });
}



function set_code()
{

    var username = $.trim($('.rusername').val());
    var mobile = $.trim($('.mobile').val());
    $.ajax({
        type: "post",
        url: "/Home/Login/set_code",
        data: {mobile: mobile, username: username},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    // location.replace(location.href);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    //location.replace(location.href);
                });
            }

        }
    });
}
function set_codes(username)
{


    $.ajax({
        type: "post",
        url: "/Home/Member/set_codes",
        data: {username: username},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {

                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                  //  location.replace(location.href);
                });
            }

        }
    });
}

function addWithdrawals() {
    var token = $("input[name='token']").val();
    var bizhong = $.trim($('.bizhong').val());
    var money = $.trim($('.money').val());
    var threepassword = $.trim($('.threepassword').val());
    $.ajax({
        type: "post",
        url: "/Home/Report/addWithdrawals",
        data: {money: money, bizhong: bizhong, threepassword: threepassword, token: token},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }

        }
    });
}

function jifentixian() {
    var token = $("input[name='token']").val();
    var threepassword = $.trim($('.threepassword').val());
    $.ajax({
        type: "post",
        url: "/Home/Report/jifentixian",
        data: {threepassword: threepassword, token: token},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }

        }
    });
}


function mergedongtaimoney() {

    var token = $("input[name='token']").val();
    var bizhong = $.trim($('.bizhong').val());
    var money = $.trim($('.money').val());
    var threepassword = $.trim($('.threepassword').val());
    $.ajax({
        type: "post",
        url: "/Home/Report/mergedongtaimoney",
        data: {money: money, bizhong: bizhong, threepassword: threepassword, token: token},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }

        }
    });

}
function mergejingtaimoney() {
    var token = $("input[name='token']").val();
    var bizhong = $.trim($('.bizhong').val());
    var money = $.trim($('.money').val());
    var threepassword = $.trim($('.threepassword').val());
    $.ajax({
        type: "post",
        url: "/Home/Report/mergejingtaimoney",
        data: {money: money, bizhong: bizhong, threepassword: threepassword, token: token},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });
            }

        }
    });
}
function del(username) {
   
 
    $.ajax({
        type: "post",
        url: "/Home/Member/del",
        data: {username: username},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1500}, function () {
                  location.replace(location.href);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1500}, function () {
                    
                });
            }

        }
    });
}
