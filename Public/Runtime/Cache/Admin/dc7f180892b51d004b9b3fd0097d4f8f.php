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
<title></title>
</head>
<body class="pos-r">
    <div class="pos-a" style="width:100%;left:0;top:0; bottom:0; height:100%; border-right:1px solid #e5e5e5;">
        <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span>会员管理 <span class="c-gray en">&gt;</span>组织关系图 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
        <div class="cl pd-5 bg-1 bk-gray mt-0" >
            <form action="/index.php/Member/chart.html" method="get" class="search_from">
                <span class="l" > 会员账号：
                    <input type="text" class="input-text search_username" style="width:150px" placeholder="输入账号" id="search_username" <?php if($_GET['search_username']!= '' ): ?>value="<?php echo ($_GET['search_username']); ?>"<?php endif; ?> name="search_username" nullmsg="请输入会员账号！"  datatype="*"  > 
                       <select name="level" id="level"  onchange="selectLayer(this.value)" class='select' style='width:60px;height:31px;line-height:31px;'></span>
                    <option value="3" <?php if($arr["search_level"] == "3" ): ?>selected='selectde' <?php elseif($_GET['level']== '3'): ?> selected='selectde'<?php endif; ?>>3层</option>
                    <option value="4" <?php if($arr["search_level"] == "4" ): ?>selected='selectde' <?php elseif($_GET['level']== '4'): ?> selected='selectde'<?php endif; ?>>4层</option>
                    <option value="5" <?php if($arr["search_level"] == "5" ): ?>selected='selectde' <?php elseif($_GET['level']== '5'): ?> selected='selectde'<?php endif; ?>>5层</option>
                    <option value="6" <?php if($arr["search_level"] == "6" ): ?>selected='selectde' <?php elseif($_GET['level']== '6'): ?> selected='selectde'<?php endif; ?>>6层</option>
                    <option value="7" <?php if($arr["search_level"] == "7" ): ?>selected='selectde' <?php elseif($_GET['level']== '7'): ?> selected='selectde'<?php endif; ?>>7层</option>
                    </select> </span>
                      <span class="l" > 
                      &nbsp;<button type="submit" class="btn btn-success" id="" onclick='' ><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
                      <button type="button" class="btn btn-success" id="" onclick='upperStory()' ><i class="Hui-iconfont">&#xe66b;</i> 上一层</button>
                    </span>
                <input type="hidden" name="layer" id="layer" value="<?php echo ($arr["search_level"]); ?>" />
                       <input type="hidden" name="origin" id="origin" value="<?php echo ($arr['search_junction']); ?>" />        
                <input type="hidden" name="error" id="error" value="" />&nbsp;&nbsp;
            </form>
        </div>
        <ul id="org" style="display:none">
            <li>
            <?php echo ($html); ?>
            </li>
        </ul>
        <div id="chart" class="orgChart"></div>
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
<link type="text/css" rel="stylesheet" href="/Public/H-ui/lib/Chart/css/jquery.jOrgChart.css" />
<script type="text/javascript" src="/Public/H-ui/lib/Chart/js/jquery.jOrgChart.js"></script>
<script type="text/javascript">
                    jQuery(document)
                            .ready(
                                    function () {
                                        document.getElementById("level").value = document.getElementById("layer").value;

                                        $("#org").jOrgChart({
                                            chartElement: '#chart',
                                            dragAndDrop: false
                                        });

                                    });
                    function selectLayer(id) {
                        var search_username=document.getElementById("search_username").value;
                        if(search_username)
                        {
                        window.location.href = '/Admin/Member/chart/search_username/'+search_username+'/level/' + id;
                        }
                        else
                        {
                             window.location.href = '/Admin/Member/chart/level/' + id;
                        }
                    
                    }
                    function upperStory() {
		var memberAccount = document.getElementById("origin").value;
                var layer = document.getElementById("layer").value;
		if (memberAccount != null && memberAccount != "") {
			window.location.href = '/Admin/Member/chart/search_username/'+memberAccount+'/level/' + layer;
		}

	}


</script>
</body>
</html>