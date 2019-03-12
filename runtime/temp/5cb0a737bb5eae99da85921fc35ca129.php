<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:76:"F:\wamp\www\guanjia\public/../application/admin\view\member\edit_member.html";i:1550394660;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
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

/* TAB */
.nav-tabs.nav>li>a {
    padding: 10px 25px;
    margin-right: 0;
}
.nav-tabs.nav>li>a:hover,
.nav-tabs.nav>li.active>a {
    border-top: 3px solid #1ab394;
    padding-top: 8px;
    border-bottom: 1px solid #FFFFFF;
}
.nav-tabs>li>a {
    color: #A7B1C2;
    font-weight: 500;  
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    border-radius: 0;
}

.colorff{color: #432cff;word-wrap:break-word; word-break:normal; font-weight:bold;}

.control-label{ margin-top:0px;}
</style>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>编辑会员</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
					<div class="panel blank-panel">
						<div class="panel-heading">                     
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">基本信息</a></li>
									<li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">实名信息</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">储蓄卡</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">信用卡</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-5" aria-expanded="false">收款订单</a></li>
									<li class=""><a data-toggle="tab" href="#tab-6" aria-expanded="false">还款订单</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
								<div id="tab-1" class="tab-pane active">
									<form class="form-horizontal" name="edit_member" id="edit_member" method="post" action="<?php echo url('edit_member'); ?>">
										<input type="hidden" name="id"  id="user_id" value="<?php echo $member['id']; ?>">
										<div class="form-group">
											<label class="col-sm-3 control-label">账号：</label>
											<div class="input-group col-sm-4">
												<input id="account" type="text" class="form-control" name="account" value="<?php echo $member['account']; ?>" placeholder="请输入账号">
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">昵称：</label>
											<div class="input-group col-sm-4">
												<input id="nickname" type="text" class="form-control" name="nickname" value="<?php echo $member['nickname']; ?>" placeholder="请输入昵称">
											</div>
										</div>
										
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">推荐人：</label>
											<div class="input-group col-sm-4" style="line-height: 30px;">
												 <?php if(!empty($member_invite)): ?>
												 	<?php echo $member_invite['account']; ?>|<?php echo $member_invite['realname']; else: ?>
												 	<input id="invite_account" type="text" class="form-control" name="invite_account" value="" placeholder="请输入推荐人会员账号">
												 <?php endif; ?>
											</div>
										</div>
										
										
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">代理商：</label>
											<div class="input-group col-sm-4">
												<input id="agent_account" type="text" class="form-control" name="agent_account" value="<?php echo $member['agent_account']; ?>" placeholder="请输入代理商会员账号">
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
											<label class="col-sm-3 control-label">性别：</label>
											<div class="input-group col-sm-4">
												<div class="radio i-checks">
													<input type="radio" name='sex' value="1" <?php if($member['sex'] == 1): ?>checked<?php endif; ?>/>男&nbsp;&nbsp;
													<input type="radio" name='sex' value="2" <?php if($member['sex'] == 2): ?>checked<?php endif; ?>/>女&nbsp;&nbsp;
													<input type="radio" name='sex' value="0" <?php if($member['sex'] == 0): ?>checked<?php endif; ?>/>未知
												</div>
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">会员组：</label>
											<div class="input-group col-sm-4">
												<select class="form-control m-b chosen-select" name="group_id" id="group_id" disabled="disabled">
													<option value="">==请选择会员组==</option>
													<?php if(!empty($group)): if(is_array($group) || $group instanceof \think\Collection || $group instanceof \think\Paginator): if( count($group)==0 ) : echo "" ;else: foreach($group as $key=>$vo): ?>
															<option value="<?php echo $vo['id']; ?>" <?php if($member['group_id'] == $vo['id']): ?>selected<?php endif; ?>><?php echo $vo['group_name']; ?></option>
														<?php endforeach; endif; else: echo "" ;endif; endif; ?>
												</select>
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">头像：</label>
											<div class="input-group col-sm-4">
												<input type="hidden" id="data_photo" name="head_img" value="<?php echo $member['head_img']; ?>">
												<div id="fileList" class="uploader-list" style="float:right"></div>
												<div id="imgPicker" style="float:left">选择头像</div>
												<img id="img_data" class="img-circle" height="80px" width="80px" style="float:left;margin-left: 50px;margin-top: -10px;" src="/uploads/face/<?php echo $member['head_img']; ?>" onerror="this.src='/static/admin/images/head_default.gif'"/>
											</div>
										</div> 
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">电话：</label>
											<div class="input-group col-sm-4">
												<input id="mobile" type="number" class="form-control" name="mobile" value="<?php echo $member['mobile']; ?>" placeholder="请输入会员电话">
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">积分：</label>
											<div class="input-group col-sm-4">
												<input id="integral" type="number" class="form-control" name="integral" value="<?php echo $member['integral']; ?>">
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
											<label class="col-sm-3 control-label">余额：</label>
											<div class="input-group col-sm-4">
												<input id="money" type="number" class="form-control" name="money" value="<?php echo $member['money']; ?>">
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
								<div id="tab-2" class="tab-pane">
									<div class="ibox float-e-margins">
										<div class="form-group">
										 <label class="col-sm-5 control-label">认证状态：</label>
											<div class="input-group col-sm-6 colorff">
											   <?php if($member['is_validate'] == 1): ?>
												 已认证
											   <?php elseif($member['is_validate'] == 2): ?>
												 待审核
											   <?php else: ?>
												 未认证
											   <?php endif; ?>
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
										 <label class="col-sm-5 control-label">真实姓名：</label>
											<div class="input-group col-sm-6 colorff">
											   <?php echo $member['realname']; ?>&nbsp;
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
										 <label class="col-sm-5 control-label">身份证号：</label>
											<div class="input-group col-sm-6 colorff">
											   <?php echo $member['card']; ?>&nbsp;
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
										 <label class="col-sm-5 control-label">身份证正面：</label>
											<div class="input-group col-sm-6 colorff">
											   <img src="/uploads/card/<?php echo $member['id']; ?>/<?php echo $member['card_img_a']; ?>" width="20" height="20" class="img-circle" onerror="this.src='/static/admin/images/head_default.gif'"/>
											</div>
										</div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
										 <label class="col-sm-5 control-label">身份证反面：</label>
											<div class="input-group col-sm-6 colorff">
											   <img src="/uploads/card/<?php echo $member['id']; ?>/<?php echo $member['card_img_b']; ?>" width="20" height="20" class="img-circle" onerror="this.src='/static/admin/images/head_default.gif'"/>
											</div>
										</div>
										
										
									</div>
								</div>
								<div id="tab-3" class="tab-pane">
									<div class="example-wrap">
										<div class="example">
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th>银行名称</th>
														<th>银行卡卡号</th>
														<th>手机号</th>
														<th>状态</th>
														<th>时间</th>
														<th>操作</th>
													</tr>
												</thead>
												<tbody>
													<?php if(empty($bank_card)):?>
														<tr><td colspan="6" align="center">暂无数据</td></tr>
													<?php else:foreach($bank_card as $vo):?>
													<tr>
														<td><?php echo $vo['bank_name']; ?></td>
														<td><?php echo $vo['bank_code']; ?></td>
														<td><?php echo $vo['phone']; ?></td>
														<td><?php echo $bank_status[$vo['status']]; ?></td>
														<td><?php echo date('Y-m-d H:i:s',$vo['ctime']); ?></td>
														<td>
															-
														</td>
													</tr>
													<?php endforeach;endif;?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div id="tab-4" class="tab-pane">
									<div class="example-wrap">
										<div class="example">
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th>银行名称</th>
														<th>信用卡卡号</th>
														<th>手机号</th>
														<th>状态</th>
														<th>时间</th>
														<th>操作</th>
													</tr>
												</thead>
												<tbody>
													<?php if(empty($credit_card)):?>
														<tr><td colspan="6" align="center">暂无数据</td></tr>
													<?php else:foreach($credit_card as $vo):?>
													<tr>
														<td><?php echo $vo['bank_name']; ?></td>
														<td><?php echo $vo['credit_code']; ?></td>
														<td><?php echo $vo['phone']; ?></td>
														<td><?php echo $credit_status[$vo['status']]; ?></td>
														<td><?php echo date('Y-m-d H:i:s',$vo['ctime']); ?></td>
														<td>
															-
														</td>
													</tr>
													<?php endforeach;endif;?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div id="tab-5" class="tab-pane">
									<div class="example-wrap">
										<div class="example">
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th>真实姓名</th>
														<th>手机号</th>
														<th>信用卡号</th>
														<th>提现金额</th>
														<th>费率</th>
														<th>手续费</th>
														<th>单笔费用</th>
														<th>单笔代付费用</th>
														<th>到账银行卡号</th>
														<th>创建时间</th>
														<th>状态</th>
													</tr>
												</thead>
												<tbody id="list-money">
												</tbody>
											</table>
											<div id="AjaxPageMoney" style="text-align:right;"></div>
										</div>
									</div>
								</div>
								<div id="tab-6" class="tab-pane">
									<div class="example-wrap">
										<div class="example">
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th>真实姓名</th>
														<th>手机号</th>
														<th>信用卡号</th>
														<th>任务总金额</th>
														<th>消费服务费</th>
														<th>消费峰值</th>
														<th>最低预留额度</th>
														<th>日服务费总额</th>
														<th>创建时间</th>
														<th>状态</th>
														<th>操作</th>
													</tr>
												</thead>
												<tbody id="list-content">
												</tbody>
											</table>
											<div id="AjaxPage" style="text-align:right;"></div>
										</div>
									</div>
								</div>
							</div>
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
    var $list = $('#fileList');
    //上传图片,初始化WebUploader
    var uploader = WebUploader.create({
     
        auto: true,// 选完文件后，是否自动上传。   
        swf: '/static/admin/webupload/Uploader.swf',// swf文件路径 
        server: "<?php echo url('Upload/uploadface'); ?>",// 文件接收服务端。
        duplicate :true,// 重复上传图片，true为可重复false为不可重复
        pick: '#imgPicker',// 选择文件的按钮。可选。

        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/jpg,image/jpeg,image/png'
        },

        'onUploadSuccess': function(file, data, response) {
            $("#data_photo").val(data._raw);
            $("#img_data").attr('src', '/uploads/face/' + data._raw).show();
        }
    });

    uploader.on( 'fileQueued', function( file ) {
        $list.html( '<div id="' + file.id + '" class="item">' +
            '<h4 class="info">' + file.name + '</h4>' +
            '<p class="state">正在上传...</p>' +
        '</div>' );
    });

    // 文件上传成功
    uploader.on( 'uploadSuccess', function( file ) {
        $( '#'+file.id ).find('p.state').text('上传成功！');
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        $( '#'+file.id ).find('p.state').text('上传出错!');
    }); 

    //提交
    $(function(){
        $('#edit_member').ajaxForm({
            beforeSubmit: checkForm, 
            success: complete, 
            dataType: 'json'
        });
        
        function checkForm(){
            if( '' == $.trim($('#account').val())){
                layer.msg('请输入账号',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }
                       

            if( '' == $.trim($('#group_id').val())){
                layer.msg('请选择会员组',{icon:2,time:1500,shade: 0.1}, function(index){
                layer.close(index);
                });
                return false;
            }

        }

        function complete(data){
            if(data.code==1){
                layer.msg(data.msg, {icon: 6,time:1500,shade: 0.1}, function(index){
                    window.location.href="<?php echo url('member/index'); ?>";
                });
            }else{
                layer.msg(data.msg, {icon: 5,time:1500,shade: 0.1});
                return false;   
            }
        }   
    });
	
	AjaxPage();
	function AjaxPage(curr){
		var user_id=$('#user_id').val();
	    $.getJSON('<?php echo url("Member/repay_program_ajax"); ?>', {page: curr || 1,user_id: user_id}, function(data){
            //$(".spiner-example").css('display','none'); //数据加载完关闭动画 
            if(data.allpage==0){
                $("#list-content").html('<td colspan="11" style="padding-top:10px;padding-bottom:10px;text-align:center">暂无数据</td>');
            }else{				
				var html='';
				var res = data.list;
				for(var i = 0; i < res.length; i++){
					html+='';
					html+='<tr>';
					html+='<td>'+res[i].realname+'</td>';
					html+='<td>'+res[i].mobile+'</td>';
					html+='<td>'+res[i].credit_code+'</td>';
					html+='<td>'+res[i].total_money+'</td>';
					html+='<td>'+res[i].serve_money+'</td>';
					html+='<td>'+res[i].max_expen+'</td>';
					html+='<td>'+res[i].min_money+'</td>';
					html+='<td>'+res[i].day_money+'</td>';
					html+='<td>'+res[i].ctime+'</td>';
					html+='<td>'+res[i].status+'</td>';
					html+='<td><a href="javascript:;" onClick="repay_list('+res[i].pro_id+')" class="btn btn-primary btn-xs btn-outline">详情</a></td>';
					html+='</tr>';
					
				}
				$("#list-content").html(html);
				
				laypage({
                    cont: $('#AjaxPage'),//容器。值支持id名、原生dom对象，jquery对象,
                    pages:data.allpage,//总页数
                    skip: true,//是否开启跳页
                    skin: '#1AB5B7',//分页组件颜色
                    curr: curr || 1,
                    groups: 3,//连续显示分页数
                    jump: function(obj, first){
                        if(!first){
                            AjaxPage(obj.curr)
                        }
                    }
                });
                //console.log(data);
            }
        });
		 
	}
	
	
	
	AjaxPageMoney();
	function AjaxPageMoney(curr){
		var user_id=$('#user_id').val();
	    $.getJSON('<?php echo url("Member/get_money_ajax"); ?>', {page: curr || 1,user_id: user_id}, function(data){
            //$(".spiner-example").css('display','none'); //数据加载完关闭动画 
            if(data.allpage==0){
                $("#list-money").html('<td colspan="11" style="padding-top:10px;padding-bottom:10px;text-align:center">暂无数据</td>');
            }else{				
				var html='';
				var res = data.list;
				for(var i = 0; i < res.length; i++){
					html+='';
					html+='<tr>';
					html+='<td>'+res[i].realname+'</td>';
					html+='<td>'+res[i].mobile+'</td>';
					html+='<td>'+res[i].credit_code+'</td>';
					html+='<td>'+res[i].money+'</td>';
					html+='<td>'+res[i].rate+'‰</td>';
					html+='<td>'+res[i].fee+'</td>';
					html+='<td>'+res[i].mercfee+'</td>';
					html+='<td>'+res[i].dfee+'</td>';
					html+='<td>'+res[i].bank_card+'</td>';
					html+='<td>'+res[i].ctime+'</td>';
					html+='<td>'+res[i].status+'</td>';
					html+='</tr>';
					
				}
				$("#list-money").html(html);
				
				laypage({
                    cont: $('#AjaxPageMoney'),//容器。值支持id名、原生dom对象，jquery对象,
                    pages:data.allpage,//总页数
                    skip: true,//是否开启跳页
                    skin: '#1AB5B7',//分页组件颜色
                    curr: curr || 1,
                    groups: 3,//连续显示分页数
                    jump: function(obj, first){
                        if(!first){
                            AjaxPageMoney(obj.curr)
                        }
                    }
                });
                //console.log(data);
            }
        });
		 
	}
	
	
	//查看明细
	function repay_list(id){
		layer.open({
		  type: 2,
		  title: '任务明细',
		  shadeClose: true,
		  shade: false,
		  maxmin: true, //开启最大化最小化按钮
		  area: ['893px', '400px'],
		  content: '/admin/Service/repay_list/id/'+id+'.html'
		});
	}
	
	
	$(document).on('click',".img-circle",function(){
		layer.open({
		  type: 1,
		  title: false,
		  closeBtn: 0,
		  area: ['600px', '400px'],
		  skin: 'layui-layer-nobg', //没有背景色
		  shadeClose: true,
		  content: '<img src="'+$(this).attr("src")+'" width="600px"/>'
		});
	});

</script>
</body>
</html>