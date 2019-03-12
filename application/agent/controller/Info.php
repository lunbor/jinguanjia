<?php
namespace app\qudao\controller;
use think\Controller;
use think\Db;

class Info extends Base{
	/**
     * 信息
     * @return
     */
    public function nxInfo(){
		$qd_id = session('qd_id');
		$distributor = Db::name('distributor')->where('qd_id', $qd_id)->find();
		if(!$distributor){
		  $this->error('信息有误..	');
		}
		
		$map = [];
		//会员数
		$distributor['member_num'] = Db::name('member')->where($map)->count();
		
		//收款总金额
		$map['status'] = 4;
		$distributor['get_money'] = Db::name('get_money')->where($map)->sum('money');
		
		//还款总金额
		$map['status'] = 2;
		$distributor['repay_money'] = Db::name('repay_list')->where($map)->sum('money');
		
		$distributor['now_time'] = date('Y年m月d日  H时i分s秒');
		
		$this->assign([
            'info' => $distributor
        ]);
		
		return $this->fetch();
	}
	
	//获取数据
	public function get_sj_ajax(){
		$qd_id = session('qd_id');
		$distributor = Db::name('distributor')->where('qd_id', $qd_id)->find();
		if(!$distributor){
		  return json(['code' => -1, 'datas' => '', 'msg' => '信息有误...']);
		}
		
		$map = [];
		
		//取近6个月数据
		$month = 6;
		$thisyear=date('Y'); 
		$thismonth=date('m'); 
		$dateAll = [];
		if($thismonth>=$month){
		    $i = 0;
			$bmonth = $thismonth - $month + 1;
			for ($x=$bmonth; $x<=$thismonth; $x++) {
				if($x>=10){
					$dateAll[$i] = $thisyear.'-'.$x;
				}else{
					$dateAll[$i] = $thisyear.'-0'.$x;
				}
				$i++;	
		    }
		}else{
			$i = 0;
			$bmonth = 12 - ($month - $thismonth)+1;
			for ($x=$bmonth; $x<=12; $x++) {
				$preyear = $thisyear - 1;
				if($x>=10){
					$dateAll[$i] = $preyear.'-'.$x;
				}else{
					$dateAll[$i] = $preyear.'-0'.$x;
				}
				$i++;	
		    }
			
			for ($x=1; $x<=$thismonth; $x++) {
				if($x>=10){
					$dateAll[$i] = $thisyear.'-'.$x;
				}else{
					$dateAll[$i] = $thisyear.'-0'.$x;
				}
				$i++;
			}
		}
		
		//dump($dateAll);
		$get_list = [];
		$get_lists = [];
		foreach ($dateAll as $k => $v){
			$get_list['temperature'] = Db::name('get_money')->where("status = 4 and DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d %H:%i:%s') like '".$v."%'")->sum('money');
			
			$get_list['city'] = '收款交易金额';
			$get_list['month'] = substr($v,-2).'月';
			
			$get_lists[] = $get_list;
		}
		
		foreach ($dateAll as $k => $v){
			$get_list['temperature'] = Db::name('repay_list')->where("status = 2 and DATE_FORMAT(FROM_UNIXTIME(cutime),'%Y-%m-%d %H:%i:%s') like '".$v."%'")->sum('money');
			
			$get_list['city'] = '还款交易金额';
			$get_list['month'] = substr($v,-2).'月';
			
			$get_lists[] = $get_list;
		}
		
		//dump($get_lists);
		return json(['code' => 1, 'datas' => $get_lists, 'msg' => '获取成功！']);
	}
}
