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
<link rel="stylesheet" href="/Public/H-ui/lib/zTree/v3/css/zTreeStyle/zTreeStyle.css" type="text/css">
<title></title>
</head>
<body class="pos-r">


    <div class="pos-a" style="width:100%;left:0;top:0; bottom:0; height:100%; border-right:1px solid #e5e5e5; ">
             <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span>会员管理 <span class="c-gray en">&gt;</span>接点关系图 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
        <div class="cl pd-5 bg-1 bk-gray mt-0" >
            <form action="" method="" class="search_from">
                <span class="l" > 会员账号：<input type="text" class="input-text search_username" style="width:150px" placeholder="输入账号" id="" value="" name="search_username" nullmsg="请输入会员账号！"  datatype="*"  > </span>
                <div ><button type="button" class="btn btn-success" id="" onclick='search()' ><i class="Hui-iconfont">&#xe665;</i> 搜索</button> </div>
            </form>
        </div>
        <ul id="treeDemo" class="ztree">
        </ul>
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
<script type="text/javascript" src="/Public/H-ui/lib/zTree/v3/js/jquery.ztree.all-3.5.min.js"></script> 

<!--
全部一次性加载所有节点
<script type="text/javascript">
var setting = {
        view: {
                dblClickExpand: false,
                showLine: true,
                selectedMulti: false

        },
        data: {
                simpleData: {
                        enable:true,
                        idKey: "id",
                        pIdKey: "pId",
                        rootPId: ""
                }
        },
        callback: {
                beforeClick: function(treeId, treeNode) {
                        var zTree = $.fn.zTree.getZTreeObj("tree");
                                demoIframe.attr("src",treeNode.file );
                                return true;
                        
                }
        }
        
};

 var zNodes;//数据变量              
//ajax提交数据，请求后台PHP处理返回出目录结构json数据
$.ajax({
        url:"/Admin/Member/usertree",
        type: "post",
        async: false,
        dataType:"json",  
        success: function (data) {
                        //alert(data);
                        zNodes=data;    //将请求返回的数据存起来
                         //alert(zNodes);
        },
        error: function (){//请求失败处理函数  
                alert('请求失败');  
        },  
})

var code;
                
function showCode(str) {
        if (!code) code = $("#code");
        code.empty();
        code.append("<li>"+str+"</li>");
}
                
$(document).ready(function(){
        var t = $("#treeDemo");
        t = $.fn.zTree.init(t, setting, zNodes);
        demoIframe = $("#testIframe");
        demoIframe.bind("load", loadReady);
        var zTree = $.fn.zTree.getZTreeObj("tree");
        zTree.selectNode(zTree.getNodeByParam("id",'11'));
});
</script>-->

<!---异步加载节点--->
<SCRIPT type="text/javascript">
 var search_username = $('.search_username').val();
function search()
{
    var search_username = $('.search_username').val();
    var url2 = "/Admin/Member/listcontactman";
 

    var setting = {
        view: {
            selectedMulti: false
        },
        async: {
            enable: true,
            url: url2,
            autoParam: ["id","name"],
            otherParam: {"name": search_username},
            dataFilter: filter,
            type: 'get',
            datatype: 'text',
        }, callback: {
            beforeClick: function () {
            
            },
            beforeAsync: function () {
           
            }
        }
    };
    zTreeObj = $.fn.zTree.init($("#treeDemo"), setting);


    function filter(treeId, parentNode, childNodes) {
        if (!childNodes)
            return null;
        for (var i = 0, l = childNodes.length; i < l; i++) {
            childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
        }
        return childNodes;
    }
}

$(document).ready(function () {
    search();//树的初始化

});


</SCRIPT>
</body>
</html>