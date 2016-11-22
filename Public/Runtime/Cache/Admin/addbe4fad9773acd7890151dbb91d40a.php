<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="/Public/H-ui/lib/html5.js"></script>
<script type="text/javascript" src="/Public/H-ui/lib/respond.min.js"></script>
<script type="text/javascript" src="/Public/H-ui/lib/PIE_IE678.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="/Public/H-ui/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/Public/H-ui/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/Public/H-ui/lib/Hui-iconfont/1.0.7/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/Public/H-ui/lib/icheck/icheck.css" />
<link rel="stylesheet" type="text/css" href="/Public/H-ui/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/Public/H-ui/static/h-ui.admin/css/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<title>会员查看</title>
</head>
<body>
    <div class="cl pd-20" style=" background-color:#5bacb6">
        <img class="avatar avatar-XL l" src="/Public/H-ui/static/h-ui/images/user.png">
        <dl style="margin-left:80px; color:#fff">
            <dt><span class="f-18">账号：<?php echo ($userInfo["username"]); ?></span> <!--<span class="pl-10 f-12">推荐人：<?php echo ($member_row["recommend"]); ?></span>--></dt>
            <dd class="pt-10 f-12" style="margin-left:0"></dd>
        </dl>
    </div>
    <div class="pd-20">
        <table class="table">
            <tbody> 
                <tr>
                    <th class="text-r" width="200">市场积分：</th>
                    <td><?php echo ($userInfo["allbonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r" width="200">活动积分：</th>
                    <td><?php echo ($userInfo["alljingtaibonus"]); ?></td>
                </tr>
                 <tr>
                    <th class="text-r" width="200">现金积分：</th>
                    <td><?php echo ($userInfo["cash"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r" width="200">开发奖：</th>
                    <td><?php echo ($userInfo["lingdaobonus"]); ?></td>
                </tr>
                  <tr>
                    <th class="text-r" width="200">红包奖：</th>
                    <td><?php echo ($userInfo["hongbaobonus"]); ?></td>
                </tr>
                 <tr>
                    <th class="text-r" width="200">领导奖：</th>
                    <td><?php echo ($userInfo["daishubonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">管理奖：</th>
                    <td><?php echo ($userInfo["guanlibonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">晋级奖：</th>
                    <td><?php echo ($userInfo["jinjibonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">全球分红：</th>
                    <td><?php echo ($userInfo["quanqiufenhongbonus"]); ?></td>
                </tr>
                                <tr>
                    <th class="text-r">月薪奖：</th>
                    <td><?php echo ($userInfo["yuexinbonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">溢价积分：</th>
                    <td><?php echo ($userInfo["zengzhibonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">分红积分：</th>
                    <td><?php echo ($userInfo["fenhongbonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">电子积分：</th>
                    <td class="text-l"><?php echo ($userInfo["dianzimoney"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">购物卷：</th>
                    <td class="text-l"><?php echo ($userInfo["gouwujuan"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">购物积分：</th>
                    <td class="text-l"><?php echo ($userInfo["gouwujifen"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">个人所得税：</th>
                    <td class="text-l"><?php echo ($userInfo["gerensuodeshui"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">旅游基金：</th>
                    <td class="text-l"><?php echo ($userInfo["lvyoubonus"]); ?></td>
                </tr>     
                <tr>
                    <th class="text-r">名车基金：</th>
                    <td class="text-l"><?php echo ($userInfo["mingchebonus"]); ?></td>
                </tr>       
                <tr>
                    <th class="text-r">别墅基金：</th>
                    <td class="text-l"><?php echo ($userInfo["bieshubonus"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">重消积分：</th>
                    <td class="text-l"><?php echo ($userInfo["chongfuxiaofei"]); ?></td>
                </tr>
                  <tr>
                    <th class="text-r">月薪奖池：</th>
                    <td class="text-l"><?php echo ($userInfo["chendianchi"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">赠送积分：</th>
                    <td class="text-l"><?php echo ($userInfo["integral"]); ?></td>
                </tr>

                <tr>
                    <th class="text-r">登录次数：</th>
                    <td><?php echo ($userInfo["lognum"]); ?></td>
                </tr>
                <tr>
                    <th class="text-r">登录ip：</th>
                    <td><?php echo ($userInfo["logip"]); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
 
<script type="text/javascript" src="/Public/H-ui/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/Public/H-ui/lib/layer/2.1/layer.js"></script>
<script type="text/javascript" src="/Public/H-ui/lib/laypage/1.2/laypage.js"></script> 
<script type="text/javascript" src="/Public/H-ui/lib/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="/Public/H-ui/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="/Public/H-ui/lib/icheck/jquery.icheck.min.js"></script> 
<script type="text/javascript" src="/Public/H-ui/lib/jquery.validation/1.14.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="/Public/H-ui/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/Public/H-ui/lib/jquery.validation/1.14.0/messages_zh.min.js"></script>
<script type="text/javascript" src="/Public/H-ui/lib/Validform/5.3.2/Validform_v5.3.2_min.js"></script>
<script type="text/javascript" src="/Public/H-ui/static/h-ui/js/H-ui.js"></script> 
<script type="text/javascript" src="/Public/H-ui/static/h-ui.admin/js/H-ui.admin.js"></script> 

</body>
</html>