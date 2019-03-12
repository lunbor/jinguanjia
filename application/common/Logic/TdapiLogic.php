<?php
/**
 * 星洁科技公司接口逻辑类
 */
namespace app\common\Logic;
use think\Model;
use think\Db;
use lib\Curl;
/**
 * 星洁第三方支付接口的逻辑对象
 * 1、商户进件填写的是储蓄卡
 */
class TdapiLogic extends Model{
	protected $seller_no = 'JGJ_F0ABEA14';//商户号
	protected $key = 'ff745513c1ea719c962ee4b65bc4c062';//
	protected $produce_url = 'http://xapi.ypt5566.com';
	protected $repay_channel_no = 'S18HHZTP';//
	protected $d3repay_channel_no = 'S16DFCC';//
	
	//鉴权
	public function authent($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S7SJBZP',//固定值 ‘S7SJBZP’
			'business_no' => 'authent_channel',//固定值 ‘authent_channel’
			'paymer_name' => $data['paymer_name'],//持卡人姓名
			'paymer_idcard' => $data['paymer_idcard'],//持卡人身份证
			'paymer_bank_no' => $data['paymer_bank_no'],//持卡人银行卡号
			'paymer_phone' => $data['paymer_phone'],//持卡人预留手机号
		];
		$url = $this->produce_url.'/api/Authent/authent';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($reqData);die;
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;

	}
	
	//C3通道
	public function CompensatoryWithBase($data){
		$reqData = $data;
		$reqData['merchant_no'] = $this->seller_no;
		$reqData['channel_no'] = 'S8HHZTPDC';
		$url = $this->produce_url.'/api/CompensatoryWithBase/selectH5';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($reqData);die;
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//C3通道
	public function getUserTaskStatus($data){
		$reqData = $data;
		$reqData['merchant_no'] = $this->seller_no;
		$url = $this->produce_url.'/api/CompensatoryWithBase/getUserTaskStatus';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($reqData);die;
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//新建个体商户
	public function enternet($data){
		$reqData = $data;
		$reqData['merchant_no'] = $this->seller_no;
		$url = $this->produce_url.'/api/User/enterNet';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($reqData);die;
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//修改个体商户信息
	public function modifyinfo($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'user_no' => $data['user_no'],//新建商户返回的子商户号user_no
			'bank_branch' => $data['bank_branch'],//储蓄卡:银行卡开户支行名称,信用卡:银行卡开户行名称
			'bank_code' => $data['bank_code'],//联行号
			'validity' =>  $data['validity'],//有效期、信用卡必传(mmyy格式)
			'cvv2' =>  $data['cvv2'],//安全码、信用卡必传
		];
		$url = $this->produce_url.'/api/User/modifyInfo';
		$reqData['sign'] = $this->MakeSign($reqData);
		file_put_contents('Tdlog.txt',json_encode($reqData),FILE_APPEND);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	/**
	 * 代偿业务
	 */
	 
	 //获取激活短信
	public function entrycard($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
 		];
		$url = $this->produce_url.'/api/Repay/entryCard';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($url);
		//dump($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	 //激活通道
	public function jihuoPay($data){
		/*$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S8HHZTPDC',//固定值 ‘S8HHZTPDC’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
		];
  		$url = $this->produce_url.'/api/Other/entryCard';*/
		
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S8HHZTPDC',//固定值 ‘S8HHZTPDC’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'entryCardOrderNo' => $data['entryCardOrderNo'],//获取短信订单号 4.1.1 接口返回
			'smsCode' => $data['smsCode'],//接口对应短信
		];
  		$url = $this->produce_url.'/api/Other/entryCardConfirm';
		
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	 
	}
	
	//激活
	public function entrycardconfirm($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'rate' => $data['rate'],//渠道商给终端用户费率 0.5 是指 0.5%
			'single_payment' => $data['single_payment'],//渠道商给终端用户单笔代付费 1 是指 1 元/笔
			'ypt_order_no' => $data['entryCardOrderNo'],//获取短信订单号 4.1.1 接口返回
			'smsCode' => $data['smsCode'],//接口对应短信
		];
		$url = $this->produce_url.'/api/Repay/entryCardConfirm';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	
	//激活
	public function c2EntryCard($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S20BACB',//固定值 ‘S20BACB’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'rate' => $data['rate'],//渠道商给终端用户费率 0.5 是指 0.5%
			'single_payment' => $data['single_payment'],//渠道商给终端用户单笔代付费 1 是指 1 元/笔
			'page_url' => $data['page_url'],//获取短信订单号 4.1.1 接口返回
			'notify_url' => $data['notify_url'],//接口对应短信
			'channel' => 2,
			'channel_type' => $data['channel_type'],
		];
		$url = $this->produce_url.'/api/Other/entryCard';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	
	//修改费率
	public function c2changeRate($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S20BACB',//固定值 ‘S20BACB’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'config_no' => $data['config_no'],//商户入网返回的 user_no
			'rate' => $data['rate'],//渠道商给终端用户费率 0.5 是指 0.5%
			'single_payment' => $data['single_payment'],//渠道商给终端用户单笔代付费 1 是指 1 元/笔回
		];
		$url = $this->produce_url.'/api/Other/setUserConfigRate';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	//修改费率
	public function changeRate($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'config_no' => $data['config_no'],//商户入网返回的 user_no
			'rate' => $data['rate'],//渠道商给终端用户费率 0.5 是指 0.5%
			'single_payment' => $data['single_payment'],//渠道商给终端用户单笔代付费 1 是指 1 元/笔回
		];
		$url = $this->produce_url.'/api/Repay/changeRate';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	
	//同名付
	public function compensatorypay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S8HHZTPDC',//固定值 ‘S8HHZTPDC’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'config_no' =>  $data['config_no'],//渠道激活的配置号
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' =>  $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
		];
  		$url = $this->produce_url.'/api/Other/insteadPay';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump(json_encode($reqData));
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}

	//同名快捷
	public function otderpay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S8HHZTPDC',//固定值 ‘S8HHZTPDC’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'config_no' =>  $data['config_no'],//渠道激活的配置号
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'ypt_order_no' => $data['ypt_order_no'],//同名付返回的 ypt_order_no
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
		];
  		$url = $this->produce_url.'/api/Other/Pay';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump(json_encode($reqData));
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}

	//查询接口
	public function checkotder(){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S8HHZTPDC',//固定值 ‘S8HHZTPDC’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'ypt_order_no' => '',//YPT 订单号
		];
  		$url = $this->produce_url.'/api/Other/checkOrder';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	
	//查询接口
	public function getwallet(){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S8HHZTPDC',//固定值 ‘S8HHZTPDC’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
		];
  		$url = $this->produce_url.'/api/Other/wallet';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	
	//无卡快捷
	public function c2pay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S20BACB',//固定值 ‘S20BACB’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'config_no' => $data['config_no'],//渠道激活的配置号
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'areaCode' => $data['areaCode'], //地域码 前四位 文档6.4.2获取 city_code 前四位 当值为 0000 时,为全国可用商户
			'memo' => 'pay',
		];
		$url = $this->produce_url.'/api/Other/Pay';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;			
	}
	
	//无卡快捷
	public function pay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'config_no' => $data['config_no'],//渠道激活的配置号
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
			'areaCode' => $data['areaCode'], //地域码 前四位 文档6.4.2获取 city_code 前四位 当值为 0000 时,为全国可用商户
		];
		$url = $this->produce_url.'/api/Repay/Pay';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;			
	}
	
	
	
	//同名付
	public function c2insteadpay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => 'S20BACB',//固定值 ‘S20BACB’
			'business_no' => 'loan_channel',//固定值 ‘loan_channel’
			'config_no' => $data['config_no'],//渠道激活的配置号
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'memo' =>'repay',
		];
		$url = $this->produce_url.'/api/Other/insteadPay';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($reqData);
		//echo json_encode($reqData,true);
		$rel = Curl::http_curl($url,$reqData);
		//echo $rel;
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//同名付
	public function insteadpay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'config_no' => $data['config_no'],//渠道激活的配置号
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'orderNoList' => $data['ypt_order_no'],//支付订单号4.1.3 返回的订单号ypt_order_no . 多笔用逗号分开 单笔示例:123456 多笔示例:123456,456789,789456
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
			'areaCode' => $data['areaCode'],//地域码 前四位
		];
		$url = $this->produce_url.'/api/Repay/insteadPay';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($reqData);
		//echo json_encode($reqData,true);
		$rel = Curl::http_curl($url,$reqData);
		//echo $rel;
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//查询余额
	public function queryCustomBalance($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $data['channel_no'],//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'config_no' => $data['config_no'],//渠道激活的配置号
		];
		$url = $this->produce_url.'/api/Repay/queryCustomBalance';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel1 = json_decode($rel,true);
		
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			$balance = $rel1['balance'];
		}else{
			$balance = 0;
		}
		return $balance;
	}
	
	//查询省份
	public function getProvince($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $data['channel_no'],//固定值
			'business_no' => $data['business_no'],//固定值
		];
		$url = $this->produce_url.'/api/Landmerchant/get_province';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//查询城市
	public function getCity($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $data['channel_no'],//固定值
			'business_no' => $data['business_no'],//固定值
			'province_code' => $data['province_code'],
		];
		$url = $this->produce_url.'/api/Landmerchant/get_city';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}

	//获取mcc分类
	public function getMacc($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'p_id' => $data['p_id'],
		];
		$url = $this->produce_url.'/api/Landmerchant/getMacc';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	
	//*********************************************d3通道*********************************************//
	//激活通道
	public function d3entrycard($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->d3repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'user_no' => $data['user_no'],//商户入网返回的 user_no
			'rate' => $data['rate'],
			'single_payment' => $data['single_payment'],
			'rate' => $data['rate'],
			'page_url' => $data['page_url'],
			'notify_url' => $data['notify_url'],
			'channel' => 2
 		];
		$url = $this->produce_url.'/api/Repay/entryCard';
		$reqData['sign'] = $this->MakeSign($reqData);
		//dump($url);
		//dump($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//快捷
	public function d3pay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->d3repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'config_no' => $data['config_no'],//渠道激活的配置号
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			'areaCode' => $data['areaCode'], //地域码 前四位 文档6.4.2获取 city_code 前四位 当值为 0000 时,为全国可用商户
			'mcc' => $data['mcc'],
			//'device_id' => $data['device_id'],
			//'clientip' => $data['clientip'],
			'memo' => $data['memo'],
		];
		$url = $this->produce_url.'/api/Repay/Pay';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;			
	}
	
	//同名付
	public function d3insteadpay($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->d3repay_channel_no,//固定值 ‘S8HHZTP’
			'business_no' => 'back_channel',//固定值 ‘back_channel’
			'config_no' => $data['config_no'],//渠道激活的配置号
			'price' => $data['price'],//订单金额 单位元(1 = 人民币 1元)
			'order_no' => $data['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' => $data['notifyUrl'],//渠道商接受回调路由,用于异步接受通知
			//'device_id' => $data['device_id'],
			//'clientip' => $data['clientip'],
			'memo' => $data['memo'],
		];
		$url =$this->produce_url.'/api/Repay/insteadPay';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;
	}
	
	//修改费率
	public function d3changeRate($data){
		$reqData = [
			'merchant_no' => $this->seller_no,//渠道商商户号YPT分配的渠道商户号
			'channel_no' => $this->d3repay_channel_no,//固定值
			'business_no' => 'back_channel',//固定值
			'config_no' => $data['config_no'],//商户入网返回的 user_no
			'rate' => $data['rate'],//渠道商给终端用户费率 0.5 是指 0.5%
			'single_payment' => $data['single_payment'],//渠道商给终端用户单笔代付费 1 是指 1 元/笔回
		];
		$url = $this->produce_url.'/api/Repay/setUserConfigRate';
		$reqData['sign'] = $this->MakeSign($reqData);
		$rel = Curl::http_curl($url,$reqData);
		$rel = json_decode($rel,true);
		return $rel;		
	}
	
	//*********************************************d3通道*********************************************//

	/**
	 * 格式化参数格式化成url参数
	 */
	private function ToUrlParams($reqData)
	{
		$buff = "";
		foreach ($reqData as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 生成签名
	 */
	private function MakeSign($reqData)
	{
		//签名步骤一：按字典序排序参数
		ksort($reqData);
		$string = $this->ToUrlParams($reqData);
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".$this->key;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}

}
