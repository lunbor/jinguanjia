<?php
namespace app\api\controller;
use app\api\model\ArticleModel;
use app\api\model\CreditCardModel;
use think\Db;
use think\Config;
use lib\Curl;
class Agent extends Apibase
{
	/*public function xiufu()
    {
		$sg = 16;
	    $agent = Db::name("agent")->alias('a')->field('a.*,m.path,m.agent_id')->join('member m', 'm.id=a.user_id', 'LEFT')->where(['admin_id'=>$sg])->order('admin_id asc')->select();
		foreach($agent as $k=>$v){
			$path = '';
			if($v['path']!=''){
				$path = $v['path'].','.$v['user_id'];
			}else{
				$path = $v['user_id'];
			}
			Db::name("member")->where(['agent_id'=>$v['agent_id'],'path'=>['like',$path.'%']])->update(['agent_id'=>$v['admin_id']]);
		}
		echo 'ok'.$sg;
	}*/
	/**
     * [getAgentData APP获取代理数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAgentData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		
		$agent = Db::name("agent")->alias('a')->field('a.*,ag.group_name')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->where(['a.user_id'=>$user_id])->find();
		$agentUserCount = Db::name("member")->where('','exp',' FIND_IN_SET ('.$user_id.',path) ')->count();
		
		if($agent){
			$rel['money'] = $agent['money'];
			$rel['z_money'] = $agent['z_money'];
			$rel['group_name'] = $agent['group_name'];
			$rel['agentUserCount'] = $agentUserCount;
			$rel['agentCount'] = Db::name("agent")->where(['prev_id'=>$agent['admin_id']])->count();			
			$rel['user_sy'] = Db::name("agent_account_log")->where(['type'=>1,'admin_id'=>$agent['admin_id']])->sum('money');
			$rel['agent_sy'] = Db::name("agent_account_log")->where(['type'=>['IN','2,5'],'admin_id'=>$agent['admin_id']])->sum('money');
		}else{
			$rel['money'] = '0.00';
			$rel['z_money'] = '0.00';
			$rel['group_name'] = '会员';
			$rel['agentUserCount'] = 0;
			$rel['agentCount'] = 0;			
			$rel['user_sy'] =  '0.00';
			$rel['agent_sy'] =  '0.00';
		
		}
		
		//绑卡须知
		$articleMD = new ArticleModel();
		$article = $articleMD->getOne(['id'	=> 4]);		
		return rel(1,'获取成功',['agent'=>$rel,'article'=>$article]);
	}
	
	/**
     * [getAgentUserData APP获取代理-用户数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAgentUserData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		
		//$map['status']= 1;

		$Nowpage = $data['page'] ? $data['page']:1;
		$limits = 10;
		
		$agentUserCount = Db::name("member")->where('','exp',' FIND_IN_SET ('.$user_id.',path) ')->count();

		$list = Db::name("member")->field('head_img,nickname,create_time')->where('','exp',' FIND_IN_SET ('.$user_id.',path) ')->page($Nowpage,$limits)->order('id desc')->select();   
		
		foreach($list as $k=>$v){
			if($v['head_img']!=''){
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$list[$k]['head_img']  = $site.'/uploads/face/'.str_replace('\\','/',$v['head_img']);
			}
			$list[$k]['create_time']=date('Y-m-d',$v['create_time']);
		}
		return rel(1,'获取成功',['agentUserCount'=>$agentUserCount,'list'=>$list]);
	}
	
	/**
     * [getAgentShareData APP获取代理-代理数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAgentShareData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		
		//$map['status']= 1;

		$Nowpage = $data['page'] ? $data['page']:1;
		$limits = 10;
		
		$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
		if(!$agent) return rel(1,'获取成功',['agentCount'=>0,'list'=>[]]);
		
		$agentCount = Db::name("agent")->where(['prev_id'=>$agent['admin_id']])->count();

		$list = Db::name("agent")->alias('a')->field('m.head_img,m.nickname,a.create_time')->join('member m', 'm.id=a.user_id', 'LEFT')->where(['a.prev_id'=>$agent['admin_id']])->page($Nowpage,$limits)->order('a.admin_id desc')->select();   
		
		foreach($list as $k=>$v){
			if($v['head_img']!=''){
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$list[$k]['head_img']  = $site.'/uploads/face/'.str_replace('\\','/',$v['head_img']);
			}
			$list[$k]['create_time']=date('Y-m-d',$v['create_time']);
		}
		return rel(1,'获取成功',['agentCount'=>$agentCount,'list'=>$list]);
	}
	
	
	/**
     * [getAgentMoneyData APP获取代理-收益明细数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAgentMoneyData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		
		//$map['status']= 1;

		$Nowpage = $data['page'] ? $data['page']:1;
		$limits = 10;
		
		$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
		if(!$agent) return rel(1,'获取成功',['list'=>[]]);
		
		$list = Db::name("agent_account_log")->where(['admin_id'=>$agent['admin_id']])->page($Nowpage,$limits)->order('log_id desc')->select();   
		foreach($list as $k=>$v){
			switch ($v['type'])
			{
			case 1:
			  $list[$k]['type'] = '用户收益';
			  break;  
			case 2:
			  $list[$k]['type'] = '代理收益';
			  break;
			case 3:
			  $list[$k]['type'] = '提现';
			  break;
			default:
			  $list[$k]['type'] = '其他收益';
			}
			$list[$k]['change_time']=date('Y-m-d H:i',$v['change_time']);
		}
		return rel(1,'获取成功',['list'=>$list]);
	}
	
	/**
     * [getAgentCashLogData APP获取代理-提现记录数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAgentCashLogData()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		
		//$map['status']= 1;

		$Nowpage = $data['page'] ? $data['page']:1;
		$limits = 10;
		
		$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
		if(!$agent) return rel(1,'获取成功',['list'=>[]]);
		
		$bank_info_list = Db::name("bank_info")->select();
		$bank_info = [];
		foreach($bank_info_list as $k=>$v){
			$bank_info[$v['id']] = $v['bank_name'];
		}
		
		$list = Db::name("agent_withdrawals")->where(['admin_id'=>$agent['admin_id']])->page($Nowpage,$limits)->order('id desc')->select();   
		foreach($list as $k=>$v){
			if($v['bank_code']!=''){
				$list[$k]['bank_code']  = ''.substr($v['bank_code'],-4);
			}
			
			if($v['bank_real_name']!=''){
				$list[$k]['bank_real_name']  = mb_substr($v['bank_real_name'],0,1).'**';
			}
			
			if($v['bank_name']!=''){
				//$list[$k]['bank_name']  = $bank_info[$v['bank_name']];
			}
			
			switch ($v['status'])
			{
			case 0:
			  $list[$k]['status'] = '审核中';
			  break;  
			case 1:
			  $list[$k]['status'] = '审核成功';
			  break;
			case 2:
			  $list[$k]['status'] = '失败-'.$v['remark'];
			  break;
			default:
			  $list[$k]['status'] = '未知状态';
			}
			$list[$k]['create_time']=date('Y-m-d H:i',$v['create_time']);
		}
		return rel(1,'获取成功',['list'=>$list]);
	}
	
	
	/**
     * [bankInfo 获取支持的银行]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function bankInfo(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		
		$rel['bankInfo'] = Db::name('bankInfo')->field('bank_name as label,lineno as value')->select();
		$rel['agent'] = Db::name("agent")->field('bank_real_name,bank_name,bank_code,pro_name,city_name,area_name,provin,city,area')->where(['user_id'=>$user_id])->find();
		if($rel['agent'] && $rel['agent']['bank_real_name']!=''){
			$rel['agent']['address'] = $rel['agent']['pro_name'].'-'.$rel['agent']['city_name'].'-'.$rel['agent']['area_name'];
			
			$rel['agent']['bank_id'] =  Db::name("bank_info")->where(['bank_name'=>$rel['agent']['bank_name']])->whereOr(['id'=>$rel['agent']['bank_name']])->value('lineno');
			$rel['agent']['bank_name'] =  Db::name("bank_info")->where(['bank_name'=>$rel['agent']['bank_name']])->whereOr(['id'=>$rel['agent']['bank_name']])->value('bank_name');
			
			$rel['agent']['openid'] = '';
			$rel['agent']['sessionKey'] = '';
		}else{
			$rel['agent']['address'] = '';
			$rel['agent']['bank_id'] = '';
			$rel['agent']['openid'] = '';
			$rel['agent']['sessionKey'] = '';
		}
		return rel(1,'获取成功',$rel);
	}
	
	
	/**
     * [addBank 添加结算卡]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function addBank(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		
		$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
		if(!$agent) return rel(0,'请先开通同意推广协议');
		if($agent['bank_real_name']!='') return rel(0,'结算卡信息不允许修改，请联系客服人员进行调整！');
		
		if($data['bank_real_name']=='') return rel(0,'开户姓名必须填写！');
		if($data['bank_name']=='') return rel(0,'请选择开户行！');
		if($data['bank_code']=='') return rel(0,'银行卡号填写！');
		if($data['area']=='') return rel(0,'请选择开户省市！');
		$data['bank_name'] = Db::name("bank_info")->where(['lineno'=>$data['bank_id']])->value('bank_name');
		
		
		$strArray = explode("-",$data['address']); 
		if(is_array($strArray) &&  count($strArray)==3){
			$data['pro_name'] = $strArray[0];
			$data['city_name'] = $strArray[1];
			$data['area_name'] = $strArray[2];
			
			$data['provin'] = str_pad($data['provin'],6,"0",STR_PAD_RIGHT);
			$data['city'] = str_pad($data['city'],6,"0",STR_PAD_RIGHT);
			$data['area'] = str_pad($data['area'],6,"0",STR_PAD_RIGHT);
		}

		
		$update=[
			'bank_real_name' => $data['bank_real_name'],
			'bank_name' => $data['bank_name'],
			'bank_code' => $data['bank_code'],
			'provin' => $data['provin'],
			'city' => $data['city'],
			'area' => $data['area'],
			'pro_name' => $data['pro_name'],
			'city_name' => $data['city_name'],
			'area_name' => $data['area_name'],
		];
		Db::name("agent")->where(['user_id'=>$user_id])->update($update);
		return rel(1,'修改成功');
		return $res;
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
			$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
			if(!$agent) return rel(0,'请先开通同意推广协议');
			
			$rel['shareUrl']  = config('siteUrl').'/#/?agentId='.$agent['admin_id'];
			$rel['shareImg']  = $site.'/uploads/img/intive_bg_1.png';
			$rel['is_code'] = 1;
			return rel(1,'获取成功',$rel);
	    }else{
			return rel(-1,'用户不存在',$rel);
		}
	}
	
	
	/**
     * [getCashData 获取提现信息]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getCashData(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		
		$rel['agent'] = Db::name("agent")->field('bank_name,bank_code,money as yes_money')->where(['user_id'=>$user_id])->find();
		if(!$rel['agent'])  return rel(0,'请先开通同意推广协议');
		if($rel['agent']['bank_code']!=''){
			$rel['agent']['bank_code']  = ''.substr($rel['agent']['bank_code'],-4);
			$rel['agent']['cashFee'] = '3元/笔';
			return rel(1,'获取成功',$rel);
		}else{
			 return rel(0,'请先绑定结算卡');
		}
		
	}
	
	
	/**
     * [addCash 申请提现]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function addCash(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
		if(!$agent)  return rel(0,'请先开通同意推广协议');
		if($agent['bank_code']=='') return rel(0,'请先绑定结算卡');
		
			$insert['money'] = $data['money'];
			$insert['admin_id'] = $agent['admin_id'];
			$insert['bank_real_name'] = $agent['bank_real_name'];
			$insert['bank_code'] = $agent['bank_code'];
			$insert['bank_name'] = $agent['bank_name'];
			$insert['fee'] = 3;
			$insert['order_no'] = order_no();
			$insert['create_time'] = time();
			if($agent['bank_code'] =='' || $agent['bank_name']=='' || $agent['bank_real_name']==''){
				return rel(0,'结算卡信息有误，请联系平台客服修改');
			}
			
			if($data['money']<100){
				return rel(0,'提现金额不能少于100元');
			}
			
			if($data['money']>$agent['money']){
				return rel(0,'提现金额大于可提现金额');
			}
			$is_withdrawals = Db::name('agent_withdrawals')->where(array('admin_id'=>$agent['admin_id'],'status'=>0))->find();
			if($is_withdrawals){
				return rel(0,'您有正在处理的提现记录 ，请稍后再申请！');
			}
			$insert_id = Db::name('agent_withdrawals')->insertGetId($insert);
			if ($insert_id > 0 ) {
				//修改表agent/agent_withdrawals
				$shrel = $this->regThCash($user_id,$agent,$insert);
				if($shrel['code'] != 1){
					Db::name('agent_withdrawals')->where('id',$insert_id)->update(['status'=>2]);
				}else{
					insertAgentLog([
						'admin_id' => $agent['admin_id'],
						'to_user_id' => 0,
						'money' => -$data['money'],
						'desc' => '提现支出',
						'type' => 3,
						'sytype' => 0,
						'order_no' => $agent['admin_id'].'tx'.$insert_id,
						'order_id' => $insert_id,
					]);
				}
				return $shrel;
			}else{
				return rel(0,'提现失败，如有疑问可联系客服');
			}
	
	}
	
	//通道注册及处理提现
	private function regThCash($user_id,$agent,$insert){
		$aisleName = 'Th';
		$obj=model('common/'.$aisleName.'apiLogic','Logic');	//渠道逻辑对象
		if($agent['bank_mer_id']==''){
			$data2 = [
				'user_id'=>$user_id,
			];
			
			$rel=$obj->work('registCash',$data2,1);			//进件。渠道对象需要定义自己文档中的接口与统一接
			
			if($rel['code']<=0){
				return rel(-1,$rel['msg']);
			}
			$agent['bank_mer_id'] = $mer_id = $rel['data'];
			Db::name('agent')->where('admin_id',$agent['admin_id'])->update(['bank_mer_id'=>$mer_id]);
		}
		
		
		//激活通道
		if($agent['bank_config_no']==''){
			$result=$obj->bankEntryCard(array('user_no'=>$agent['bank_mer_id']));
			if(isset($result['Code']) && $result['Code'] == '10000' && isset($result['Resp_code']) && $result['Resp_code'] == '40000'){
				Db::name('agent')->where('admin_id',$agent['admin_id'])->update(['bank_config_no'=>$result['config_no']]);
				$agent['bank_config_no'] = $result['config_no'];
			}else{
				if(isset($result['Resp_msg']) && $result['Resp_msg'] != ''){
					return rel(-1,$result['Resp_msg']);
				}else{
					return rel(-1,$result['Msg']);
				}
			}
		}
		
		//代付处理
		$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
		$data = [
			'user_no' => $agent['bank_mer_id'],//商户入网返回的 user_no
			'config_no' =>  $agent['bank_config_no'],//渠道激活的配置号
			'order_no' => $insert['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' =>  $site.'/api/Agent/d10notifyUrl',//渠道商接受回调路由,用于异步接受通知
			'price' => $insert['money'] - $insert['fee'],//订单金额 单位元(1 = 人民币 1元)
		];
		$rel1=$obj->insteadpay($data);
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			return rel(1,'提现申请成功,请留意银行到账提醒。');
		}else{
			if(isset($rel1['Resp_msg']) && $rel1['Resp_msg'] != ''){
				return rel(-1,$rel1['Resp_msg']);
			}else{
				return rel(-1,$rel1['Msg']);
			}
		}
		
	}
	
	
		
	public function d10notifyUrl(){
		$rel1 = input();	
		file_put_contents('log/Tdlog/Tdlog.txt',json_encode($rel1),FILE_APPEND);
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			$agent_withdrawals = Db::name('agent_withdrawals')->where(array('order_no'=>$rel1['order_no'],'status'=>0))->find();
			if($agent_withdrawals){
				Db::name('agent_withdrawals')->where('order_no',$rel1['order_no'])->update(['ypt_order_no'=>$rel1['ypt_order_no'],'status'=>1]);
			
				Db::name('agent')->where(array('admin_id'=>$agent_withdrawals['admin_id']))->setInc('w_money',$agent_withdrawals['money']);
			
			}
			echo 'success';
			exit;
		}
	}
	
	/**
     * [getAgentSyph 收益排行]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getAgentSyph(){
		$list = Db::name('agent')->alias('a')->field("m.head_img,m.nickname,IFNULL(a.z_money,0.00) as z_money,IFNULL(ag.group_name,'会员') as group_name")->join('member m', 'm.id=a.user_id', 'RIGHT')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->order('a.z_money desc')->limit(10)->select();

		foreach($list as $k=>$v){
			if($v['head_img']!=''){
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$list[$k]['head_img']  = $site.'/uploads/face/'.str_replace('\\','/',$v['head_img']);
			}
		}
		
		if(count($list)>2){
			$sh_list = $list[0];
			$list[0] = $list[1];
			$list[1] = $sh_list;
		}
		
	    return rel(1,'获取成功',$list);
	}
	
	/**
     * [getAgentTdph 团队排行]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getAgentTdph(){
		$list = Db::name('agent')->alias('a')->field("(select count(*) from think_member where FIND_IN_SET(a.user_id,path)) as usercount,m.head_img,m.nickname,IFNULL(ag.group_name,'会员') as group_name")->join('member m', 'm.id=a.user_id', 'RIGHT')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->order('usercount desc')->limit(10)->select();

		foreach($list as $k=>$v){
			if($v['head_img']!=''){
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$list[$k]['head_img']  = $site.'/uploads/face/'.str_replace('\\','/',$v['head_img']);
			}
		}
		
		if(count($list)>2){
			$sh_list = $list[0];
			$list[0] = $list[1];
			$list[1] = $sh_list;
		}
		
	    return rel(1,'获取成功',$list);
	}
	
	
	/**
     * [getAgentInfo APP获取代理数据]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function getAgentInfo()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		$rel = [];
		if($user){
			$agent = Db::name("agent")->alias('a')->field('a.*,ag.group_name')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->where(['a.user_id'=>$user_id])->find();
			if($agent){
				$rel['viptime'] = '您的等级是'.$agent['group_name'].'，可享受'.$agent['group_name'].'特权';
			}else{
				$rel['viptime'] = '您还是会员，可立即付费升级享受等级特权';
			}
			
			$rel['payWay'] = 1;
			
			$rel['nickname'] = $user['nickname'];
			$rel['vipmoney'] = Db::name('agent_group')->field('group_name as title,fee as val,id')->order('id asc')->select();
			
			$rel['agenttq'] = config('agenttq');
			$rel['vippayff'] = config('vippayff');
			$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
			$rel['alipay'] = $site.'/uploads/img/alipay.png';
			$rel['weixin'] = $site.'/uploads/img/weixin.png';
			
			
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
			
			return rel(1,'获取数据成功',$rel);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
    }
	
	
	/**
     * [payAgentFee APP支付代理商付费]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function payAgentFee()
    {
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		if(!isset($data['group_id']) ||  $data['group_id']<=0){return rel(-1,'请选择需要升级代理商等级');	}
		
			
		$agent_group=Db::name('agent_group')->where('id',$data['group_id'])->find();	//信用卡预绑定数据
		if(!$agent_group) {return rel(-1,'请选择需要升级代理商等级');	}
		
		if($agent_group['fee'] <= 0){
			return rel(-1,'请选择需要升级代理商等级');
		}
		
		$user_agent = Db::name('agent')->where('user_id',$user_id)->find();
		if($user_agent &&  $user_agent['group_id'] >=  $data['group_id']){
			return rel(-1,'您已经是代理商，请勿重复升级');
		}
		
		
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getCreditbox($map);
		if(!$creditCard)  return rel(-1,'信用卡信息有误');
		
		//通道标识
		$keyname = 'Td';
		//查找还款通道
		$card_regist = Db::name('credit_regist')->where(['user_id'=>$user_id,'keyname'=>$keyname,'credit_code'=>$creditCard['credit_code'],'status'=>1])->find();	
		if(!$card_regist){
			//激活通道
			$reqData =[
				'user_id' =>$user_id,
				'credit_id' =>$creditCard['credit_id'],
			];
			$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
			$rel = Curl::http_curl($site."/api/Drepay/authent",$reqData);
			$rel = json_decode($rel,true);
			if(isset($rel['code']) && $rel['code'] == 1){
				$card_regist = Db::name('credit_regist')->where(['user_id'=>$user_id,'keyname'=>$keyname,'credit_id'=>$creditCard['credit_id'],'credit_code'=>$creditCard['credit_code'],'status'=>1])->find();
			
			}else{
				if(isset($rel['msg']) && $rel['msg'] != ''){
					return json(['code'=>-1,'msg'=>$rel['msg']]);
				}else{
					return json(['code'=>-1,'msg'=>'未找到对应支付通道！']);
				}
			}
		
		}else{
			if($card_regist['credit_id'] != $creditCard['credit_id']){
				//修改信息
				$credit	=Db::name('creditCard')->where(array('credit_id'=>$creditCard['credit_id'],'status'=>1))->find();
				if(!$credit){
					return json(['code'=>-1,'msg'=>'信用卡信息有误！']);
				}
				//list($m,$y)=explode('-',$credit['exp_date']);
				//$y = substr($y,-2);
				//激活通道
				$reqData =[
					'user_no' => $card_regist['mer_id'],//新建商户返回的子商户号user_no
					'bank_branch' => $credit['bank_name'],//储蓄卡:银行卡开户支行名称,信用卡:银行卡开户行名称
					'bank_code' => $credit['bank_id'],//联行号
					'validity' =>  $credit['exp_date'],//有效期、信用卡必传(mmyy格式)
					'cvv2' =>  $credit['safe_code'],//安全码、信用卡必传
				];
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$rel = Curl::http_curl($site."/api/Drepay/modifyinfo",$reqData);
				$rel = json_decode($rel,true);
				if(isset($rel['code']) && $rel['code'] == 1){
					Db::name('credit_regist')->where('regist_id',$card_regist['regist_id'])->update(array('credit_id'=>$creditCard['credit_id']));
					$card_regist = Db::name('credit_regist')->where(['user_id'=>$user_id,'keyname'=>$keyname,'credit_id'=>$creditCard['credit_id'],'credit_code'=>$creditCard['credit_code'],'status'=>1])->find();
				
				}else{
					if(isset($rel['msg']) && $rel['msg'] != ''){
						return json(['code'=>-1,'msg'=>$rel['msg']]);
					}else{
						return json(['code'=>-1,'msg'=>'未找到对应还款通道！']);
					}
				}
			}
		
		}
		
		
		//垫资还款
		if($card_regist['qdrc_id'] !='' && $card_regist['qdrc_t_id'] !=''){
			
			$agentLogic =model('common/AgentLogic','Logic');
			$is_pay = $agentLogic->exePayMoney($card_regist['regist_id'],$data['group_id']);
			if(!$is_pay){
				return rel(-1,'支付失败，无法完成扣款！');
			}
			$rel['url'] = '';
			//Db::name('repay_temp')->where('temp_id',$data['temp_id'])->update(['status'=>2]);
			return rel(1,'银行正在进行扣款，扣款成功后会自动升级为代理商！',$rel);
			
		}else{
			$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
			$rel['url'] = $site.'/api/agent/ktsfdaifu/registId/'.$card_regist['regist_id'].'/group_id/'.$data['group_id'];
		}
		
		return rel(1,'需要激活通道',$rel);
		
		
	}
	
	
	/**
     * [ktsfpay 开通快捷支付]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function ktsfdaifu(){
		Config::set('default_return_type', 'html');
		$registId 		= input("registId");			//提现金额
		$group_id 		= input("group_id");			//提现金额
		$creditRegist=Db::name('creditRegist')->where('regist_id',$registId)->find();	//信用卡预绑定数据
		$agent_group=Db::name('agent_group')->where('id',$group_id)->find();	//信用卡预绑定数据
		
		if(!$creditRegist || !$agent_group){
			$data = [
				'code' => -1,
				'msg' => '数据有误',
			];
			return $this->fetch('tshimsg', $data);
		}
		if($creditRegist['qdrc_id'] !='' && $creditRegist['qdrc_t_id'] !=''){
			
			$agentLogic =model('common/AgentLogic','Logic');
			$is_pay = $agentLogic->exePayMoney($registId,$group_id);
			
			if($is_pay){
				$data = [
					'code' => -1,
					'msg' => '支付失败，无法完成扣款！',
				];
			}else{
				$data = [
					'code' => 1,
					'msg' => '银行正在进行扣款，此界面可退出返回，扣款成功后会自动升级为代理商！',
				];
			}
			
			return $this->fetch('tshimsg', $data);
		}
		
		$c2Rel = $this->c2EntryCard(array('regist_id'=>$registId,'group_id'=>$group_id));
		if($c2Rel['code']==0){
			$data = [
				'code' => -1,
				'msg' => $c2Rel['msg'],
			];
			return $this->fetch('tshimsg', $data);
		}elseif($c2Rel['code']==2){
			echo base64_decode($c2Rel['result']);
			exit;		
		}elseif($c2Rel['code']==1){
			
			$agentLogic =model('common/AgentLogic','Logic');
			$is_pay = $agentLogic->exePayMoney($registId,$group_id);
			
			if(!$is_pay){
				$data = [
					'code' => -1,
					'msg' => '支付失败，无法完成扣款！',
				];
			}else{
				$data = [
					'code' => 1,
					'msg' => '银行正在进行扣款，此界面可退出返回，扣款成功后会自动升级为代理商！',
				];
			}
			

			return $this->fetch('tshimsg', $data);	
		}
	    
	}
	
	
	
		//C2激活通道
	protected function c2EntryCard($data){	 
		$credit_regist	=Db::name('credit_regist')->where(array('regist_id'=>$data['regist_id']))->find();
		if($credit_regist['qdrc_id']!='' && $credit_regist['qdrc_t_id']!=''){
			return rel(1,'激活成功');	
		}
		
		//农行走自由通道
		$credit_card	=Db::name('credit_card')->where(array('credit_id'=>$credit_regist['credit_id']))->find();
		if(!$credit_card){
			return rel(0,'信用卡信息有误');	
		}
		$channel_type = 1;
		if($credit_card['bank_id'] == '103100000026'){
			$channel_type = 2;
		}
		
		$memberfee = getFee($credit_regist['user_id'],3);
		$rate =  $memberfee['rate'];
		$single_payment = $memberfee['dfee'];
		
		$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
		$data2 = [
			'user_no'=> $credit_regist['mer_id'],
			'rate'=> $rate,
			'single_payment'=> $single_payment,
			'page_url'=> $site.'/api/Repay/c2pageUrl/regist_id/'.$data['regist_id'].'/group_id/'.$data['group_id'].'',
			'notify_url'=> $site.'/api/Agent/c2notifyUrl/regist_id/'.$data['regist_id'].'/group_id/'.$data['group_id'].'',
			'channel_type' => $channel_type,
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->c2EntryCard($data2);
		//dump($rel1);
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			if(isset($rel1['confirm_status'])){
				if($rel1['confirm_status']==0){
					return rel(2,'需要激活',$rel1['html']);	
				}elseif($rel1['confirm_status']==1){
					return rel(0,'通道激活失败');	
				}elseif($rel1['confirm_status']==2){
					if(isset($rel1['config_no']) && $rel1['config_no']!=''){
						if($credit_regist['qdrc_id']!='' && $credit_regist['qdrc_t_id']==''){
							Db::name('credit_regist')->where(array('regist_id'=>$data['regist_id']))->update(array('qdrc_id'=>$rel1['config_no'],'qdrc_t_id'=>$credit_regist['qdrc_id']));
						}else{
							Db::name('credit_regist')->where(array('regist_id'=>$data['regist_id']))->update(array('qdrc_id'=>$rel1['config_no'],'qdrc_t_id'=>$rel1['config_no']));
						}
					}
					return rel(1,'激活成功');	
				}
			}else{
				if(isset($rel1['Resp_msg'])){
					return rel(0,$rel1['Resp_msg']);	
				}else{
					return rel(0,'通道开通失败');	
				}
			}
		}else{
			if(isset($rel1['Resp_msg'])){
				return rel(0,$rel1['Resp_msg']);	
			}else{
				return rel(0,'通道开通失败');	
			}
		}
	}
	
	
	
	public function c2notifyUrl(){
		$rel1 = input();	
		file_put_contents('log/Tdlog/Tdlog.txt',json_encode($rel1),FILE_APPEND);
		if(isset($rel1['Code']) && $rel1['Code'] == '10000'){
			if(isset($rel1['confirm_status'])){
				if($rel1['confirm_status']==2){
					if(isset($rel1['config_no']) && $rel1['config_no']!=''){
						$credit_regist = Db::name('credit_regist')->where(array('regist_id'=>$rel1['regist_id']))->find();
						if($credit_regist['qdrc_id']!='' && $credit_regist['qdrc_t_id']==''){
							Db::name('credit_regist')->where(array('regist_id'=>$rel1['regist_id']))->update(array('qdrc_id'=>$rel1['config_no'],'qdrc_t_id'=>$credit_regist['qdrc_id']));
						}else{
							Db::name('credit_regist')->where(array('regist_id'=>$rel1['regist_id']))->update(array('qdrc_id'=>$rel1['config_no'],'qdrc_t_id'=>$rel1['config_no']));
						}
						$agentLogic =model('common/AgentLogic','Logic');
						$agentLogic->exePayMoney($rel1['regist_id'],$rel1['group_id']);
						echo 'success';
						exit;
					}
				}
			}
		}
		
	}
	
	
	public function c3notifyUrl(){
	    $rel1 = input();	
		file_put_contents('log/Tdlog/Tdlog.txt',json_encode($rel1),FILE_APPEND);
		if(isset($rel1['order_no']) && $rel1['order_no'] != ''){	
			$ghostArr=Db::name('agent_buy')->where(['order_no'=>$rel1['order_no']])->find();
			if($ghostArr && ($ghostArr['status']==0 || $ghostArr['status']==2)){
				if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
					
					//其他版本 需要添加agent
					$user = Db::name('member')->where('id',$ghostArr['user_id'])->find();
					$user_agent = Db::name('agent')->where('user_id',$ghostArr['user_id'])->find();
					if(!$user_agent){
						//添加代理
						//默认添加为代理商
						$this->addAgent($user,$ghostArr['user_id']);
						$user_agent = Db::name('agent')->where('user_id',$ghostArr['user_id'])->find();
					}
					
					Db::name('agent_buy')->where(['id'=>$ghostArr['id']])->update(['resp_msg'=>$rel1['Msg'],'resp'=>json_encode($rel1),'status'=>1]);
					
					Db::name('agent')->where('user_id',$ghostArr['user_id'])->update(array('group_id'=>$ghostArr['group_id']));
					
					
					//上级
					$prev_id = $user_agent['prev_id'];
					//分润
					if($prev_id > 0){
						shareProfit($user_agent['admin_id'],$prev_id,$ghostArr['group_id']);
					}
				}
				return ['Code'=>10000];
			}else{
				return ['Code'=>10000];
			}
		}else{
		
			return ['Code'=>10000];
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

}
