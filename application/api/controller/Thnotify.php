<?php
//信用卡支付异步回调处理
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Db;
class Thnotify extends Controller{
	public function index(){
		$rel = input();
		writeF('Tdlog.txt',json_encode($rel));

		$queryid	=$rel['ypt_order_no'];
		$order_no   =$rel['order_no'];
		$code		=$rel['Code'];
		$money		='';
		$status		=$rel['Resp_code'];
		$list		=Db::name('get_money')->where(array('status'=>['in','0,3'],'order_no'=>$order_no,'keyname'=>'Th'))->find();

		if(!$list){
			return;
		}
		
		$is_tz = 0;

					$setStat	=0;								//本地订单最终结果状态
					$setMsg		='';							//本地订单最终结果描述
					switch($status){
						case '1001':		//未下单
						case '1002':		//下单成功，商户成功下单，但客户未进行支付或者支付未完成
						break;
						case '1004':		//下单失败
							$setStat=1;
							$setMsg='发起申请失败';
							$is_tz = 1;
							insertMoneyLog(['get_id'=>$list['get_id'],'msg'=>'发起申请失败','status'=>0]);
						break;
						case '40000':		//支付成功
							$setStat=4;
							$setMsg='结算成功';
							$is_tz = 1;
							insertMoneyLog(['get_id'=>$list['get_id'],'msg'=>'结算成功','status'=>1]);
						break;
						case '1003':		//支付失败
							$setStat=5;
							$setMsg='结算失败';
							$is_tz = 1;
							insertMoneyLog(['get_id'=>$list['get_id'],'msg'=>'结算失败','status'=>0]);
						break;
					}

					if($setStat!=0){
						
						Db::name('getMoney')->where('order_no',$order_no)->update(['order_num'=>$queryid,'utime'=>time(),'dsc'=>$setMsg,'status'=>$setStat]);
						if($setStat==4){
							//$orderInfo=Db::name('getMoney')->where('get_id',$list['get_id'])->find();
							insertCreditValLog(['order_no'=>$order_no,'user_id'=>$list['user_id'],'form_userid'=>0,'type'=>1,'val'=>round($list['fee']+$list['mercfee'],0)]);
						}
					}
		
			if($is_tz =='1'){
				return ['Code'=>10000];
			}
			
		
	}

	
}
