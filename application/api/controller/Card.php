<?php
namespace app\api\controller;
use app\api\model\BankCardModel;
use app\api\model\CreditCardModel;
use think\Db;
use think\Config;
class Card extends Apibase
{
	/**
     * [getPrion 获取省代码]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getPrion(){
		$data=$this->_data;
		$rel=Db::name('area')->field('areaname as name,areacode as value,if(parentcode=1,"0",parentcode) as parent')->select();
		return rel(1,'获取省代码成功',$rel);
	}
	
	/**
     * [bankInfo 获取支持的银行]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function bankInfo(){
		$rel=Db::name('bankInfo')->field('bank_name as label,lineno as value')->select();
		return rel(1,'获取成功',$rel);
	}
	
		
	/**
     * [addBank 添加储蓄卡]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function addBank(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(empty($user['card']) || $user['is_validate']!=1) return rel(-1,'请先实名认证，提交身份证');
		$data['user_id'] = $user_id;
		if(!check_mobile($data['phone'])){
			return rel(-1,'预留手机号码格式有误');
		}
		$member = Db::name('member')->where(['id'=>$user_id])->find();
		
		$data['name'] = $member['realname'];
		$data['idcard'] = $member['card'];
		
		$bank_info = Db::name('bank_info')->where(['lineno'=>$data['bank_id']])->find();
		$data['bank_no'] = $bank_info['acronym'];
		
		unset($data['openid']);
		unset($data['sessionKey']);
		$bankCardMD = new BankCardModel();
		$res = $bankCardMD->addBank($data);
		
		 return $res;
	}
	
	/**
     * [addCredit 添加信用卡]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function addCredit(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(empty($user['card']) || $user['is_validate']!=1) return rel(-1,'请先实名认证，提交身份证');
		$data['user_id'] = $user_id;
		if(!check_mobile($data['phone'])){
			return rel(-1,'预留手机号码格式有误');
		}
		$member = Db::name('member')->where(['id'=>$user_id])->find();
		
		$data['name'] = $member['realname'];
		$data['idcard'] = $member['card'];
		
		$bank_info = Db::name('bank_info')->where(['lineno'=>$data['bank_id']])->find();
		$data['bank_no'] = $bank_info['acronym'];
		
		unset($data['openid']);
		unset($data['sessionKey']);
		$creditCardMD = new CreditCardModel();
		$res = $creditCardMD->addCredit($data);
		
		 return $res;
	}
	
	
	/**
     * [delCredit 删除信用卡]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function delCredit(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		
		$isrepay = Db::name('repayProgram')->where(['status'=>['IN','1,2'],'credit_id'=>$data['credit_id']])->find();
		if($isrepay){return rel(-1,'该信用卡有正在执行的还款计划，请先取消计划');}
		
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$res = $creditCardMD->delCredit($map);
		 return $res;
	}
	
	
	/**
     * [delBank 删除储蓄卡]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function delBank(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['bankcard_id']) ||  $data['bankcard_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['bankcard_id'] = $data['bankcard_id'];
		$bankCardMD = new BankCardModel();
		$res = $bankCardMD->delBank($map);
		 return $res;
	}
	
	
	/**
     * [getCreditCardInfo 获取信用卡信息]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getCreditCardInfo(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$res = $creditCardMD->getCredit($map);
		if($res){
			$memberfee = getFee($user_id,2);
			$res['rate'] = $memberfee['rate'];
			$res['dfee'] = $memberfee['dfee'];
			
			$res['jyk_rate'] = $memberfee['rate'];
			$res['jyk_dfee'] = $memberfee['dfee'];
			
			$memberfee = getFee($user_id,3);
			$res['dz_rate'] = $memberfee['rate'];
			$res['dz_dfee'] = $memberfee['dfee'];
			/*$res['rate'] = config('rate');
			$res['vip_rate'] = config('vip_rate');
			$res['wm_rate'] = config('wm_rate');
			$res['wm_vip_rate'] = config('wm_vip_rate');
			$res['jyk_rate'] = config('jyk_rate');
			$res['jyk_vip_rate'] = config('jyk_vip_rate');*/
			return rel(1,'获取成功',$res);
		}else{
			return rel(-1,'获取失败',$res);
		}
	}
	
	
	/**
     * [editCredit 修改信用卡资料]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function editCredit(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$res = $creditCardMD->getCredit($map);
		if($res){
			if(!isset($data['line_credit']) || $data['line_credit']==''){
				return ['code'=>-1,'msg'=>'请输入信用卡额度','result'=>''];
			}
			$update = [
				'line_credit' => $data['line_credit'],
				'bill_time' => $data['bill_time'],
				'repay_time' => $data['repay_time'],
			];
			return $creditCardMD->editCredit($map,$update);
		}else{
			return rel(-1,'资料修改失败');
		}
	}
	
	
	/**
     * [getBankCardInfo 获取储蓄卡信息]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getBankCardInfo(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['bankcard_id']) ||  $data['bankcard_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['bankcard_id'] = $data['bankcard_id'];
		$bankCardMD = new BankCardModel();
		$res = $bankCardMD->getBank($map);
		if($res){
			return rel(1,'获取成功',$res);
		}else{
			return rel(-1,'获取失败',$res);
		}
	}
	
	
	
}
