<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class PayAisleModel extends Model
{
    protected $name = 'payAisle';  
    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取列表信息
     */
    public function getPayAisleByWhere($map, $Nowpage, $limits)
    {
        return $this->where($map)->order('block_id asc')->paginate($limits);
    }

    /**
     * 根据搜索条件获取所有的用户数量
     * @param $where
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }


    /**
     * 插入信息
     */
    public function insertPayAisle($param)
    {
        try{
            $result = $this->validate('PayAisleValidate')->allowField(true)->save($param);
            if(false === $result){
				writelog(session('uid'),session('username'),'用户【'.session('username').'】添加通道失败',2);            
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
			    writelog(session('uid'),session('username'),'用户【'.session('username').'】添加通道成功',1);
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑信息
     * @param $param
     */
    public function editPayAisle($param)
    {
        try{
            $result =  $this->validate('PayAisleValidate')->allowField(true)->save($param, ['block_id' => $param['block_id']]);
            if(false === $result){     
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑通道失败',2);               
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑通道成功',1);        
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    /**
     * 根据通道id获取信息
     * @param $block_id
     */
    public function getOnePayAisle($block_id)
    {
        return $this->where('block_id', $block_id)->find();
    }


    /**
     * 删除通道
     * @param $block_id
     */

    public function delPayAisle($block_id)
    {
        try{
            $this->where('block_id', $block_id)->delete();
			writelog(session('uid'),session('username'),'用户【'.session('username').'】删除通道成功',1);   
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


}