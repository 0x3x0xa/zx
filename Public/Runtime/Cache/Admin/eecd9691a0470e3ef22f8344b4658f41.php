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
<title>公告管理</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 公告管理 <span class="c-gray en">&gt;</span> 公告列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
<form method="post" action="/index.php/Admin/Article/index.html">
	<div class="text-c"> 
            类别：
	<span class="select-box" style='width:200px;'>
	<select name="search_type"  class='select'  value='<?php echo ($arr["search_type"]); ?>'>
	<option value="">全部显示</option>
         <?php if(is_array($list1)): $i = 0; $__LIST__ = $list1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo1["id"]); ?>" <?php if($arr["search_type"] == $vo1["id"]): ?>selected <?php elseif($_GET['search_type']== $vo1['id']): ?>selected<?php endif; ?>><?php echo (htmlspecialchars_decode($vo1["type_name"])); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
  </select> 
  </span>
 &nbsp;&nbsp;&nbsp;&nbsp;
	日期范围：
    <input type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" name='search_starttime' class="input-text Wdate" style="width:120px;" value="<?php echo ($arr['search_starttime']); ?>">
    -
    <input type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" name='search_endtime' id="datemax" class="input-text Wdate" style="width:120px;" value="<?php echo ($arr['search_endtime']); ?>">
    <input type="text" class="input-text" style="width:250px" placeholder="输入公告名称" id="" value="<?php echo ($arr['search_title']); ?>" name="search_title" ><button type="submit" class="btn btn-success" id="" ><i class="Hui-iconfont">&#xe665;</i> 搜公告</button> </form>
	</div></form>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
	<a href="javascript:;"  onClick="article_datadel()"  class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
	<a href="javascript:;" onClick="showPage('1050','','添加公告','<?php echo U('Article/articleadd');?>')"  class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加公告</a></span> <span class="r">共有数据：<strong><?php echo ($count); ?></strong> 条</span> </div>
	<div class="mt-0">
	<table class="table table-border table-bordered table-hover table-bg ">
		<thead>
			<tr class="text-c">
			  <th width="25"><input type="checkbox" name="" value=""></th>
        <th width="80">NO</th>
        <th>标题</th>
        <th width="280">类别</th>
        <th width="80">作者</th>
        <th width="120">更新时间</th>
       
        <th width="60">发布状态</th>
        <th width="70">操作</th>
			</tr>
		</thead>
		<tbody>
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
        <td><input type="checkbox" value="<?php echo ($vo["id"]); ?>" name="delid"></td>
        <td><?php echo ($i); ?></td>
        <td class="text-l"><u style="cursor:pointer" class="text-primary" onClick="showPage('940','','查看','<?php echo U('Article/articlezhang',array('id'=>$vo['id']));?>')" title="查看"><?php echo ($vo["art_title"]); ?></u></td>
        <td><?php echo ($vo["type"]); ?></td>
        <td><?php echo ($vo["art_author"]); ?></td>
        <td><?php echo (date('Y-m-d H:i:s',$vo["art_time"])); ?></td>
		<?php if($vo["art_status"] == 2 ): ?><td class="td-status"><span  class="label">已下架</span></td>
        <td class="f-14 td-manage"><a style="text-decoration:none" onClick="article_fabu(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe631;</i></a>
		<?php else: ?>
		<td class="td-status"><span class="label label-success">已发布</span></td>
        <td class="f-14 td-manage"><a style="text-decoration:none" onClick="article_xiajia(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title=""><i class="Hui-iconfont">&#xe6e1;</i></i></a><?php endif; ?>
		<a style="text-decoration:none" class="ml-5" onClick="showPage('1050','','公告编辑','<?php echo U('Article/articleedit',array('id'=>$vo['id']));?>')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i>
</a> <a style="text-decoration:none" class="ml-5" onClick="article_del(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a></td><?php endforeach; endif; else: echo "" ;endif; ?>
		
		
	 
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