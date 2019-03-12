<?php
namespace app\api\model;

use think\Model;

class BankCardModel extends Model
{
    protected $name = 'bank_card';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getAll($map)
    {
		$map['status'] = 1;
		$list = $this->field('idcard,bank_id,bank_info,ctime,mer_id,remark,user_id,utime',true)->where($map)->order('bankcard_id desc')->select(); 
		foreach ($list as $k => $v){
			if($v['phone']!=''){
				$list[$k]['phone']  = substr_replace($v['phone'],'****',3,4);
			}
			
			if($v['bank_code']!=''){
				$list[$k]['bank_code']  = ''.substr($v['bank_code'],-4);
			}
			
			if($v['name']!=''){
				$list[$k]['name']  = mb_substr($v['name'],0,1).'**';
			}
			
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
	
	public function getBankbox($map)
    {
		$map['status'] = 1;
		$info = $this->where($map)->find(); 
		return $info;
		
	}
	
	 /**
     * 根据条件获取信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getBank($map)
    {
		$map['status'] = 1;
		$v = $this->field('idcard,bank_id,bank_info,ctime,mer_id,remark,user_id,utime',true)->where($map)->find(); 
		if(!$v){
			return false;
		}
			if($v['phone']!=''){
				$v['phone']  = substr_replace($v['phone'],'****',3,4);
			}
			
			if($v['bank_code']!=''){
				$v['bank_code']  = ''.substr($v['bank_code'],-4);
			}
			
			if($v['name']!=''){
				$v['name']  = mb_substr($v['name'],0,1).'**';
			}
			
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
    public function addBank($map)
    {
		if($map['bank_code']==''){
			return ['code'=>-1,'msg'=>'银行卡号不能为空','result'=>''];
		}
		
		if($map['user_id']==''){
			return ['code'=>-1,'msg'=>'用户信息失效，请重新登录','result'=>''];
		}
		
		$is_bank = $this->where(['bank_code'=>$map['bank_code'],'user_id'=>$map['user_id'],'status'=>1])->find();
		if($is_bank){
			return ['code'=>-1,'msg'=>'储蓄卡已经绑定，请勿重复绑定','result'=>''];
		}
		
		if($map['bank_name']==''){
			return ['code'=>-1,'msg'=>'请选择发卡银行','result'=>''];
		}

		if($map['city']==''){
			return ['code'=>-1,'msg'=>'请选择发卡省市','result'=>''];
		}
		
		if($map['bank_child']==''){
			return ['code'=>-1,'msg'=>'请输入开户支行名称','result'=>''];
		}
		
		if($map['phone']==''){
			return ['code'=>-1,'msg'=>'请输入银行预留手机号','result'=>''];
		}
		
		$strArray = explode("-",$map['address']); 
		if(is_array($strArray) &&  count($strArray)==3){
			$map['pro_name'] = $strArray[0];
			$map['city_name'] = $strArray[1];
			$map['area_name'] = $strArray[2];
			
			$map['provin'] = str_pad($map['provin'],6,"0",STR_PAD_RIGHT);
			$map['city'] = str_pad($map['city'],6,"0",STR_PAD_RIGHT);
			$map['area'] = str_pad($map['area'],6,"0",STR_PAD_RIGHT);
		}
		$map['ctime'] = $map['utime'] = time();
		
		/*$data = [
			'user_id'=>$map['user_id'],
			'phone'=>$map['phone'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
			'name'=>$map['name'],
		];*/
		$rel = $this->insert($map);
		if($rel){
			return ['code'=>1,'msg'=>'储蓄卡绑定成功','result'=>''];
		}else{
			return ['code'=>-1,'msg'=>'参数有误，添加失败','result'=>''];
		}
	
	}
	
	 /**
     * 删除储蓄卡
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function delBank($map)
    {
		$rel = $this->where($map)->update(['status'=>0]);
		if($rel){
			return ['code'=>1,'msg'=>'删除成功','result'=>''];
		}else{
			return ['code'=>-1,'msg'=>'参数有误，删除失败','result'=>''];
		}
	
	}
}