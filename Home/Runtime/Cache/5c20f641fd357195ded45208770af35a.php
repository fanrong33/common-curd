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

                    <div style="margin-bottom: 20px;">
                        <form id="page_form" action="<?php echo U('Page/index');?>" method="GET">
                            <a class="btn btn-default" href="<?php echo U('Page/add');?>" style="margin-right: 5px;">添加页面</a>
                            
                            <div class="input-group inline-block" style="width: 263px; vertical-align: middle;display: inline-block;margin-right: 5px;">
                                <input class="form-control" id="keyword" name="keyword" value="<?php echo ($keyword); ?>" type="text" placeholder="输入页面名称关键字" autocomplete="off" style="width: 210px;">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">搜索</button>
                                </span>
                            </div>

                            <div class="dropdown" id="group_id_dropdown" style="display: inline-block;">
                                <button class="btn btn-default dropdown-toggle" type="button" id="group_id_dropdown_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo ($group_id_text); ?>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="group_id_dropdown_menu" style="max-height: 300px;overflow-y: auto;">
                                    <li><a href="javascript:;">所有分组</a></li>
                                    <?php if(is_array($group_list)): $i = 0; $__LIST__ = $group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><li><a href="javascript:;" data-group-id="<?php echo ($rs['id']); ?>"><?php echo ($rs['name']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </div>
                            
                            <input type="hidden" id="group_id" name="group_id" value="<?php echo ($group_id); ?>" />
                            <input type="hidden" id="order_by" name="order_by" value="" />
                            <input type="hidden" id="direction" name="direction" value="" />
                        </form>
                    </div>

                    <table class="table table-bordered table-hover" id="page_list">
                        <thead>
                            <tr>
                                <td>
                                    <span class="sort <?php if($order_by == 'name'): echo ($direction); endif; ?>" data-order-by="name">名称</span>
                                </td>
                                <td>分组</td>
                                <td><span class="sort <?php if($order_by == 'url'): echo ($direction); endif; ?>" data-order-by="url">Url地址</span></td>
                                <td style="width:150px;">操作</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($list): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rs): $mod = ($i % 2 );++$i;?><tr id="page_<?php echo ($rs['id']); ?>">
                                    <td><?php echo highlight($rs['name'], 'keyword');?></td>
                                    <td><?php if($rs['group_name']): echo ($rs['group_name']); else: ?>-<?php endif; ?></td>
                                    <td><?php echo ($rs['url']); ?></td>
                                    <td>
                                        <a href="<?php echo U('Page/edit', array('id'=>$rs['id']));?>">编辑</a> &nbsp;<span class="text-muted">|</span>&nbsp;
                                        <a href="javascript:;" onclick="delete_page(<?php echo ($rs['id']); ?>)">删除</a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">暂无数据</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>

                    <nav class="text-right">
                        <ul class="pagination" style="margin-top: 0;"><?php echo ($page); ?></ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function(){

            // group dropdown click event
            $("#group_id_dropdown").delegate("a", 'click', function(){
                var group_id_text = $(this).text();
                var group_id      = $(this).data('group-id');

                $("#group_id_dropdown button").html(group_id_text+' <span class="caret"></span>');
                $("#group_id").val(group_id);
                $("#page_form").submit();
            });


            // bind sort event
            $("#page_list").delegate('.sort', 'click', function(){
                var $this = $(this);
                if($this.hasClass('desc')){
                    $this.removeClass('desc').addClass('asc');
                    // asc
                    $("#order_by").val($this.data('order-by'));
                    $("#direction").val('asc');
                }else{
                    // if($this.hasClass('asc')){
                    //     $this.removeClass('asc');
                    //     // default
                    //     $("#order_by").val('');
                    //     $("#direction").val('');
                    // }else{
                        $this.removeClass('asc').addClass('desc');
                        // desc
                        $("#order_by").val($this.data('order-by'));
                        $("#direction").val('desc');
                    // }
                }

                $("#page_form").submit();
            });


            if(window.location.hash){
                $(window.location.hash).addClass('highlight-fade');
            }
        });


        function delete_page(id){
            if(confirm('确定删除？')){
                $.ajax({
                    type: "POST",
                    url: "/page/delete",
                    data: { id: id },
                    dataType: "json",
                    success: function(json){
                        if(json.status == 1){
                            $("#page_"+id).remove();

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