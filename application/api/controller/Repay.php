<?php
namespace app\api\controller;
use app\api\model\BankCardModel;
use app\api\model\CreditCardModel;
use app\api\model\RepayModel;
use app\api\model\MemberModel;
use think\Db;
use think\Config;
use lib\Curl;
class Repay extends Apibase
{
	/**
     * [getRepayStep 定制计划]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getRepayStep(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getCreditbox($map);
		if($creditCard){
			if(!isset($data['current']) ||  ($data['current']!==0 && $data['current']!==1 && $data['current']!==2)){return rel(-1,'参数无效');	}
			if($data['current'] == 2){
				if(!isset($data['pay_money']) ||  $data['pay_money']<=0){return rel(-1,'请输入每笔最大消费金额');	}
				if($data['pay_money']>1000){return rel(-1,'消费金额不能大于1000元');	}
				if(!isset($data['start_time']) ||  $data['start_time']==''){return rel(-1,'请选择开始日期');	}
				if(!isset($data['end_time']) ||  $data['end_time']==''){return rel(-1,'请选择结束日期');	}
				
				if(!isset($data['pay_num']) ||  $data['pay_num']<=0){return rel(-1,'请选择周期还款笔数');	}
				if(!isset($data['period_num']) ||  $data['period_num']<=0){return rel(-1,'请选择周期数');	}
				
				$start_time = strtotime($data['start_time']);
				
				if($start_time > strtotime($data['end_time'])){  
					return rel(-1,'结束日期不能大于开始日期');	
				}
				
				if($start_time < strtotime(date("y-m-d"))){  
					return rel(-1,'开始日期小于了今天日期');	
				}
			}else{
				if(!isset($data['repay_money']) ||  $data['repay_money']<=0){return rel(-1,'请输入还款金额');	}
				if($data['repay_money']<1000){return rel(-1,'还款金额必须大于1000元');	}
				if(!isset($data['current']) ||  ($data['current']!==0 && $data['current']!==1)){return rel(-1,'参数无效');	}
				if(!isset($data['start_time']) ||  $data['start_time']==''){return rel(-1,'请选择还款开始日期');	}
				if(!isset($data['end_time']) ||  $data['end_time']==''){return rel(-1,'请选择还款结束日期');	}
				
				$start_time = strtotime($data['start_time']);
				
				if($start_time > strtotime($data['end_time'])){  
					return rel(-1,'还款结束日期不能大于还款开始日期');	
				}
				
				if($start_time < strtotime(date("y-m-d"))){  
					return rel(-1,'还款开始日期小于了今天日期');	
				}
			}
			
			$aisle=Db::name('payAisle')->where(['type'=>['IN','2,3'],'status'=>1])->find();		//查询当前默认使用的还款渠道
			if(!$aisle) return rel(-1,'因银行通道调整，暂时不能进行生成还款计划，请稍后再试');	
			
			if($data['current'] == 0){
				//普通计划
				if(in_array($creditCard['bank_id'],config('repay_bank'))){
					return rel(-1,'普通计划暂不支持'.$creditCard['bank_name'].',请使用完美计划');	
				}
				if(!isset($data['repay_num']) ||  $data['repay_num']<=0 || $data['repay_num']>3){return rel(-1,'请选择还款次数');}
				
				$repay_list = $this->creditProgram($start_time,$data,$user_id,$creditCard,$aisle);
				if($repay_list>0){
					return rel(1,'计划预下单成功',$repay_list);
				}else{
					return rel(-1,$repay_list);
				}
				
			}elseif($data['current'] == 1){
				//$data['repay_num'] = 1;
				//单笔限额
				$db_xe = 700;
				//if($user_id == 1079){
					//$db_xe = 1500;
				//}else{
					if($data['repay_money'] <= 50000 && $data['repay_money']>40000){
						$db_xe = 1100;
					}elseif($data['repay_money'] <= 40000 && $data['repay_money']>=30000){
						$db_xe = 900;
					}elseif($data['repay_money'] > 50000 ){
						$db_xe = 1400;
					}else{
						$db_xe = 700;
					}
					
					if($creditCard['bank_id'] == '103100000026'){
						$db_xe = 700;
					}
				//}
				
				
				//相差天数
				$count_days = count_days($start_time, strtotime($data['end_time']));
				
				$data['repay_num'] = ceil($data['repay_money'] / ($count_days+1) / $db_xe);
				
				//限制没有还款最多2次
				if($data['repay_num']>3){
					$zc_day = ceil($data['repay_money'] / $db_xe / 3);
					$zc_date = date('Y-m-d',$start_time + (($zc_day - 1) * 24 * 60 * 60));
					return rel(-1,'根据银行风控需求，还款结束日期最早只能选择'.$zc_date);
				}
				
				$repay_list = $this->creditProgramWM($start_time,$data,$user_id,$creditCard,$aisle);
				if($repay_list>0){
					return rel(1,'计划预下单成功',$repay_list);
				}else{
					return rel(-1,$repay_list);
				}
				//return rel(-1,'完美计划即将开放');
			}elseif($data['current'] == 2){
				$repay_list = $this->creditProgramJY($start_time,$data,$user_id,$creditCard,$aisle);
				if($repay_list>0){
					return rel(1,'计划预下单成功',$repay_list);
				}else{
					return rel(-1,$repay_list);
				}
				//return rel(-1,'目前只有VIP会员才能执行精养卡计划');
			}
		}else{
			return rel(-1,'信用卡信息有误，请重新操作');
		}
	}
	
	
	/**
     * [getRepayTemp 获取临时还款任务]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getRepayTemp(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		if(!isset($data['temp_id']) ||  $data['temp_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$res['creditCard'] = $creditCardMD->getCredit($map);
		if($res){
			$repayTemp = Db::name('repay_temp')->where(['credit_id'=>$data['credit_id'],'temp_id'=>$data['temp_id']])->find();
			if($repayTemp){
				$repayTemp['temp_body'] = json_decode($repayTemp['temp_body'],true);
				$res['repayTemp'] = $repayTemp;
				return rel(1,'获取成功',$res);
			}else{
				return rel(-1,'获取失败');
			}
		}else{
			return rel(-1,'获取失败');
		}
	}
	
	
	/**
     * [submitRepayStep 提交计划]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function submitRepayStep(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		if(!isset($data['temp_id']) ||  $data['temp_id']<=0){return rel(-1,'参数无效');	}
		$is_dz = 0;
		if(isset($data['is_dz']) && $data['is_dz']==1){
			$is_dz = 1;
			//return rel(-1,'目前只有VIP会员可以享有周转金垫资服务');
		}
		
		//限制有垫资还款失败的用户，再发起任务
		$map_dz['p.is_dz'] = 1;
		$map_dz['l.type'] = 1;
		$map_dz['l.status'] = 3;
		$map_dz['l.user_id'] = $user_id;
		$map_dz['l.a_money'] = ['gt',0];
		$dz_count = Db::name('repay_list')
			->alias('l')
			->field('l.*')
			->join('repay_program p','p.pro_id = l.repay_id','LEFT')
			->where($map_dz)
			->count();
		if($dz_count>0) {return rel(-1,'您有垫资任务还款失败，暂时无法为您提供服务，请及时归还垫资款！');	}
		
		
		
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getCredit($map);
		if($creditCard){
			$repayTemp = Db::name('repay_temp')->where(['credit_id'=>$data['credit_id'],'temp_id'=>$data['temp_id']])->find();
			if($repayTemp){
				
				if($repayTemp['status']==2){return rel(-1,'该计划已经下单成功了，请勿重复生成计划');}
				
				$isrepay = Db::name('repayProgram')->where(['status'=>['IN','1,2'],'credit_id'=>$data['credit_id']])->find();
				if($isrepay){return rel(-1,'该信用卡有正在执行的还款计划，请先取消计划');}
				
				//查找还款通道
				$card_regist = Db::name('credit_regist')->where(['user_id'=>$user_id,'keyname'=>$repayTemp['keyname'],'credit_code'=>$repayTemp['credit_code'],'status'=>1])->find();	
				if(!$card_regist){
					//激活通道
					$reqData =[
						'user_id' =>$user_id,
						'credit_id' =>$repayTemp['credit_id'],
					];
					$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
					$rel = Curl::http_curl($site."/api/Drepay/authent",$reqData);
					$rel = json_decode($rel,true);
					if(isset($rel['code']) && $rel['code'] == 1){
						$card_regist = Db::name('credit_regist')->where(['user_id'=>$user_id,'keyname'=>'Td','credit_id'=>$repayTemp['credit_id'],'credit_code'=>$repayTemp['credit_code'],'status'=>1])->find();
					
					}else{
						if(isset($rel['msg']) && $rel['msg'] != ''){
							return json(['code'=>-1,'msg'=>$rel['msg']]);
						}else{
							return json(['code'=>-1,'msg'=>'未找到对应还款通道！']);
						}
					}
				
				}else{
					if($card_regist['credit_id'] != $repayTemp['credit_id']){
						//修改信息
						$credit	=Db::name('creditCard')->where(array('credit_id'=>$repayTemp['credit_id'],'status'=>1))->find();
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
							Db::name('credit_regist')->where('regist_id',$card_regist['regist_id'])->update(array('credit_id'=>$repayTemp['credit_id']));
							$card_regist = Db::name('credit_regist')->where(['user_id'=>$user_id,'keyname'=>'Td','credit_id'=>$repayTemp['credit_id'],'credit_code'=>$repayTemp['credit_code'],'status'=>1])->find();
						
						}else{
							if(isset($rel['msg']) && $rel['msg'] != ''){
								return json(['code'=>-1,'msg'=>$rel['msg']]);
							}else{
								return json(['code'=>-1,'msg'=>'未找到对应还款通道！']);
							}
						}
					}
				
				}
				
				if(isset($data['zx_area']) && $data['zx_area']==1){
					Db::name('repay_temp')->where('temp_id',$data['temp_id'])->update(['province'=>$data['province'],'city'=>$data['city'],'regist_id'=>$card_regist['regist_id']]);
				}else{
					Db::name('repay_temp')->where('temp_id',$data['temp_id'])->update(['regist_id'=>$card_regist['regist_id']]);
				}
				
					//精养卡
					$temp_body_array = json_decode($repayTemp['temp_body'],true);
					if($temp_body_array['current'] == 2){
						if($card_regist['qdrjy_id'] !=''){
							//修改费率
							$changeRel = $this->d3changeRate($repayTemp['temp_body'],$card_regist);		
							if(isset($changeRel['Code']) && $changeRel['Code'] == '10000' && isset($changeRel['Resp_code']) && $changeRel['Resp_code'] == '40000'){
								$repayLogic =model('common/RepayLogic','Logic');
								$repayLogic->exeProgram($card_regist['regist_id'],$data['temp_id'],$is_dz);
								$rel['url'] = '';
								//Db::name('repay_temp')->where('temp_id',$data['temp_id'])->update(['status'=>2]);
								return rel(1,'还款计划执行成功',$rel);
							}else{
								if(isset($changeRel['Resp_msg'])){
									return rel(-1,$changeRel['Resp_msg']);
								}else{
									return rel(-1,$changeRel['Msg']);
								}
							}	
						}else{
							$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
							$rel['url'] = $site.'/api/repay/ktsfjy/temp_id/'.$data['temp_id'].'/registId/'.$card_regist['regist_id'].'';
							return rel(1,'需要激活通道',$rel);
						}
					}
					
					
				//普通还款
				if($card_regist['qdr_id'] !='' && $is_dz==0){
				    //修改费率
					$changeRel = $this->changeRate($repayTemp['temp_body'],$card_regist);		
					if(isset($changeRel['Code']) && $changeRel['Code'] == '10000' && isset($changeRel['Resp_code']) && $changeRel['Resp_code'] == '40000'){
						$repayLogic =model('common/RepayLogic','Logic');
						$repayLogic->exeProgram($card_regist['regist_id'],$data['temp_id'],$is_dz);
						$rel['url'] = '';
						//Db::name('repay_temp')->where('temp_id',$data['temp_id'])->update(['status'=>2]);
						return rel(1,'还款计划执行成功',$rel);
					}else{
						if(isset($changeRel['Resp_msg'])){
							return rel(-1,$changeRel['Resp_msg']);
						}else{
							return rel(-1,$changeRel['Msg']);
						}
					}					
				}
				
				//垫资还款
				if($card_regist['qdrc_id'] !='' && $card_regist['qdrc_t_id'] !='' && $is_dz==1){
					//修改费率
					$changeRel = $this->c2changeRate($repayTemp['temp_body'],$card_regist);		
					if(isset($changeRel['Code']) && $changeRel['Code'] == '10000' && isset($changeRel['Resp_code']) && $changeRel['Resp_code'] == '40000'){
						$repayLogic =model('common/RepayLogic','Logic');
						$repayLogic->exeProgram($card_regist['regist_id'],$data['temp_id'],$is_dz);
						$rel['url'] = '';
						//Db::name('repay_temp')->where('temp_id',$data['temp_id'])->update(['status'=>2]);
						return rel(1,'还款计划执行成功',$rel);
					}else{
						if(isset($changeRel['Resp_msg'])){
							return rel(-1,$changeRel['Resp_msg']);
						}else{
							return rel(-1,$changeRel['Msg']);
						}
					}
				}
				
				if($is_dz == 0){
					$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
					$rel['url'] = $site.'/api/repay/ktsfPay/temp_id/'.$data['temp_id'].'/registId/'.$card_regist['regist_id'].'';
				}elseif($is_dz == 1){
					$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
					$rel['url'] = $site.'/api/repay/ktsfRepay/temp_id/'.$data['temp_id'].'/registId/'.$card_regist['regist_id'].'';
				}
				
				return rel(1,'需要激活通道',$rel);
				//dump($card_regist);
				//exit();
			}else{
				return rel(-1,'请重新生成计划');
			}
	    }else{
			return rel(-1,'信用卡信息无效');
		}
	}
	
	protected function changeRate($temp_body,$credit_regist){
	
		$memberfee = getFee($credit_regist['user_id'],2);
		$rate =  $memberfee['rate'];
		$single_payment = $memberfee['dfee'];
	
		$data2 = [
			'user_no'=> $credit_regist['mer_id'],
			'config_no'=> $credit_regist['qdr_id'],
			'rate'=> $rate,
			'single_payment'=> $single_payment
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->changeRate($data2);
		return $rel1;
	}
	
	
	protected function d3changeRate($temp_body,$credit_regist){
		$memberfee = getFee($credit_regist['user_id'],2);
		$rate =  $memberfee['rate'];
		$single_payment = $memberfee['dfee'];

		$data2 = [
			'config_no'=> $credit_regist['qdrjy_id'],
			'rate'=> $rate,
			'single_payment'=> $single_payment
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->d3changeRate($data2);
		return $rel1;
	}
	
	protected function c2changeRate($temp_body,$credit_regist){
		$memberfee = getFee($credit_regist['user_id'],3);
		$rate =  $memberfee['rate'];
		$single_payment = $memberfee['dfee'];

		$data2 = [
			'config_no'=> $credit_regist['qdrc_id'],
			'rate'=> $rate,
			'single_payment'=> $single_payment
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->c2changeRate($data2);
		return $rel1;
	}
	
	
	//dc激活
	
	protected function d3EntryCard($data){	 
		$credit_regist	=Db::name('credit_regist')->where(array('regist_id'=>$data['regist_id']))->find();
		if($credit_regist['qdrjy_id']!=''){
			return rel(1,'激活成功');	
		}
		
		$memberfee = getFee($credit_regist['user_id'],2);
		$rate =  $memberfee['rate'];
		$single_payment = $memberfee['dfee'];
		
		$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
		$data2 = [
			'user_no'=> $credit_regist['mer_id'],
			'rate'=> $rate,
			'single_payment'=> $single_payment,
			'page_url'=> $site.'/api/Repay/c2pageUrl/regist_id/'.$data['regist_id'].'/temp_id/'.$data['temp_id'].'',
			'notify_url'=> $site.'/api/Drepay/d3notifyUrl/regist_id/'.$data['regist_id'].'/temp_id/'.$data['temp_id'].'',
		];
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->d3entrycard($data2);
		//dump($rel1);
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			if(isset($rel1['confirm_status'])){
				if($rel1['confirm_status']==0){
					return rel(2,'需要激活',$rel1['html']);	
				}elseif($rel1['confirm_status']==1){
					return rel(0,'通道激活失败');	
				}elseif($rel1['confirm_status']==2){
					if(isset($rel1['config_no']) && $rel1['config_no']!=''){
						Db::name('credit_regist')->where(array('regist_id'=>$data['regist_id']))->update(array('qdrjy_id'=>$rel1['config_no']));
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
			'page_url'=> $site.'/api/Repay/c2pageUrl/regist_id/'.$data['regist_id'].'/temp_id/'.$data['temp_id'].'',
			'notify_url'=> $site.'/api/Drepay/c2notifyUrl/regist_id/'.$data['regist_id'].'/temp_id/'.$data['temp_id'].'',
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
	/**
     * [creditProgram 生成信用卡还款任务]
     * @author [汤汤] [1582978230@qq.com]
     */
	protected function creditProgram($start_time,$data,$user_id,$creditCard,$aisle){
				//判断是否有正在执行的计划
				$isrepay = Db::name('repayProgram')->where(['status'=>['IN','1,2'],'credit_id'=>$creditCard['credit_id']])->find();
				if($isrepay){return '该信用卡有正在执行的还款计划，请先取消计划';}
				//相差天数
				$count_days = count_days($start_time, strtotime($data['end_time']));
				
				//还款次数
				$repay_num = $data['repay_num'] * ($count_days + 1);
				
				$pj_je = floor($data['repay_money'] / ($repay_num + 1));
				
				$arr_js = [];
				$jishux = $pj_je;
				for ($x=0; $x<$repay_num - 1; $x++) { 
					 $arr_js[$x] = rand(floor($jishux/4), floor($jishux/3));
					 $jishux = $pj_je - array_sum($arr_js);
				}
				
				//dump($arr_js);
				$repay_m = [];
				foreach ($arr_js as $v){
					$repay_m[] = $pj_je + $v;
				}
				
				$ys_money = $data['repay_money'] - array_sum($repay_m);
				if($ys_money<0){
					return '还款计划生成失败，请联系客服人员';
				}
				$repay_m[] = $ys_money;
				
				$repay_time=[];
				$day = 0;
				$i = 43200 / $data['repay_num'];
				$list = [];
				$z_dMoney = 0;
				$z_fee = 0;
				
				$maxExpen = max($repay_m) * 100;
				$run_day = '';
				$day_time = '';
				for ($x=0; $x<$repay_num; $x++) { 
				   $zchus = ($x+1)%$data['repay_num'];
				   $zchus2 = $x%$data['repay_num'];
				   $startRand =  36000 + ($i * $zchus2);
				   $endRand =  79200 - ($i * ($data['repay_num'] - $zchus2 - 1))-5400;
				   $repay_time[$x] = $start_time + ($day * 24 * 60 * 60) + rand($startRand,$endRand);
				   
				   //判断生成日期是否小于现在的日期
				   if($repay_time[$x] < time() ){
				   	   $repay_time[$x] = time() + (($x+1) * 1800);
				   }
				   
				   //把还款的日期记录下来
				   $sggtime = date('Y-m-d',$repay_time[$x]);
				   if($day_time != $sggtime){
				   	  $run_day = $run_day.','.$sggtime;
				   }
				   $day_time = $sggtime;
				   
				   if($zchus==0){
						$day = $day + 1;
				   }
				   
				   //判断用户是否VIP
				    $memberMD = new MemberModel();
					$map['id'] = $user_id;
					$group_id = $memberMD->getUserID($map,'group_id');	
				   
				   $memberfee = getFee($user_id,2);
				   $dMoney = $memberfee['dfee'] * 100; //代付费
				   $fee = $repay_m[$x] * $memberfee['rate']; //手续费
	
				   $serverFeilv = $memberfee['rate']/100;
				   $fee = round(((($memberfee['dfee']*0.01) + $repay_m[$x]) / (1 - $serverFeilv)) * $serverFeilv,2);
				   $fee = $fee*100;
				   
				   $aMoney = $repay_m[$x] * 100; //还款金额
				   
				   $z_dMoney = $z_dMoney + $dMoney;
				   $z_fee = $z_fee + $fee;
				   $list[] = [
				   		'type' => 1,
						'day' => date('d',$repay_time[$x]), 
						'time' => $repay_time[$x]-300, 
						'chtime' => date('Y-m-d H:i:s',$repay_time[$x]-300), 
						'aMoney' => $aMoney, 
						'dMoney' => $dMoney, 
						'sMoney' => $fee, 
						'money' => $fee + $dMoney + $aMoney, 
				   ];
				   
				   
				   $list[] = [
				   		'type' => 2,
						'day' => date('d',$repay_time[$x]), 
						'time' => $repay_time[$x], 
						'chtime' => date('Y-m-d H:i:s',$repay_time[$x]), 
						'money' => $aMoney, 
				   ];
				}
				
				$run_day = ltrim($run_day, ','); 
				
				$res['list'] = $list;
				$res['total'] = $data['repay_money'] * 100;
				$res['maxExpen'] = $maxExpen;
				$res['dMoney'] = $z_dMoney;
				$res['sMoney'] = round($z_fee,0);
				$res['run_day'] = $run_day;
				$res['minMoney'] = $maxExpen+$z_dMoney+$z_fee;
				$res['payNum'] = $repay_num;
				
				$res['start_time'] = $data['start_time'];
				$res['end_time'] = $data['end_time'];
				$res['repay_num'] = $data['repay_num'];
				
				$res['current'] = $data['current'];
				
				
				$temp_id = Db::name('repay_temp')->insertGetId(['status' => 1,'credit_id' => $creditCard['credit_id'],  'credit_code' => $creditCard['credit_code'], 'temp_body' => json_encode($res), 'cutime' => time(), 'keyname' => $aisle['keyname']]);
				return $temp_id;
				//$min_repay = min($repay_m);
				//dump($repay_m);
				//dump($repay_time);
	}
	
	
	/**
     * [creditProgram 生成信用卡还款任务]
     * @author [汤汤] [1582978230@qq.com]
     */
	protected function creditProgramWM($start_time,$data,$user_id,$creditCard,$aisle){
				//判断是否有正在执行的计划
				$isrepay = Db::name('repayProgram')->where(['status'=>['IN','1,2'],'credit_id'=>$creditCard['credit_id']])->find();
				if($isrepay){return '该信用卡有正在执行的还款计划，请先取消计划';}
				//相差天数
				$count_days = count_days($start_time, strtotime($data['end_time']));
				
				//还款次数
				$repay_num = $data['repay_num'] * ($count_days + 1);
				
				$pj_je = floor($data['repay_money'] / ($repay_num + 1));
				
				$arr_js = [];
				$jishux = $pj_je;
				for ($x=0; $x<$repay_num - 1; $x++) { 
					 $arr_js[$x] = rand(floor($jishux/4), floor($jishux/3))+rand(0,3);
					 $jishux = $pj_je - array_sum($arr_js);
				}
				
				//dump($arr_js);
				$repay_m = [];
				foreach ($arr_js as $v){
					$repay_m[] = $pj_je + $v;
				}
				
				$ys_money = $data['repay_money'] - array_sum($repay_m);
				if($ys_money<0){
					return '还款计划生成失败，请联系客服人员';
				}
				$repay_m[] = $ys_money;
				
				$repay_time=[];
				$day = 0;
				$i = 36000 / $data['repay_num'];
				$list = [];
				$z_dMoney = 0;
				$z_fee = 0;
				$z_sjMoney = 0;
				
				$maxExpen = max($repay_m) * 100;
				$run_day = '';
				$day_time = '';
				$first_repay_time = time();
				$end_repay_time = time();
				for ($x=0; $x<$repay_num; $x++) { 
				   
				   $zchus = ($x+1)%$data['repay_num'];
				   $zchus2 = $x%$data['repay_num'];
				   $startRand =  36000 + ($i * $zchus2);
				   $endRand =  72000 - ($i * ($data['repay_num'] - $zchus2 - 1));
				   $repay_time[$x] = $start_time + ($day * 24 * 60 * 60) + rand($startRand,$endRand);
				   
				   //判断生成日期是否小于现在的日期
				   if($repay_time[$x] < time() ){
				   	   $repay_time[$x] = time() + (($x+1) * 1800);
				   }
				   
				   //把还款的日期记录下来
				   $sggtime = date('Y-m-d',$repay_time[$x]);
				   if($day_time != $sggtime){
				   	  $run_day = $run_day.','.$sggtime;
				   }
				   $day_time = $sggtime;
				   
				   if($zchus==0){
						$day = $day + 1;
				   }
				   
				   //判断用户是否VIP
				    $memberMD = new MemberModel();
					$map['id'] = $user_id;
					$group_id = $memberMD->getUserID($map,'group_id');	
				   
				   $memberfee = getFee($user_id,3);
				   $dMoney = $memberfee['dfee'] * 100; //代付费
				   $fee = $repay_m[$x] * $memberfee['rate']; //手续费
				   				   
				   $fee = round($fee,0);
				   $aMoney = $repay_m[$x] * 100; //还款金额
				   
				   $z_dMoney = $z_dMoney + $dMoney;
				   $z_fee = $z_fee + $fee;
				   
				   if($x == 0){
				   		//第一笔还款时间
						$first_repay_time = $repay_time[$x];
				   }
				   
				   if($x+1 == $repay_num){
				   		//最后一笔还款时间
						$end_repay_time = $repay_time[$x];
				   }
				   
				    /*$list[] = [
				   		'type' => 1,
						'day' => date('d',$repay_time[$x]), 
						'time' => $repay_time[$x]-60, 
						'chtime' => date('Y-m-d H:i:s',$repay_time[$x]-60), 
						'aMoney' => 0, 
						'dMoney' => $dMoney, 
						'sMoney' => $fee, 
						'money' => $fee + $dMoney, 
				   ];*/
				   $sj_h_time = rand(301,330);
				   
				   $list[] = [
				   		'type' => 2,
						'day' => date('d',$repay_time[$x]), 
						'time' => $repay_time[$x]-$sj_h_time, 
						'chtime' => date('Y-m-d H:i:s',$repay_time[$x]-$sj_h_time), 
						'money' => $aMoney, 
				   ];
				   
				   $sjMoney = round(rand(10,200)/10)*10;
				   $z_sjMoney = $sjMoney + $z_sjMoney;
				   
				   $list[] = [
				   		'type' => 1,
						'day' => date('d',$repay_time[$x]), 
						'time' => $repay_time[$x], 
						'chtime' => date('Y-m-d H:i:s',$repay_time[$x]), 
						'aMoney' => $aMoney - $sjMoney, 
						'dMoney' => 0, 
						'sMoney' => 0, 
						'money' => $aMoney - $sjMoney, 
				   ];
				   
				   
				   

				}
				
				
				$sj_h_time = rand(301,330);
				
				//添加扣除总手续费
				array_unshift($list,[
				   		'type' => 1,
						'day' => date('d',$first_repay_time), 
						'time' => $first_repay_time-$sj_h_time, 
						'chtime' => date('Y-m-d H:i:s',$first_repay_time-$sj_h_time), 
						'aMoney' => 0, 
						'dMoney' => $z_dMoney, 
						'sMoney' => $z_fee, 
						'money' => $z_fee + $z_dMoney, 
				   ]);
				   
				//添加总预留金额
				array_push($list,[
				   		'type' => 1,
						'day' => date('d',$end_repay_time), 
						'time' => $end_repay_time+$sj_h_time, 
						'chtime' => date('Y-m-d H:i:s',$end_repay_time+$sj_h_time), 
						'aMoney' => $z_sjMoney, 
						'dMoney' => 0, 
						'sMoney' => 0, 
						'money' => $z_sjMoney, 
				   ]);
				
				$run_day = ltrim($run_day, ','); 
				
				$res['list'] = $list;
				$res['total'] = $data['repay_money'] * 100;
				$res['maxExpen'] = $maxExpen;
				$res['dMoney'] = $z_dMoney;
				$res['sMoney'] = round($z_fee,0);
				$res['run_day'] = $run_day;
				$res['minMoney'] = $maxExpen+$z_dMoney+$z_fee;
				$res['payNum'] = $repay_num;
				
				$res['start_time'] = $data['start_time'];
				$res['end_time'] = $data['end_time'];
				$res['repay_num'] = 0;//假设为0，代表随机
				$res['current'] = $data['current'];
				
				$temp_id = Db::name('repay_temp')->insertGetId(['status' => 1,'credit_id' => $creditCard['credit_id'],  'credit_code' => $creditCard['credit_code'], 'temp_body' => json_encode($res), 'cutime' => time(), 'keyname' => $aisle['keyname']]);
				return $temp_id;
				//$min_repay = min($repay_m);
				//dump($repay_m);
				//dump($repay_time);
	}
	
	
	/**
     * [creditProgram 生成信用卡还款任务]
     * @author [汤汤] [1582978230@qq.com]
     */
	protected function creditProgramJY($start_time,$data,$user_id,$creditCard,$aisle){
				//判断是否有正在执行的计划
				$isrepay = Db::name('repayProgram')->where(['status'=>['IN','1,2'],'credit_id'=>$creditCard['credit_id']])->find();
				if($isrepay){return '该信用卡有正在执行的还款计划，请先取消计划';}
				
				//还款次数
				$repay_num = $data['pay_num'] * $data['period_num'];
				
				//计算还款天数
				$count_days = 0;
				$pay_date = $data['pay_date'];
				if(!is_array($pay_date) || count($pay_date) == 0) {return '还款日期有误！';}
				$date_list = [];
				foreach ($pay_date as $k => $v){
					if($v['checked']){
						$count_days = $count_days + 1;
						$date_list[] = $v['value'];
					}
				}
				if($count_days == 0){
					return '还款日期有误！';
				}
				
				//每日消费次数
				$data['repay_num'] = $day_repay_num = ceil($repay_num / $count_days);
				if($day_repay_num > 5){
					return '每日消费笔数不能超过5次，至少需要'.ceil($repay_num / 5).'天！';
				}
				
				//手续费
				$memberfee = getFee($user_id,2);
				
				//初始化
				$list = [];
				$z_dMoney = 0;
				$z_fee = 0;
				$day = 0;
				$i = 36000 / $data['repay_num'];
				$pay_num = 0;
				$period_money = 0;
				$data['repay_money'] = 0;
				$maxExpen = 0;
				foreach($date_list as $v){
					//随机还款日期
					$repay_time = [];
					for ($x=0; $x<$data['repay_num']; $x++) { 
					   $zchus = ($x+1)%$data['repay_num'];
					   $zchus2 = $x%$data['repay_num'];
					   $startRand =  36000 + ($i * $zchus2);
					   $endRand =  72000 - ($i * ($data['repay_num'] - $zchus2 - 1));
					   $repay_time[$x] = strtotime($v) + rand($startRand,$endRand);
					   
					   //判断生成日期是否小于现在的日期
					   if($repay_time[$x] < time() ){
						   $repay_time[$x] = time() + (($x+1) * 1800);
					   }
				   
					    //随机消费金额
					   $pay_money = rand(150,$data['pay_money']-10);
					   if($period_money == 0){
					   	 $dMoney = $memberfee['dfee'] * 100; //代付费
					   }else{
					  	 $dMoney = 0;
					   }
					   $fee = $pay_money * $memberfee['rate']; //手续费
									   
					   $serverFeilv = $memberfee['rate']/100;
				   	   $fee = round(((($dMoney*0.01) + $pay_money) / (1 - $serverFeilv)) * $serverFeilv,2);
				       $fee = $fee*100;
					   
					   
					   $aMoney = $pay_money * 100; //消费金额
					   $period_money = $period_money + $aMoney;
					   $z_dMoney = $z_dMoney + $dMoney;
					   $z_fee = $z_fee + $fee;
					   
					   $resmcc = config('repayMcc');
					   if(count($resmcc)>0){
					   	 $randmcc = $resmcc[rand(0,count($resmcc) - 1)];
					   }else{
					   		$randmcc['value'] = 0;
							$randmcc['label'] = '';
					   }
					   $list[] = [
							'type' => 1,
							'day' => date('d',$repay_time[$x]), 
							'time' => $repay_time[$x], 
							'chtime' => date('Y-m-d H:i:s',$repay_time[$x]), 
							'aMoney' => $aMoney, 
							'dMoney' => $dMoney, 
							'sMoney' => $fee, 
							'money' => $fee + $dMoney + $aMoney, 
							'mcc_p_id' => $randmcc['value'],
							'mcc_p_name' => $randmcc['label'],
					   ];
					   
					   $pay_num = $pay_num+1;					   
					   if($pay_num % $data['pay_num'] == 0 || $pay_num  == $repay_num){
					       $list[] = [
								'type' => 2,
								'day' => date('d',$repay_time[$x]+620), 
								'time' => $repay_time[$x]+620, 
								'chtime' => date('Y-m-d H:i:s',$repay_time[$x]+620),
								'aMoney' => $period_money,  
								'money' =>  $period_money, 
						   ];
						   $data['repay_money'] = $data['repay_money'] + $period_money - $dMoney;
						   if($maxExpen < $period_money){
						   	  $maxExpen = $period_money;
						   }
						   $period_money = 0;
						   if($pay_num  == $repay_num){
						   	  break;
						   }
					   } 
					}
					if($pay_num  == $repay_num){
						 break;
					}
				}
				
				$res['list'] = $list;
				$res['total'] = $data['repay_money'];
				$res['maxExpen'] = $maxExpen;
				$res['dMoney'] = $z_dMoney;
				$res['sMoney'] = round($z_fee,0);
				$res['run_day'] = implode(",",$date_list);
				$res['minMoney'] = $maxExpen+$z_dMoney+$z_fee;
				$res['payNum'] = $data['period_num'];
				
				$res['start_time'] = $data['start_time'];
				$res['end_time'] = $data['end_time'];
				$res['repay_num'] = 0;//假设为0，代表随机
				$res['current'] = $data['current'];
				$temp_id = Db::name('repay_temp')->insertGetId(['status' => 1,'credit_id' => $creditCard['credit_id'],  'credit_code' => $creditCard['credit_code'], 'temp_body' => json_encode($res), 'cutime' => time(), 'keyname' => $aisle['keyname']]);
				return $temp_id;
	}
	
	
	/**
     * [getRepayInfo 获取还款计划]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getRepayInfo(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$res['creditCard'] = $creditCardMD->getCredit($map);
		if($res){
			$repayMD = new RepayModel();
			$list = $repayMD->getAll(['credit_id'=>$data['credit_id'],'user_id'=>$user_id]);
			foreach ($list as $k => $v){
				$list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			}
			
			$res['list'] = $list;
			return rel(1,'获取成功',$res);
		}else{
			return rel(-1,'获取失败');
		}
	}
	
	
	/**
     * [getRepayDetail 获取还款计划详情]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getRepayDetail(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['credit_id']) ||  $data['credit_id']<=0){return rel(-1,'参数无效');	}
		if(!isset($data['pro_id']) ||  $data['pro_id']<=0){return rel(-1,'参数无效');	}
		$map['user_id'] = $user_id;
		$map['credit_id'] = $data['credit_id'];
		$creditCardMD = new CreditCardModel();
		$res['creditCard'] = $creditCardMD->getCredit($map);
		if($res){
			$repayMD = new RepayModel();
			$repayDetail = $repayMD->getOne(['credit_id'=>$data['credit_id'],'pro_id'=>$data['pro_id'],'user_id'=>$user_id]);
			$repayDetail['ctime'] = date('Y-m-d H:i:s',$repayDetail['ctime']);
			
			$yes_money = Db::name('repay_list')->field('sum(money) as money,sum(d_money) as d_money,sum(s_money) as s_money')->where(['status'=>['IN','2'],'type'=>2,'repay_id'=>$data['pro_id']])->find();
			$repayDetail['yes_money'] = $yes_money['money'];//已还款金额
			$repayDetail['yes_fee'] = $yes_money['s_money']+$yes_money['d_money'];//已扣手续费
			
			$xf_money = Db::name('repay_list')->field('sum(money) as money,sum(d_money) as d_money,sum(s_money) as s_money')->where(['status'=>['IN','2'],'type'=>1,'repay_id'=>$data['pro_id']])->find();
			$repayDetail['pay_money'] = $xf_money['money'];//已消费金额
			$repayDetail['yes_fee'] = $repayDetail['yes_fee'] + $xf_money['s_money']+$xf_money['d_money'];//已扣手续费
			$repayDetail['repayBak'] = '';
		    if($repayDetail['status'] == 5){
				//失败原因
				$no_drepay= Db::name('repayList')->where(['repay_id'=>$data['pro_id'],'status'=>3])->order('id desc')->limit(1)->find();
				if($no_drepay){
					$repayDetail['repayBak'] = '失败原因：'.$no_drepay['resp_msg'];
				}
			}
			
			$res['repayDetail'] = $repayDetail;
			
			$res['repayList'] = Db::name('repay_list')->where(['repay_id'=>$data['pro_id']])->select();
			return rel(1,'获取成功',$res);
		}else{
			return rel(-1,'获取失败');
		}
	}
	
	/**
     * [getRepayDetail 获取还款计划详情]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function delRepay(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['pro_id']) ||  $data['pro_id']<=0){return rel(-1,'参数无效');	}
		
		$isrepay = Db::name('repayProgram')->where(['status'=>['IN','1,2'],'pro_id'=>$data['pro_id'],'user_id'=>$user_id])->find();
		if(!$isrepay){return rel(-1,'该还款计划状态不允许取消,请勿重复操作');}
		
		if($isrepay['is_dz'] == 1){
			return rel(-1,'垫资中的还款计划不允许取消！');
		}
		
		Db::name('repayProgram')->where(['pro_id'=>$data['pro_id']])->update(['status'=>4]);
		Db::name('repayList')->where(['status'=>1,'repay_id'=>$data['pro_id']])->update(['status'=>4]);
		
		return rel(1,'操作成功');
	}
	
	
	/**
     * [c2pageUrl 通道返回]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function c2pageUrl(){
		Config::set('default_return_type', 'html');
		$data = [
				'code' => 1,
				'msg' => '操作成功',
		];
		return $this->fetch('tshimsg', $data);
	}
	/**
     * [ktsfpay 开通快捷支付]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function ktsfRepay(){
		Config::set('default_return_type', 'html');
		$registId 		= input("registId");			//提现金额
		$temp_id 		= input("temp_id");			//还款
		$creditRegist=Db::name('creditRegist')->where('regist_id',$registId)->find();	//信用卡预绑定数据
		$repayTemp=Db::name('repay_temp')->where('temp_id',$temp_id)->find();	//信用卡预绑定数据
		
		
		if(!$creditRegist || !$repayTemp){
			$data = [
				'code' => -1,
				'msg' => '数据有误',
			];
			return $this->fetch('tshimsg', $data);
		}
		if($creditRegist['qdrc_id'] !='' && $creditRegist['qdrc_t_id'] !=''){
			$repayLogic =model('common/RepayLogic','Logic');
			$repayLogic->exeProgram($registId,$temp_id);
			//Db::name('repay_temp')->where('temp_id',$temp_id)->update(['status'=>2]);
			$data = [
				'code' => 1,
				'msg' => '还款计划执行成功',
			];
			return $this->fetch('tshimsg', $data);
		}
		
		$c2Rel = $this->c2EntryCard(array('regist_id'=>$registId,'temp_id'=>$temp_id));
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
			$repayLogic =model('common/RepayLogic','Logic');
			$repayLogic->exeProgram($registId,$temp_id);
			$data = [
				'code' => 1,
				'msg' => '还款计划执行成功',
			];
			return $this->fetch('tshimsg', $data);	
		}
	    
	}
	
	
	
	/**
     * [ktsfpay 开通快捷支付]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function ktsfjy(){
		Config::set('default_return_type', 'html');
		$registId 		= input("registId");			//提现金额
		$temp_id 		= input("temp_id");			//还款
		$creditRegist=Db::name('creditRegist')->where('regist_id',$registId)->find();	//信用卡预绑定数据
		$repayTemp=Db::name('repay_temp')->where('temp_id',$temp_id)->find();	//信用卡预绑定数据
		
		
		if(!$creditRegist || !$repayTemp){
			$data = [
				'code' => -1,
				'msg' => '数据有误',
			];
			return $this->fetch('tshimsg', $data);
		}
		if($creditRegist['qdrjy_id'] !=''){
			$repayLogic =model('common/RepayLogic','Logic');
			$repayLogic->exeProgram($registId,$temp_id);
			//Db::name('repay_temp')->where('temp_id',$temp_id)->update(['status'=>2]);
			$data = [
				'code' => 1,
				'msg' => '还款计划执行成功',
			];
			return $this->fetch('tshimsg', $data);
		}
		
		$c2Rel = $this->d3EntryCard(array('regist_id'=>$registId,'temp_id'=>$temp_id));
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
			$repayLogic =model('common/RepayLogic','Logic');
			$repayLogic->exeProgram($registId,$temp_id);
			$data = [
				'code' => 1,
				'msg' => '还款计划执行成功',
			];
			return $this->fetch('tshimsg', $data);	
		}
	    
	}
	/**
     * [ktsfpay 开通快捷支付]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function ktsfpay(){
		Config::set('default_return_type', 'html');
		$registId 		= input("registId");			//提现金额
		$temp_id 		= input("temp_id");			//还款
		$creditRegist=Db::name('creditRegist')->where('regist_id',$registId)->find();	//信用卡预绑定数据
		$repayTemp=Db::name('repay_temp')->where('temp_id',$temp_id)->find();	//信用卡预绑定数据
		
		
		if(!$creditRegist || !$repayTemp){
			$data = [
				'code' => -1,
				'msg' => '数据有误',
			];
			return $this->fetch('tshimsg', $data);
		}
		if($creditRegist['qdr_id'] !=''){
			$repayLogic =model('common/RepayLogic','Logic');
			$repayLogic->exeProgram($registId,$temp_id);
			//Db::name('repay_temp')->where('temp_id',$temp_id)->update(['status'=>2]);
			$data = [
				'code' => 1,
				'msg' => '还款计划执行成功',
			];
			return $this->fetch('tshimsg', $data);
		}
		
		$data = [
			'registId' => $registId,
			'temp_id' => $temp_id,
			'creditRegist' => $creditRegist,
		];
	    return $this->fetch('', $data);

	}
	
	/**
     * [payReturnHtml 返回状态显示页面]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function payReturnHtml(){
		Config::set('default_return_type', 'html');
		$code = input('code');
		$msg = input('msg');
		$data = [
				'code' => $code,
				'msg' => $msg,
			];
		return $this->fetch('tshimsg', $data);
	}
	
	
	
	/**
     * [getPlan 获取我的计划]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function getPlan(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		
		//获取当前正在执行的计划任务
		$yes_repay = Db::name('repayProgram')->alias('r')->field('r.*,c.bank_name')->join('credit_card c', 'c.credit_id = r.credit_id', 'LEFT')->where(['r.status'=>['IN','1,2'],'r.user_id'=>$user_id])->select();
		$z_yes_money = 0;
		$z_total_money = 0;
		$yes_credit_id = [];
		foreach ($yes_repay as $k => $v){
			//获取已还款金额
			$yes_money = Db::name('repay_list')->where(['status'=>['IN','2'],'type'=>2,'repay_id'=>$v['pro_id']])->sum('money');
			$z_yes_money =$z_yes_money + $yes_money;
			$yes_repay[$k]['yes_money'] = $yes_money;
			$yes_repay[$k]['no_money'] = $v['total_money'] - $yes_money;
			$yes_repay[$k]['credit_code']  = substr($v['credit_code'],-4);
		    //获取总还款金额
			$z_total_money = $z_total_money + $v['total_money'];
			
			$yes_credit_id[] = $v['credit_id'];
		}
		
		//获取执行失败的计划任务
		$no_repay = Db::name('repayProgram')->alias('r')->field('r.*,c.bank_name')->join('credit_card c', 'c.credit_id = r.credit_id', 'LEFT')->where(['r.status'=>['IN','5'],'r.user_id'=>$user_id])->select();
		
		foreach ($no_repay as $k => $v){
			$yes_money = Db::name('repay_list')->where(['status'=>['IN','2'],'type'=>2,'repay_id'=>$v['pro_id']])->sum('money');
			$no_repay[$k]['yes_money'] = $yes_money;
			$no_repay[$k]['no_money'] = $v['total_money'] - $yes_money;
			$no_repay[$k]['credit_code']  = substr($v['credit_code'],-4);
		}
		
		//获取没有任务的银行卡
		$map['user_id'] = $user_id;
		$map['credit_id'] = ['not in',$yes_credit_id];
		$creditCardMD = new CreditCardModel();
		$creditCard = $creditCardMD->getAll($map);
		
		$rel = [
			'z_total_money' => $z_total_money,
			'z_yes_money' => $z_yes_money,
			'z_no_money' => $z_total_money - $z_yes_money,
			'creditCard' => $creditCard,
			'yes_repay' => $yes_repay,
			'no_repay' => $no_repay,
		];
		 return rel(1,'获取成功',$rel);	
	}
	
	public function getProvince(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		if(!isset($data['current'])) return rel(-1,'参数错误');
		if($data['current'] == 1){
			$data2 = [
				'channel_no'=> 'S20BACB',
				'business_no'=> 'loan_channel',
			];
		}elseif($data['current'] == 0){
			$data2 = [
				'channel_no'=> 'S18HHZTP',
				'business_no'=> 'back_channel',
			];
		}elseif($data['current'] == 2){
			$data2 = [
				'channel_no'=> 'S16DFCC',
				'business_no'=> 'back_channel',
			];
		}
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
		if(!isset($data['current'])) return rel(-1,'参数错误');
		if(!isset($data['province'])) return rel(-1,'参数错误');
		if($data['current'] == 1){
			$data2 = [
				'channel_no'=> 'S20BACB',
				'business_no'=> 'loan_channel',
			];
		}elseif($data['current'] == 0){
			$data2 = [
				'channel_no'=> 'S18HHZTP',
				'business_no'=> 'back_channel',
			];
		}elseif($data['current'] == 2){
			$data2 = [
				'channel_no'=> 'S16DFCC',
				'business_no'=> 'back_channel',
			];
		}
		
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
	
	public function getMacc(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		if(empty($user)) return rel(-1,'该用户信息无效');	//验证该用户是否已经注册
		//if(!isset($data['p_id'])) return rel(-1,'参数错误');
		$data2['p_id'] = '0';
		$outapiLogic=model('common/TdapiLogic','Logic');
		$rel1=$outapiLogic->getMacc($data2);
		if(isset($rel1['Code']) && $rel1['Code'] == '10000' && isset($rel1['Resp_code']) && $rel1['Resp_code'] == '40000'){
			if(!isset($rel1['data'])) return rel(0,'MCC分类获取失败');
			$res = [];
			foreach($rel1['data'] as $k=>$v){
			  $res[$k]['label'] = $v['mcc_name'];
			  $res[$k]['value'] = $v['id'];
			}
		
			 return rel(1,'获取成功',$res);
		}else{
			return rel(0,'MCC分类获取失败');
		}
	
	}
	
}
