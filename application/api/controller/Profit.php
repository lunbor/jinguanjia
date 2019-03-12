<?php
namespace app\api\controller;
use think\Db;
use think\Config;
class Profit extends Apibase
{
	/**
     * [jiesuan 结算收益]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function jiesuan(){	 
		$seadata['r.status'] = 2;
		$seadata['r.type'] = 2;
		$seadata['r.is_jiesuan'] = 0;
		
		$ghostArr = Db::name('repayList')->alias('r')
		->field('r.*,p.current')
		->join('repay_program p','p.pro_id = r.repay_id','left')
		->where($seadata)
		->order('r.list_id asc')
		->select();
		foreach($ghostArr as $ghost){
			if($ghost['current'] == 1){
				jiesuanProfit($ghost['user_id'],3,$ghost['money']/100,$ghost['order_no'],$ghost['list_id']);
			}else{
				jiesuanProfit($ghost['user_id'],2,$ghost['money']/100,$ghost['order_no'],$ghost['list_id']);
			}
			
			Db::name('repayList')->where(['list_id'=>$ghost['list_id']])->update(['is_jiesuan'=>1]);
		}
		
		
		$seadata2['status'] = 4;
		$seadata2['is_jiesuan'] = 0;
		
		$ghostArr2 = Db::name('getMoney')
		->where($seadata2)
		->order('get_id asc')
		->select();
		foreach($ghostArr2 as $ghost){
			jiesuanProfit($ghost['user_id'],1,$ghost['money'],$ghost['order_no'],$ghost['get_id']);
			Db::name('getMoney')->where(['get_id'=>$ghost['get_id']])->update(['is_jiesuan'=>1]);
		}
		return true;
	}

}
