<?php
namespace app\api\model;

use think\Model;

class MemberModel extends Model
{
    protected $name = 'member';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getOne($map)
    {
		$map['closed'] = 0;
		$info = $this->field('id,password,card_img_a,card_img_b,create_time,update_time',true)->where($map)->find(); 
		if($info){
			$info['mobile'] = $info['account'] = substr_replace($info['account'],'****',3,4);
			//$info['mobile'] = substr_replace($info['mobile'],'****',3,4);
			if($info['card'] !=''){
				$info['card'] = substr_replace($info['card'],' **** **** ****',3,12);
			}
			
			if($info['realname'] !=''){
				$info['realname']  = mb_substr($info['realname'],0,1).'**';
			}
		}
		return $info;
    }
	
	
	/**
     * 根据条件获取信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getUserID($map,$val='id')
    {
		$map['closed'] = 0;
		$user_id = $this->where($map)->value($val); 
		return $user_id;
    }
	
	
	/**
     * 修改用户信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function edit($map,$data)
    {
		$info = $this->where($map)->update($data); 
		return $info;
    }
}