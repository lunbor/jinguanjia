<?php
/**
 * 还款逻辑类
 */
namespace app\common\Logic;
use think\Model;
use think\Db;
class AgentLogic extends Model{
	/**
	 * 执行任务$user_id,$creditInfo,$temp
	 */
	public function exePayMoney($regist_id,$group_id){
		$agent_group = Db::name('agent_group')->where('id',$group_id)->find();
		if(!$agent_group) return false;
		$creditInfo = Db::name('credit_regist')->where('regist_id',$regist_id)->find();
		if(!$creditInfo) return false;
		$user_id = $creditInfo['user_id'];
		
		$user = Db::name('member')->where('id',$user_id)->find();
		if(!$user) return false;
		
		$user_agent = Db::name('agent')->where('user_id',$user_id)->find();
		if($user_agent &&  $user_agent['group_id'] >=  $group_id){
			return false;
		}
		
		//其他版本
		//if(!$user_agent) return false;
		
		$ghost['money'] = $agent_group['fee'];
		$ghost['order_no'] = order_no();		
		$insert=[
			'order_no'		=> $ghost['order_no'],
			'user_id'		=> $user_id,
			'regist_id'	=> $regist_id,
			'money'		=> $ghost['money'],
			'group_id'		=> $group_id,
			'status'		=> 0,
			'create_time'			=> time()
		];
		$agent_buy_id=Db::name('agent_buy')->insertGetId($insert);
		$rel1=$this->c2pay($creditInfo,$ghost);
		
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			Db::name('agent_buy')->where(['id'=>$agent_buy_id])->update(['resp_msg'=>$rel1['Msg'],'resp'=>json_encode($rel1)]);
			return true;
		}else{
			Db::name('agent_buy')->where(['id'=>$agent_buy_id])->update(['resp_msg'=>$rel1['Msg'],'resp'=>json_encode($rel1),'status'=>2]);
			return false;
		}
		
	}
	
	private function c2pay($credit_regist,$ghostArr){	

		$drepay_lxr	=Db::name('bank_card')->where(array('status'=>1,'user_id'=>$credit_regist['user_id']))->find();
		if($drepay_lxr && $drepay_lxr['city']!=''){
			if($drepay_lxr['city'] == '520600'){
				$areaCode = '0000';
			}else{
				$areaCode = substr($drepay_lxr['city'],0,4);
			}
		}else{
			$areaCode = '0000';
		}
		
		$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
		$data = [
			'config_no' =>  $credit_regist['qdrc_id'],//渠道激活的配置号
			'price' => $ghostArr['money'],//订单金额 单位元(1 = 人民币 1元)
			'order_no' => $ghostArr['order_no'],//订单号商户自定义不重复订单号长度不小于16位
			'notifyUrl' =>  $site.'/api/Agent/c3notifyUrl',//渠道商接受回调路由,用于异步接受通知
			'areaCode' => $areaCode, //地域码 前四位 文档6.4.2获取 city_code 前四位 当值为 0000 时,为全国可用商户
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->c2pay($data);
		return $rel1;
	}
}
