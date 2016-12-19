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
<title>已审核订单</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span>订单管理 <span class="c-gray en">&gt;</span>已审核订单 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
<form method="get" action="/index.php/Admin/Orders/listaudited.html">
	<div class="text-c"> 
            <span class="select-box" style='width:130px;'>
            <select name="searchCondition"  class='select'  value=''>
             <option value="username"  <?php if($arr['searchCondition'] == 'username' ): ?>selected="selected"<?php endif; ?> >会员账号</option>
            <option value="receiver" <?php if($arr['searchCondition'] == 'receiver' ): ?>selected="selected"<?php endif; ?> >收货人姓名</option>
            <option value="order_eg" <?php if($arr['searchCondition'] == 'order_eg' ): ?>selected="selected"<?php endif; ?> >订单号</option>
            </select> 
          </span>
		  <input type="text" class="input-text" style="width:150px" placeholder="" id="" value="<?php echo ($arr['search_value']); ?>" name="search_value" >
<!--           <span class="select-box" style='width:140px;'>
            <select name="orderType"  class='select'  value=''>
            <option value="">选择订单类型</option>
            <option value="1" <?php if($arr['orderType'] == 1 ): ?>selected='selected'<?php endif; ?>>注册报单</option>
            <option value="2" <?php if($arr['orderType'] == 2 ): ?>selected='selected'<?php endif; ?>>重复购买</option>
            </select> 
          </span>
          <span class="select-box" style='width:140px;'>
            <select name="delivery_mode"  class='select'>
            <option value="">选择提货方式</option>
            <option value="1" <?php if($arr['delivery_mode'] == 1 ): ?>selected='selected'<?php endif; ?>>自提</option>
            <option value="2" <?php if($arr['delivery_mode'] == 2 ): ?>selected='selected'<?php endif; ?>>物流</option>
            </select> 
          </span>
	-->
	日期范围：
    <input type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" name='search_starttime' class="input-text Wdate" style="width:120px;" value="<?php echo ($arr['search_starttime']); ?>">
    -
    <input type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" name='search_endtime' id="datemax" class="input-text Wdate" style="width:120px;" value="<?php echo ($arr['search_endtime']); ?>">
    <button type="submit" class="btn btn-success" id="" ><i class="Hui-iconfont">&#xe665;</i> 搜索</button> 
	
	</div></form>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l" >
            <button type="button" class="btn btn-success" id="" onClick="showFahuo('900','600','发货订单产品明细','<?php echo U('Orders/showFahuo');?>')"  ><i class="Hui-iconfont">&#xe669;</i> 发货</button>
<!--            <button type="button" class="btn btn-success excel" id="" onclick='window.location.href="<?php echo U("Orders/downloadexcel");?>"' ><i class="Hui-iconfont">&#xe640;</i> Excel</button>-->
        </span>  <span class="r">共有数据：<strong><?php echo ($count); ?></strong> 条</span> </div>
	<div class="mt-0">
	<table class="table table-border table-bordered table-hover table-bg ">
		<thead>
			<tr class="text-l">
                            <th width="25"><input type="checkbox" ></th>
			<th width="">ID</th>
			<th width="">会员账号</th>
			<th width="">会员呢称</th>
			<th width="">收货人姓名</th>
			<th width="">提货方式</th>
			<th width="">联系电话 </th>
			<th width="">收货地址 </th>
			<!--<th width="">创建人 </th>-->
			<th width="">创建时间</th>
			<th width="">订单号</th>
			<!--<th width="">订单类型 </th>-->
			<th width="">数量</th>
			<th width="">总金额</th>
			<th width="50">状态</th> 
                        <th width="100">备注</th>
                        <th width="60">操作</th>
                       
			</tr>
		</thead>
		<tbody>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-l">
                <td><input type="checkbox" name="orderId" value='<?php echo ($vo["id"]); ?>'></td>
                <td><?php echo ($vo["id"]); ?></td>
                <td class="text-l"><?php echo ($vo["username"]); ?></td>
                <td class="text-l"><?php echo ($vo["name"]); ?></td>
		<td class="text-l"><?php echo ($vo["receiver"]); ?></td>
                <td class="text-l"><?php echo ($vo["delivery_mode"]); ?></td>
		<td class="text-l"><?php echo ($vo["mobile"]); ?></td>
                <td class="text-l"><?php echo ($vo["address"]); ?></td>
		<!--<td class="text-l"><?php echo ($vo["adminname"]); ?></td>-->
                <td class="text-l"><?php echo (date('Y-m-d H:i:s',$vo["create_date"])); ?></td>
		<td class="text-l"><?php echo ($vo["order_eg"]); ?></td>
                <!--<td class="text-l"><?php echo ($vo["order_type"]); ?></td>-->
                <td class="text-l"><?php echo ($vo["total_num"]); ?></td>
		<td class="text-l"><?php echo ($vo["total_sum"]); ?></td>
		<td class="text-l"><?php echo ($vo["status"]); ?></td> 
                <td class="text-l"><?php echo ($vo["message"]); ?></td>
                <td>
                <a title="查看" href="javascript:;" onClick="showPage('900','500','查看订单详情','<?php echo U('Orders/detailOrder',array('id'=>$vo['id']));?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe695;</i></a>
               	<a title="编辑" href="javascript:;" onClick="showPage('900','500','修改订单资料','<?php echo U('Orders/editOrderRemak',array('id'=>$vo['id']));?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> 
		</td>
              
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