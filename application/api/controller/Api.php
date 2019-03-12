<?php
namespace app\api\controller;
use app\api\model\BannerModel;
use app\api\model\CreditCardModel;
use app\api\model\BankCardModel;
use app\api\model\MemberModel;
use app\api\model\ArticleModel;
use app\api\model\GetMoneyModel;
use think\Db;
use think\Page;
use think\Config;
use lib\Curl;
class Api extends Apibase
{
	/**
     * [getIndexData APP获取首页数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getIndexData()
    {
		$data=$this->_data;
		$rel = [];
		$map=[
			'ad_position_id'	=>1,		
		];
		$bannerMD = new BannerModel();
		$banner = $bannerMD->getAdAll($map,5);
		$rel['banner'] = $banner;
		$rel['cardReapy'] = [];
		return rel(1,'获取数据成功',$rel);
		
    }
	
	/**
     * [getIndexData APP获取卡包数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getCardData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		$map=[
			'user_id'	=> $user_id,		
		];
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getAll($map);
		$rel['creditCard'] = $creditCard;
		
		$bankCardMD = new BankCardModel();
		$bankCard = $bankCardMD->getAll($map);
		$rel['bankCard'] = $bankCard;
		
		if($user){
			$rel['is_validate'] = $user['is_validate'];
		}else{
			$rel['is_validate'] = 0;
		}
		
		//绑卡须知
		$articleMD = new ArticleModel();
		$article = $articleMD->getOne(['id'	=> 3]);
		$rel['article'] = $article;
		
		return rel(1,'获取数据成功',$rel);
	
	}
	
	
	/**
     * [getIndexData APP获取用户信息]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getUserData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
			if($user['group_id'] == 2){
				$user['vip_name'] = 'VIP会员';
			}else{
				$user['vip_name'] = '普通会员';
			}
			$user['message'] = 0;
			$user['repay_num'] = Db::name('repayProgram')->alias('r')->where(['r.status'=>['IN','1,2'],'r.user_id'=>$user_id])->count();
			$user['invite'] = Db::name('member')->where(['closed'=>0,'invite_id'=>$user_id])->count();
			if($user['head_img']!=''){
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$user['head_img']  = $site.'/uploads/face/'.str_replace('\\','/',$user['head_img']);
			}
			$rel['userInfo'] = $user;
		}else{
			return rel(-1,'用户不存在',$rel);
		}
		return rel(1,'获取数据成功',$rel);
        /*$map=[
			'id'	=> 212135,		
		];
		
		if(!isset($data['account']) || $data['account']==''){
			return rel(-1,'手机号不能为空',$rel);
		}
		
		$memberMD = new MemberModel();
		$userInfo = $memberMD->getOne($map);
		if(!$userInfo){
			return rel(-1,'用户不存在',$rel);
		}
		$rel['userInfo'] = $userInfo;*/
	}
	
	
	/**
     * [getClause APP获取隐私条款]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getClause()
    {
		$data=$this->_data;
		$rel = [];
		$map=[
			'id'	=> 1,		
		];
		$articleMD = new ArticleModel();
		$article = $articleMD->getOne($map);
		if(!$article){
			return rel(-1,'文章不存在',$rel);
		}
		$rel['article'] = $article;
		
		return rel(1,'获取数据成功',$rel);
	
	}
	
	
	/**
     * [getAbout APP关于我们]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAbout()
    {
		$data=$this->_data;
		$rel = [];
		$map=[
			'id'	=> 2,		
		];
		$articleMD = new ArticleModel();
		$article = $articleMD->getOne($map);
		if(!$article){
			return rel(-1,'文章不存在',$rel);
		}
		$rel['article'] = $article;
		
		return rel(1,'获取数据成功',$rel);
	
	}
	
	
	/**
     * [doLogin APP登录用户]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function doLogin()
    {
		$data=$this->_data;
		$rel = [];
		if(!isset($data['account']) || $data['account']==''){
			return rel(-1,'手机号不能为空',$rel);
		}
		
		if(!isset($data['password']) || $data['password']==''){
			return rel(-1,'密码不能为空',$rel);
		}
		
		//加密
		$data['password'] = md5(md5($data['password']) . config('auth_key'));
		
		$map=[
			'account'	=> $data['account'],
			'password'	=> $data['password'],		
		];
		$memberMD = new MemberModel();
		$userInfo = $memberMD->getOne($map);
		
		if(!$userInfo) return rel(-2,'手机号或密码错误');
		
		if($userInfo['status']!='1'){
			return rel(-2,'您的账号被禁用,如有疑问请咨询客服');
		}
		
		$user_id = $memberMD->getUserID($map);
		
		$last_login_time = time();
		//生成令牌
		$param['token'] = $userInfo['token'] =do_mencrypt($user_id);
		$param['session_id']= $userInfo['session_id'] = md5($last_login_time);
		$param['last_login_time'] = $last_login_time;
		$memberMD->edit($map,$param);

		return rel(1,'登陆成功',$userInfo);
	
	}
	
	
	/**
     * [getUserInfoData APP获取用户信息]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getUserInfoData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
			if($user['group_id'] == 2){
				$user['vip_name'] = 'VIP会员';
			}else{
				$user['vip_name'] = '普通会员';
			}
			
			//改成代理模式
			$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
			if($agent){
				$user['group_name'] = Db::name("agent_group")->where(['id'=>$agent['group_id']])->value('group_name');
			}else{
				$user['group_name'] = '会员';
			}
			
			if($user['head_img']!=''){
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$user['head_img']  = $site.'/uploads/face/'.str_replace('\\','/',$user['head_img']);
			}
			$user['telphone'] = config('sitePhone');
			$rel['userInfo'] = $user;
		}else{
			return rel(-1,'用户不存在',$rel);
		}
		return rel(1,'获取数据成功',$rel);
	}
	
	/**
     * [setNickname APP设置昵称]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function setNickname()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
				if(!isset($data['nickname']) || $data['nickname']==''){
					return rel(-1,'昵称不能为空',$rel);
				}
				$param['nickname'] = $data['nickname'];
				$memberMD = new MemberModel();
				$map['id'] = $user_id;
				$memberMD->edit($map,$param);
				return rel(1,'保存成功',$rel);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	
		
	/**
     * [sendSms APP发送短信接口]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function sendSms(){
		$data	= $this->_data;
		$user_id=$this->user_id;
		$getPass = 0;
		$rel = [];
		if(isset($data['getPass']) && $data['getPass']==1){
			$getPass = 1;
		}
		if(isset($data['is_user']) && $data['is_user']==1){
			if($user_id<=0){
				return rel(-1,'用户不存在',$rel);
			}
			$memberMD = new MemberModel();
			$map['id'] = $user_id;
			$data['phone'] = $memberMD->getUserID($map,'account');	
		}else{
			$memberMD = new MemberModel();
			if(!isset($data['phone']) && empty($data['phone'])) return rel(-1,'手机号码不能为空');
			$is_phone = $memberMD->getUserID(['account'=>$data['phone']]);
			if($is_phone){
				if($getPass == 0){
					//不是找回密码情况下
					return rel(-1,'手机号已存在',$rel);
				}
			}else{
				if($getPass == 1){
					return rel(-1,'该手机号未注册',$rel);
				}
			}
		}
		//if(!isset($data['phone']) && empty($data['phone'])) return rel(-1,'手机号码不能为空');
		$phone=$data['phone'];
		
		
		if(!check_mobile($phone)){
			return rel(-1,'手机号码格式有误');
		}
		//$smsCode=Db::name('smsLog')->where(['phone' => $phone,'status'=>0])->find();
		$time=5;			//几分钟后过期
		$verifyCode = rand(1000,9999);
		if($getPass == 1){
			$content = "【".config('siteName')."】尊敬的用户您好，您的短信验证码为：".$verifyCode.",您正在修改登录密码，请勿泄露验证码！";
		}else{
			$content = "【".config('siteName')."】尊敬的用户您好，您的短信验证码为：".$verifyCode.",感谢您的使用，祝您生活愉快！";
		}
		$result = Db::name('smsLog')->insertGetId(['phone' => $phone,'content' => $content,  'create_time' => time() + $time * 60, 'code' => $verifyCode]);
		if(!$result){
			return rel(-1,'发送失败，稍后重试');
		}else{
//			$res = true;
			$res = $this->sendSmsByzk($phone,$content);
			if($res){
				$verifyCode = do_mencrypt($result);			//对验证码进行加
				return rel(1,'发送成功',$verifyCode);
			}else{
				return rel(-1,'发送失败，稍后重试');
			}
		}
	}
	public function testsendSms(){
		$phone = "13971684432";
		$content = "【".config('siteName')."】您在平台进行的信用卡还款，因还款失败，导致还款任务终止，请前往APP查看详细信息。";
		$res = $this->sendSmsByzk($phone,$content);
	}
	/**
	* 发送手机短信
	* @author byzk
	* @param number $phone
	* @param string $content
	* @return number
	*/
	private function sendSmsByzk($phone, $content){
		if(!empty($phone) && !empty($content)){
			$content.="";
			$time = time();		//$url="http://sdk.entinfo.cn:8061/webservice.asmx/mdsmssend?sn=SDK-FLH-010-00309&pwd=".strtoupper(md5('SDK-FLH-010-003092fEad6-f'))."&mobile=".$phone."&content=".$content."&ext=&stime=&rrid=&msgfmt=";
			//$url="http://121.42.250.120:8888/v2sms.aspx?action=send&userid=5629&timestamp=".$time."&sign =".strtoupper(md5('阿诺会员112233'.$time))."&mobile=".$phone."&content=".$content."&sendTime=&extno=";
			$url="http://121.42.250.120:8888/v2sms.aspx";
			$reqData = [
				'action' => 'send',
				'userid' => '5629',
				'timestamp' => $time,
				'sign' => strtolower(md5('阿诺会员112233'.$time)),
				'mobile' => $phone,
				'content' => $content,
				'sendTime' => '',
				'extno' => '',
			];
			$xml=Curl::http_curl($url,$reqData);
			$xmlob=simplexml_load_string($xml);
			$arr=(array)$xmlob;
			//dump($arr);
			//if($arr[0] > 0){
			if($arr['successCounts'] > 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
	
	/**
     * [verification APP手机号修改验证]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function verification(){
		$data	= $this->_data;
		$user_id=$this->user_id;
		$rel = [];
		if($user_id<=0){
			return rel(-1,'用户不存在',$rel);
		}
		
		if(!isset($data['verifyCode']) || $data['verifyCode']==''){
			return rel(-1,'请先获取验证码',$rel);
		}
		
		if(!isset($data['smsCode']) || $data['smsCode']==''){
			return rel(-1,'验证码不能为空',$rel);
		}

		
		$memberMD = new MemberModel();
		$map['id'] = $user_id;
		$mapSms['phone'] = $memberMD->getUserID($map,'account');	
		
		$mapSms['id']=do_mdecrypt($data['verifyCode']);
		//$mapSms['create_time'] = ['lt',time()];
		$mapSms['status'] = 0;
		$sms = Db::name('smsLog')->where($mapSms)->order('id desc')->find();
		if(!$sms){
			return rel(-1,'验证码错误，请重新获取',$rel);
		}
		
		if($sms['create_time'] < time()){
			return rel(-1,'验证码已过期，请重新获取',$rel);
		}
		
		if($sms['code']!=$data['smsCode']){
			return rel(-1,'验证码错误，请重新输入',$rel);
		}
		
		$res = do_mencrypt($mapSms['id'].'|'.time());			
		return rel(1,'验证成功',$res);

	}
	
	
	/**
     * [editPhone APP修改手机号]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function editPhone(){
		$data	= $this->_data;
		$user_id=$this->user_id;
		$rel = [];
		if($user_id<=0){
			return rel(-1,'用户不存在',$rel);
		}
		
		if(!isset($data['phone']) || $data['phone']==''){
			return rel(-1,'手机号不能为空',$rel);
		}
		
		if(!isset($data['verifyCode']) || $data['verifyCode']==''){
			return rel(-1,'请先获取验证码',$rel);
		}
		
		if(!isset($data['smsCode']) || $data['smsCode']==''){
			return rel(-1,'请输入验证码',$rel);
		}
		
		if(!isset($data['toverifyCode']) || $data['toverifyCode']==''){
			return rel(-1,'原手机号验证操作已失效',$rel);
		}
		
		$memberMD = new MemberModel();
		$map['id'] = $user_id;
		$phone = $memberMD->getUserID($map,'account');	
		$mapSms['phone'] = $data['phone'];
		
		$is_phone = $memberMD->getUserID(['account'=>$data['phone']]);
		if($is_phone){
			return rel(-1,'手机号已存在',$rel);
		}
		$mapSms['id']=do_mdecrypt($data['verifyCode']);
		//$mapSms['create_time'] = ['lt',time()];
		$mapSms['status'] = 0;
		$sms = Db::name('smsLog')->where($mapSms)->order('id desc')->find();
		if(!$sms){
			return rel(-1,'验证码错误，请重新获取',$rel);
		}
		
		if($sms['create_time'] < time()){
			return rel(-1,'验证码已过期，请重新获取',$rel);
		}
		
		if($sms['code']!=$data['smsCode']){
			return rel(-1,'验证码错误，请重新输入',$rel);
		}
		
		$toverifyCode = do_mdecrypt($data['toverifyCode']);
		$strArray = explode("|",$toverifyCode); 
		if(is_array($strArray)){
			try{
			  $yzsms	= Db::name('smsLog')->where(['phone'=>$phone,'id'=>$strArray[0]])->find();
			  if(!$yzsms){
			  	return rel(-1,'操作验证码无法通过，请重新操作',$rel);
			  }
			  
			  if(time()-600 >  $strArray[1]){
			  	return rel(-1,'操作超时，请重新操作',$rel);
			  }
			  
			  //修改手机号
			  
			  $memberMD->edit(['id'=>$user_id],['account'=>$data['phone'],'mobile'=>$data['phone']]);
			  return rel(1,'操作成功',$rel);
			} catch(Exception $e){
				return rel(-1,'操作流程有误，请重新操作',$rel);
			}
		
		}else{
			return rel(-1,'操作流程有误，请重新操作',$rel);
		}
	
	
	}
	
	
	/**
     * [getIdCard APP实名认证]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getIdCard()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
		    //if($user['is_validate']==1){
				//$memberMD = new MemberModel();
				//$map['id'] = $user_id;
				//$user['card_img_a'] = $memberMD->getUserID($map,'card_img_a');
				//$user['card_img_b'] = $memberMD->getUserID($map,'card_img_b');	
				//$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				//$user['card_img_a']  = $site.'/uploads/card/'.$user_id.'/'.str_replace('\\','/',$user['card_img_a']);
				//$user['card_img_b']  = $site.'/uploads/card/'.$user_id.'/'.str_replace('\\','/',$user['card_img_b']);
			//}else{
				$user['card_img_a']  = '';
				$user['card_img_b']  = '';
			//}
			$rel['cardInfo'] = $user;
			return rel(1,'获取成功',$rel);
	    }else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	/**
     * [addIdCard APP实名认证]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function addIdCard()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
				if(!isset($data['realname']) || $data['realname']==''){
					return rel(-1,'请填写真实姓名',$rel);
				}
				
				if(!isset($data['card']) || $data['card']==''){
					return rel(-1,'请填写身份证号',$rel);
				}
				
				/*if(!isset($data['card_a_data']) || $data['card_a_data']==''){
					return rel(-1,'身份证正面未上传成功',$rel);
				}
				
				if(!isset($data['card_b_data']) || $data['card_b_data']==''){
					return rel(-1,'身份证反面未上传成功',$rel);
				}*/
				
				
				if(!isIdCard($data['card'])){
					return rel(-1,'身份证号码不符合规范，注意X大写',$rel);
				}
				
				
				$param['realname'] = $data['realname'];
				$param['card'] = $data['card'];
				//$param['card_img_a'] = $data['card_a_data'];
				//$param['card_img_b'] = $data['card_b_data'];
				$param['is_validate'] = 1;
				$memberMD = new MemberModel();
				$map['id'] = $user_id;
				$memberMD->edit($map,$param);
				return rel(1,'认证成功',$rel);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	
	/**
     * [getAccountData APP交易明细]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAccountData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
			$map['user_id'] = $user_id;
			$Nowpage = $data['page'] ? $data['page']:1;
			$getmoneyMD = new GetMoneyModel();
			$getmoney = $getmoneyMD->getAll($map,$Nowpage,10);
			
			$list=[];
			foreach($getmoney as $k=>$v){
				$list[$k]['get_id']=$v['get_id'];
				$list[$k]['title']='极速付款（D+0）';
				$list[$k]['tail']=substr($v['credit_code'], -4);
				if($v['status']==4){
					$list[$k]['money']='+'.$v['money'];
				}else{
					$list[$k]['money']=$v['money'];
				}
				$list[$k]['balance']=0;
				$list[$k]['time']=date('Y-m-d H:i:s',$v['ctime']);
				$list[$k]['dscInc'] = $v['dsc'];
				switch ($v['status'])
				{
				case 0:
				 	$list[$k]['dsc']='执行中';
				  break;  
				case 1:
				    $list[$k]['dsc']='交易失败';
				  break;
				case 2:
				    $list[$k]['dsc']='交易中';
				  break;
				case 3:
				    $list[$k]['dsc']='结算中';
				  break;
				case 4:
				    $list[$k]['dsc']='交易成功';
				  break;
				case 5:
				    $list[$k]['dsc']='结算失败';
				  break;
				default:
				  	$list[$k]['dsc']='未知';
				}
				$list[$k]['status'] = $v['status'];
			}
			return rel(1,'获取成功',['list'=>$list]);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	
	/**
     * [getAccountDetail APP交易明细]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAccountDetail()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['get_id']) ||  $data['get_id']<=0){return rel(-1,'参数无效');	}
		$rel = [];
		if($user){
			$map['user_id'] = $user_id;
			$map['get_id'] = $data['get_id'];
			$getmoneyMD = new GetMoneyModel();
			$getmoney = $getmoneyMD->getOne($map);
			if(!$getmoney)  return rel(-1,'该信息无效');	//验证该用户是否已经注册
				$bank_name = Db::name('credit_card')->where('credit_id',$getmoney['credit_id'])->value('bank_name');
				$accountDetail['get_id']=$getmoney['get_id'];
				$accountDetail['title']='极速付款（D+0）';
				$accountDetail['credit_code']=$bank_name.'(尾号'.substr($getmoney['credit_code'], -4).')';
				$bank_name = Db::name('bank_card')->where('bankcard_id',$getmoney['bankcard_id'])->value('bank_name');
				$accountDetail['bank_card']=$bank_name.'(尾号'.substr($getmoney['bank_card'], -4).')';
				$accountDetail['money']=$getmoney['money'];

				$accountDetail['order_no']=$getmoney['order_no'];
				$accountDetail['ctime']=date('Y-m-d H:i:s',$getmoney['ctime']);
				$accountDetail['dscInc'] = $getmoney['dsc'];
				switch ($getmoney['status'])
				{
				case 0:
				 	$accountDetail['dsc']='执行中';
				  break;  
				case 1:
				    $accountDetail['dsc']='交易失败';
				  break;
				case 2:
				    $accountDetail['dsc']='交易中';
				  break;
				case 3:
				    $accountDetail['dsc']='结算中';
				  break;
				case 4:
				    $accountDetail['dsc']='交易成功';
				  break;
				case 5:
				    $accountDetail['dsc']='结算失败';
				  break;
				default:
				  	$accountDetail['dsc']='未知';
				}
				$accountDetail['status'] = $getmoney['status'];
				$accountDetail['mercfee'] = $getmoney['mercfee'];
				$accountDetail['fee'] = $getmoney['fee'];
			return rel(1,'获取成功',['accountDetail'=>$accountDetail]);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	
	/**
     * [getInviteData APP邀请好友]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getInviteData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
		if($user){
			$rel['shareUrl']  = config('siteUrl').'/#/?shareId='.$user_id;
			$rel['shareImg']  = $site.'/uploads/img/intive_bg_1.png';
			$rel['is_code'] = 1;
			return rel(1,'获取成功',$rel);
	    }else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	
	/**
     * [reg APP注册用户]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function reg()
    {
		$data=$this->_data;
		$rel = [];
		//校验
		if (!isset($data['phone'])) {
			return rel(-1, '请填写手机号');
		}
		
		if(!check_mobile($data['phone'])){
			return rel(-1,'手机号码格式有误');
		}
		
		$memberMD = new MemberModel();
		$is_phone = $memberMD->getUserID(['account'=>$data['phone']]);
		if($is_phone){
			return rel(-1,'你已经注册过了,请直接登录',$rel);
		}
		
		if(!isset($data['verifyCode']) || $data['verifyCode']==''){
			return rel(-1,'请先获取验证码',$rel);
		}
		
		if(!isset($data['smsCode']) || $data['smsCode']==''){
			return rel(-1,'请输入验证码',$rel);
		}
		
		
		if(!isset($data['password']) || $data['password']==''){
			return rel(-1,'密码不能为空',$rel);
		}
		
		if(mb_strlen($data['password']) < 6 || mb_strlen($data['password']) > 18 ){
			return rel(-1,'请填写6~8位字符密码',$rel);
		}
		
		//加密
		$data['password'] = md5(md5($data['password']) . config('auth_key'));
		
		$mapSms['phone'] = $data['phone'];
		$mapSms['id']=do_mdecrypt($data['verifyCode']);
		//$mapSms['create_time'] = ['lt',time()];
		$mapSms['status'] = 0;
		$sms = Db::name('smsLog')->where($mapSms)->order('id desc')->find();
		if(!$sms){
			return rel(-1,'验证码错误，请重新获取',$rel);
		}
		
		if($sms['create_time'] < time()){
			return rel(-1,'验证码已过期，请重新获取',$rel);
		}
		
		if($sms['code']!=$data['smsCode']){
			return rel(-1,'验证码错误，请重新输入',$rel);
		}
		
		$data['invite_code'] = $data['shareId'] ?? 0;
		//注册
		$invite_code = $data['invite_code'] ?? 0;
		$agent_id = $data['agentId'] ?? 0;
		if($agent_id > 0){
			$agentdd = Db::name('agent')->where(['admin_id' => $agent_id])->find();
			if($agentdd){
				$data['invite_code'] = $invite_code = $agentdd['user_id'];
			}
		}
		$qd_id = $data['qdId'] ?? 0;
		$invite_id = 0;
		if ($invite_code) {
			$invite_info = Db::name('member')->where(['id' => $data['invite_code']])->find();
			if(!$invite_info){return rel(-1,'推荐码错误',$rel);}
			$agent = Db::name('agent')->where(['user_id' => $data['invite_code']])->find();
			$invite_id = $invite_info['id'] ?? 0;
			if($agent){
				$agent_id = $agent['admin_id'];
			}else{
				$agent_id = $invite_info['agent_id'] ?? 0;
			}
			if ($invite_id > 0) {
				$data['invite_id'] = $invite_id;
				$integralusers = Db::name('member')->where('id', $invite_info['id'])->find();
				if($integralusers['path'] != ''){
					$path =   $integralusers['path'].','.$invite_id;
				}else{
					$path =   $invite_id;
				}
			}else{
				return rel(-1,'推荐码错误',$rel);
			}
		}
		$nowtime = time();
		$nickname = substr_replace($data['phone'],'****',3,4);
		$insertdata = [
			'account' => $data['phone'],
			'password' => $data['password'],
			'nickname' => $nickname,
			'group_id' => 1,
			'mobile' => $data['phone'],
			'invite_id' => $invite_id,
			'agent_id' => $agent_id,
			'qd_id' => $qd_id,
			'invite_code' => $invite_code,
			'path' => $path	?? '',
			'create_time' => $nowtime,
			'update_time' => $nowtime,
			'last_login_time' => $nowtime,
			'login_num' => 1,
			'status' => 1,
			'sex' => 1,
		];
		$user_id = Db::name('member')->insertGetId($insertdata);
		if ($user_id>0) {
			
			$userInfo = $memberMD->getOne(['id'=>$user_id]);
			//生成令牌
			$param['token'] = $userInfo['token'] =do_mencrypt($user_id);
			$param['session_id']= $userInfo['session_id'] = md5($nowtime);
			$memberMD->edit(['id'=>$user_id],$param);
			
			//默认添加为代理商
			$this->addAgent($insertdata,$user_id);
			return rel(1, '注册成功',$userInfo);
		} else {
			return rel(-1, '注册失败');
		}
	}
	
	
	
	/**
     * [getpassword APP找回密码]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getpassword()
    {
		$data=$this->_data;
		$rel = [];
		//校验
		if (!isset($data['phone'])) {
			return rel(-1, '请填写手机号');
		}
		
		if(!check_mobile($data['phone'])){
			return rel(-1,'手机号码格式有误');
		}
		
		$memberMD = new MemberModel();
		$is_phone = $memberMD->getUserID(['account'=>$data['phone']]);
		if(!$is_phone){
			return rel(-1,'该手机号未注册',$rel);
		}
		
		if(!isset($data['verifyCode']) || $data['verifyCode']==''){
			return rel(-1,'请先获取验证码',$rel);
		}
		
		if(!isset($data['smsCode']) || $data['smsCode']==''){
			return rel(-1,'请输入验证码',$rel);
		}
		
		
		if(!isset($data['password']) || $data['password']==''){
			return rel(-1,'新密码不能为空',$rel);
		}
		
		if(mb_strlen($data['password']) < 6 || mb_strlen($data['password']) > 18 ){
			return rel(-1,'请填写6~8位字符新密码',$rel);
		}
		
		//加密
		$data['password'] = md5(md5($data['password']) . config('auth_key'));
		
		$mapSms['phone'] = $data['phone'];
		$mapSms['id']=do_mdecrypt($data['verifyCode']);
		//$mapSms['create_time'] = ['lt',time()];
		$mapSms['status'] = 0;
		$sms = Db::name('smsLog')->where($mapSms)->order('id desc')->find();
		if(!$sms){
			return rel(-1,'验证码错误，请重新获取',$rel);
		}
		
		if($sms['create_time'] < time()){
			return rel(-1,'验证码已过期，请重新获取',$rel);
		}
		
		if($sms['code']!=$data['smsCode']){
			return rel(-1,'验证码错误，请重新输入',$rel);
		}
		
		$user_id = $is_phone;
		if ($user_id>0) {
			//生成令牌
			$nowtime = time();
			$param['password'] = $data['password'];
			$param['token']  =do_mencrypt($user_id);
			$param['session_id'] = md5($nowtime);//重置密码后更新session_id
			$memberMD->edit(['id'=>$user_id],$param);
			return rel(1, '密码重置成功,立即登录');
		} else {
			return rel(-1, '失败');
		}
	}
	
	
	/**
     * [uppassword APP修改]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function uppassword()
    {
		$data=$this->_data;
		$user_id = $this->user_id;
		$rel = [];
		if($user_id<=0){
			return rel(-1,'用户不存在',$rel);
		}
		$memberMD = new MemberModel();
		$map['id'] = $user_id;
		$data['phone'] = $memberMD->getUserID($map,'account');	

		
		if(!isset($data['verifyCode']) || $data['verifyCode']==''){
			return rel(-1,'请先获取验证码',$rel);
		}
		
		if(!isset($data['smsCode']) || $data['smsCode']==''){
			return rel(-1,'请输入验证码',$rel);
		}
		
		
		if(!isset($data['password']) || $data['password']==''){
			return rel(-1,'新密码不能为空',$rel);
		}
		
		if(mb_strlen($data['password']) < 6 || mb_strlen($data['password']) > 18 ){
			return rel(-1,'请填写6~8位字符新密码',$rel);
		}
		
		//加密
		$data['password'] = md5(md5($data['password']) . config('auth_key'));
		
		$mapSms['phone'] = $data['phone'];
		$mapSms['id']=do_mdecrypt($data['verifyCode']);
		//$mapSms['create_time'] = ['lt',time()];
		$mapSms['status'] = 0;
		$sms = Db::name('smsLog')->where($mapSms)->order('id desc')->find();
		if(!$sms){
			return rel(-1,'验证码错误，请重新获取',$rel);
		}
		
		if($sms['create_time'] < time()){
			return rel(-1,'验证码已过期，请重新获取',$rel);
		}
		
		if($sms['code']!=$data['smsCode']){
			return rel(-1,'验证码错误，请重新输入',$rel);
		}
		
		if ($user_id>0) {
			//生成令牌
			$nowtime = time();
			$param['password'] = $data['password'];
			$param['token']  =do_mencrypt($user_id);
			$param['session_id'] = md5($nowtime);//重置密码后更新session_id
			$memberMD->edit(['id'=>$user_id],$param);
			return rel(1, '密码重置成功,请重新登录');
		} else {
			return rel(-1, '失败');
		}
	}
	
	
	/**
     * [getVipInfo APP获取VIP数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getVipInfo()
    {
		$data=$this->_data;
		$user=$this->_user;
		$rel = [];
		if($user){
			if($user['group_id'] == 2){
				$rel['viptime'] = 'VIP会员于'.date('Y-m-d',$user['vip_end_time']).'到期';
			}elseif($user['vip_end_time']!=''){
				$rel['viptime'] = '您的VIP会员已于'.date('Y-m-d',$user['vip_end_time']).'到期';
			}else{
				$rel['viptime'] = '您还是普通会员，可立即付费成为VIP享受会员特权';
			}
			$rel['nickname'] = $user['nickname'];
			$rel['vipmoney'] = Db::name('vip')->order('orderby asc')->select();
			
			$rel['viptq'] = config('viptq');
			$rel['vippayff'] = config('vippayff');
			$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
			$rel['alipay'] = $site.'/uploads/img/alipay.png';
			$rel['weixin'] = $site.'/uploads/img/weixin.png';
			return rel(1,'获取数据成功',$rel);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
    }

	/**
	* 添加代理商
	* @author byzk
	* @param number $phone
	* @param string $content
	* @return number
	*/
	private function addAgent($param,$user_id){
		$data = [
				'real_name' => '',
				'username' => $param['account'],
				'password' => $param['password'],
				'phone' => $param['account'],
				'groupid' => 4,
				'status' => 1,
				'create_time' => time(),
			];
            
			$admin_id = Db::name('admin')->insertGetId($data);
			if($admin_id<=0){
				  return ['code' => 0, 'data' => '', 'msg' => '添加失败'];
			}
			
			//添加权限组
			$accdata = array(
                'uid'=> $admin_id,
                'group_id'=> 4,
            );
            $group_access = Db::name('auth_group_access')->insert($accdata);
			
			//添加基本信息
			$data2 = [
				'bank_name' => '',
				'bank_real_name' => '',
				'bank_code' => '',
				'group_id' => 1,
				'admin_id' => $admin_id,
				'user_id' => $user_id,
				'prev_id' => $param['agent_id'],
				'create_time' => time(),
			];
			$result =  Db::name('agent')->insert($data2);
			if(false === $result){
				return ['code' => 0, 'data' => '', 'msg' => '添加失败'];
			}else{
				return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
			}
	}
	
	/*public function updateAgent()
    {
		$member = Db::name("member")->order('id asc')->select();
		foreach($member as $k=>$v){
			$agent = Db::name("agent")->where(['user_id'=>$v['id']])->find();
			if(!$agent){
				$agent_id = 0;
				if($v['invite_id']>0){
					$agent_id = Db::name("agent")->where(['user_id'=>$v['invite_id']])->value('admin_id');
				}
				$insertdata = [
					'account' => $v['account'],
					'password' => $v['password'],
					'agent_id' => $agent_id,
				];
				$this->addAgent($insertdata,$v['id']);
			}
		}
		echo 'OK';
		exit;
	}*/
	
}
