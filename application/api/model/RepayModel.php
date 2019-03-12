<?php
namespace app\api\model;

use think\Model;

class RepayModel extends Model
{
    protected $name = 'repayProgram';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getAll($map)
    {
		$list = $this->where($map)->order('pro_id desc')->select(); 
		return $list;
    }
	
	
	/**
     * 根据条件获取信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getOne($map)
    {
		$info = $this->where($map)->find(); 
		return $info;
    }
	
}