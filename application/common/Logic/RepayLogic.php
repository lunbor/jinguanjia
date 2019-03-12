<?php
/**
 * 还款逻辑类
 */
namespace app\common\Logic;
use think\Model;
use think\Db;
class RepayLogic extends Model{
	/**
	 * 执行任务$user_id,$creditInfo,$temp
	 */
	public function exeProgram($regist_id,$temp_id,$is_dz=0){
		$temp = Db::name('repayTemp')->where('temp_id',$temp_id)->find();
		if(!$temp) return;
		if($temp['status'] == 2) return;
		$creditInfo = Db::name('credit_regist')->where('regist_id',$regist_id)->find();
		if(!$creditInfo) return;
		$user_id = $creditInfo['user_id'];
		
		$user = Db::name('member')->where('id',$user_id)->find();
		if(!$user) return;
		
		$tempBody=json_decode($temp['temp_body'],true);
		
		if($tempBody['current'] == 1){
			$is_dz = 1;//垫资
		}
		$time=time();
		$insert=[
			'user_id'    => $user_id,
			'order_no'   => order_no(),
			'temp_id'	 => $temp['temp_id'],
			'keyname'	 => $temp['keyname'],
			'regist_id'	 => $temp['regist_id'],
			'credit_code'=> $creditInfo['credit_code'],
			'credit_id'  => $creditInfo['credit_id'],
			'total_money'=> $tempBody['total'],
			'run_day'	 => $tempBody['run_day'],
			'serve_money'=> $tempBody['sMoney'],
			'max_expen'	 => $tempBody['maxExpen'],
			'min_money'	 => $tempBody['minMoney'],
			'day_money'	 => $tempBody['dMoney'],
			'start_time'	 => $tempBody['start_time'],
			'end_time'	 => $tempBody['end_time'],
			'repay_num'	 => $tempBody['repay_num'],
			'current' => $tempBody['current'],
			'ctime'		 => $time,
			'qd_id' => $user['qd_id'],
			'agent_id' => $user['agent_id'],
			'is_dz' => $is_dz,
			'province' => $temp['province'],
			'city' => $temp['city'],
			'utime'		 => $time
		];
		$proId=Db::name('repayProgram')->insertGetId($insert);
		$this->allotList($user,$user_id,$proId,$tempBody['list']);
		Db::name('repayTemp')->where('temp_id',$temp['temp_id'])->update(['status'=>2]);
	}

	protected function allotList($user,$user_id,$proId,$list){
		$time=time();
		$i=1;
		foreach($list as $item){
			//if($i>2) $i=1;
			if($i==1){
				//第一条可执行
				$insert['is_post'] = 1;
			}else{
				$insert['is_post'] = 0;
			}
			
			$insert['qd_id']=$user['qd_id'];
			$insert['agent_id']=$user['agent_id'];
			
			$insert['user_id']=$user_id;
			$insert['repay_id']=$proId;
			$insert['order_no']=order_no();
			$insert['id']  =$i;
			$insert['day'] =$item['day'];
			$insert['cutime']=$time;
			$insert['type']= $item['type'];
			if($item['type'] == 1){
				$insert['a_money']=$item['aMoney'];
				$insert['d_money']=$item['dMoney'];
				$insert['s_money']=$item['sMoney'];
				$insert['money']=$item['money'];
			}else{
				$insert['a_money']=$insert['money']=$item['money'];
				$insert['d_money']=$insert['s_money']=0;
			}
			$insert['exechtime']=$item['chtime'];
			$insert['exetime']=$item['time'];
			
			//mcc
			if(isset($item['mcc_p_id'])){
				$insert['mcc_p_id']=$item['mcc_p_id'];
			}else{
				$insert['mcc_p_id']= '';
			}
			if(isset($item['mcc_p_id'])){
				$insert['mcc_p_name']=$item['mcc_p_name'];
			}else{
				$insert['mcc_p_name']= '';
			}
			Db::name('repayList')->insert($insert);
			$i++;	
		}
	}

}
