/* -----------H-ui前端框架-------------
 * H-ui.admin.js v2.4
 * http://www.h-ui.net/
 * Created & Modified by guojunhui
 * Date modified 15:42 2016.03.14
 *
 * Copyright 2013-2016 北京颖杰联创科技有限公司 All rights reserved.
 * Licensed under MIT license.
 * http://opensource.org/licenses/MIT
 *
 */
var num = 0, oUl = $("#min_title_list"), hide_nav = $("#Hui-tabNav");

/*获取顶部选项卡总长度*/
function tabNavallwidth() {
    var taballwidth = 0,
            $tabNav = hide_nav.find(".acrossTab"),
            $tabNavWp = hide_nav.find(".Hui-tabNav-wp"),
            $tabNavitem = hide_nav.find(".acrossTab li"),
            $tabNavmore = hide_nav.find(".Hui-tabNav-more");
    if (!$tabNav[0]) {
        return
    }
    $tabNavitem.each(function (index, element) {
        taballwidth += Number(parseFloat($(this).width() + 60))
    });
    $tabNav.width(taballwidth + 25);
    var w = $tabNavWp.width();
    if (taballwidth + 25 > w) {
        $tabNavmore.show()
    } else {
        $tabNavmore.hide();
        $tabNav.css({left: 0})
    }
}

/*左侧菜单响应式*/
function Huiasidedisplay() {
    if ($(window).width() >= 768) {
        $(".Hui-aside").show()
    }
}
function getskincookie() {
    var v = getCookie("Huiskin");
    var hrefStr = $("#skin").attr("href");
    if (v == null || v == "") {
        v = "default";
    }
    if (hrefStr != undefined) {
        var hrefRes = hrefStr.substring(0, hrefStr.lastIndexOf('skin/')) + 'skin/' + v + '/skin.css';
        $("#skin").attr("href", hrefRes);
    }
}
function Hui_admin_tab(obj) {
    if ($(obj).attr('_href')) {
        var bStop = false;
        var bStopIndex = 0;
        var _href = $(obj).attr('_href');
        var _titleName = $(obj).attr("data-title");
        var topWindow = $(window.parent.document);
        var show_navLi = topWindow.find("#min_title_list li");
        show_navLi.each(function () {
            if ($(this).find('span').attr("data-href") == _href) {
                bStop = true;
                bStopIndex = show_navLi.index($(this));
                return false;
            }
        });
        if (!bStop) {
            creatIframe(_href, _titleName);
            min_titleList();
        } else {
            show_navLi.removeClass("active").eq(bStopIndex).addClass("active");
            var iframe_box = topWindow.find("#iframe_box");
            iframe_box.find(".show_iframe").hide().eq(bStopIndex).show().find("iframe").attr("src", _href);
        }
    }

}
function min_titleList() {
    var topWindow = $(window.parent.document);
    var show_nav = topWindow.find("#min_title_list");
    var aLi = show_nav.find("li");
}
;
function creatIframe(href, titleName) {
    var topWindow = $(window.parent.document);
    var show_nav = topWindow.find('#min_title_list');
    show_nav.find('li').removeClass("active");
    var iframe_box = topWindow.find('#iframe_box');
    show_nav.append('<li class="active"><span data-href="' + href + '">' + titleName + '</span><i></i><em></em></li>');
    var taballwidth = 0,
            $tabNav = topWindow.find(".acrossTab"),
            $tabNavWp = topWindow.find(".Hui-tabNav-wp"),
            $tabNavitem = topWindow.find(".acrossTab li"),
            $tabNavmore = topWindow.find(".Hui-tabNav-more");
    if (!$tabNav[0]) {
        return
    }
    $tabNavitem.each(function (index, element) {
        taballwidth += Number(parseFloat($(this).width() + 60))
    });
    $tabNav.width(taballwidth + 25);
    var w = $tabNavWp.width();
    if (taballwidth + 25 > w) {
        $tabNavmore.show()
    } else {
        $tabNavmore.hide();
        $tabNav.css({left: 0})
    }
    var iframeBox = iframe_box.find('.show_iframe');
    iframeBox.hide();
    iframe_box.append('<div class="show_iframe"><div class="loading"></div><iframe frameborder="0" src=' + href + '></iframe></div>');
    var showBox = iframe_box.find('.show_iframe:visible');
    showBox.find('iframe').load(function () {
        showBox.find('.loading').hide();
    });
}
function removeIframe() {
    var topWindow = $(window.parent.document);
    var iframe = topWindow.find('#iframe_box .show_iframe');
    var tab = topWindow.find(".acrossTab li");
    var showTab = topWindow.find(".acrossTab li.active");
    var showBox = topWindow.find('.show_iframe:visible');
    var i = showTab.index();
    tab.eq(i - 1).addClass("active");
    iframe.eq(i - 1).show();
    tab.eq(i).remove();
    iframe.eq(i).remove();
}
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
        type:2,
        area: [w + 'px', h + 'px'],
        fix: false, //不固定
        maxmin: true,
        shade: 0.4,
        title: title,
        content: url,
		
    });
}

