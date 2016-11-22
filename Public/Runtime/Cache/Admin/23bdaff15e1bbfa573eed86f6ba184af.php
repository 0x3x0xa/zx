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
<title>修改会员信息</title>
</head>
<body>
<article class="page-container">
	<form action="" method="get" class="form form-horizontal submitform" id="form-member-add">
		<div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>会员账号：</label>
			<div class="formControls col-xs-7 col-sm-9">
				<input type="text" class="input-text username" value="<?php echo ($userInfo["username"]); ?>" placeholder="" id="username" name="username"   datatype="u6-16"   errormsg="会员账号格式不对" nullmsg="请输入会员账号！">
			</div>
		</div>
                <div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>会员昵称：</label>
			<div class="formControls col-xs-7 col-sm-9">
				<input type="text" class="input-text name" value="<?php echo ($userInfo["name"]); ?>" placeholder="" id="name" name="name"  >
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>手机：</label>
			<div class="formControls col-xs-7 col-sm-9">
				<input type="text" class="input-text mobile" value="<?php echo ($userInfo["mobile"]); ?>" placeholder="" id="mobile" name="mobile"  name="mobile" id="mobile" >
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>开户姓名：</label>
			<div class="formControls col-xs-7 col-sm-9">
				<input type="text" class="input-text account_name" placeholder="" name="account_name" id="account_name" value="<?php echo ($userInfo["account_name"]); ?>" >
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>银行卡号：</label>
			<div class="formControls col-xs-7 col-sm-9">
				<input type="text" class="input-text bankno" placeholder="" name="bankno" id="bankno" value="<?php echo ($userInfo["bankno"]); ?>" name="bankno" id="bankno" >
			</div>
		</div>
                
		
		<div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>银行名称：</label>
			<div class="formControls col-xs-7 col-sm-9"> <span class="select-box">
				<select class="select bank" size="1" name="bank">
					<?php if(is_array($banklist)): $i = 0; $__LIST__ = $banklist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["bankname"]); ?>" <?php if($userInfo["bank"] == $vo["bankname"] ): ?>selected<?php endif; ?>><?php echo ($vo["bankname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				</select>
				</span> </div>
		</div>
                <div class="row cl">
			<label class="form-label col-xs-3 col-sm-3" style='text-align:right;'>银行网点：</label>
			<div class="formControls col-xs-7 col-sm-9">
				<input type="text" class="input-text bank_outlets" placeholder="" name="bank_outlets" id="bank_outlets" value="<?php echo ($userInfo["bank_outlets"]); ?>" >
			</div>
		</div>
                <div class="row cl">
			<label class="form-label col-xs-3 col-sm-2" style='text-align:right;'>所在地区：</label>
                     
			<div class="formControls ">
                            <div class="formControls col-xs-7 col-sm-3 " id="region_container" >
                            
                            </div>
                                
			</div>
                        
		</div>
                <div class="row cl">
			<label class="form-label col-xs-3 col-sm-2" style='text-align:right;'>详细地址：</label>
			<div class="formControls col-xs-7 col-sm-3">
                            <input type="text" value="<?php echo ($userInfo["detailed_address"]); ?>" class="input-text detailed_address"  placeholder="" name="detailed_address" id="detailed_address" >
			</div>
		</div>
            <div class="row cl">
			<label class="form-label col-xs-3 col-sm-2" style='text-align:right;'>身份证：</label>
			<div class="formControls col-xs-7 col-sm-3">
                            <input type="text" value="<?php echo ($userInfo["id_card"]); ?>" class="input-text id_card" placeholder="" name="id_card" id="id_card" >
			</div>
                         <div class="Validform_checktip"></div>
		</div>
             <div class="row cl">
			<label class="form-label col-xs-3 col-sm-2" style='text-align:right;'>邮政编码：</label>
			<div class="formControls col-xs-7 col-sm-3">
                            <input type="text" class="input-text post_code" value="<?php echo ($userInfo["post_code"]); ?>" placeholder="" name="post_code" id="post_code" >
			</div> <div class="Validform_checktip"></div>
		</div>

		
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-primary radius"><i class="Hui-iconfont">  </i>提交</button>
			</div>
		</div>
	</form>
