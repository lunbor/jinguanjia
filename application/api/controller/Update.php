<?php
namespace app\api\controller;
use think\Db;
class Update extends Apibase
{
	/**
     * [getIndexData APP获取首页数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function api()
    {
		$data =	$this->_data;

		$config['appVersion'] = 1;	

		$config['telphone'] = "027-59595696";

		$config['IOS'] = "http://".$_SERVER['HTTP_HOST']."/static/download/hdgj.apk";

		$config['Android'] = "http://".$_SERVER['HTTP_HOST']."/static/download/hdgj.ipa";

		$config['content'] = "有的新版本请立即进行更新~";
		
		$config['isUpdate'] = false;

		return json(['code' => 1, 'result' => $config, 'msg' => '操作成功！']);
    }
}
