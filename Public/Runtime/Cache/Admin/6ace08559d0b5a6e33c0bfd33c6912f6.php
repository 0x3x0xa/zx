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
<title>添加产品</title>
<body>  
<div class="page-container">
    <form action=""   method="get" class="form form-horizontal SubmiForm" id="form-article-add"  >
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品名称：</label>
			<div class="formControls col-xs-3 col-sm-4">
				<input type="text" class="input-text product_title" value="" placeholder="" id="" name="product_title"   nullmsg='不能为空' datatype='*' >
			</div>
                        <div class="Validform_checktip"></div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品规格：</label>
			<div class="formControls col-xs-3 col-sm-4">
				<input type="text" class="input-text product_norms" value="" placeholder="" id="" name="product_norms"  nullmsg='不能为空' datatype='*' >
                        </div>
                           <div class="Validform_checktip"></div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品类别：</label>
			<div class="formControls col-xs-3 col-sm-4">
                         
				<select name="product_type" class="select product_type select-box"  nullmsg='不能为空' datatype='*' >   
                                <option value="" >请选择产品</option>
				<?php if(is_array($list1)): $i = 0; $__LIST__ = $list1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo1["id"]); ?>"><?php echo (htmlspecialchars_decode($vo1["type_name"])); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			
                           
                        </div>  <div class="Validform_checktip"></div>
                          
		</div>
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品可见范围：</label>
			<div class="formControls col-xs-3 col-sm-4">
                         
				<select name="show" class="select show select-box"  nullmsg='不能为空' datatype='*' >   
                                <option value="" >请选择等级</option><option value="0">所有等级可见</option>    
				<?php if(is_array($memberlevel)): $i = 0; $__LIST__ = $memberlevel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo2["id"]); ?>"><?php echo (htmlspecialchars_decode($vo2["title"])); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			
                           
                        </div>  <div class="Validform_checktip"></div>
                          
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>市场价：</label>
			<div class="formControls col-xs-3 col-sm-4">
				<input type="text" class="input-text market_price"  placeholder="" id="" name="market_price"  nullmsg='不能为空' datatype='*' >
			</div>
                           <div class="Validform_checktip"></div>
		</div>
                <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>会员价：</label>
			<div class="formControls col-xs-3 col-sm-4">
				<input type="text" class="input-text member_price"  placeholder="" id="" name="member_price"  nullmsg='不能为空' datatype='*' >
			</div>
                           <div class="Validform_checktip"></div>
		</div>
		
                <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">产品描述：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<textarea name="product_message" cols="" rows="" class="textarea product_message"  placeholder="说点什么...最少输入10个字符" dragonfly="true" onKeyUp="textarealength(this,200)"></textarea>
				<p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
			</div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                            <button  class="btn btn-secondary radius"   type="submit"><i class="Hui-iconfont">&#xe632;</i> 确认添加</button>
				<button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</div>

<!--_footer 作为公共模版分离出去-->
 
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
<!--/_footer /作为公共模版分离出去-->
<script type="text/javascript">
$(function(){
	  
	$(".SubmiForm").Validform({
                callback:function(form){
			product_add_save();
			return false;
		},
		tiptype:2,
         
	});	
	
	
})
</script>