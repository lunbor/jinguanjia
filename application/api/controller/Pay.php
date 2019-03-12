<?php
namespace app\api\controller;
use app\api\model\BankCardModel;
use app\api\model\CreditCardModel;
use app\api\model\MemberModel;
use think\Db;
use think\Config;
use lib\Curl;
class Pay extends Apibase
{

	/**
     * [getPosData APP获取收款数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getPosData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		$map=[
			'user_id'	=> $user_id,		
		];
		
		$bankCardMD = new BankCardModel();
		$bankCard = $bankCardMD->getAll($map);
		$list = [];
		$is_mrk = 0;//是否有默认卡
		
		$rel['bankcard_id'] = '';
		$rel['bank_name'] = '';
		foreach ($bankCard as $k => $v){
				$list[$k]['name'] = $v['bankcard_id'].'';
				$list[$k]['value'] = $v['bank_name'] . ' | 尾号' .$v['bank_code'];
				if($v['type'] == 1){
					$list[$k]['checked'] = 'true';
					$is_mrk = 1;
					$rel['bankcard_id'] = $v['bankcard_id'].'';
					$rel['bank_name'] = $v['bank_name'] . ' | 尾号' .$v['bank_code'];
				}
		}
		
		if($is_mrk == 0 && count($list) > 0){
			$rel['bankcard_id'] = $list[0]['name'];
			$rel['bank_name'] = $list[0]['value'];
			$list[0]['checked'] = 'true';
		}
		
		$rel['bankCard'] = $list;
		$rel['is_validate'] = $user['is_validate'];
		
		$rel['block_id'] = '';
		$rel['aisle_name'] = '';
			
		$aisle=Db::name('payAisle')->where(['type'=>['IN','1,3'],'status'=>1])->select();		//查询使用的取现渠道
		$qxlist = [];
		foreach ($aisle as $k => $v){
				$qxlist[$k]['name'] = $v['block_id'].'';
				$qxlist[$k]['value'] = $v['name'];
		}
		
		//区分部分高费率银行
		$qxlist[] = ["name"=>'90',"value"=>'快捷支付-自选商户'];
		
		if(count($qxlist) > 0){
			$qxlist[0]['checked'] = 'true';
			$rel['block_id'] = $qxlist[0]['name'];
			$rel['aisle_name'] = $qxlist[0]['value'];
		}
		$rel['qxlist'] = $qxlist;
		
		return rel(1,'获取数据成功',$rel);
	
	}
	
	/**
     * [getPosStepData APP获取收款数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getPosStepData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		$map=[
			'user_id'	=> $user_id,		
		];
		
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getAll($map);
		$list = [];
		foreach ($creditCard as $k => $v){
				$list[$k]['name'] = $v['credit_id'].'';
				$list[$k]['value'] = $v['bank_name'] . ' ' .$v['credit_code'];
		}
		
		$rel['creditCard'] = $list;
		$rel['is_area'] = 0;
		return rel(1,'获取数据成功',$rel);
	
	}
	
	/**
     * [getPosFee APP获取收款手续费]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getPosFee()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['block_id']) ||  $data['block_id']<=0){return rel(-1,'支付通道有误');	}
		if(!isset($data['money']) ||  $data['money']<=0){return rel(-1,'收款金额有误');}
		$rel = [];
		$map=[
			'user_id'	=> $user_id,		
		];
		
		//区分部分高费率银行
		//if($data['block_id'] == '90'){
			//$fee = round($data['money'] * config('ge_pay_rate') / 100,2) + 1;
			//$vip_fee = round($data['money'] * config('ge_vip_pay_rate') / 100,2) + 1;
			//$rel['payFee'] = '费率: '.config('ge_pay_rate').'%, 手续费: '.config('pay_dfee').'元, 总手续费: '.$fee.'元';
			//$rel['vipPayFee'] = 'VIP费率: '.config('ge_vip_pay_rate').'%, 手续费: '.config('pay_dfee').'元, 总手续费: '.$vip_fee.'元';
		//}else{
			
			$memberfee = getFee($user_id,1);
			$fee = round($data['money'] * $memberfee['rate'] / 100,2) + $memberfee['dfee'];
			$rel['payFee'] = '费率: '.$memberfee['rate'].'%, 手续费: '.$memberfee['dfee'].'元, 总手续费: '.$fee.'元';
			$rel['vipPayFee'] = '';
		//}
		return rel(1,'获取数据成功',$rel);
	
	}
	
	
	/**
     * [getPayMoney APP收款]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function goPayMoney()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'请选择支付信用卡');}
		if(!isset($data['money']) ||  $data['money']<=0){return rel(-1,'收款金额有误');}
		if(!isset($data['bankcard_id']) ||  $data['bankcard_id']<=0){return rel(-1,'收款账户有误');	}
		if(!isset($data['block_id']) ||  $data['block_id']<=0){return rel(-1,'支付通道有误');	}

		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getCreditbox($map);
		if(!$creditCard){
			return rel(-1,'信用卡不能正常使用');
		}
		
		
		if($data['block_id'] == '9' && in_array($creditCard['bank_id'],config('repay_bank'))){
			return rel(-1,'此通道暂不支持'.$creditCard['bank_name'].',请使用快捷支付2');	
		}
		
		//区分部分高费率银行
		$is_ge = 0;
		if($data['block_id'] == '90'){
			$data['block_id'] = '9';
			$is_ge = 1;
			//$is_ge = 0;
		}
		
		$map2['user_id'] = $user_id;
		$map2['bankcard_id'] = $data['bankcard_id'];
		$bankCardMD = new BankCardModel();
		$bankCard = $bankCardMD->getBankbox($map2);
		if(!$bankCard){
			return rel(-1,'储蓄卡不能正常使用');
		}
		
		$aisle = Db::name('payAisle')->where(['type'=>['IN','1,3'],'block_id'=>$data['block_id']])->find();
		if(!$aisle){
			return rel(-1,'支付通道已关闭');
		}
		
		if($data['money']<=10){
			return rel(-1,'支付金额不能小于10元');
		}
		
		if(round($data['money'],2) != $data['money']){
			return rel(-1,'支付金额只能是两位小数');
		}
		
		//设置默认卡
		Db::name('bank_card')->where(['user_id' => $user_id])->update(['type'=>0]);
		Db::name('bank_card')->where(['bankcard_id'=>$data['bankcard_id'],'status'=>1,'user_id' => $user_id])->update(['type'=>1]);
		
		//查询进件信息
		$map3=[
			'bank_code'=>$bankCard['bank_code'],		//结算卡卡号
			'block_id'=>$data['block_id'],
			'status'=>1
		];	
		$bankRegist=Db::name('bankRegist')->field('block_id,regist_id,status,rate,mer_id,bank_code,dfee')->where($map3)->find();
		
		$aisleName = $aisle['keyname'];
		
		$obj=model('common/'.$aisleName.'apiLogic','Logic');	//渠道逻辑对象
		
		//判断用户是否VIP
		$memberMD = new MemberModel();
		$group_id = $memberMD->getUserID(['id'=>$user_id],'group_id');	
		
		if($is_ge == 0){
			$memberfee = getFee($user_id,1);
			
			$rateReal=$memberfee['rate']*10;
			$dfee=$memberfee['dfee']*100;			//当前单笔费用
		}elseif($is_ge == 1){
			$memberfee = getFee($user_id,1);
			
			$rateReal=$memberfee['rate']*10;
			$dfee=$memberfee['dfee']*100;			//当前单笔费用
			/*if($group_id == 2){
				$rateReal=config('ge_vip_pay_rate')*10;
				$dfee=config('pay_dfee')*100;			//当前单笔费用
			}else{
				$rateReal=config('ge_pay_rate')*10;
				$dfee=config('pay_dfee')*100;			//当前单笔费用
			}*/
		}
				
		
			//进件、修改基础数据
			$data2=[
				'bankCode'		=> $bankCard['bank_code'],
				'name'			=> $bankCard['name'],
				'idcard'		=> $bankCard['idcard'],
				'phone'			=> $bankCard['phone'],
				'bankName'		=> $bankCard['bank_name'],
				'bank_id'		=> $bankCard['bank_id'],
				'bank_no'		=> $bankCard['bank_no'],
				'provinceCode'  => $bankCard['provin'],
				'pro'			=> $bankCard['pro_name'],
				'cityCode'		=> $bankCard['city'],
				'city'			=> $bankCard['city_name'],
				'address'		=> $bankCard['address'],
				'dfee'			=> $dfee,								//单笔费用。单位分
				'rate'			=> $rateReal,								//费率。 Ye取百分之，Xj取千分之
				'pointsType'	=> $aisle['points'],				//0带积分 2不带积分
				'user_id'		=> $user_id,
				'appId'			=> $aisle['agent_id'],//10058816 9000016553
			];
		
		
		if(!$bankRegist || !$bankRegist['mer_id'] ){		//该结算卡在该支付渠道下没有进件
			//先创建进件数据
			$insert=[
				'block_id'		=> $data['block_id'],
				'bankcard_id'	=> $bankCard['bankcard_id'],
				'bank_code'		=> $bankCard['bank_code'],
				'user_id'		=> $user_id,
				'status'		=> 0,						//默认该进件状态为不可用
				'rate'			=> $rateReal,
				'dfee'			=> $dfee,
				'mer_id'		=> 0,
				'ctime'			=> time()
			];
			$registId=Db::name('bankRegist')->insertGetId($insert);
			$data['regist_id']=$registId;
			$rel=$obj->work('regist',$data2,1);			//进件。渠道对象需要定义自己文档中的接口与统一接口名
			
			if($rel['code']<0){
					return rel(-1,$rel['msg']);
			}
			$mer_id=$rel['data'];
			Db::name('bankRegist')->where('regist_id',$registId)->update(['mer_id'=>$mer_id,'status'=>1,'utime'=>time()]);
		}elseif($bankRegist['rate']!=$rateReal || $bankRegist['dfee']!=$dfee || $bankRegist['bank_code']!=$bankCard['bank_code']){
				//核对当前费率、单笔待付费
				$data['mchId']=$mer_id=$bankRegist['mer_id'];
				$rel=$obj->work('mod',$data,1);
				if($rel['code']<0){
					return rel(-1,$rel['msg']);
				}
				Db::name('bankRegist')->where('regist_id',$bankRegist['regist_id'])->update(['utime'=>time(),'bank_code'=>$bankCard['bank_code'],'rate'=>$rateReal,'dfee'=>$dfee]);
				$registId=$bankRegist['regist_id'];
		}else{
				$mer_id=$bankRegist['mer_id'];
				$registId=$bankRegist['regist_id'];
		}
		
		
		$bankRegist	=Db::name('bankRegist')->where('regist_id',$registId)->find();		//重新获取进件信息
		//通道进件完毕，现在创建支付订单
		$aisleName=$aisle['keyname'];
		$money	=$data['money'];
		$fee	=$money*$rateReal/1000;
		$order_no=order_no();
		$order1=[
			'user_id'	=> $user_id,
			'block_id'	=> $data['block_id'],			//渠道主键
			'keyname'	=> $aisle['keyname'],			//渠道标识符
			'regist_id' => $registId,		//进件主键
			'agent_id'	=> $aisle['agent_id'],
			'mer_id'	=> $mer_id,
			'credit_id'	=> $creditCard['credit_id'],
			'credit_code'=>$creditCard['credit_code'],		//信用卡号
			'money'		=> $money,
			'rate'		=> $rateReal,
			'fee'		=> $fee,						//手续费，单位元
			'mercfee'	=> $dfee/100,			//单笔代付，单位元
			'bankcard_id' => $bankCard['bankcard_id'],		//结算卡id
			'bank_card' => $bankCard['bank_code'],		//结算卡号
			'order_no'	=> $order_no,
			'status'	=> 0,
			'qd_id' => $user['qd_id'],
			'admin_id' => $user['agent_id'],
			'ctime'		=> time()
		];
		
		if(isset($data['zx_area']) && $data['zx_area']==1){
			$order1['province'] = $data['province'];
			$order1['city'] = $data['city'];
		}
		$getPayId = Db::name('getMoney')->insertGetId($order1); 				//生成预定义提现订单编号
		
		$_baseUrl = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME').'/api';
		$order2=[
			'is_ge'         => $is_ge,
			'bankCode'		=> $creditCard['credit_code'],
			'user_id'	=> $user_id,
			'name'			=> $creditCard['name'],
			'idcard'		=> $creditCard['idcard'],
			'rate'		=> $rateReal,
			'phone'			=> $creditCard['phone'],
			'totalFee'		=> $money*100,					//单位分
			'agentOrderNo'	=> $order_no,
			'mchId'			=> $mer_id,
			'merkey'			=> $bankRegist['merkey'],
			'appId'			=> $aisle['agent_id'],//10058816 9000016553
			'dsc'			=> '普通消费',
			'fee0'       => $bankRegist['rate'],												//手续费，单位分
			'd0fee'   => round($bankRegist['dfee'],0),											//单笔代付费用，单位分
			'notifyUrl'	    => $_baseUrl.'/'.$aisleName.'notify/index',			//由相应的渠道通知控制器处理回调
			'returnUrl'		=> $_baseUrl.'/repay/payReturnHtml?code=1&msg=发起请求成功，请在账单中关注结果'
			
		];
		$rel=$obj->work('pay',$order2,1);
		if($rel['code']==1){
			//插入本次提现记录表
			insertMoneyLog(['get_id'=>$getPayId,'msg'=>'申请成功','status'=>1]);
			$res['url'] = $rel['result']['pl_url'];
			return rel(1,'操作成功',$res);
		}else{
			insertMoneyLog(['get_id'=>$getPayId,'msg'=>'申请失败','status'=>0]);
			Db::name('getMoney')->where('order_no',$order_no)->update(['utime'=>time(),'dsc'=>$rel['msg'],'status'=>1]);
			return rel(-1,$rel['msg']);
		}
	}
	
	
	public function getProvince(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['block_id'])) return rel(-1,'参数错误');

		$data2 = [
			'channel_no'=> 'S16DFCC',
			'business_no'=> 'back_channel',
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->getProvince($data2);
		
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			if(!isset($rel1['data'])) return rel(0,'渠道区域获取失败');
			$res = [];
			foreach($rel1['data'] as $k=>$v){
			  $res[$k]['label'] = $v['merchant_province'].'('. $v['count'].')';
			  $res[$k]['value'] = $v['province_code'];
			}
			 return rel(1,'获取成功',$res);
		}else{
			return rel(0,'渠道区域获取失败');
		}
	}
	
	public function getCity(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['block_id'])) return rel(-1,'参数错误');
		if(!isset($data['province'])) return rel(-1,'参数错误');

		$data2 = [
			'channel_no'=> 'S20BACB',
			'business_no'=> 'loan_channel',
		];

		$data2['province_code'] = $data['province'];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->getCity($data2);
		
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			if(!isset($rel1['data'])) return rel(0,'渠道区域获取失败');
			$res = [];
			foreach($rel1['data'] as $k=>$v){
			  $res[$k]['label'] = $v['merchant_city'].'('. $v['count'].')';
			  $res[$k]['value'] = $v['city_code'];
			}
		
			 return rel(1,'获取成功',$res);
		}else{
			return rel(0,'渠道区域获取失败');
		}
	}
}
