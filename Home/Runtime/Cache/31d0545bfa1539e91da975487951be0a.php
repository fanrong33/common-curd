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
            <div class="row" style="padding-top: 20px;">

                <div class="col-xs-2">
                    <ul class="nav nav-pills nav-stacked">
      <li <?php if(strtolower(MODULE_NAME) == 'page'): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Page/index');?>">页面</a></li>
      <li <?php if(strtolower(MODULE_NAME) == 'group'): ?>class="active"<?php endif; ?> ><a href="<?php echo U('Group/index');?>">分组</a></li>
</ul>
                </div>
                <div class="col-xs-10">

                    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
                        <li class="active"><a href="javascript:;"><?php if($model): ?>修改页面<?php else: ?>添加页面<?php endif; ?></a></li>
                    </ul>

                    <form class="form-horizontal mb30" id="page_form" action="" method="POST">
                        <?php if($model): ?><input type="hidden" name="id" value="<?php echo ($model['id']); ?>"><?php endif; ?>

                        <div class="form-group">
                            <label class="col-xs-2 control-label" style="font-weight: normal;">名称</label>
                            <div class="col-xs-5">
                                <input type="text" class="form-control" name="name" value="<?php echo ($model['name']); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label" style="font-weight: normal;">Url</label>
                            <div class="col-xs-5">
                                <input type="text" class="form-control" name="url" value="<?php echo ($model['url']); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label" style="font-weight: normal;">分组</label>
                            <div class="col-xs-3">
                                <select class="form-control" name="group_id">
                                    <option value="" <?php if(!$model['group_id']): ?>selected<?php endif; ?>>无</option>
                                    <?php if(is_array($group_list)): $i = 0; $__LIST__ = $group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><option value="<?php echo ($rs['id']); ?>" <?php if($model['group_id'] == $rs['id']): ?>selected<?php endif; ?>><?php echo ($rs['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-offset-2 col-xs-10">
                                <button class="btn btn-primary">保存</button>
                                <?php
 $back_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : U(MODULE_NAME.'/index'); ?>
                                <a class="btn btn-default" href="<?php echo ($back_url); ?>">返回</a>
                            </div>
                        </div>
                        
                    </form>

                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        $(document).ready(function(){

            $("#page_form").submit(function(){ // bind submit event
                $(this).ajaxSubmit({
                    type: "POST",
                    dataType: "json",
                    beforeSubmit: function(){

                    },
                    success: function(json){
                        if(json.status == 1){

                            toastr.success(json.info);
                            
                            <?php if($_GET['id']): ?>var url = '<?php echo ($back_url); ?>';
                            <?php else: ?>
                                var url = '/page/index.html';<?php endif; ?>
                            url += '#page_'+json.data.id;

                            setTimeout(function(){
                                window.location.href = url;
                            }, 1500);
                        }else{
                            toastr.error(json.info);
                        }
                    }
                });
                return false;
            });
            
        });
    </script>
</body>
</html>