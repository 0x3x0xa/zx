<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit|ie-comp|ie-stand">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <!--[if lt IE 9]>
        <script type="text/javascript" src="/Public/H-ui//Public/H-ui/lib/html5.js"></script>
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
        <script type="text/javascript" src="/Public/H-ui/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title>我的桌面</title>
    </head>
    <body>
        <div class="pd-20" style="padding-top:20px;">
            <p class="f-20 text-success">欢迎使用<?php echo ($config["webname"]); ?>网站后台管理系统</p>
            <p>登录次数：<?php echo ($userinfo['lognum']); ?>   上次登录IP：<?php echo ($userinfo['logip']); ?>  上次登录时间：<?php echo (date('Y-m-d H:i:s',$userinfo['lasttime'])); ?></p>
            <table class="table table-border table-bordered table-bg">
                <thead>
                    <tr>
                        <th colspan="14" scope="col">信息统计 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></th>
                    </tr>
                    <tr class="text-c">
                        <th>统计</th>
                        <th>人数</th>
                        <th>新增业绩</th>
                        <th>拨出奖金</th>
                        <th>现金积分</th>
                        <th>开发奖</th>
                        <th>管理奖</th>
                        <th>领导奖</th>
                        <th>全球分红</th>
                        <th>月薪奖</th>
                        <th>溢价积分</th>
                        <th>分红积分</th>
                        <th>市场积分</th>
                        <th>活动积分</th>
                    </tr>
                </thead>
                <tbody>
                      <tr class="text-c">
                        <td>余数</td>
                        <td><?php echo ($data1[0]); ?></td>
                        <td><?php echo ($data2[0]); ?></td>
                        <td><?php echo ($data3[0]); ?></td>
                        <td><?php echo ($data4[0]); ?></td>
                        <td><?php echo ($data5[0]); ?></td>
                        <td><?php echo ($data6[0]); ?></td>
                        <td><?php echo ($data7[0]); ?></td>
                        <td><?php echo ($data8[0]); ?></td>
                        <td><?php echo ($data9[0]); ?></td>
                        <td><?php echo ($data10[0]); ?></td>
                        <td><?php echo ($data11[0]); ?></td>
                        <td><?php echo ($data12[0]); ?></td>
                        <td><?php echo ($data12[1]); ?></td>
                    </tr>
                    <tr class="text-c">
                        <td>总数</td>
                        <td><?php echo ($data1[5]); ?></td>
                        <td><?php echo ($data2[5]); ?></td>
                        <td><?php echo ($data3[5]); ?></td>
                        <td><?php echo ($data4[5]); ?></td>
                        <td><?php echo ($data5[5]); ?></td>
                        <td><?php echo ($data6[5]); ?></td>
                        <td><?php echo ($data7[5]); ?></td>
                        <td><?php echo ($data8[5]); ?></td>
                        <td><?php echo ($data9[5]); ?></td>
                        <td><?php echo ($data10[5]); ?></td>
                        <td><?php echo ($data11[5]); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-c">
                        <td>今日</td>
                        <td><?php echo ($data1[1]); ?></td>
                        <td><?php echo ($data2[1]); ?></td>
                        <td><?php echo ($data3[1]); ?></td>
                        <td><?php echo ($data4[1]); ?></td>
                        <td><?php echo ($data5[1]); ?></td>
                        <td><?php echo ($data6[1]); ?></td>
                        <td><?php echo ($data7[1]); ?></td>
                        <td><?php echo ($data8[1]); ?></td>
                        <td><?php echo ($data9[1]); ?></td>
                        <td><?php echo ($data10[1]); ?></td>
                        <td><?php echo ($data11[1]); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-c">
                        <td>昨日</td>
                        <td><?php echo ($data1[2]); ?></td>
                        <td><?php echo ($data2[2]); ?></td>
                        <td><?php echo ($data3[2]); ?></td>
                        <td><?php echo ($data4[2]); ?></td>
                        <td><?php echo ($data5[2]); ?></td>
                        <td><?php echo ($data6[2]); ?></td>
                        <td><?php echo ($data7[2]); ?></td>
                        <td><?php echo ($data8[2]); ?></td>
                        <td><?php echo ($data9[2]); ?></td>
                        <td><?php echo ($data10[2]); ?></td>
                        <td><?php echo ($data11[2]); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-c">
                        <td>本周</td>
                        <td><?php echo ($data1[3]); ?></td>
                        <td><?php echo ($data2[3]); ?></td>
                        <td><?php echo ($data3[3]); ?></td>
                        <td><?php echo ($data4[3]); ?></td>
                        <td><?php echo ($data5[3]); ?></td>
                        <td><?php echo ($data6[3]); ?></td>
                        <td><?php echo ($data7[3]); ?></td>
                        <td><?php echo ($data8[3]); ?></td>
                        <td><?php echo ($data9[3]); ?></td>
                        <td><?php echo ($data10[3]); ?></td>
                        <td><?php echo ($data11[3]); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-c">
                        <td>本月</td>
                        <td><?php echo ($data1[4]); ?></td>
                        <td><?php echo ($data2[4]); ?></td>
                        <td><?php echo ($data3[4]); ?></td>
                        <td><?php echo ($data4[4]); ?></td>
                        <td><?php echo ($data5[4]); ?></td>
                        <td><?php echo ($data6[4]); ?></td>
                        <td><?php echo ($data7[4]); ?></td>
                        <td><?php echo ($data8[4]); ?></td>
                        <td><?php echo ($data9[4]); ?></td>
                        <td><?php echo ($data10[4]); ?></td>
                        <td><?php echo ($data11[4]); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
<!--            <table class="table table-border table-bordered table-bg mt-20">
                <thead>
                    <tr>
                        <th colspan="2" scope="col">服务器信息</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th width="200">系统</th>
                        <td><span id="lbServerName"><?php echo ($systeminfo["os"]); ?></span></td>
                    </tr>

                    <tr>
                        <td>客户端IP </td>
                        <td><?php echo ($systeminfo["remote_addr"]); ?></td>
                    </tr>
                    <tr>
                        <td>服务器端IP</td>
                        <td><?php echo ($systeminfo["server_name"]); ?></td>
                    </tr>
                    <tr>
                        <td>脚本运行占用最大内存 </td>
                        <td><?php echo ($systeminfo["memorylimit"]); ?></td>
                    </tr>
                    <tr>
                        <td>最大上传文件大小</td>
                        <td><?php echo ($systeminfo["maxuploadfile"]); ?></td>
                    </tr>

                    <tr>
                        <td>PHP版本 </td>
                        <td><?php echo ($systeminfo["phpversion"]); ?></td>
                    </tr>
                    <tr>
                        <td>ZEND版本 </td>
                        <td><?php echo ($systeminfo["zendversion"]); ?></td>
                    </tr>
                    <tr>
                        <td>服务器当前时间 </td>
                        <td><?php echo ($systeminfo["nowtime"]); ?></td>
                    </tr>

                </tbody>
            </table>
        </div>-->
        <script type="text/javascript" src="/Public/H-ui/lib/jquery/1.9.1/jquery.min.js"></script> 
        <script type="text/javascript" src="/Public/H-ui/static/h-ui/js/H-ui.js"></script> 
    </body>
</html>