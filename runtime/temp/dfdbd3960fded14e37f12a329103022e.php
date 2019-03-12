<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:77:"F:\wamp\www\guanjia\public/../application/admin\view\agent\withdraw_list.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo config('WEB_SITE_TITLE'); ?></title>
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/switchery/switchery.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/elementUI-1.4.12/css/index.min.css" rel="stylesheet">
    <script src="/static/admin/elementUI-1.4.12/js/vue.min.js"></script>
    <script src="/static/admin/elementUI-1.4.12/js/index.min.js"></script>
    <style type="text/css">
    .long-tr th{
        text-align: center
    }
    .long-td td{
        text-align: center
    }
    /*elementUI分页样式*/
    .layout-pagination {
        text-align: right;
        margin-top: 15px;
    }
    .control-label{
        margin-top: 7px;
        text-align: right;
    }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>提现列表</h5>
        </div>
        <div class="ibox-content">
            <!--搜索框开始-->           
            <div class="row">
                <div class="col-sm-12">                                      
                    <form name="admin_list_sea" class="form-search" method="post" action="<?php echo url('withdraw_list'); ?>">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" id="key" class="form-control" name="key" value="<?php echo $val; ?>" placeholder="输入需查询的登录账号/手机号/名称" />   
                                <span class="input-group-btn"> 
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 搜索</button> 
                                </span>
                            </div>
                        </div>
                    </form>                         
                </div>
            </div>
            <!--搜索框结束-->
            <div class="hr-line-dashed"></div>
            <div class="example-wrap">
                <div class="example">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>代理商名称</th>
                                <th>手机号</th>
                                <th>登录账号</th>
								<th>提现金额</th>
								<th>银行名称</th>
								<th>银行户名</th>
								<th>银行账号</th>
                                <th>创建时间</th>
                                <th>状态</th>
								<th>备注</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($list)):?>
                                <tr><td colspan="11" align="center">暂无数据</td></tr>
                            <?php else:foreach($list as $vo):?>
                            <tr>
                                <td><?php echo $vo['real_name']; ?></td>
                                <td><?php echo $vo['phone']; ?></td>
                                <td><?php echo $vo['username']; ?></td>
                                <td><?php echo $vo['money']; ?></td>
								<td><?php echo $vo['bank_name']; ?></td>
								<td><?php echo $vo['bank_real_name']; ?></td>
								<td><?php echo $vo['bank_code']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
                                <td><?php echo $status[$vo['status']]; ?></td>
								<td><?php echo $vo['remark']; ?></td>
                                <td>
                                    <a href="javascript:;" onClick="withdraw_check(<?php echo $vo['id']; ?>)" class="btn btn-primary btn-xs btn-outline">
                                        <i class="fa fa-paste"></i>审核</a>&nbsp;&nbsp;
                                </td>
                            </tr>
                            <?php endforeach;endif;?>
                            
                        </tbody>
                                                   
                                    

                    </table>
                    <div id="AjaxPage" style="text-align:right;"></div>
                    <div style="text-align: right;">
                          <?php echo $list->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/chosen/chosen.jquery.js"></script>
<script src="/static/admin/js/plugins/iCheck/icheck.min.js"></script>
<script src="/static/admin/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/admin/js/plugins/switchery/switchery.js"></script><!--IOS开关样式-->
<script src="/static/admin/js/jquery.form.js"></script>
<script src="/static/admin/js/moment.min.js"></script>
<script src="/static/admin/js/layer/layer.js"></script>
<script src="/static/admin/js/laypage/laypage.js"></script>
<script src="/static/admin/js/laytpl/laytpl.js"></script>
<script src="/static/admin/js/lunhui.js"></script>
<script>
    $(document).ready(function(){$(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",})});
</script>

<script type="text/javascript">
  
	//提现审核
	function withdraw_check(id){
		location.href = './withdraw_check/id/'+id+'.html';
	}


	function AjaxPage(curr){
		$(".form-search").append('<input type="hidden" name="page" id="page" value="'+curr+'">');
		$('.form-search').submit();
    }

</script>
</body>
</html>