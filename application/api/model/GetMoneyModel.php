<?php
namespace app\api\model;

use think\Model;

class GetMoneyModel extends Model
{
    protected $name = 'getMoney';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    /**
     * 根据条件获取全部数据
     */
    public function getAll($map, $Nowpage, $limits)
    {
        return $this->where($map)->page($Nowpage,$limits)->order('get_id desc')->select();     
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