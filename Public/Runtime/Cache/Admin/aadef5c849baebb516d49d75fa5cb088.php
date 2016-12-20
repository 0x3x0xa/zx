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
<title>会员列表</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span>会员管理 <span class="c-gray en">&gt;</span>会员列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
<form method="get" action="/index.php/Admin/Member/index.html?search_level=&amp;search_username=nc123456&amp;search_starttime=&amp;search_endtime=&amp;token=6b0bbb368c3a99becd4deef87ecae23a_a74a4b206ffba9e0198d38c90a41e898">
	<div class="text-c"> 
            
           <span class="select-box" style='width:140px;'>
            <select name="search_level"  class='select'  value=''>
            <option value="">选择会员等级</option>
            <?php if(is_array($memberlevel)): $i = 0; $__LIST__ = $memberlevel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"  <?php if($vo["id"] == $arr['search_level']): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select> 
          </span>
	<input type="text" class="input-text" style="width:150px" placeholder="输入账号" id="" value="<?php echo ($arr['search_username']); ?>" name="search_username" >
	日期范围：
    <input type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" name='search_starttime' class="input-text Wdate" style="width:120px;" value="<?php echo ($arr['search_starttime']); ?>">
    -
    <input type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" name='search_endtime' id="datemax" class="input-text Wdate" style="width:120px;" value="<?php echo ($arr['search_endtime']); ?>">
    <button type="submit" class="btn btn-success" id="" ><i class="Hui-iconfont">&#xe665;</i> 搜索</button> 
	
	</div></form>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l" >
              <button type="button" class="btn btn-success excel" id="" onClick="showPage('600','350','充值','<?php echo U('Member/addMemberCoin');?>')" href="javascript:;" ><i class="Hui-iconfont">&#xe600;</i> 充值</button>
              <button type="button" class="btn btn-success excel" id="" onClick="showPage('600','350','扣币','<?php echo U('Member/subtractMemberCoin');?>')" href="javascript:;" ><i class="Hui-iconfont">&#xe6df;</i> 扣币</button>
              <button type="button" class="btn btn-success excel" id="" onClick="showPage('600','350','重置密码','<?php echo U('Member/userpasswordedit');?>')" href="javascript:;"  ><i class="Hui-iconfont">&#xe6df;</i> 重置密码</button>
              <button type="button" class="btn btn-success excel" id="" onClick="showPage('600','250','修改推荐人','<?php echo U('Member/openEditRecommend');?>')"><i class="Hui-iconfont">&#xe6df;</i> 修改推荐人</button>
              <button type="button" class="btn btn-success excel" id="" onClick="showPage('600','320','会员升级','<?php echo U('Member/upgrade');?>')"><i class="Hui-iconfont">&#xe679;</i> 会员升级</button>
              <button type="button" class="btn btn-success excel" id="" onclick='window.location.href="<?php echo U("Member/downloadexcel");?>"' ><i class="Hui-iconfont">&#xe640;</i> Excel</button>
        </span>  <span class="r">共有数据：<strong><?php echo ($count); ?></strong> 条</span> </div>
	<div class="mt-0">
	<table class="table table-border table-bordered table-hover table-bg ">
		<thead>
			<tr class="text-c">
			<th width="">ID</th>
			<th width="">会员账号</th>
			<th width="">会员昵称</th>
                        <th width="">开户姓名</th>
                        <th width="">会员等级</th>
                        <th width="">会员星级</th>
                        <th width="">手机号码</th>
			<th width="">推荐人</th>
                        <th width="">接点人</th>
                        <th width="">创建人</th>
			<th width="">区域</th>
			<!--<th width="">参与结算</th>-->
			<th width="120">注册时间</th>
			<th width="30">状态</th>
			<th width="120">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
                <td><?php echo ($vo["id"]); ?></td>
                <td class="text-l"><u style="cursor:pointer" class="text-primary" onclick="showPage('460','650','<?php echo ($vo["username"]); ?>','<?php echo U('Member/usershow',array('id'=>$vo['id']));?>')"><?php echo ($vo["username"]); ?></u></td>
		<td class="text-l"><u style="cursor:pointer" class="text-primary"><a href='<?php echo U("/Home/Login/back_login",array("m"=>base64_encode($vo["id"])));?>' target='_blank'><?php echo ($vo["name"]); ?></a></u></td>
		<td class="text-l"><?php echo ($vo["account_name"]); ?></td>
		<td class="text-l"><?php echo ($vo["level"]); ?></td>
                <td class="text-l"><?php echo ($vo["position"]); ?></td>
                <td class="text-l"><?php echo ($vo["mobile"]); ?></td>
		<td class="text-l"><?php echo ($vo["recommend"]); ?></td>
		<td class="text-l"><?php echo ($vo["junction"]); ?></td>
		<td class="text-l"><?php echo ($vo["hid"]); ?></td>
                <td class="text-l"><?php echo ($vo["region"]); ?></td>
		<!--<td class="text-l"><?php echo ($vo["is_accounts"]); ?></td>-->
		<td class="text-l"><?php echo (date('Y-m-d H:i:s',$vo["regtime"])); ?></td>
		<?php if($vo["status"] == 3 ): ?><td class="td-status"><span  class="label">已冻结</span></td>
                <td class="f-14 td-manage"><a style="text-decoration:none" onClick="user_start(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe631;</i></a>
		<?php else: ?>
		<td class="td-status"><span class="label label-success">已启用</span></td>
                <td class="f-14 td-manage"><a style="text-decoration:none" onClick="user_stop(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe6e1;</i></a><?php endif; ?>
		<a title="编辑" href="javascript:;" onClick="showPage('660','620','修改会员信息','<?php echo U('Member/useredit',array('id'=>$vo['id']));?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> 
		
              </tr><?php endforeach; endif; else: echo "" ;endif; ?>
	 
	  </tbody>
	</table>  
	<div id="pageNav" class="pageNav"><?php echo ($page); ?></div>
	</div>
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