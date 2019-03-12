<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:69:"F:\wamp\www\guanjia\public/../application/admin\view\index\index.html";i:1548857842;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
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
<div class="wrapper wrapper-content">
	<?php if($is_agent !=0): ?>
	<div class="row">
		<div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
					<a href="<?php echo url('agent/add_withdraw'); ?>"><span class="label label-success pull-right">提现</span></a>
                    <h5>可提现金额</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $is_agent['money']; ?></h1>
                    <small>元</small>
                </div>
            </div>
        </div>
		
		<div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>待结算金额</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $is_agent['d_money']; ?></h1>
                    <small>元</small>
                </div>
            </div>
        </div>
		
		<div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
					<a href="<?php echo url('agent/agent_account'); ?>"><span class="label label-success pull-right">明细</span></a>
                    <h5>总收益</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $is_agent['z_money']; ?></h1>
                    <small>元</small>
                </div>
            </div>
        </div>
		
		<div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>已提现金额</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $is_agent['w_money']; ?></h1>
                    <small>元</small>
                </div>
            </div>
        </div>
    </div>
	<?php endif; ?>
        <!-- 上方tab -->
    <div class="row">
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <!--<span class="label pull-right">日</span>
                    <span class="label pull-right">周</span>
                    <span class="label label-success pull-right">月</span>-->
                    <h5>会员</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $member; ?></h1>
                    <small>今日新增</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <!--<span class="label pull-right">日</span>
                    <span class="label pull-right">周</span>
                    <span class="label label-success pull-right">月</span>-->
                    <h5>订单</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $repay_list_z+$get_money_z; ?></h1>
                    <small>总交易量</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <!--<span class="label pull-right">日</span>
                    <span class="label pull-right">周</span>
                    <span class="label label-success pull-right">月</span>-->
                    <h5>今日交易量</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $repay_list+$get_money; ?></h1>
                    <small>日交易量</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <a href="#question">
                        <i class="fa fa-question" style="color:red;margin-left:10px" data-container="body" data-toggle="popover" data-placement="bottom"
                           data-content="日活跃用户 = 当日登录游戏的用户 - 当日新增用户数(去重)@@@@@@@@月活跃用户 = 最近30天登录游戏的用户 - 最近30天新增用户(去重)"></i>
                    </a>
                    <!--<span class="label pull-right">日</span>
                    <span class="label pull-right">周</span>
                    <span class="label label-success pull-right">月</span>-->
                    <h5>活跃用户</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $member_hy; ?></h1>
                    <small>今日活跃</small>
                </div>
            </div>
        </div>

        <div class="col-sm-3" style="display:none">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                   <!-- <span class="label pull-right">日</span>
                    <span class="label pull-right">周</span>
                    <span class="label label-success pull-right">月</span>-->
                    <h5>注册</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $member_z; ?></h1>
                    <small>总注册量</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 中间折线 -->
    <div class="row" style="display:none">
        <div class="col-sm-12">
            <div class="col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-cogs"></i> 系统信息
                    </div>
                    <div class="panel-body">
                        <p><i class="fa fa-sitemap"></i> 框架版本：ThinkPHP<?php echo $info['think_v']; ?>
                        </p>
                        <p><i class="fa fa-windows"></i> 服务环境：<?php echo $info['web_server']; ?>
                        </p>
                        <p><i class="fa fa-warning"></i> 上传附件限制：<?php echo $info['onload']; ?>
                        </p>
                        <p><i class="fa fa-credit-card"></i> PHP 版本：<?php echo $info['phpversion']; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <i class="fa fa-cogs"></i> 系统信息
                    </div>
                    <div class="panel-body">
                        <p><i class="fa fa-send-o"></i> 博客：<a href="http://tw186.com" target="_blank">http://tw186.com</a>
                        </p>
                        <p><i class="fa fa-qq"></i> QQ：<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=1582978230&amp;site=qq&amp;menu=yes" target="_blank">1582978230</a>
                        </p>
                        <p><i class="fa fa-weixin"></i> 微信：<a href="javascript:;">13971684432</a>
                        </p>
                        <p><i class="fa fa-credit-card"></i> 支付宝：<a href="javascript:;" class="支付宝信息">425847184@qq.com / 汤天文</a>
                        </p>
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

</body>
</html>