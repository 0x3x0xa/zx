<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE HTML>
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
<title></title>
</head>
<body>
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参数管理 <span class="c-gray en">&gt;</span> 参数设置 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="pd-20">
        <form method="post" action="/index.php/Admin/Param/setparam.html">
            <table class="table table-border table-bordered table-hover table-bg">
                <tbody>
                    <tr>
                        <th class="text-r" width="200">会员账号前缀：</th><td><input type="text" name='memberAccountPrefix' maxlength="6"  placeholder="" value="<?php echo ($setparam["memberAccountPrefix"]); ?>" style="width:200px" class="input-text memberAccountPrefix"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">会员账号长度：</th><td><input type="number" name='maxlength' maxlength="1" min="6" max="10" precision="0" size="16"  value="<?php echo ($setparam["maxlength"]); ?>" style="width:200px" class="input-text maxlength"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">一个手机号可注册会员数：</th><td><input type="number" name='registMobileNum' maxlength="1" min="0" max="10" precision="0" size="16"  value="<?php echo ($setparam["registMobileNum"]); ?>" style="width:200px" class="input-text registMobileNum">(0表示不限制)</td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">一个身份证号可注册会员数：</th><td><input type="number" name='registIdcardNum' maxlength="1" min="0" max="10" precision="0" size="16"  value="<?php echo ($setparam["registIdcardNum"]); ?>" style="width:200px" class="input-text registIdcardNum">(0表示不限制)</td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">一个银行卡号可注册会员数：</th><td><input type="number" name='registBanknoNum' maxlength="1" min="0" max="10" precision="0" size="16"  value="<?php echo ($setparam["registBanknoNum"]); ?>" style="width:200px" class="input-text registBanknoNum">(0表示不限制)</td>
                    </tr>
                      <tr>
                        <th class="text-r" width="200">企业利润卖出：</th>
                         
                            <td><div class="radio-box">
                                    <input name="qiyeliruanmaichu_status" type="radio" id="sex-1"  value="1" <?php if($setparam["qiyeliruanmaichu_status"] == 1 ): ?>checked="checked"<?php endif; ?>>
					<label for="sex-1">开启</label>
				</div>
                             <div class="radio-box">
					<input name="qiyeliruanmaichu_status" type="radio" id="sex-1" value="0" <?php if($setparam["qiyeliruanmaichu_status"] == 0 ): ?>checked="checked"<?php endif; ?> >
					<label for="sex-1">关闭</label>
				</div>
                           </td>
                    </tr>
                        <tr>
                        <th class="text-r" width="200">活动：</th>
                         
                            <td><div class="radio-box">
                                    <input name="zengsong_status" type="radio" id="sex-1"  value="1" <?php if($setparam["zengsong_status"] == 1 ): ?>checked="checked"<?php endif; ?>>
					<label for="sex-1">开启</label>
				</div>
                             <div class="radio-box">
					<input name="zengsong_status" type="radio" id="sex-1" value="0" <?php if($setparam["zengsong_status"] == 0 ): ?>checked="checked"<?php endif; ?> >
					<label for="sex-1">关闭</label>
				</div>
                            首次120天送：<input type="text" name='zengsongfenhong' placeholder="" value="<?php echo ($setparam["zengsongfenhong"]); ?>" style="width:200px" class="input-text zengsongfenhong"></td>
                    </tr>
                     <tr>
                        <th class="text-r" width="200">升级钻卡赠送积分参数：</th>
                          <td> <input type="text" name='zuankacanshu' placeholder="" value="<?php echo ($setparam["zuankacanshu"]); ?>" style="width:200px" class="input-text zuankacanshu"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">重消积分比例：</th><td><input type="text" name='chongfuxiaofei' placeholder="" value="<?php echo ($setparam["chongfuxiaofei"]); ?>" style="width:200px" class="input-text chongfuxiaofei"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">个人税比例：</th><td><input type="text" name='gerensuodeshui' placeholder="" value="<?php echo ($setparam["gerensuodeshui"]); ?>" style="width:200px" class="input-text gerensuodeshui"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">名车基金比例：</th><td><input type="text" name='mingchejiang' placeholder="" value="<?php echo ($setparam["mingchejiang"]); ?>" style="width:200px" class="input-text mingchejiang"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">别墅基金比例：</th><td><input type="text" name='bieshujiang' placeholder="" value="<?php echo ($setparam["bieshujiang"]); ?>" style="width:200px" class="input-text bieshujiang"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">旅游基金比例：</th><td><input type="text" name='lvyouBonus' placeholder="" value="<?php echo ($setparam["lvyouBonus"]); ?>" style="width:200px" class="input-text lvyouBonus"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">公益基金比例：</th><td><input type="text" name='gongyijijin' placeholder="" value="<?php echo ($setparam["gongyijijin"]); ?>" style="width:200px" class="input-text gongyijijin"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">提现扣除比例：</th><td><input type="text" name='tixian' placeholder="" value="<?php echo ($setparam["tixian"]); ?>" style="width:200px" class="input-text tixian"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">月薪奖划分比例：</th><td><input type="text" name='yuexinhuafen' placeholder="" value="<?php echo ($setparam["yuexinhuafen"]); ?>" style="width:200px" class="input-text yuexinhuafen"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">月薪奖结算比例：</th><td><input type="text" name='yuexin' placeholder="" value="<?php echo ($setparam["yuexin"]); ?>" style="width:200px" class="input-text yuexin"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">溢价积分总利润的划分比例：</th><td><input type="text" name='zenzhizonglirunhuafenbili' placeholder="" value="<?php echo ($setparam["zenzhizonglirunhuafenbili"]); ?>" style="width:200px" class="input-text zenzhizonglirunhuafenbili"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">分红积分总利润的划分比例：</th><td><input type="text" name='fenhongzonglirunhuafenbili' placeholder="" value="<?php echo ($setparam["fenhongzonglirunhuafenbili"]); ?>" style="width:200px" class="input-text fenhongzonglirunhuafenbili"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">红包奖比例：</th><td><input type="text" name='hongbao' placeholder="" value="<?php echo ($setparam["hongbao"]); ?>" style="width:200px" class="input-text hongbao"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">红包奖可拿层数：</th><td><input type="text" name='hongbaolevel' placeholder="" value="<?php echo ($setparam["hongbaolevel"]); ?>" style="width:200px" class="input-text hongbaolevel"></td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">管理奖比例设置：</th><td>
                            一代： <input type="text" name='guanlione' placeholder="" value="<?php echo ($setparam["guanlione"]); ?>" style="width:100px" class="input-text guanlione"> <!--平级： <input type="text" name='guanlipingji' placeholder="" value="<?php echo ($setparam["guanlipingji"]); ?>" style="width:100px" class="input-text guanlipingji">-->
                            二代： <input type="text" name='guanlitwo' placeholder="" value="<?php echo ($setparam["guanlitwo"]); ?>" style="width:100px" class="input-text guanlitwo">
                            三代： <input type="text" name='guanlithree' placeholder="" value="<?php echo ($setparam["guanlithree"]); ?>" style="width:100px" class="input-text guanlithree">
                            四代： <input type="text" name='guanlifour' placeholder="" value="<?php echo ($setparam["guanlifour"]); ?>" style="width:100px" class="input-text guanlifour">
                            五代： <input type="text" name='guanlifive' placeholder="" value="<?php echo ($setparam["guanlifive"]); ?>" style="width:100px" class="input-text guanlifive">
                            六代： <input type="text" name='guanlisix' placeholder="" value="<?php echo ($setparam["guanlisix"]); ?>" style="width:100px" class="input-text guanlisix">

                        </td>
                    </tr>
                        <tr>
                      <th class="text-r" width="200">领导奖比例设置：</th><td>
                            一代： <input type="text" name='daishuone' placeholder="" value="<?php echo ($setparam["daishuone"]); ?>" style="width:100px" class="input-text guanlione"> 
                            二代： <input type="text" name='daishutwo' placeholder="" value="<?php echo ($setparam["daishutwo"]); ?>" style="width:100px" class="input-text guanlitwo">
                            三代： <input type="text" name='daishuthree' placeholder="" value="<?php echo ($setparam["daishuthree"]); ?>" style="width:100px" class="input-text guanlithree">
                            四代： <input type="text" name='daishufour' placeholder="" value="<?php echo ($setparam["daishufour"]); ?>" style="width:100px" class="input-text guanlifour">
                            五代： <input type="text" name='daishufive' placeholder="" value="<?php echo ($setparam["daishufive"]); ?>" style="width:100px" class="input-text guanlifive">
                            六代： <input type="text" name='daishusix' placeholder="" value="<?php echo ($setparam["daishusix"]); ?>" style="width:100px" class="input-text guanlisix">
                            七代： <input type="text" name='daishuseven' placeholder="" value="<?php echo ($setparam["daishuseven"]); ?>" style="width:100px" class="input-text guanlisix">
                        </td>
                    </tr>
                    <tr>
                        <th class="text-r" width="200">全球分红：</th>
                        <td>
                            四星比例<input type="text" name='quanqiufenhongfour' placeholder="" value="<?php echo ($setparam["quanqiufenhongfour"]); ?>" style="width:200px" class="input-text quanqiufenhongfour">
                            五星比例<input type="text" name='quanqiufenhongfive' placeholder="" value="<?php echo ($setparam["quanqiufenhongfive"]); ?>" style="width:200px" class="input-text quanqiufenhongfive">
                            董事比例<input type="text" name='quanqiufenhongdongshi' placeholder="" value="<?php echo ($setparam["quanqiufenhongdongshi"]); ?>" style="width:200px" class="input-text quanqiufenhongdongshi">
                        </td>
                    </tr>
                      <tr>
                        <th class="text-r" width="200">提现设置：</th>
                        <td>
                           最底：<input type="text" name='tixianmin' placeholder="" value="<?php echo ($setparam["tixianmin"]); ?>" style="width:200px" class="input-text tixianmin">
                           最高：<input type="text" name='tixianmax' placeholder="" value="<?php echo ($setparam["tixianmax"]); ?>" style="width:200px" class="input-text tixianmax">
                           倍数：<input type="text" name='tixianbeishu' placeholder="" value="<?php echo ($setparam["tixianbeishu"]); ?>" style="width:200px" class="input-text tixianbeishu">
                        </td>
                    </tr>
                      <tr>
                        <th class="text-r" width="200">充值设置：</th>
                        <td>
                           最底：<input type="text" name='chongzhimin' placeholder="" value="<?php echo ($setparam["chongzhimin"]); ?>" style="width:200px" class="input-text chongzhimin">
                           最高：<input type="text" name='chongzhimax' placeholder="" value="<?php echo ($setparam["chongzhimax"]); ?>" style="width:200px" class="input-text chongzhimax">
                           倍数：<input type="text" name='chongzhibeishu' placeholder="" value="<?php echo ($setparam["chongzhibeishu"]); ?>" style="width:200px" class="input-text chongzhibeishu">
                        </td>
                    </tr>
<!--                      <tr>
                        <th class="text-r" width="200">分红积分：</th>
                        <td>
                            第一个单价<input type="text" name='fenhongdanjia' placeholder="" value="<?php echo ($setparam["fenhongdanjia"]); ?>" style="width:200px" class="input-text fenhongdanjia">
                            增长比例<input type="text" name='fenhongzenzhang' placeholder="" value="<?php echo ($setparam["fenhongzenzhang"]); ?>" style="width:200px" class="input-text fenhongzenzhang">
                           
                        </td>
                    </tr>-->
                    <tr>
                        <th class="text-r" width="200">开放右市场  中市场业绩：</th><td><input type="text" name='centermoney' placeholder="" value="<?php echo ($setparam["centermoney"]); ?>" style="width:200px" class="input-text centermoney"></td>
                    </tr>
                    <tr>
                        <th class="text-r"></th><td><button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i>  确定</button></td>
                    </tr> 

                </tbody>
            </table>
    </div>	
</form>
 
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