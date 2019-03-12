<?php

namespace app\admin\controller;
use app\admin\model\MemberModel;
use app\admin\model\MemberGroupModel;
use app\admin\model\VipModel;
use app\admin\model\DistributorModel;
use think\Db;

class Member extends Base{
	
	protected $bank_status = [
		'0' => '禁用',
		'1' => '正常',
	];
	
	protected $credit_status = [
		'2' => '禁用',
		'1' => '正常',
		'0' => '禁用',
	];
	
	
	protected $card_status = [
		'0' => '待执行',
		'1' => '申请失败',
		'2' => '申请成功，提现失败',
		'3' => '申请成功，提现待处理',
		'4' => '处理结果成功',
		'5' => '处理结果失败',
	];

	protected $repay_status = [
		'1' => '待执行',
		'2' => '执行中',
		'3' => '全部完成',
		'4' => '全部取消',
		'5' => '失败',
	];

	protected $repay_list_status = [
		'1' => '执行中',
		'2' => '已完成',
		'3' => '已失败',
		'4' => '已取消',
	];

	protected $repay_list_type = [
		'1' => '消费',
		'2' => '还款',
	];
	
    //*********************************************会员组*********************************************//
    /**
     * [group 会员组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function group(){
        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['group_name'] = ['like',"%" . $key . "%"];
        }
        $group = new MemberGroupModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');
        $count = $group->getAllCount($map);         //获取总条数
        $allpage = intval(ceil($count / $limits));  //计算总页面
        $lists = $group->getAll($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('val', $key);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }


    /**
     * [add_group 添加会员组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function add_group(){
        if(request()->isAjax()){
            $param = input('post.');
            $group = new MemberGroupModel();
            $flag = $group->insertGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }


    /**
     * [edit_group 编辑会员组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function edit_group(){
        $group = new MemberGroupModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $group->editGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('group',$group->getOne($id));
        return $this->fetch();
    }


    /**
     * [del_group 删除会员组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function del_group(){
        $id = input('param.id');
        $group = new MemberGroupModel();
        $flag = $group->delGroup($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [group_status 会员组状态]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function group_status(){
        $id=input('param.id');
        $status = Db::name('member_group')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('member_group')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        } else {
            $flag = Db::name('member_group')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    }


    //*********************************************会员列表*********************************************//
    /**
     * 会员列表
     * @author [汤汤] [1582978230@qq.com]
     */
    public function index(){
        $key = input('key');
		$group_id = input('group_id');
		$qd_id = input('qd_id');
        $map['m.closed'] = 0;//0未删除，1已删除
        if($key&&$key!==""){
            $map['m.account|m.nickname|m.mobile'] = ['like',"%" . $key . "%"];
        }
		if($group_id&&$group_id!==""){
			$map['m.group_id'] = $group_id;
		}
		if($qd_id&&$qd_id!==""){
			$map['m.qd_id'] = $qd_id;
		}
        $member = new MemberModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = $member->getAllCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = $member->getMemberByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('val', $key);
		$this->assign('group_id', $group_id);
		$this->assign('qd_id', $qd_id);
        if(input('get.page'))
        {
            return json($lists);
        }
		
		$group = new MemberGroupModel();
		$distributor = new DistributorModel();
        $this->assign([
            'group' => $group->getGroup(),
			'distributor' => $distributor->getDistributor()
        ]);
        return $this->fetch();
    }


