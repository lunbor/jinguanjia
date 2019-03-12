<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"F:\wamp\www\guanjia\public/../application/admin\view\agent\add_group.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
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
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加类型</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" name="add_group" id="add_group" method="post" action="<?php echo url('add_group'); ?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">类型名称：</label>
                            <div class="input-group col-sm-4">
                                <input id="group_name" type="text" class="form-control" name="group_name" >
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">开通费用：</label>
                            <div class="input-group col-sm-4">
                                <input id="fee" type="text" class="form-control" name="fee" required="" aria-required="true" value="">
								 <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 填0代表免费开通</span>
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">刷卡费率(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="pay_rate" type="text" class="form-control" name="pay_rate" required="" aria-required="true" value="" placeholder="请输入刷卡费率(%)">
								
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">刷卡代付费(元)：</label>
                            <div class="input-group col-sm-4">
                                <input id="pay_dfee" type="text" class="form-control" name="pay_dfee" required="" aria-required="true" value="" placeholder="请输入刷卡代付费(元)">
								
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">本金还款费率(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="repay_rate" type="text" class="form-control" name="repay_rate" required="" aria-required="true" value="" placeholder="请输入本金还款费率(%)">
								
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">本金还款代付费(元)：</label>
                            <div class="input-group col-sm-4">
                                <input id="repay_dfee" type="text" class="form-control" name="repay_dfee" required="" aria-required="true" value="" placeholder="请输入本金还款代付费(元)">
								
                            </div>
                        </div>
						
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">空卡还款费率(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="dz_rate" type="text" class="form-control" name="dz_rate" required="" aria-required="true" value="" placeholder="请输入空卡还款费率(%)">
								
                            </div>
                        </div>
						
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">空卡还款代付费(元)：</label>
                            <div class="input-group col-sm-4">
                                <input id="dz_dfee" type="text" class="form-control" name="dz_dfee" required="" aria-required="true" value="" placeholder="请输入空卡还款代付费(元)">
								
                            </div>
                        </div>
						
						
                        <div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">刷卡分润(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="pay_profit" type="text" class="form-control" name="pay_profit" required="" aria-required="true" value="" placeholder="请输入刷卡分润(%)">
								
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">本金还款分润-直推(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="repay_profit" type="text" class="form-control" name="repay_profit" required="" aria-required="true" value="" placeholder="请输入本金还款分润(%)">
								
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">本金还款分润-间推(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="repay_profit_jt" type="text" class="form-control" name="repay_profit_jt" required="" aria-required="true" value="" placeholder="请输入本金还款分润-间推(%)">
								
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">空卡还款分润-直推(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="dz_profit" type="text" class="form-control" name="dz_profit" required="" aria-required="true" value="" placeholder="请输入空卡还款分润(%)">
								
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">空卡还款分润-间推(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="dz_profit_jt" type="text" class="form-control" name="dz_profit_jt" required="" aria-required="true" value="" placeholder="请输入空卡还款分润-间推(%)">
								
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">状&nbsp;态：</label>
                            <div class="col-sm-6">
                                <div class="radio i-checks">
                                    <input type="radio" name='status' value="1" checked="checked"/>开启&nbsp;&nbsp;
                                    <input type="radio" name='status' value="0" />关闭
                                </div>
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

    $(function(){
        $('#add_group').ajaxForm({
            beforeSubmit: checkForm, 
            success: complete, 
            dataType: 'json'
        });
        
        function checkForm(){
            if( '' == $.trim($('#group_name').val())){
                layer.msg('请输入类型名称',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }            
        }

        function complete(data){
            if(data.code==1){
                layer.msg(data.msg, {icon: 6,time:1500,shade: 0.1}, function(index){
                    window.location.href="<?php echo url('agent/group'); ?>";
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