function showPage(w, h, title, url) {
    layer_show(w, h, title, url);
}

/*关闭弹出框口*/
function layer_close() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}
$(function () {
    getskincookie();
    //layer.config({extend: 'extend/layer.ext.js'});
    Huiasidedisplay();
    var resizeID;
    $(window).resize(function () {
        clearTimeout(resizeID);
        resizeID = setTimeout(function () {
            Huiasidedisplay();
        }, 500);
    });

    $(".nav-toggle").click(function () {
        $(".Hui-aside").slideToggle();
    });
    $(".Hui-aside").on("click", ".menu_dropdown dd li a", function () {
        if ($(window).width() < 768) {
            $(".Hui-aside").slideToggle();
        }
    });
    /*左侧菜单*/
    $.Huifold(".menu_dropdown dl dt", ".menu_dropdown dl dd", "fast", 1, "click");
    /*选项卡导航*/

    $(".Hui-aside").on("click", ".menu_dropdown a", function () {
        Hui_admin_tab(this);
    });

    $(document).on("click", "#min_title_list li", function () {
        var bStopIndex = $(this).index();
        var iframe_box = $("#iframe_box");
        $("#min_title_list li").removeClass("active").eq(bStopIndex).addClass("active");
        iframe_box.find(".show_iframe").hide().eq(bStopIndex).show();
    });
    $(document).on("click", "#min_title_list li i", function () {
        var aCloseIndex = $(this).parents("li").index();
        $(this).parent().remove();
        $('#iframe_box').find('.show_iframe').eq(aCloseIndex).remove();
        num == 0 ? num = 0 : num--;
        tabNavallwidth();
    });
    $(document).on("dblclick", "#min_title_list li", function () {
        var aCloseIndex = $(this).index();
        var iframe_box = $("#iframe_box");
        if (aCloseIndex > 0) {
            $(this).remove();
            $('#iframe_box').find('.show_iframe').eq(aCloseIndex).remove();
            num == 0 ? num = 0 : num--;
            $("#min_title_list li").removeClass("active").eq(aCloseIndex - 1).addClass("active");
            iframe_box.find(".show_iframe").hide().eq(aCloseIndex - 1).show();
            tabNavallwidth();
        } else {
            return false;
        }
    });
    tabNavallwidth();

    $('#js-tabNav-next').click(function () {
        num == oUl.find('li').length - 1 ? num = oUl.find('li').length - 1 : num++;
        toNavPos();
    });
    $('#js-tabNav-prev').click(function () {
        num == 0 ? num = 0 : num--;
        toNavPos();
    });

    function toNavPos() {
        oUl.stop().animate({'left': -num * 100}, 100);
    }

    /*换肤*/
    $("#Hui-skin .dropDown-menu a").click(function () {
        var v = $(this).attr("data-val");
        setCookie("Huiskin", v);
        var hrefStr = $("#skin").attr("href");
        var hrefRes = hrefStr.substring(0, hrefStr.lastIndexOf('skin/')) + 'skin/' + v + '/skin.css';

        $(window.frames.document).contents().find("#skin").attr("href", hrefRes);
        //$("#skin").attr("href",hrefResd);
    });
});


//myjs

/*批量删除 用户*/
function datadel_admin() {
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid"]:checked').each(function () {
            str += $(this).val() + ","
        });

        $.ajax({
            type: "get",
            url: "/Admin/Rbac/dataadmindel",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {

                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });



    });
}