    /**
     * 添加会员
     * @author [汤汤] [1582978230@qq.com]
     */
    public function add_member(){
        if(request()->isAjax()){
            $param = input('post.');
            $param['password'] = md5(md5($param['password']) . config('auth_key'));
            $member = new MemberModel();
            $flag = $member->insertMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $group = new MemberGroupModel();
        $this->assign('group',$group->getGroup());
        return $this->fetch();
    }

	
	/**
     * 续费VIP
     * @author [汤汤] [1582978230@qq.com]
     */
    public function vip_member(){
		$member = new MemberModel();
		if(request()->isAjax()){
            $param = input('post.');
			if($param['months']<=0) return json(['code' => 0, 'msg' => '请选择续费日期']);

			$member = $member->getOneMember($param['id']);
			if($member['vip_end_time']!=''){
				$vip_end_time = $member['vip_end_time'];
			}else{
				$vip_end_time = time();
			}
			$data['vip_end_time'] = strtotime("+".$param['months']." months", $vip_end_time);
			//$data['vip_end_time'] = strtotime($param['end_time']);
			$data['id'] = $param['id'];
			$data['group_id'] = 2;
            $flag = $member->editMember($data);
			if($flag['code']==1){
				//vip日志
				$vip_log = array(
					'user_id' => $data['id'],
					'months' => $param['months'],
					'start_time' => $vip_end_time,
					'end_time' => $data['vip_end_time'],
					'create_time' => time(),
				);
				Db::name('vip_log')->insert($vip_log);
			}
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
		$id = input('param.id');
		$member = $member->getOneMember($id);
		if($member['vip_end_time']!=''){
			$member['vip_end_time'] = date('Y-m-d',$member['vip_end_time']);
		}
		$vip_log = Db::name('vip_log')->where(['user_id'=>$id])->select();
		
		$this->assign([
            'member' => $member,
			'vip_log' => $vip_log
        ]);
        return $this->fetch();
	}
	
    /**
     * 编辑会员
     * @author [汤汤] [1582978230@qq.com]
     */
    public function edit_member(){
        $member = new MemberModel();
        if(request()->isAjax()){
            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }

			if(isset($param['invite_account'])){
				//修改推荐人
				if($param['invite_account']!=''){
					if($param['invite_account'] == $param['account']) return json(['code' => 0, 'msg' => '推荐人不能是自己']);
					
					$invite_member = Db::name('member')->alias('im')->where(array('im.account'=>$param['invite_account']))->find();
					if(!$invite_member){
						return json(['code' => 0, 'msg' => '推荐人会员账号不存在']);
					}
					$param['invite_code'] = $param['invite_id'] = $invite_member['id'];
					if($invite_member['path']!=''){
						$param['path'] = $invite_member['path'].','. $invite_member['id'];
					}else{
						$param['path'] = $invite_member['id'];
					}
				}
				unset($param['invite_account']);
			}
			
			//修改代理商
			if($param['agent_account']!=''){
				if($param['agent_account'] == $param['account']) return json(['code' => 0, 'msg' => '代理商不能是自己']);
				$param['agent_id'] = Db::name('member')->alias('am')->join('agent a', 'a.user_id = am.id','LEFT')->where(array('am.account'=>$param['agent_account']))->value('a.admin_id');
				if($param['agent_id']<=0){
					return json(['code' => 0, 'msg' => '代理商会员账号不存在']);
				}
			}else{
				$param['agent_id'] = 0;
			}
			
			unset($param['agent_account']);
            $flag = $member->editMember($param);
			
			if($flag['code']==1){
				if(isset($param['invite_id']) && $param['invite_id'] > 0 ){
					Db::name('member')->where(array('path'=>['like',$param['invite_id'].'%']))->update(array('path'=>['exp',"CONCAT('".$param['path']."',',',path)"]));
				}
			}
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $group = new MemberGroupModel();
		
		$credit_card = Db::name('credit_card')->where(array('user_id'=>$id))->order('status')->select();
		
		$bank_card = Db::name('bank_card')->where(array('user_id'=>$id))->order('status desc')->select();
		
		$member = $member->getOneMember($id);
		$member_invite = '';
		if($member['invite_id'] > 0 ){
			$member_invite = $member->getOneMember($member['invite_id']);
		}
		
		
		$member['agent_account'] = '';
		if($member['agent_id'] > 0 ){
			$member['agent_account'] = Db::name('agent')->alias('a')->join('member am', 'a.user_id = am.id','LEFT')->where(array('a.admin_id'=>$member['agent_id']))->value('am.account');
		}
		
		
        $this->assign([
            'member' => $member,
			'member_invite' => $member_invite,
            'group' => $group->getGroup(),
			'credit_card' => $credit_card,
			'bank_card' => $bank_card,
			'bank_status' => $this->bank_status,
			'credit_status' => $this->credit_status,
        ]);
        return $this->fetch();
    }
	
	
	/**
     * 获取还款列表
     * @author [汤汤] [1582978230@qq.com]
     */
	 public function repay_program_ajax(){
	 
	 	$map['w.user_id'] = input('get.user_id') ? input('get.user_id'):0;
		$Nowpage = input('get.page') ? input('get.page'):1;
		$limits = config('list_rows');// 获取总条数
        $count = Db::name('repay_program')->alias('w')->where($map)->count();//计算总页面
        $allpage = intval(ceil($count / $limits));
		
		$repay_program = Db::name('repay_program')
			->alias('w')
			->field('w.*,m.realname,m.mobile')
			->join('member m', 'w.user_id=m.id', 'LEFT')
			->where($map)
			->page($Nowpage, $limits)
			->order('w.pro_id desc')
			->select();
		 $repay_status = $this->repay_status;
		 /*dump($repay_program);*/
		 foreach ($repay_program as $k => $v){
			$repay_program[$k]['total_money'] = $v['total_money']/100;
			$repay_program[$k]['serve_money'] = $v['serve_money']/100;
			$repay_program[$k]['max_expen'] = $v['max_expen']/100;
			$repay_program[$k]['min_money'] = $v['min_money']/100;
			$repay_program[$k]['day_money'] = $v['day_money']/100;
			$repay_program[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$repay_program[$k]['status'] = $repay_status[$v['status']];
		 }	
		return json(['allpage'=>$allpage,'list'=>$repay_program]);
	 
	 }
	 
	 
	 
	/**
     * 获取收款列表
     * @author [汤汤] [1582978230@qq.com]
     */
	 public function get_money_ajax(){
	 
	 	$map['w.user_id'] = input('get.user_id') ? input('get.user_id'):0;
		$Nowpage = input('get.page') ? input('get.page'):1;
		$limits = config('list_rows');// 获取总条数
        $count = Db::name('get_money')->alias('w')->where($map)->count();//计算总页面
        $allpage = intval(ceil($count / $limits));
		
		$get_money = Db::name('get_money')
			->alias('w')
			->field('w.*,m.realname,m.mobile')
			->join('member m', 'w.user_id=m.id', 'LEFT')
			->where($map)
			->page($Nowpage, $limits)
			->order('w.get_id desc')
			->select();
		 $card_status = $this->card_status;
		 /*dump($repay_program);*/
		 foreach ($get_money as $k => $v){

			//$get_money[$k]['day_money'] = $v['day_money']/100;
			$get_money[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$get_money[$k]['status'] = $card_status[$v['status']];
		 }	
		return json(['allpage'=>$allpage,'list'=>$get_money]);
	 
	 }

    /**
     * 删除会员
     * @author [汤汤] [1582978230@qq.com]
     */
    public function del_member(){
        $id = input('param.id');
        $member = new MemberModel();
        $flag = $member->delMember($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * 会员状态
     * @author [汤汤] [1582978230@qq.com]
     */
    public function member_status(){
        $id = input('param.id');
        $status = Db::name('member')->where('id',$id)->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('member')->where('id',$id)->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        } else {
            $flag = Db::name('member')->where('id',$id)->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }

    }
	
	
	//*********************************************VIP费用*********************************************//
    /**
     * [vip VIP费用]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function vip(){
        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['title'] = ['like',"%" . $key . "%"];
        }
        $vip = new VipModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');
        $count = $vip->getAllCount($map);         //获取总条数
        $allpage = intval(ceil($count / $limits));  //计算总页面
        $lists = $vip->getAll($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('val', $key);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }
	
	 /**
     * [edit_vip 编辑VIP]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function edit_vip(){
        $vip = new VipModel();
        if(request()->isPost()){
            $param = input('post.');
            $flag = $vip->editVip($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('vip',$vip->getOne($id));
        return $this->fetch();
    }

}
