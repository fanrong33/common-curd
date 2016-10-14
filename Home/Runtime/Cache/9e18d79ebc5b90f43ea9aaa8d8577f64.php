<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <!--[if lt IE 9]>
    <script src='//cdnjs.cloudflare.com/ajax/libs/es5-shim/4.1.1/es5-shim.min.js'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/es5-shim/4.1.1/es5-sham.min.js'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js'></script>
    <script src='//cdn.uriit.ru/console-polyfill/index.js'></script>
    <![endif]-->
    <meta charset="UTF-8">
    <title>CURD</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo JS_PATH;?>artDialog/ui-dialog.css">
    <link rel="stylesheet" href="<?php echo JS_PATH;?>toastr/toastr.css">
    <link rel="stylesheet" href="<?php echo JS_PATH;?>jquery-autosuggest/jquery.autoSuggest.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>style.css?v=20160921">
    <link rel="shortcut icon" href="/favicon.ico" /> 
    <script src="<?php echo JS_PATH;?>jquery.min.js"></script>
    <script src="<?php echo JS_PATH;?>bootstrap.min.js"></script>
    <script src="<?php echo JS_PATH;?>template.min.js"></script>
    <script src="<?php echo JS_PATH;?>ajaxForm.js"></script>
    <script src="<?php echo JS_PATH;?>artDialog/dialog.js"></script>
    <script src="<?php echo JS_PATH;?>artDialog/dialog-plus-min.js"></script>
    <script src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script>
    <script src="<?php echo JS_PATH;?>toastr/toastr.min.js"></script>
    <script src="<?php echo JS_PATH;?>clipboard.min.js"></script>
    <script src="<?php echo JS_PATH;?>jquery.cookie.js"></script>
    <script src="<?php echo JS_PATH;?>jquery-autosuggest/jquery.autoSuggest.js"></script>
    <script src="<?php echo JS_PATH;?>echarts/echarts.common.min.js"></script>
    <script src="<?php echo JS_PATH;?>echarts/macarons.js"></script>
</head>
<body>

    <section>
        <div class="container-fluid">
            <div class="row">

                <div class="col-xs-2" style="padding-top:20px;">
                    <ul class="nav nav-pills nav-stacked">
      <li <?php if(strtolower(MODULE_NAME) == 'page'): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Page/index');?>">页面</a></li>
      <li <?php if(strtolower(MODULE_NAME) == 'group'): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Group/index');?>">分组</a></li>
</ul>
                </div>
                <div class="col-xs-10">

                    <div style="margin-top: 20px; margin-bottom: 20px;">
                        <a class="btn btn-default" href="<?php echo U('Group/add');?>">添加分组</a>
                    </div>

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <td>名称</td>
                                <td style="width:150px;">操作</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($list): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><tr id="group_<?php echo ($rs['id']); ?>">
                                        <td><?php echo ($rs['name']); ?></td>
                                        <td>
                                            <a href="<?php echo U('Group/edit', array('id'=>$rs['id']));?>">编辑</a> <span class="text-muted">|</span>
                                            <a href="javascript:;" onclick="delete_group(<?php echo ($rs['id']); ?>)">删除</a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">暂无数据</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>


    <script>
        $(document).ready(function(){

        });


        function delete_group(id){
            if(confirm('确定删除？')){
                $.ajax({
                    type: "POST",
                    url: "/group/delete",
                    data: { id: id },
                    dataType: "json",
                    success: function(json){
                        if(json.status == 1){
                            $("#group_"+id).remove();

                            toastr.success(json.info);
                        }else{
                            toastr.error(json.info);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>