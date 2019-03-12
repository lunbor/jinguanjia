<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:75:"F:\wamp\www\guanjia\public/../application/admin\view\member\vip_member.html";i:1549177278;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
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
<link rel="stylesheet" type="text/css" media="all" href="/sldate/daterangepicker-bs3.css" />
<script type="text/javascript" src="/sldate/moment.js"></script>
<script type="text/javascript" src="/sldate/daterangepicker.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
      $('#reservation').daterangepicker(null, function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
      });
   });
</script>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>续费VIP</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" name="vip_member" id="vip_member" method="post" action="<?php echo url('vip_member'); ?>">
                        <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                        
						
						 
						<div class="form-group">
                            <label class="col-sm-3 control-label">VIP结束日期：</label>
                            <div class="input-group col-sm-4">
                               
                                <input type="text"  name="end_time" disabled="disabled" value="<?php echo $member['vip_end_time']; ?>" onClick="laydate()" id="reservation" class="form-control layer-date" placeholder="结束日期"/>
                            </div>
                        </div>
                        
                        <div class="hr-line-dashed"></div>
						
						<div class="form-group">
                            <label class="col-sm-3 control-label">续费日期：</label>
                            <div class="input-group col-sm-4">
                               
                                <select class="form-control m-b chosen-select" name="months" id="months">
									<option value="">==请选择月份==</option>
										<?php for($x=1; $x<=48; $x++) {?>
										<option value="<?php echo $x; ?>"><?php echo $x; ?>个月</option>
										<?php }?>
								</select>
                            </div>
                        </div>
						 <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-3">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;
                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                            </div>
                        </div>
                    </form>
					
									<div class="example-wrap">
										<div class="example">
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th>序号</th>
														<th>续费月份</th>
														<th>开始日期</th>
														<th>结束日期</th>
														<th>续费时间</th>
													</tr>
												</thead>
												<tbody>
													<?php if(empty($vip_log)):?>
														<tr><td colspan="5" align="center">暂无数据</td></tr>
													<?php else:foreach($vip_log as $key=>$vo):?>
													<tr>
														<td><?php echo $key+1; ?></td>
														<td><?php echo $vo['months']; ?>个月</td>
														<td><?php echo date('Y-m-d H:i:s',$vo['start_time']); ?></td>
														<td><?php echo date('Y-m-d H:i:s',$vo['end_time']); ?></td>
														<td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
													</tr>
													<?php endforeach;endif;?>
												</tbody>
											</table>
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
<script type="text/javascript" src="/static/admin/webupload/webuploader.min.js"></script>
<script type="text/javascript">
   
    $(function(){
        $('#vip_member').ajaxForm({
            beforeSubmit: checkForm, 
            success: complete, 
            dataType: 'json'
        });
        
        function checkForm(){
            if( '' == $.trim($('#months').val())){
                layer.msg('请选择续费日期',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }
        }


        function complete(data){
            if(data.code==1){
                layer.msg(data.msg, {icon: 6,time:1500,shade: 0.1}, function(index){
                    window.location.href="<?php echo url('member/vip_member',array('id'=>$member['id'])); ?>";
                });
            }else{
                layer.msg(data.msg, {icon: 5,time:1500,shade: 0.1});
                return false;   
            }
        }
     
    });



    //IOS开关样式配置
   var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, {
            color: '#1AB394'
        });
    var config = {
        '.chosen-select': {},                    
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }

</script>
</body>
</html>