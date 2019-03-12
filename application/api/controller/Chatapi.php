<?php
namespace app\api\controller;
use think\Config;
use think\Db;
class Chatapi extends Apibase
{
	/**
     * [getIndexData APP获取首页数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function api()
    {
		$data=$this->_data;
		return rel(1,'回复成功','我是'.config('siteName').'客服小艾，请问您有什么问题需要咨询？，您也可以直接拨打客服电话'.config('sitePhone').'进行咨询');
    }
}
