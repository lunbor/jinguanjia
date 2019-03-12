<?php
namespace app\api\model;

use think\Model;

class CreditCardModel extends Model
{
    protected $name = 'credit_card';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getAll($map)
    {
		$map['status'] = 1;
		$list = $this->field('safe_code,idcard,bank_id,credit_info,ctime,exp_date,mer_id,remark,user_id,utime',true)->where($map)->order('credit_id desc')->select(); 
		foreach ($list as $k => $v){
			if($v['phone']!=''){
				$list[$k]['phone']  = substr_replace($v['phone'],'****',3,4);
			}
			
			if($v['credit_code']!=''){
				$list[$k]['credit_code']  = ' | 尾号'.substr($v['credit_code'],-4);
			}
			
			if($v['name']!=''){
				$list[$k]['name']  = mb_substr($v['name'],0,1).'**';
			}
			
			$list[$k]['is_repay']  = 1;
			
			if($v['bank_no'] == 'ICBC'){
				$list[$k]['icon']="icon-gongshang";
				$list[$k]['color']="#c7000a";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'ABC'){
				$list[$k]['icon']="icon-nongyeyinhang";
				$list[$k]['color']="#319c8b";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'BOC'){
				$list[$k]['icon']="icon-zhongguoyinhang";
				$list[$k]['color']="#a71e32";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CCB'){
				$list[$k]['icon']="icon-jiansheyinhang";
				$list[$k]['color']="#0066b3";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'COMM'){
				$list[$k]['icon']="icon-jiaotongyinhang";
				$list[$k]['color']="#01367a";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CITIC'){
				$list[$k]['icon']="icon-zhongxinyinhang";
				$list[$k]['color']="#ff2e3c";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CEB'){
				$list[$k]['icon']="icon-guangdayinhang";
				$list[$k]['color']="#943EBD";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'HXB'){
				$list[$k]['icon']="icon-huaxiayinhang";
				$list[$k]['color']="#cc0000";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CMBC'){
				$list[$k]['icon']="icon-minshengyinhang";
				$list[$k]['color']="#2474BB";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'GBD'){
				$list[$k]['icon']="icon-guangfa";
				$list[$k]['color']="#ae0009";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CMB'){
				$list[$k]['icon']="icon-zhaoshang";
				$list[$k]['color']="#b1120d";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CIB'){
				$list[$k]['icon']="icon-xingyeyinhang";
				$list[$k]['color']="#004097";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'SPDB'){
				$list[$k]['icon']="icon-pufa";
				$list[$k]['color']="#003473";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'SPAB'){
				$list[$k]['icon']="icon-pingan";
				$list[$k]['color']="#ea5404";
				$list[$k]['color2']="#dc5005";
			}elseif($v['bank_no'] == 'PSBC'){
				$list[$k]['icon']="icon-youzheng";
				$list[$k]['color']="#00744a";
				$list[$k]['color2']="#dc5005";
			}else{
				$list[$k]['icon']="icon-qiahuihuan";
				$list[$k]['color']="#e50012";
				$list[$k]['color2']="#dc5005";
			}
		}
		return $list;
    }
	
	
	 /**
     * 卡片详情
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
	
	public function getCreditbox($map)
    {
		$map['status'] = 1;
		$info = $this->where($map)->find(); 
		return $info;
		
	}
	 /**
     * 卡片详情
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
	
	public function getCredit($map)
    {
		$map['status'] = 1;
		$v = $this->field('safe_code,idcard,bank_id,credit_info,ctime,exp_date,mer_id,remark,user_id,utime',true)->where($map)->find(); 
		if(!$v){
			return false;
		}
			if($v['phone']!=''){
				$v['phone']  = substr_replace($v['phone'],'****',3,4);
			}
			
			if($v['credit_code']!=''){
				$v['credit_code']  = ' | 尾号'.substr($v['credit_code'],-4);
			}
			
			if($v['name']!=''){
				$v['name']  = mb_substr($v['name'],0,1).'**';
			}
			
			$v['is_repay']  = 1;
			
			if($v['bank_no'] == 'ICBC'){
				$v['icon']="icon-gongshang";
				$v['color']="#c7000a";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'ABC'){
				$v['icon']="icon-nongyeyinhang";
				$v['color']="#319c8b";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'BOC'){
				$v['icon']="icon-zhongguoyinhang";
				$v['color']="#a71e32";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CCB'){
				$v['icon']="icon-jiansheyinhang";
				$v['color']="#0066b3";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'COMM'){
				$v['icon']="icon-jiaotongyinhang";
				$v['color']="#01367a";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CITIC'){
				$v['icon']="icon-zhongxinyinhang";
				$v['color']="#ff2e3c";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CEB'){
				$v['icon']="icon-guangdayinhang";
				$v['color']="#943EBD";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'HXB'){
				$v['icon']="icon-huaxiayinhang";
				$v['color']="#cc0000";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CMBC'){
				$v['icon']="icon-minshengyinhang";
				$v['color']="#2474BB";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'GBD'){
				$v['icon']="icon-guangfa";
				$v['color']="#ae0009";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CMB'){
				$v['icon']="icon-zhaoshang";
				$v['color']="#b1120d";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'CIB'){
				$v['icon']="icon-xingyeyinhang";
				$v['color']="#004097";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'SPDB'){
				$v['icon']="icon-pufa";
				$v['color']="#003473";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'SPAB'){
				$v['icon']="icon-pingan";
				$v['color']="#ea5404";
				$v['color2']="#dc5005";
			}elseif($v['bank_no'] == 'PSBC'){
				$v['icon']="icon-youzheng";
				$v['color']="#00744a";
				$v['color2']="#dc5005";
			}else{
				$v['icon']="icon-qiahuihuan";
				$v['color']="#e50012";
				$v['color2']="#dc5005";
			}
		return $v;
    }
	
	 /**
     * 添加卡片
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function addCredit($map)
    {
		if($map['credit_code']==''){
			return ['code'=>-1,'msg'=>'信用卡号不能为空','result'=>''];
		}
		
		if($map['user_id']==''){
			return ['code'=>-1,'msg'=>'用户信息失效，请重新登录','result'=>''];
		}
		
		$is_bank = $this->where(['credit_code'=>$map['credit_code'],'user_id'=>$map['user_id'],'status'=>1])->find();
		if($is_bank){
			return ['code'=>-1,'msg'=>'信用卡已经绑定，请勿重复绑定','result'=>''];
		}
		
		if($map['bank_name']==''){
			return ['code'=>-1,'msg'=>'请选择发卡银行','result'=>''];
		}

		if($map['safe_code']==''){
			return ['code'=>-1,'msg'=>'请输入CVV2码','result'=>''];
		}
		
		if($map['line_credit']==''){
			return ['code'=>-1,'msg'=>'请输入信用卡额度','result'=>''];
		}
		
		if($map['exp_date']==''){
			return ['code'=>-1,'msg'=>'请输入信用卡有效日期','result'=>''];
		}
		
		if($map['phone']==''){
			return ['code'=>-1,'msg'=>'请输入银行预留手机号','result'=>''];
		}
		
		$map['ctime'] = $map['utime'] = time();
		$rel = $this->insert($map);
		if($rel){
			return ['code'=>1,'msg'=>'信用卡绑定成功','result'=>''];
		}else{
			return ['code'=>-1,'msg'=>'参数有误，添加失败','result'=>''];
		}
	
	}
	
	
	 /**
     * 删除信用卡
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function delCredit($map)
    {
		$rel = $this->where($map)->update(['status'=>0]);
		if($rel){
			return ['code'=>1,'msg'=>'删除成功','result'=>''];
		}else{
			return ['code'=>-1,'msg'=>'参数有误，删除失败','result'=>''];
		}
	
	}
	
	 /**
     * 修改信用卡
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function editCredit($map,$update)
    {
		$rel = $this->where($map)->update($update);
		if($rel){
			return ['code'=>1,'msg'=>'资料修改成功','result'=>''];
		}else{
			return ['code'=>-1,'msg'=>'资料没有进行修改','result'=>''];
		}
	
	}
	
}