</article>
 
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
<script type="text/javascript" src="/Public/H-ui/lib/address/addre_area.js"></script> 
<script type="text/javascript" src="/Public/H-ui/lib/address/address-select.min_1.js"></script>
<script type="text/javascript">
$(function(){
	
        $.extend($.Datatype,{
		//"z2-4" : /^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/,
               
	});
        
	$(".submitform").Validform({
                callback:function(form){
			user_edit_save(<?php echo ($userInfo["id"]); ?>);
			return false;
		},
		tiptype:1,
                datatype:{//传入自定义datatype类型【方式二】;
			"z2-4" : /^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/,
                        "yb-6":/[1-9]\d{5}(?!\d)/,
                        "u6-16":/^[A-Za-z0-9]{6,12}$/,
                        "yhno-16-19":/^(\d{16}|\d{19})$/,
                        "xxdz":/^(?=.*?[\u4E00-\u9FA5])[\d\u4E00-\u9FA5]+/,
                        "idcard":function(gets,obj,curform,datatype){
				//该方法由佚名网友提供;
			
				var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];// 加权因子;
				var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];// 身份证验证位值，10代表X;
			
				if (gets.length == 15) {   
					return isValidityBrithBy15IdCard(gets);   
				}else if (gets.length == 18){   
					var a_idCard = gets.split("");// 得到身份证数组   
					if (isValidityBrithBy18IdCard(gets)&&isTrueValidateCodeBy18IdCard(a_idCard)) {   
						return true;   
					}   
					return false;
				}
				return false;
				
				function isTrueValidateCodeBy18IdCard(a_idCard) {   
					var sum = 0; // 声明加权求和变量   
					if (a_idCard[17].toLowerCase() == 'x') {   
						a_idCard[17] = 10;// 将最后位为x的验证码替换为10方便后续操作   
					}   
					for ( var i = 0; i < 17; i++) {   
						sum += Wi[i] * a_idCard[i];// 加权求和   
					}   
					valCodePosition = sum % 11;// 得到验证码所位置   
					if (a_idCard[17] == ValideCode[valCodePosition]) {   
						return true;   
					}
					return false;   
				}
				
				function isValidityBrithBy18IdCard(idCard18){   
					var year = idCard18.substring(6,10);   
					var month = idCard18.substring(10,12);   
					var day = idCard18.substring(12,14);   
					var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
					// 这里用getFullYear()获取年份，避免千年虫问题   
					if(temp_date.getFullYear()!=parseFloat(year) || temp_date.getMonth()!=parseFloat(month)-1 || temp_date.getDate()!=parseFloat(day)){   
						return false;   
					}
					return true;   
				}
				
				function isValidityBrithBy15IdCard(idCard15){   
					var year =  idCard15.substring(6,8);   
					var month = idCard15.substring(8,10);   
					var day = idCard15.substring(10,12);
					var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
					// 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法   
					if(temp_date.getYear()!=parseFloat(year) || temp_date.getMonth()!=parseFloat(month)-1 || temp_date.getDate()!=parseFloat(day)){   
						return false;   
					}
					return true;
				}
				
			}
		}
	});	
	
       
        
        
 var province, capital, city;
        for (var i = 0; i < address_data[0].Province.length; i++) {
            if (address_data[0].Province[i].Name == "") {
                province = address_data[0].Province[i].Name;
                for (var j = 0; j < address_data[0].Province[i].Capital.length; j++) {
                    if (address_data[0].Province[i].Capital[j].Name == "") {
                        city = address_data[0].Province[i].Capital[j].Name;
                        for (var k = 0; k < address_data[0].Province[i].Capital[j].City.length; k++) {
                            if (address_data[0].Province[i].Capital[j].City[k].Name == "") {
                                capital = address_data[0].Province[i].Capital[j].City[k].Name;
                            }
                        }
                    }
                }
            }
        }
        create_address_select('', 'region_container', 'Name', '<?php echo ($userInfo["province"]); ?>', '<?php echo ($userInfo["city"]); ?>', '<?php echo ($userInfo["area"]); ?>');
});
</script>