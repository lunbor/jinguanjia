<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class DistributorModel extends Model
{
    protected $name = 'distributor';   
    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据条件获取全部数据
     */
    public function getAll($map, $Nowpage, $limits)
    {
        return $this->where($map)->page($Nowpage,$limits)->order('qd_id asc')->select();     
    }


    /**
     * 根据条件获取所有数量
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 获取所有的会员组信息
     */ 
    public function getDistributor()
    {
        return $this->select();
    }


    /**
     * 插入信息
     */
    public function insertDistributor($param)
    {
        try{
            $result =  $this->validate('DistributorValidate')->save($param);
            if(false === $result){
				writelog(session('uid'),session('username'),'用户【'.session('username').'】添加渠道商失败',2);    
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
				writelog(session('uid'),session('username'),'用户【'.session('username').'】添加渠道商成功',1);
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑信息
     */
    public function editDistributor($param)
    {
        try{
            $result =  $this->validate('DistributorValidate')->save($param, ['qd_id' => $param['qd_id']]);
            if(false === $result){
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑渠道商失败',2);       
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑渠道商成功',1);        
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取一条信息
     */
    public function getOne($qd_id)
    {
        return $this->where('qd_id', $qd_id)->find();
    }


    /**
     * 删除信息
     */
    public function delDistributor($qd_id)
    {
        try{
            $this->where('qd_id', $qd_id)->delete();
			writelog(session('uid'),session('username'),'用户【'.session('username').'】删除渠道商成功',1);   
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}