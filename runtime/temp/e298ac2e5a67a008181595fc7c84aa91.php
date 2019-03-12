<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:70:"F:\wamp\www\guanjia\public/../application/admin\view\config\index.html";i:1548736428;s:61:"F:\wamp\www\guanjia\application\admin\view\public\header.html";i:1548489490;s:61:"F:\wamp\www\guanjia\application\admin\view\public\footer.html";i:1548489490;}*/ ?>
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
<style type="text/css">
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
</style>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>网站配置</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="form_basic.html#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">       
                    <div class="panel blank-panel">
                        <div class="panel-heading">                     
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">基本配置</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">内容配置</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">系统配置</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">短信配置</a></li>
									<li class=""><a data-toggle="tab" href="#tab-5" aria-expanded="false">费率配置</a></li>
									<li class=""><a data-toggle="tab" href="#tab-6" aria-expanded="false">分润配置</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <form action="<?php echo url('save'); ?>" method="post" class="form-horizontal">  
                                        <div class="hr-line-dashed"></div>                                
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">网站标题：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[web_site_title]" value="<?php echo $config['web_site_title']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 网站标题</span>                                           
                                            </div>
                                        </div>                                 
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">网站描述：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea class="form-control" type="text" rows="3" name="config[web_site_description]"><?php echo $config['web_site_description']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 网站搜索引擎描述</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">网站关键字：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea class="form-control" type="text" rows="3" name="config[web_site_keyword]"><?php echo $config['web_site_keyword']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 网站搜索引擎关键字</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">网站备案号：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[web_site_icp]" value="<?php echo $config['web_site_icp']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 设置在网站底部显示的备案号，如“鄂ICP备14007255号-1”</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">统计代码：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <textarea class="form-control" type="text" rows="3" name="config[web_site_cnzz]"><?php echo $config['web_site_cnzz']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 设置在网站底部显示的站长统计信息</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">版权信息：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[web_site_copy]" value="<?php echo $config['web_site_copy']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 设置在网站底部显示的版权信息</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">站点状态：</label>
                                            <div class="input-group col-sm-4">
                                                <div class="radio i-checks">
                                                    <input type="radio" name='config[web_site_close]' value="1" <?php if($config['web_site_close'] == 1): ?>checked<?php endif; ?>/>开启&nbsp;&nbsp;
                                                    <input type="radio" name='config[web_site_close]' value="0" <?php if($config['web_site_close'] == 0): ?>checked<?php endif; ?>/>关闭
                                                </div>
                                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 站点关闭后除管理员外所有人访问不了后台</span>
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-3">
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存信息</button>&nbsp;&nbsp;&nbsp;
                                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                                            </div>
                                        </div>                               
                                    </form>
                                </div>
                                <div id="tab-2" class="tab-pane">
                                    <form action="<?php echo url('save'); ?>" method="post" class="form-horizontal">  
                                        <div class="hr-line-dashed"></div>                                
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">后台每页记录数：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[list_rows]" value="<?php echo $config['list_rows']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 后台数据每页显示记录数</span>                                           
                                            </div>
                                        </div>                                 
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-3">
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存信息</button>&nbsp;&nbsp;&nbsp;
                                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                                            </div>
                                        </div>                               
                                    </form>
                                </div>
                                <div id="tab-3" class="tab-pane">
                                    <form action="<?php echo url('save'); ?>" method="post" class="form-horizontal">                             
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">禁止后台访问IP：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea class="form-control" type="text" rows="3" name="config[admin_allow_ip]"><?php echo $config['admin_allow_ip']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 多个用#号分隔，如果不配置表示不限制IP访问</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
										<div class="form-group">
                                            <label class="col-sm-2 control-label">VIP特权：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea class="form-control" type="text" rows="3" name="config[viptq]"><?php echo $config['viptq']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> VIP特权介绍文字</span>                                           
                                            </div>
                                        </div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
                                            <label class="col-sm-2 control-label">代理特权：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea class="form-control" type="text" rows="3" name="config[agenttq]"><?php echo $config['agenttq']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 代理特权介绍文字</span>                                           
                                            </div>
                                        </div>
										<div class="hr-line-dashed"></div>
										<div class="form-group">
                                            <label class="col-sm-2 control-label">支付方法：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea class="form-control" type="text" rows="3" name="config[vippayff]"><?php echo $config['vippayff']; ?></textarea>
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 支付方法介绍文字</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-3">
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存信息</button>&nbsp;&nbsp;&nbsp;
                                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                                            </div>
                                        </div>                               
                                    </form>
                                </div>
                                <div id="tab-4" class="tab-pane">
                                    <form action="<?php echo url('save'); ?>" method="post" class="form-horizontal">  
                                        <div class="hr-line-dashed"></div>                                
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">AppKey：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[alisms_appkey]" value="<?php echo $config['alisms_appkey']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请前往阿里云云通信平台查看AppKey</span>                                           
                                            </div>
                                        </div>                                 
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">AppSecret：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[alisms_appsecret]" value="<?php echo $config['alisms_appsecret']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请前往阿里云云通信平台查看AppSecret</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">短信签名：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[alisms_signname]" value="<?php echo $config['alisms_signname']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请前往阿里云云通信平台查看短信签名</span>                                           
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-3">
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存信息</button>&nbsp;&nbsp;&nbsp;
                                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                                            </div>
                                        </div>                               
                                    </form>
                                </div>
								<div id="tab-5" class="tab-pane">
                                    <form action="<?php echo url('save'); ?>" method="post" class="form-horizontal">  
										<div class="form-group">
                                            <label class="col-sm-2 control-label">应用名称：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[siteName]" value="<?php echo $config['siteName']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 前台应用的名称</span>                                           
                                            </div>
                                        </div>   
										<div class="hr-line-dashed"></div>        
										<div class="form-group">
                                            <label class="col-sm-2 control-label">应用地址：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[siteUrl]" value="<?php echo $config['siteUrl']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 前台应用地址(以http://开头,结尾不用带/)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>        
										<div class="form-group">
                                            <label class="col-sm-2 control-label">客服电话：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[sitePhone]" value="<?php echo $config['sitePhone']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> APP客服电话</span>                                           
                                            </div>
                                        </div>
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">刷卡费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[pay_rate]" value="<?php echo $config['pay_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 刷卡费率(%)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">刷卡代付费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[pay_dfee]" value="<?php echo $config['pay_dfee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 刷卡代付费(元)</span>                                           
                                            </div>
                                        </div> 
                                        <div class="hr-line-dashed"></div>                                
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">本金还款费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[rate]" value="<?php echo $config['rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 本金还款费率(%)</span>                                           
                                            </div>
                                        </div>                                 
                                       <!-- 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">完美计划费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[wm_rate]" value="<?php echo $config['wm_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 完美计划费率(%)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">完美计划VIP费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[wm_vip_rate]" value="<?php echo $config['wm_vip_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 完美计划VIP费率(%)</span>                                           
                                            </div>
                                        </div>  
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">精养卡费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[jyk_rate]" value="<?php echo $config['jyk_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 精养卡费率(%)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">精养卡VIP费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[jyk_vip_rate]" value="<?php echo $config['jyk_vip_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 精养卡VIP费率(%)</span>                                           
                                            </div>
                                        </div>-->
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">本金还款代付费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[dfee]" value="<?php echo $config['dfee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 本金还款代付费(元)</span>                                           
                                            </div>
                                        </div>   

										
										
										<!--<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">高成本收款费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[ge_pay_rate]" value="<?php echo $config['ge_pay_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 高成本收款费率(%)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">VIP高成本收款费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[ge_vip_pay_rate]" value="<?php echo $config['ge_vip_pay_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> VIP高成本收款费率(%)</span>                                           
                                            </div>
                                        </div> --> 
										
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">空卡还款费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[dz_fee]" value="<?php echo $config['dz_fee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 空卡还款费率(%)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">空卡还款代付费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[dz_dfee]" value="<?php echo $config['dz_dfee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 空卡还款代付费(元)</span>                                           
                                            </div>
                                        </div>  
										
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">VIP刷卡费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[vip_pay_rate]" value="<?php echo $config['vip_pay_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> VIP刷卡费率(%)</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">VIP本金还款费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[vip_rate]" value="<?php echo $config['vip_rate']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> VIP本金还款费率(%)</span>                                           
                                            </div>
                                        </div>
										
										<div class="hr-line-dashed"></div>
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">VIP空卡还款费率：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[vip_dz_fee]" value="<?php echo $config['vip_dz_fee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> VIP空卡还款费率(%)</span>                                           
                                            </div>
                                        </div>
										                            
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-3">
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存信息</button>&nbsp;&nbsp;&nbsp;
                                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                                            </div>
                                        </div>                               
                                    </form>
                                </div>
								
								<div id="tab-6" class="tab-pane">
                                    <form action="<?php echo url('save'); ?>" method="post" class="form-horizontal">  
										<div class="form-group">
                                            <label class="col-sm-2 control-label">平级分润(%)：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[levelProfit]" value="<?php echo $config['levelProfit']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 奖励平级推荐人收益为被推荐分润的比例(%)</span>                                           
                                            </div>
                                        </div>   
										<div class="hr-line-dashed"></div>        
										<div class="form-group">
                                            <label class="col-sm-2 control-label">代理商推荐代理商推荐费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[dtjdFee]" value="<?php echo $config['dtjdFee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 代理商推荐代理商推荐费（%）</span>                                           
                                            </div>
                                        </div> 
										
										<div class="hr-line-dashed"></div>        
										<div class="form-group">
                                            <label class="col-sm-2 control-label">代理商推荐代理商-运营中心推荐费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[dtjdYyFee]" value="<?php echo $config['dtjdYyFee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 代理商推荐代理商-运营中心推荐费（%）</span>                                           
                                            </div>
                                        </div> 
										
										<div class="hr-line-dashed"></div>        
										<div class="form-group">
                                            <label class="col-sm-2 control-label">运营中心推荐代理商推荐费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[ytjdFee]" value="<?php echo $config['ytjdFee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 运营中心推荐代理商推荐费（%）</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>        
										<div class="form-group">
                                            <label class="col-sm-2 control-label">运营中心推荐运营中心推荐费：</label>
                                            <div class="input-group col-sm-4">                                              
                                                <input type="text" class="form-control" name="config[ytjyFee]" value="<?php echo $config['ytjyFee']; ?>">
                                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 运营中心推荐运营中心推荐费（%）</span>                                           
                                            </div>
                                        </div> 
										<div class="hr-line-dashed"></div>
                                        <div class="form-group">
                                            <div class="col-sm-4 col-sm-offset-3">
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存信息</button>&nbsp;&nbsp;&nbsp;
                                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i> 返回</a>
                                            </div>
                                        </div> 
									</form>
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

<script type="text/javascript">

    var config = {
        '.chosen-select': {},                    
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
</script>
</body>
</html>
