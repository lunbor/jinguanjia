<?php
namespace app\api\controller;
use think\Db;
use think\Page;
class Message extends Apibase{
   /**
    * 消息通知接口
    * @param $page int 页码 默认第一页 可选
    * @param $pageSize int 大小 默认10条 可选 
    */
    public function getMessage(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
			$Nowpage = $data['page'] ? $data['page']:1;
			$message_List= DB::name('message')
			->alias('m')
            ->field("m.admin_id as rec_id,m.message_id, m.message, m.data,DATE_FORMAT(FROM_UNIXTIME(m.send_time),'%Y-%m-%d %H:%i') as  send_time")
           ->where("(m.user_id = 0 and m.category = 0) or (m.user_id = '".$user_id."' and m.category = 1)")
			->page($Nowpage,10)
			->order('send_time desc')
            ->select();
		    return rel(1, '获取数据成功', ['list'=>$message_List]);
		}else{
			return rel(-1,'用户不存在',$rel);
		}
   }
   
   public function getMessageInfo(){
		$data=$this->_data;
		$user=$this->_user;
		$user_id=$this->user_id;
		$rel = [];
		if($user){
			if(!isset($data['m_id']) ||  $data['m_id']<=0){return rel(-1,'参数无效');	}
			$message = DB::name('message')
			->alias('m')
            ->field("m.admin_id as rec_id,m.message_id, m.message, m.data,DATE_FORMAT(FROM_UNIXTIME(m.send_time),'%Y年%m月%d日 %H:%i') as  send_time")
            ->where("(m.user_id = 0 and m.category = 0 and m.message_id = '".$data['m_id']."') or (m.user_id = '".$user_id."' and m.category = 1 and m.message_id = '".$data['m_id']."')")
			->find();
			if($message){
		    	return rel(1, '获取数据成功', ['article'=>$message]);
			}else{
				return rel(-1, '消息已经不存在');
			}
		}else{
			return rel(-1,'用户不存在',$rel);
		}
   }
}