/*批量删除 角色*/
function datadel_role() {
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid_role"]:checked').each(function () {
            str += $(this).val() + ","
        });


        $.ajax({
            type: "get",
            url: "/Admin/Rbac/datadel_role",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});

                }

            }
        });



    });
}
/*批量删除 节点*/
function datadel_power() {
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid_power"]:checked').each(function () {
            str += $(this).val() + ","
        });


        $.ajax({
            type: "get",
            url: "/Admin/Rbac/datadel_power",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});

                }

            }
        });



    });
}
//会员升级
function upgrade_add()
{
    var status = $('.status').val();
    var level = $('.level').val();
    var username = $('.username').val();
    $.ajax({
        type: "post",
        url: "/Admin/Member/upgrade",
        data: {level: level, username: username, status: status},
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

//充值
function  addMemberCoin_add_save()
{

    var type = $('.type').val();
    var username = $('.username').val();
    var income = $('.income').val();
    var message = $('.message').val();
    layer.confirm('确认要给<span style="color:green;">' + username + '</span>充值<span style="color:red">' + income + '</span>个币？', function (index) {
        $.ajax({
            type: "post",
            url: "/Admin/Member/addMemberCoin",
            data: {type: type, username: username, income: income, message: message},
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
    });
}

//扣币
function  subtractMemberCoin_add_save()
{

    var type = $('.type').val();
    var username = $('.username').val();
    var expend = $('.expend').val();
    var message = $('.message').val();
    layer.confirm('确认要扣<span style="color:green;">' + username + '</span>  <span style="color:red">' + expend + '</span>个币？', function (index) {
        $.ajax({
            type: "post",
            url: "/Admin/Member/subtractMemberCoin",
            data: {type: type, username: username, expend: expend, message: message},
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
    });
}


function message_add_class_save()
{

    var type_name = $('.type_name').val();
    var type_message = $('.type_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Message/message_class_add",
        data: {type_message: type_message, type_name: type_name},
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

/*留言-删除*/
function message_del(obj, id) {
    layer.confirm('确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Message/message_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
//留言 批量删除
function message_datadel()
{
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid"]:checked').each(function () {
            str += $(this).val() + ","
        });

        $.ajax({
            type: "get",
            url: "/Admin/Message/datadel_message",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {

                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });



    });
}

//回复留言
function message_edit_save(id)
{

    var reply = $('.reply').val();
    $.ajax({
        type: "post",
        url: "/Admin/Message/messageedit",
        data: {reply: reply, id: id},
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
/***
 * 
 删除留言分类
 * 
 */
function message_class_del(obj, id) {
    layer.confirm('删除须谨慎，确认要删除吗？', function (index) {
        $.ajax({
            type: "get",
            url: "/Admin/Message/message_class_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
/**编辑留言分类**/
function message_class_edit(id, w, h, title, url) {
    layer_show(w, h, title, url + '?id=' + id);
}


//保存分类修改
function message_edit_class_save(id) {


    var type_name = $('.type_name').val();
    var type_message = $('.type_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Product/productclassedit",
        data: {id: id, type_name: type_name, type_message: type_message},
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

/*----------用户管理------------------*/
/*用户-添加*/

function user_add_save()
{
    layer.confirm('确定要添加会员？', function (index) {
    var token = $("input[name='token']").val();
    var username = $('.username').val();
    var name = $('.name').val();
    var password = $('.password').val();
    var recommend = $('.recommend').val();
    var region = $('.region').val();
    var mobile = $('.mobile').val();
 
    $.ajax({
        type: "post",
        url: "/Admin/Member/useradd",
        data: {token: token, username: username, name: name, password: password,recommend: recommend,region: region,mobile:mobile},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                    location.reload();
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
     });
}

//
function user_password_save(w, h, title, url)
{
    var newpassword = $('.newpassword').val();
    var username = $('.username').val();
    var type = $('.type').val();
    $.ajax({
        type: "post",
        url: "/Admin/Member/userpasswordedit",
        data: {type: type, newpassword: newpassword, username: username},
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
//修改推荐人
function recommend_edit_save(w, h, title, url)
{
    var username = $('.username').val();
    var recommend = $('.recommend').val();
    $.ajax({
        type: "post",
        url: "/Admin/Member/openEditRecommend",
        data: {recommend: recommend, username: username},
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

/*用户-编辑-保存*/
function user_edit_save(id) {

    var username = $('.username').val();
    var name = $('.name').val();
    var mobile = $('.mobile').val();
    var account_name = $('.account_name').val();
    var bankno = $('.bankno').val();
    var bank = $('.bank').val();
    var bank_outlets = $('.bank_outlets').val();
    var province = $('.province').val();
    var city = $('.city').val();
    var area = $('.area').val();
    var detailed_address = $('.detailed_address').val();
    var id_card = $('.id_card').val();
    var post_code = $('.post_code').val();
    $.ajax({
        type: "post",
        url: "/Admin/Member/useredit",
        data: {id: id, username: username, name: name, mobile: mobile, account_name: account_name, bankno: bankno, bank_outlets: bank_outlets, bank: bank, province: province,
            city: city, area: area, detailed_address: detailed_address, id_card: id_card, post_code: post_code,
        },
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

/*用户-停用*/
function user_stop(obj, id) {
    $.ajax({
        type: "get",
        url: "/Admin/Member/user_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="user_start(this,' + id + ')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已冻结</span>');
                $(obj).remove();

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
/*用户-启用*/
function user_start(obj, id) {
    $.ajax({
        type: "get",
        url: "/Admin/Member/user_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="user_stop(this,' + id + ')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已启用</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });
}

/*------------资讯管理----------------*/
/*获取分类值*/
/**************添加分类****************/

function article_add_class_save()
{

    var type_name = $('.type_name').val();
    var type_message = $('.type_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Article/article_class_add",
        data: {type_message: type_message, type_name: type_name},
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
/*咨讯-分类-删除*/
function article_class_del(obj, id) {
    layer.confirm('删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Article/article_class_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

//保存分类修改
function class_save(id) {


    var pid = $('.pid').val();
    var art_class_name = $('.art_class_name').val();
    $.ajax({
        type: "post",
        url: "/Admin/Article/articleclassedit",
        data: {id: id, pid: pid, art_class_name: art_class_name},
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
/*资讯-分类-编辑*/

function article_edit_class_save(id) {


    var type_name = $('.type_name').val();
    var type_message = $('.type_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Article/articleclassedit",
        data: {id: id, type_name: type_name, type_message: type_message},
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
/*资讯-添加*/
function article_add_save()
{

    var art_title = $('.art_title').val();
    var art_source = $('.art_source').val();
    var art_author = $('.art_author').val();
    var art_type = $('.art_type').val();
    var editorvalue = UE.getEditor('editor').getContent();
    $.ajax({
        type: "post",
        url: "/Admin/Article/articleadd",
        data: {art_title: art_title, art_source: art_source, art_author: art_author, art_type: art_type, editorvalue: editorvalue},
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
/*资讯-编辑*/
function article_edit_save() {
    var id = $('.id').val();
    var art_title = $('.art_title').val();
    var art_source = $('.art_source').val();
    var art_author = $('.art_author').val();
    var art_type = $('.art_type').val();
    var editorvalue = UE.getEditor('editor').getContent();
    $.ajax({
        type: "post",
        url: "/Admin/Article/articleedit",
        data: {id: id, art_title: art_title, art_source: art_source, art_author: art_author, art_type: art_type, editorvalue: editorvalue},
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

/*资讯-下架*/
function article_xiajia(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Article/article_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.art_status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="article_fabu(this,' + id + ')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已下架</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
/*资讯-发布*/
function article_fabu(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Article/article_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.art_status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="article_xiajia(this,' + id + ')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已发布</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });

}
/*咨讯-删除*/
function article_del(obj, id) {
    layer.confirm('文章删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Article/article_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
/*咨询批量-删除*/
function article_datadel()
{
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid"]:checked').each(function () {
            str += $(this).val() + ","
        });

        $.ajax({
            type: "get",
            url: "/Admin/Article/datadel_article",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {

                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });



    });
}
//添加产品分类
function product_add_class_save()
{

    var type_name = $('.type_name').val();
    var type_message = $('.type_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Product/product_class_add",
        data: {type_message: type_message, type_name: type_name},
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

/*产品-分类-删除*/
function product_class_del(obj, id) {
    layer.confirm('删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Product/product_class_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {

                        location.reload();

                    });
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
/**************添加产品分类****************/
//保存分类修改
function product_edit_class_save(id) {


    var type_name = $('.type_name').val();
    var type_message = $('.type_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Product/productclassedit",
        data: {id: id, type_name: type_name, type_message: type_message},
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

function product_add_save()
{

    var product_title = $('.product_title').val();
    var product_norms = $('.product_norms').val();
    var product_type = $('.product_type').val();
    var market_price = $('.market_price').val();
    var member_price = $('.member_price').val();
    var imageurl = $('.imageurl').val();
    var product_message = $('.product_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Product/productadd",
        data:
                {
                    product_title: product_title, product_norms: product_norms,
                    product_type: product_type, market_price: market_price,
                    product_message: product_message, imageurl: imageurl, member_price: member_price
                },
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
/*产品-编辑*/
function product_edit_save(id) {
    var product_title = $('.product_title').val();
    var product_norms = $('.product_norms').val();
    var product_type = $('.product_type').val();
    var market_price = $('.market_price').val();
    var member_price = $('.member_price').val();
    var imageurl = $('.imageurl').val();
    var product_message = $('.product_message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Product/productedit",
        data: {id: id, product_title: product_title, product_norms: product_norms,
            product_type: product_type, market_price: market_price,
            product_message: product_message, imageurl: imageurl, member_price: member_price},
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

/*产品-下架*/
function product_xiajia(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Product/product_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="product_fabu(this,' + id + ')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已下架</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
/*产品-发布*/
function product_fabu(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Product/product_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="product_xiajia(this,' + id + ')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已发布</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });

}
/*产品-下架*/
function product_img_xiajia(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Product/product_img_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {

                    location.reload();

                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
/*产品-发布*/
function product_img_fabu(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Product/product_img_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                layer.msg(data.msg, {icon: 1, time: 2000}, function () {

                    location.reload();

                });
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });

}
/*产品-删除*/
function product_del(obj, id) {
    layer.confirm('产品删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Product/product_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {

                        location.reload();

                    });
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
/*产品-删除*/
function product_img_del(obj, id) {
    layer.confirm('确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Product/product_img_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {

                        location.reload();

                    });
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
/*产品批量-删除*/
function product_datadel()
{
    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid"]:checked').each(function () {
            str += $(this).val() + ","
        });

        $.ajax({
            type: "get",
            url: "/Admin/Product/datadel_product",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {

                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });



    });
}
//修改会员等级
function userlevel_edit_save(id) {
    var title = $('.title').val();
    var nextlevel = $('.nextlevel').val();
    var status = $('.status').val();
    var achievement = $('.achievement').val();
    var group = $('.group').val();
    var bonus = $('.bonus').val();
    $.ajax({
        type: "post",
        url: "/Admin/Param/userleveledit",
        data: {id: id, title: title, nextlevel: nextlevel,
            status: status, achievement: achievement,
            group: group, bonus: bonus,
        },
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
//会员注册等级修改
function memberlevel_edit_save(id) {
    var title = $('.title').val();
    var status = $('input[name="status"]:checked').val();
    var registermoney = $('.registermoney').val();
    var daycap = $('.daycap').val();
    var stockrate = $('.stockrate').val();
    var minmarketplace = $('.minmarketplace').val();
    var middlemarketplace = $('.middlemarketplace').val();
    var zhengzhiplace = $('.zhengzhiplace').val();

    $.ajax({
        type: "post",
        url: "/Admin/Param/memberleveledit",
        data: {id: id, title: title,status:status, registermoney: registermoney, daycap: daycap, stockrate: stockrate, minmarketplace: minmarketplace, middlemarketplace: middlemarketplace, zhengzhiplace: zhengzhiplace,
        },
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
//职位管理
function position_edit_save(id) {
    var middle = $('.middle').val();
    var min = $('.min').val();
    var jinjiscale = $('.jinjiscale').val();
    $.ajax({
        type: "post",
        url: "/Admin/Param/positionedit",
        data: {id: id, middle: middle, min: min,jinjiscale:jinjiscale},
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

//添加快速奖
function levelvalue_add() {
    var memberlevelid = $('.memberlevelid').val();
    var setlevelid = $('.setlevelid').val();
    var value = $('.value').val();

    $.ajax({
        type: "post",
        url: "/Admin/Param/levelvalueadd",
        data: {memberlevelid: memberlevelid, setlevelid: setlevelid, value: value,
        },
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
//修改快速奖
function levelvalue_edit(id) {
    var memberlevelid = $('.memberlevelid').val();
    var setlevelid = $('.setlevelid').val();
    var value = $('.value').val();

    $.ajax({
        type: "post",
        url: "/Admin/Param/levelvalueedit",
        data: {id: id, memberlevelid: memberlevelid, setlevelid: setlevelid, value: value,
        },
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
//添加快速奖的层级
function setlevel_add()
{

    var min = $('.min').val();
    var max = $('.max').val();

    $.ajax({
        type: "post",
        url: "/Admin/Param/setleveladd",
        data: {min: min, max: max},
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
//修改快速奖层级
function setlevel_edit_save(id)
{
    var min = $('.min').val();
    var max = $('.max').val();

    $.ajax({
        type: "post",
        url: "/Admin/Param/setleveledit",
        data: {id: id, min: min, max: max},
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

/*************订单管理************************/
//发货列表
function showFahuo(w, h, title, url) {
    var orderId = '';
    $('input[name="orderId"]:checked').each(function () {
        orderId += $(this).val() + ','
    });
    if (orderId == '') {
        layer.msg('请选择需要发货的订单', {icon: 2, time: 2000});
    } else {
        layer_show(w, h, title, url + '?id=' + orderId);
    }



}
//更新发货状态
function order_fuhuo_save() {

    var orders = [];
    var ordersid = [];
    var message = [];
    var i = 1;
    $('input[name="orderId"]:checked').each(function () {
        ordersid = $(this).val();
        message = $('.message_' + ordersid).val();
        var jsons2 = {};
        jsons2.id = ordersid;
        jsons2.message = message;
        orders.push(jsons2);
        i++;
    });
    $.ajax({
        type: "post",
        url: "/Admin/Orders/showFahuo",
        data: {orders: orders,
        },
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

//收货
function order_shouhuo(obj, id, w, h) {
    layer.confirm('确认对方已经收到货了吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Orders/shouhuo",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

//修改订单信息
function order_edit_save(id) {

    var receiver = $('.receiver').val();
    var mobile = $('.mobile').val();
    var address = $('.address').val();
    var message = $('.message').val();
    var express = $('.express').val();
    var express_no = $('.express_no').val();
    var post_code = $('.post_code').val();
    $.ajax({
        type: "post",
        url: "/Admin/Orders/editOrderRemak",
        data: {id: id, receiver: receiver, mobile: mobile,
            address: address, message: message,
            express: express, express_no: express_no, post_code: post_code
        },
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
/*------------图片库--------------*/
/*图片库-分类-添加*/
function picture_class_add(obj) {
    var v = $("#picture-class-val").val();
    if (v == "" || v == null) {
        return false;
    } else {
        //ajax请求 添加分类
    }
}

function picture_add_save(w, h, title, url) {
    var src = $('.src').val();
    var title = $('.title').val();
    var tage = $('.tage').val();
    var href = $('.href').val();

    $.ajax({
        type: "post",
        url: "/Admin/Picture/pictureadd",
        data: {src: src, title: title, tage: tage, href: href},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg('添加成功', {icon: 1, time: 2000});

            } else
            {
                layer.msg('添加失败', {icon: 2, time: 2000});
            }

        }
    });
}

/*图片库-下架*/
function picture_xiajia(obj, id) {
    $.ajax({
        type: "get",
        url: "/Admin/Picture/picture_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_fabu(this,' + id + ')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已下架</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
/*图片库-发布*/
function picture_fabu(obj, id) {
    $.ajax({
        type: "get",
        url: "/Admin/Picture/picture_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_xiajia(this,' + id + ')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已发布</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });


}
//审核通过汇款
function shenPiRemittance(id) {
    layer.confirm('确认要审核通过吗？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/shenPiRemittance",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

//拒绝审核汇款
function jujieRemittance(id) {
    layer.confirm('确认要审核通过吗？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/jujieRemittance",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
//发放汇款
function fafangRemittance(id) {
    layer.confirm('确认要发放现金积分吗？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/fafangRemittance",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

//提现通过
function tixiangongguo(id)
{
    layer.confirm('确认审核通过？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/tixiangongguo",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });

}
//提现拒绝通过
function tixianjujue(id) {

    layer.confirm('确认审核不通过？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/tixianjujue",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

//积分提现通过
function jifentixiangongguo(id)
{
    layer.confirm('确认审核通过？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/jifentixiangongguo",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });

}
//积分提现拒绝通过
function jifentixianjujue(id) {

    layer.confirm('确认审核不通过？', function (index) {

        $.ajax({
            type: "post",
            url: "/Admin/Report/jifentixianjujue",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

//发放提现
function fafangtixian(id) {
    layer.confirm('确认已经给对方打款？', function (index) {

        $.ajax({
            type: "Post",
            url: "/Admin/Report/fafangtixian",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    layer.msg(data.msg, {icon: 1, time: 2000}, function () {
                        location.replace(location.href);
                    });

                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}


//添加每周的积分单价
function addzengzhi_add() {
    layer.confirm('确认添加？', function (index) {
         var price = $('.price').val();
          var wdate = $('.Wdate').val();
        $.ajax({
            type: "Post",
            url: "/Admin/Report/addzengzhi",
            data: {price: price,wdate:wdate},
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
                      layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                    });
                }

            }
        });
    });
}

//更新每周的积分单价
function zengzhi_save(id) {
    layer.confirm('要更新数据？', function (index) {
         var price = $('.price').val();
          var wdate = $('.Wdate').val();
          
        $.ajax({
            type: "Post",
            url: "/Admin/Report/savezengzhi",
            data: {id:id,price: price,wdate:wdate},
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
                     layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                    });
                }

            }
        });
    });
}


//添加每月单价
function addfenhong_add() {
    layer.confirm('确认添加？', function (index) {
         var price = $('.price').val();
          var wdate = $('.Wdate').val();
        $.ajax({
            type: "Post",
            url: "/Admin/Report/addfenhong",
            data: {price: price,wdate:wdate},
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
                      layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                    });
                }

            }
        });
    });
}

//更新每月的单价
function fenhong_save(id) {
    layer.confirm('要更新数据？', function (index) {
         var price = $('.price').val();
          var wdate = $('.Wdate').val();
          
        $.ajax({
            type: "Post",
            url: "/Admin/Report/savefenhong",
            data: {id:id,price: price,wdate:wdate},
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
                     layer.msg(data.msg, {icon: 2, time: 2000}, function () {
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                    });
                }

            }
        });
    });
}




/*图片-删除*/
function picture_del(obj, id) {
    layer.confirm('确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Picture/picture_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

function datadel_picture()
{

    layer.confirm('确认要删除吗？', function (index) {
        var str = "";
        $('input[name="delid"]:checked').each(function () {
            str += $(this).val() + ","
        });

        $.ajax({
            type: "get",
            url: "/Admin/Picture/datadel_picture",
            data: {str: str},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {

                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 2000});
                    location.replace(location.href);
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}
/*------------管理员管理--------------*/

function password_edit()
{
    var newpassword = $('.newpassword').val();
    var repassword = $('.repassword').val();
    var code = $('.code').val();
    $.ajax({
        type: "post",
        url: "/Admin/Rbac/password",
        data: {newpassword: newpassword, code: code},
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

/*管理员-密码-修改*/
function admin_password_save(id) {
    var newpassword = $('.newpassword').val();
    var repassword = $('.repassword').val();
    var code = $('.code').val();
    $.ajax({
        type: "post",
        url: "/Admin/Rbac/adminpasswordedit",
        data: {id: id, newpassword: newpassword, code: code},
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
/*管理员-角色-添加*/

function admin_role_add_save() {
    var text = '';
    $('input[name="power_id[]"]:checked').each(function () {
        text += "," + $(this).val();
    });

    var rolename = $('.rolename').val();
    var remarks = $('.remarks').val();

    $.ajax({
        type: "post",
        url: "/Admin/Rbac/adminroleadd",
        data: {powerid: text, remarks: remarks, rolename: rolename},
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
/*管理员-角色-编辑*/

function admin_role_edit_save() {
    var text = '';
    $('input[name="power_id[]"]:checked').each(function () {
        text += "," + $(this).val();
    });
    var id = $('.id').val();
    var rolename = $('.rolename').val();
    var remarks = $('.remarks').val();

    $.ajax({
        type: "post",
        url: "/Admin/Rbac/adminroleedit",
        data: {id: id, powerid: text, remarks: remarks, rolename: rolename},
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


function admin_add()
{

    var password = $('.password').val();
    var username = $('.username').val();
    var mobile = $('.mobile').val();
    var type = $('.type').val();
    $.ajax({
        type: "post",
        url: "/Admin/Rbac/adminadd",
        data: {username: username, type: type, password: password, mobile: mobile},
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
/*管理员-角色-删除*/
function admin_role_del(obj, id) {

    layer.confirm('角色删除须谨慎，确认要删除吗？', function (index) {


        $.ajax({
            type: "get",
            url: "/Admin/Rbac/admin_role_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });
}

/*管理员-权限-添加*/
function addadminpermission() {

    var pid = $('.pid').val();
    var name = $('.name').val();
    var control_action = $('.control_action').val();
    $.ajax({
        type: "post",
        url: "/Admin/Rbac/addadminpermission",
        data: {pid: pid, name: name, control_action: control_action},
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


/*管理员-权限-删除*/
function admin_permission_del(obj, id) {

    layer.confirm('节点删除须谨慎，确认要删除吗？', function (index) {


        $.ajax({
            type: "get",
            url: "/Admin/Rbac/del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }


            }
        });
    });

}
//保存节点资料
function power_edit_save(id) {

    var name = $('.name').val();
    var control_action = $('.control_action').val();
    var pid = $('.pid').val();
    var sort = $('.sort').val();
    var level = $('.level').val();
    var style = $('.style').val();
    $.ajax({
        type: "post",
        url: "/Admin/Rbac/poweredit",
        data: {id: id, name: name, control_action: control_action, pid: pid, sort: sort, level: level, style: style},
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


//保存管理员资料
function admin_edit_save(id) {



    var sex = $('input[name="sex"]:checked').val();
    var groupid = $("#groupid  option:selected").val();
    var username = $('.username').val();
    var email = $('.email').val();
    var address = $('.address').val();
    var abstract = $('.abstract').val();
    var mobile = $('.mobile').val();
    var code = $('.code').val();

    $.ajax({
        type: "post",
        url: "/Admin/Rbac/adminedit",
        data: {id: id, username: username, sex: sex, email: email, address: address, abstract: abstract, mobile: mobile, groupid: groupid, code: code},
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

/*管理员-删除*/
function admin_del(obj, id) {
    layer.confirm('角色删除须谨慎，确认要删除吗？', function (index) {


        $.ajax({
            type: "get",
            url: "/Admin/Rbac/admin_del",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 2000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 2000});
                }

            }
        });
    });

}
/*管理员-停用*/
function admin_stop(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Rbac/admin_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="admin_start(this,' + id + ')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已冻结</span>');
                $(obj).remove();

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });



}
/*管理员-启用*/
function admin_start(obj, id) {
    $.ajax({
        type: "get",
        url: "/Admin/Rbac/admin_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="admin_stop(this,' + id + ')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已启用</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });
}

//基本设置
function baseconfig()
{
    var onoff = $('input[name="onoff"]:checked').val();
    var overtime = $('input[name="overtime"]:checked').val();
    var webname = $('.webname').val();
    var weburl = $('.weburl').val();
    var title = $('.title').val();
    var keywords = $('.keywords').val();
    var description = $('.description').val();
    var copyright = $('.copyright').val();
    var icp = $('.icp').val();
    var cnzz = $('.cnzz').val();
    var ip = $('.ip').val();
    var num = $('.num').val();
    var email_status = $('input[name="email_status"]:checked').val();
    var smtpserver = $('.smtpserver').val();
    var smtpport = $('.smtpport').val();
    var smtpuser = $('.smtpuser').val();
    var smtppwd = $('.smtppwd').val();
    var interst = $('.interst').val();
    var sms_status = $('input[name="sms_status"]:checked').val();
    var smsusername = $('.smsusername').val();
    var smspwd = $('.smspwd').val();
    var hottel = $('.hottel').val();

    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/index",
        data: {onoff: onoff, webname: webname, weburl: weburl, title: title, keywords: keywords, description: description, copyright: copyright,hottel:hottel,
            icp: icp, cnzz: cnzz, ip: ip, num: num, email_status: email_status, smtpserver: smtpserver, smtpport: smtpport, smtpuser: smtpuser, smtppwd: smtppwd,
            interst: interst, sms_status: sms_status, smsusername: smsusername, smspwd: smspwd, overtime: overtime},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 2000});
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 2000});
            }

        }
    });

}

//停用银行
function bank_stop(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Webconfig/bank_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="bank_start(this,' + id + ')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已停用</span>');
                $(obj).remove();

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});
            }

        }
    });


}
//启用银行
function bank_start(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Webconfig/bank_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="bank_stop(this,' + id + ')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已启用</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});
            }

        }
    });
}

//添加银行
function bankadd()
{

    var bankname = $('.bankname').val();
    var banknum = $('.banknum').val();
    var sort = $('.sort').val();

    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/bankadd",
        data: {bankname: bankname, sort: sort,banknum:banknum},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {

                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

//保存银行信息
function bank_edit_save(id) {

    var bankname = $('.bankname').val();
      var banknum = $('.banknum').val();
    var sort = $('.sort').val();

    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/bankedit",
        data: {id: id, bankname: bankname, sort: sort,banknum:banknum},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

/*银行-删除*/
function bankdel(obj, id) {
    layer.confirm('删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Webconfig/bankdel",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 1000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1000});
                }

            }
        });
    });
}

//停用银行
function pay_stop(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Webconfig/pay_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="pay_start(this,' + id + ')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已停用</span>');
                $(obj).remove();

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});
            }

        }
    });


}
//启用支付账号
function pay_start(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Webconfig/pay_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="pay_stop(this,' + id + ')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已启用</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});
            }

        }
    });
}

//添加支付账号
function payadd()
{

    var username = $('.username').val();
    var md5pwd = $('.md5pwd').val();
     var message = $('.message').val();


    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/payadd",
        data: {username: username, md5pwd: md5pwd,message:message},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {

                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

//保存支付信息
function pay_edit_save(id) {

    var username = $('.username').val();
      var md5pwd = $('.md5pwd').val();
         var message = $('.message').val();
    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/payedit",
        data: {id: id, username: username, md5pwd: md5pwd,message:message},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

/*支付账号-删除*/
function paydel(obj, id) {
    layer.confirm('删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Webconfig/paydel",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 1000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1000});
                }

            }
        });
    });
}

function banks_stop(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Webconfig/banks_stop",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="banks_start(this,' + id + ')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label">已停用</span>');
                $(obj).remove();

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});
            }

        }
    });


}
//启用银行
function banks_start(obj, id) {

    $.ajax({
        type: "get",
        url: "/Admin/Webconfig/banks_start",
        data: {id: id},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="banks_stop(this,' + id + ')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success">已启用</span>');
                $(obj).remove();
            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});
            }

        }
    });
}

//添加收款银行
function bankadds()
{

    var bankname = $('.bankname').val();
    var bankno = $('.bankno').val();
    var name = $('.name').val();
    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/bankadds",
        data: {bankname: bankname, bankno: bankno, name: name},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {

                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

//保存银行信息
function bank_edit_saves(id) {

    var bankname = $('.bankname').val();
    var bankno = $('.bankno').val();
    var name = $('.name').val();

    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/bankedits",
        data: {id: id, bankname: bankname, bankno: bankno, name: name},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

/*银行-删除*/
function bankdels(obj, id) {
    layer.confirm('删除须谨慎，确认要删除吗？', function (index) {

        $.ajax({
            type: "get",
            url: "/Admin/Webconfig/bankdels",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {

                    $(obj).parents("tr").remove();
                    layer.msg(data.msg, {icon: 1, time: 1000});
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1000});
                }

            }
        });
    });
}
//添加快递公司
function expressadd()
{

    var expressNum = $('.expressNum').val();
    var title = $('.title').val();

    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/expressadd",
        data: {expressNum: expressNum, title: title},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}

function express_edit_save(id) {

    var expressNum = $('.expressNum').val();
    var title = $('.title').val();

    $.ajax({
        type: "post",
        url: "/Admin/Webconfig/expressedit",
        data: {id: id, expressNum: expressNum, title: title},
        dataType: 'json',
        async: false, //设置为同步操作就可以给全局变量赋值成功 
        success: function (data) {
            if (data.status == 1)
            {

                layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.location.reload();
                    parent.layer.close(index);
                });

            } else
            {
                layer.msg(data.msg, {icon: 2, time: 1000});

            }

        }
    });

}
function expressdel(obj, id) {
    layer.confirm('确认要删除吗？', function (index) {
        $.ajax({
            type: "get",
            url: "/Admin/Webconfig/expressdel",
            data: {id: id},
            dataType: 'json',
            async: false, //设置为同步操作就可以给全局变量赋值成功 
            success: function (data) {
                if (data.status == 1)
                {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function () {

                        location.reload();

                    });
                } else
                {
                    layer.msg(data.msg, {icon: 2, time: 1000});
                }

            }
        });
    });
}

function set_code(id)
{


    $.ajax({
        type: "post",
        url: "/Admin/Rbac/set_code",
        data: {id: id},
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
                    location.replace(location.href);
                });
            }

        }
    });
}