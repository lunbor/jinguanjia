<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"F:\wamp\www\guanjia\public/../application/admin\view\agent\add_agent.html";i:1550823494;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
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
<link rel="stylesheet" type="text/css" href="/static/admin/webupload/webuploader.css">
<link rel="stylesheet" type="text/css" href="/static/admin/webupload/style.css">
<style>
.file-item{float: left; position: relative; width: 110px;height: 110px; margin: 0 20px 20px 0; padding: 4px;}
.file-item .info{overflow: hidden;}
.uploader-list{width: 100%; overflow: hidden;}
</style>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加代理商</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" name="add_agent" id="add_agent" method="post" action="<?php echo url('add_agent'); ?>">
						<div class="form-group">
                            <label class="col-sm-3 control-label">代理商名称：</label>
                            <div class="input-group col-sm-4">
                                <input id="real_name" type="text" class="form-control" name="real_name" value="" placeholder="请输入代理商名称">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">关联会员：</label>
                            <div class="input-group col-sm-4">
                                <input id="user_account" type="text" class="form-control" name="user_account" value="" placeholder="请输入关联会员的手机号">
                            </div>
                        </div>
						
						<div class="hr-line-dashed"></div>
						<?php if($admindq['groupid'] == $groupiddq): ?>
							<div class="form-group">
								<label class="col-sm-3 control-label">上级代理商账号：</label>
								<div class="input-group col-sm-4">
									
									<input id="prev_id" type="text" class="form-control" disabled="disabled" name="prev_id" value="<?php echo $admindq['username']; ?>" placeholder="请输入上级代理商账号">
									
									 
								</div>
							</div>
							
							
						<?php else: ?>
							<div class="form-group">
								<label class="col-sm-3 control-label">上级代理商账号：</label>
								<div class="input-group col-sm-4">
									
									 <input id="prev_id" type="text" class="form-control" name="prev_id" value="" placeholder="请输入上级代理商账号">
										
										 
								</div>
							</div>
							
							<div class="hr-line-dashed"></div>
							<div class="form-group">
								<label class="col-sm-3 control-label">是否奖励推荐费：</label>
								<div class="col-sm-6">
									<div class="radio i-checks">
										<input type="radio" name='is_fr' value="1" />是&nbsp;&nbsp;
										<input type="radio" name='is_fr' value="0" checked/>否
									</div>
								</div>
							</div>
						
						<?php endif; ?>    
						
	
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">登录账号：</label>
                            <div class="input-group col-sm-4">
                                <input id="username" type="text" class="form-control" name="username" value="" placeholder="请输入登录账号">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">密码：</label>
                            <div class="input-group col-sm-4">
                                <input id="password" type="password" class="form-control" name="password" placeholder="请输入密码">
                            </div>
                        </div>
						 <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">类型：</label>
                            <div class="input-group col-sm-4">
                                <select class="form-control m-b chosen-select" name="group_id" id="group_id">
                                    <option value="">==请选择类型==</option>
                                    <?php if(!empty($group)): if(is_array($group) || $group instanceof \think\Collection || $group instanceof \think\Paginator): if( count($group)==0 ) : echo "" ;else: foreach($group as $key=>$vo): ?>
                                            <option value="<?php echo $vo['id']; ?>"><?php echo $vo['group_name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">联系号码：</label>
                            <div class="input-group col-sm-4">
                                <input id="phone" type="number" class="form-control" name="phone" value="" placeholder="请输入联系号码">
                            </div>
                        </div>
						 <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">结算银行：</label>
                            <div class="input-group col-sm-4">
                                <select class="form-control m-b chosen-select" name="bank_name" id="bank_name">
                                    <option value="">==类型==</option>
                                    <?php if(!empty($bank_info)): if(is_array($bank_info) || $bank_info instanceof \think\Collection || $bank_info instanceof \think\Paginator): if( count($bank_info)==0 ) : echo "" ;else: foreach($bank_info as $key=>$vo): ?>
                                            <option value="<?php echo $vo['bank_name']; ?>" ><?php echo $vo['bank_name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </select>
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">结算卡户名：</label>
                            <div class="input-group col-sm-4">
                                <input id="bank_real_name" type="text" class="form-control" name="bank_real_name" value="" placeholder="请输入结算卡户名">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">结算卡号：</label>
                            <div class="input-group col-sm-4">
                                <input id="bank_code" type="text" class="form-control" name="bank_code" value="" placeholder="请输入结算卡号">
                            </div>
                        </div>
						 <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">联系号码：</label>
                            <div class="input-group col-sm-4">
                                <input id="phone" type="number" class="form-control" name="phone" value="" placeholder="请输入联系号码">
                            </div>
                        </div>
						 <!--<div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">收款代付费(元)：</label>
                            <div class="input-group col-sm-4">
                                <input id="pay_dfee" type="text" class="form-control" name="pay_dfee" value="" placeholder="请输入收款代付费收益(元)">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						 <div class="form-group">
                            <label class="col-sm-3 control-label">收款收益(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="pay_rate" type="text" class="form-control" name="pay_rate" value="" placeholder="请输入收款收益(%)">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">还款代付费(元)：</label>
                            <div class="input-group col-sm-4">
                                <input id="repay_dfee" type="text" class="form-control" name="repay_dfee" value="" placeholder="请输入还款代付费收益(元)">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						 <div class="form-group">
                            <label class="col-sm-3 control-label">还款收益(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="repay_rate" type="text" class="form-control" name="repay_rate" value="" placeholder="请输入还款收益(%)">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">垫资代付费(元)：</label>
                            <div class="input-group col-sm-4">
                                <input id="dz_dfee" type="text" class="form-control" name="dz_dfee" value="" placeholder="请输入垫资代付费收益(元)">
                            </div>
                        </div>
						<div class="hr-line-dashed"></div>
						 <div class="form-group">
                            <label class="col-sm-3 control-label">垫资收益(%)：</label>
                            <div class="input-group col-sm-4">
                                <input id="dz_rate" type="text" class="form-control" name="dz_rate" value="" placeholder="请输入垫资收益(%)">
                            </div>
                        </div>-->
						<div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">状&nbsp;态：</label>
                            <div class="col-sm-6">
                                <div class="radio i-checks">
                                    <input type="radio" name='status' value="1" checked/>开启&nbsp;&nbsp;
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
<script type="text/javascript" src="/static/admin/webupload/webuploader.min.js"></script>

<script type="text/javascript">

    //提交
    $(function(){
        $('#add_agent').ajaxForm({
            beforeSubmit: checkForm, 
            success: complete, 
            dataType: 'json'
        });
        
        function checkForm(){
            if( '' == $.trim($('#username').val())){
                layer.msg('请输入登录账号',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }

            if( '' == $.trim($('#real_name').val())){
                layer.msg('请输入代理商名称',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }
			
			if( '' == $.trim($('#password').val())){
                layer.msg('请输入登录密码',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }
			
			if( '' == $.trim($('#group_id').val())){
                layer.msg('请选择类型',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }

        }


        function complete(data){
            if(data.code==1){
                layer.msg(data.msg, {icon: 6,time:1500,shade: 0.1}, function(index){
                    window.location.href="<?php echo url('agent/agent'); ?>";
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