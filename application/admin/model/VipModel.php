<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class VipModel extends Model
{
    protected $name = 'vip';   
    protected $autoWriteTimestamp = false;   // 开启自动写入时间戳

    /**
     * 根据条件获取全部数据
     */
    public function getAll($map, $Nowpage, $limits)
    {
        return $this->where($map)->page($Nowpage,$limits)->order('id asc')->select();     
    }


    /**
     * 根据条件获取所有数量
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 编辑信息
     */
    public function editVip($param)
    {
        try{
            $result =  $this->validate('VipValidate')->save($param, ['id' => $param['id']]);
            if(false === $result){
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑VIP费用失败',2);        
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑VIP费用成功',1);   
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取一条信息
     */
    public function getOne($id)
    {
        return $this->where('id', $id)->find();
    }

}