<?php

namespace app\admin\controller;
use app\admin\model\MemberGroupModel;
use app\admin\model\AgentGroupModel;
use think\Db;

class Agent extends Base{	
	protected $agent_status = [
		'0' => '禁用',
		'1' => '正常',
	];
	
	protected $account_type = [
		'1' => '直接收益',
		'2' => '间接收益',
		'3' => '提现支出',
		'4' => '提现退回',
		'5' => '推荐收益',
	];
	
	protected $account_sytype = [
		'1' => '收款收益',
		'2' => '还款收益',
		'3' => '垫资收益',
		'4' => '其他收益',
	];
	
	protected $groupid = 4;
	
	protected $withdraw_status = [
		'0' => '申请中',
		'1' => '已通过',
		'2' => '已拒绝',
	];
	//*********************************************代理商*********************************************//
	/**
     * [agent 代理商]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function agent(){
		$key = input('key');
		$group_id = input('group_id');
        $map = [];
        if($key&&$key!==""){
            $map['a.username|a.real_name|a.phone'] = ['like',"%" . $key . "%"];
        }
		$map['a.groupid'] = $this->groupid;//代理商
		
		if($group_id&&$group_id!==""){
			$map['g.group_id'] = $group_id;
		}
		
		$admin = Db::name('admin')->where('id',session('uid'))->find();
		if(!$admin) return $this->error('抱歉，您没有操作权限');
		//获取管理员信息
		if($admin['groupid'] == $this->groupid){
		   $map['g.prev_id'] =  $admin['id'];
		}
		
		$pageSize = config('list_rows');
		$list = Db::name('admin')
			->alias('a')
			->field('a.*,g.*,p.real_name as prev_agent,ag.group_name')
			->join('agent g', 'g.admin_id=a.id', 'LEFT')
			->join('agent_group ag', 'ag.id=g.group_id', 'LEFT')
			->join('admin p', 'p.id=g.prev_id', 'LEFT')
			->where($map)
			->order('a.id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
        //print_r($list);die;
		$group = new AgentGroupModel();
		$data = [
			'val' => $key,	
			'list' => $list,
			'group_id' => $group_id,
			'group' => $group->getGroup(),
			'status' =>$this->agent_status,
		];
		return $this->fetch('', $data);			
	}
	
	
	
    /**
     * [add_agent 添加代理商]
     * @return [type] [description]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function add_agent(){
        if(request()->isAjax()){
            $param = input('post.');
            $password = md5(md5($param['password']) . config('auth_key'));
          	
			$admin = Db::name('admin')->where('id',session('uid'))->find();
			if(!$admin) return json(['code' => 0, 'data' => '', 'msg' => '添加失败']);
			
			$member = Db::name('member')->where(['closed'=>0,'account'=>$param['user_account']])->find();
			if(!$member) return json(['code' => 0, 'data' => '', 'msg' => '关联会员手机号错误']);
			
			$is_agent = Db::name('agent')->where(['user_id'=>$member['id']])->find();
			if($is_agent) return json(['code' => 0, 'data' => '', 'msg' => '关联会员的代理已经存在']);
			
			
			
			$is_admin = Db::name('admin')->where('username',$param['username'])->find();
			if($is_admin) return json(['code' => 0, 'data' => '', 'msg' => '账号已经存在']);
			
			if($param['prev_id']!=''){
				$prev_admin = Db::name('admin')->where(['username'=>$param['prev_id']])->find();
				if(!$prev_admin) return json(['code' => 0, 'data' => '', 'msg' => '上级代理商账号错误']);
				$prev_id = $prev_admin['id'];
			}else{
				$prev_id = 0;
			}
			
			//获取管理员信息
			
			if($admin['groupid'] == $this->groupid){
			   $prev_id = $admin['id'];
			   
			   /*//判断设置的收益不能超过自己的收益
			    $prev_agent = Db::name('agent')->where(array('admin_id'=>$admin['id']))->find();
				if($param['pay_dfee'] > $prev_agent['pay_dfee']){
					return json(['code' => 0, 'data' => '', 'msg' => '收款代付费不能超过自己的'.$prev_agent['pay_dfee'].'元收益']);
				}
				
				if($param['pay_rate'] > $prev_agent['pay_rate']){
					return json(['code' => 0, 'data' => '', 'msg' => '收款收益不能超过自己的'.$prev_agent['pay_rate'].'%收益']);
				}
				
				
				if($param['repay_dfee'] > $prev_agent['repay_dfee']){
					return json(['code' => 0, 'data' => '', 'msg' => '还款代付费不能超过自己的'.$prev_agent['repay_dfee'].'元收益']);
				}
				
				if($param['repay_rate'] > $prev_agent['repay_rate']){
					return json(['code' => 0, 'data' => '', 'msg' => '还款收益不能超过自己的'.$prev_agent['repay_rate'].'%收益']);
				}
				
				
				if($param['dz_dfee'] > $prev_agent['dz_dfee']){
					return json(['code' => 0, 'data' => '', 'msg' => '垫资代付费不能超过自己的'.$prev_agent['dz_dfee'].'元收益']);
				}
				
				if($param['dz_rate'] > $prev_agent['dz_rate']){
					return json(['code' => 0, 'data' => '', 'msg' => '垫资收益不能超过自己的'.$prev_agent['dz_rate'].'%收益']);
				}*/
				
			}
		  	 $data = [
				'real_name' => $param['real_name'],
				'username' => $param['username'],
				'password' => $password,
				'phone' => $param['phone'],
				'groupid' => $this->groupid,
				'status' => $param['status'],
				'create_time' => time(),
			];
            
			$admin_id = Db::name('admin')->insertGetId($data);
			if($admin_id<=0){
				  return json(['code' => 0, 'data' => '', 'msg' => '添加失败']);
			}
			
			//添加权限组
			$accdata = array(
                'uid'=> $admin_id,
                'group_id'=> $this->groupid,
            );
            $group_access = Db::name('auth_group_access')->insert($accdata);
			
			//添加基本信息
			$data2 = [
				'bank_name' => $param['bank_name'],
				'bank_real_name' => $param['bank_real_name'],
				'bank_code' => $param['bank_code'],
				'group_id' => $param['group_id'],
				/*'pay_dfee' => $param['pay_dfee'],
				'pay_rate' => $param['pay_rate'],
				'repay_dfee' => $param['repay_dfee'],
				'repay_rate' => $param['repay_rate'],
				'dz_dfee' => $param['dz_dfee'],
				'dz_rate' => $param['dz_rate'],*/
				'admin_id' => $admin_id,
				'user_id' => $member['id'],
				'prev_id' => $prev_id,
				'create_time' => time(),
			];
			$result =  Db::name('agent')->insert($data2);
			if(false === $result){
				writelog(session('uid'),session('username'),'用户【'.session('username').'】添加代理商失败',2);       
                return json(['code' => 0, 'data' => '', 'msg' => '添加失败']);
            }else{
				//修改以前的用户为自己的代理
				$path = '';
				if($member['path']!=''){
					$path = $member['path'].','.$member['id'];
				}else{
					$path = $member['id'];
				}
				Db::name("member")->where(['agent_id'=>$member['agent_id'],'path'=>['like',$path.'%']])->update(['agent_id'=>$admin_id]);
				
				//分润
				if(isset($param['is_fr']) && $param['is_fr'] == 1 && $prev_id > 0){
					shareProfit($admin_id,$prev_id,$param['group_id']);
				}
				writelog(session('uid'),session('username'),'用户【'.session('username').'】添加代理商成功',1);        
                return json(['code' => 1, 'data' => '', 'msg' => '添加成功']);
            }            
        }
		
		$bank_info = Db::name('bank_info')->where(array('status'=>1))->select();
        $this->assign([
			'bank_info' => $bank_info,
			'groupiddq' => $this->groupid
        ]);
		
		$admin = Db::name('admin')->where('id',session('uid'))->find();
		$this->assign('admindq',$admin);
		
		$group = new AgentGroupModel();
        $this->assign('group',$group->getGroup());
        return $this->fetch();
    }
	
	/**
     * [edit_agent 代理商]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function edit_agent(){
		$admin = Db::name('admin')->where('id',session('uid'))->find();
		if(!$admin) return $this->error('抱歉，您没有操作权限');
		
		
		if(request()->isAjax()){
            $param = input('post.');
			 if(empty($param['password'])){
                unset($param['password']);
				$password = '';
            }else{
                $password = md5(md5($param['password']) . config('auth_key'));
            }
			
			$agent = Db::name('agent')->where(array('admin_id'=>$param['id']))->find();
			
			//获取管理员信息
			if($admin['groupid'] == $this->groupid){
			   if($agent['prev_id'] !=  $admin['id']){
				    return json(['code' => 0, 'data' => '', 'msg' => '抱歉，您没有操作权限']);
			   }
			   
			   /*//判断设置的收益不能超过自己的收益
			    $prev_agent = Db::name('agent')->where(array('admin_id'=>$admin['id']))->find();
				if($param['pay_dfee'] > $prev_agent['pay_dfee']){
					return json(['code' => 0, 'data' => '', 'msg' => '收款代付费不能超过自己的'.$prev_agent['pay_dfee'].'元收益']);
				}
				
				if($param['pay_rate'] > $prev_agent['pay_rate']){
					return json(['code' => 0, 'data' => '', 'msg' => '收款收益不能超过自己的'.$prev_agent['pay_rate'].'%收益']);
				}
				
				
				if($param['repay_dfee'] > $prev_agent['repay_dfee']){
					return json(['code' => 0, 'data' => '', 'msg' => '还款代付费不能超过自己的'.$prev_agent['repay_dfee'].'元收益']);
				}
				
				if($param['repay_rate'] > $prev_agent['repay_rate']){
					return json(['code' => 0, 'data' => '', 'msg' => '还款收益不能超过自己的'.$prev_agent['repay_rate'].'%收益']);
				}
				
				
				if($param['dz_dfee'] > $prev_agent['dz_dfee']){
					return json(['code' => 0, 'data' => '', 'msg' => '垫资代付费不能超过自己的'.$prev_agent['dz_dfee'].'元收益']);
				}
				
				if($param['dz_rate'] > $prev_agent['dz_rate']){
					return json(['code' => 0, 'data' => '', 'msg' => '垫资收益不能超过自己的'.$prev_agent['dz_rate'].'%收益']);
				}*/
				
			   
			}
		
            $data = [
				'real_name' => $param['real_name'],
				'username' => $param['username'],
				'phone' => $param['phone'],
				'status' => $param['status'],
			];
			
			if($password!=''){
				$data['password'] = $password;
			}
            
			Db::name('admin')->where(array('id'=>$param['id']))->update($data);
			
			$data2 = [
				'bank_name' => $param['bank_name'],
				'bank_real_name' => $param['bank_real_name'],
				'bank_code' => $param['bank_code'],
				/*'pay_dfee' => $param['pay_dfee'],
				'pay_rate' => $param['pay_rate'],
				'repay_dfee' => $param['repay_dfee'],
				'repay_rate' => $param['repay_rate'],
				'dz_dfee' => $param['dz_dfee'],
				'dz_rate' => $param['dz_rate'],*/
			];
			if(isset($param['group_id'])){
				$data2['group_id'] = $param['group_id'];
			}
			$result =  Db::name('agent')->where(array('admin_id'=>$param['id']))->update($data2);
			if(false === $result){
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑代理商失败',2);       
                return json(['code' => 0, 'data' => '', 'msg' => '编辑失败']);
            }else{
				writelog(session('uid'),session('username'),'用户【'.session('username').'】编辑代理商成功',1);        
                return json(['code' => 1, 'data' => '', 'msg' => '编辑成功']);
            }
        }
		$id = input('param.id');
		$agent = Db::name('admin')->alias('a')
			->field('a.*,g.*,p.real_name as prev_agent')
			->join('agent g', 'g.admin_id=a.id', 'LEFT')
			->join('admin p', 'p.id=g.prev_id', 'LEFT')->where(array('a.id'=>$id))->find();
		
		//获取管理员信息
		if($admin['groupid'] == $this->groupid){
		   if($agent['prev_id'] !=  $admin['id']){
		   	   return $this->error('抱歉，您没有操作权限');
		   }
		}
		$site = config('siteUrl');
		$agent['shareUrl']  = $site.'/#/?agentId='.$agent['id'];
		
		$bank_info = Db::name('bank_info')->where(array('status'=>1))->select();
        $this->assign([
            'agent' => $agent,
			'bank_info' => $bank_info
        ]);
		
		$group = new AgentGroupModel();
        $this->assign('group',$group->getGroup());
        return $this->fetch();
	
	}
	
	
	/**
     * [agent_account 代理商资金明细]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function agent_account(){
		$id = input('param.id');
		if($id==""){
			$id = session('uid');
		}
		$map['admin_id'] = $id;//代理商
		$type = input('type');
		if($type&&$type!==""){
			$map['type'] = $type;
		}elseif($type==="0"){
			$map['type'] = $type;
		}
		
		$sytype = input('sytype');
		if($sytype&&$sytype!==""){
			$map['sytype'] = $sytype;
		}elseif($type==="0"){
			$map['sytype'] = $sytype;
		}
		
		
		$admin = Db::name('admin')->where('id',session('uid'))->find();
		if(!$admin) return $this->error('抱歉，您没有操作权限');
		
		$agent = Db::name('agent')->where(array('admin_id'=>$id))->find();
			
		//获取管理员信息
		if($admin['groupid'] == $this->groupid){
		   if($agent['prev_id'] !=  $admin['id'] && $id!=session('uid')){
			   return $this->error('抱歉，您没有操作权限');
		   }
		}
		
		$pageSize = config('list_rows');
		$list = Db::name('agent_account_log')
			->where($map)
			->order('log_id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
		$data = [
			'id' => $id,	
			'list' => $list,
			'sea_type' => $type,
			'sea_sytype' => $sytype,
			'account_type' =>$this->account_type,
			'account_sytype' =>$this->account_sytype,
		];
		return $this->fetch('', $data);			
	}
	
	
	
	/**
     * [agent_member 代理商推广会员]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function agent_member(){
		$id = input('param.id');
		if($id==""){
			$id = session('uid');
		}
		$map['agent_id'] = $id;//代理商
		
		$key = input('key');
		if($key&&$key!==""){
            $map['account'] = ['like',"%" . $key . "%"];
        }
		

		$admin = Db::name('admin')->where('id',session('uid'))->find();
		if(!$admin) return $this->error('抱歉，您没有操作权限');
		
		$agent = Db::name('agent')->where(array('admin_id'=>$id))->find();
			
		//获取管理员信息
		if($admin['groupid'] == $this->groupid){
		   if($agent['prev_id'] !=  $admin['id'] && $id!=session('uid')){
			   return $this->error('抱歉，您没有操作权限');
		   }
		}
		
		$pageSize = config('list_rows');
		$list = Db::name('member')
			->field('account,nickname,integral,is_validate,create_time,last_login_time,status,login_num,vip_end_time,group_id')
			->where($map)
			->order('id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
		
		$count = Db::name('member')
			->where($map)
			->count();
		$group = new MemberGroupModel();
		$grouplist = $group->getGroup();
		$grouparray = [];
		foreach ($grouplist as $k => $v){
		 	$grouparray[$v['id']] = $v['group_name'];
		}
		$data = [
			'id' => $id,	
			'list' => $list,
			'val' => $key,
			'count' => $count,
			'group' => $grouparray,
		];
		return $this->fetch('', $data);			
	}
	
	
	//*********************************************提现管理*********************************************//
	/**
	 * 提现列表
	 */
	public function withdraw_list() {
		$key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['m.username|m.real_name|m.phone'] = ['like',"%" . $key . "%"];
        }
		$pageSize = config('list_rows');
		$list = Db::name('agent_withdrawals')
			->alias('w')
			->field('w.*,m.real_name,m.phone,m.username')
			->join('admin m', 'w.admin_id=m.id', 'LEFT')
			->where($map)
			->order('w.id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
		$data = [
			'list' => $list,
			'val' => $key,
			'status' =>$this->withdraw_status,
		];
		return $this->fetch('', $data);

	}
	
	
	
	/**
	 * 提现审核
	 */
	public function withdraw_check() {
		if (request()->isAjax()) {
			$data = input('post.');
			$id = $data['id'];
			$update['status'] = $data['status'];
			$update['remark'] = $data['remark'];
			$agent_withdrawals = Db::name('agent_withdrawals')->where(['id' => $id])->find();
			if(!$agent_withdrawals || $agent_withdrawals['status'] != 0){
				return json(['code' => -1, 'msg' => '操作失败']);
			}
			//拒绝审核
			if (Db::name('agent_withdrawals')->where(['id' => $id])->update($update)) {
				if($data['status'] == 2){
					insertAgentLog([
						'admin_id' => $agent_withdrawals['admin_id'],
						'to_user_id' => 0,
						'money' => $agent_withdrawals['money'],
						'desc' => '提现退回-'.$data['remark'],
						'type' => 4,
						'sytype' => 0,
						'order_no' => $agent_withdrawals['admin_id'].'tx'.$id,
						'order_id' => $id,
					]);
				}else{
					Db::name('agent')->where(array('admin_id'=>$agent_withdrawals['admin_id']))->setInc('w_money',$agent_withdrawals['money']);
				}
				writelog(session('uid'),session('username'),'用户【'.session('username').'】操作代理商提现审核',1);       
				return json(['code' => 1, 'msg' => '操作成功']);
			} else {
				writelog(session('uid'),session('username'),'用户【'.session('username').'】操作代理商提现审核失败',2);       
				return json(['code' => -1, 'msg' => '操作失败']);
			}

		} else {
			//信息
			$id = input('id', 0, 'intval');
			if (!$id) {
				return $this->error('id不能为空');
			}

			$info = Db::name('agent_withdrawals')
				->alias('w')
				->field('w.*,m.real_name,m.phone,m.username')
				->join('admin m', 'w.admin_id=m.id', 'LEFT')
				->where(['w.id' => $id])
				->find();
			if (!$info) {
				return $this->error('提现信息不存在');
			}
			$data = [
				'info' => $info,
			];
			return $this->fetch('', $data);
		}
	}
	
	
	 /**
     * [add_withdraw 代理商提现]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function add_withdraw(){
		//代理商
		$admin = Db::name('admin')->where('id',session('uid'))->find();
		if($admin['groupid'] == $this->groupid){
			$agent = Db::name('agent')->where(array('admin_id'=>$admin['id']))->find();
			if(!$agent) $this->error('抱歉，您没有操作权限');
			$bank_info = Db::name('bank_info')->where(array('id'=>$agent['bank_name']))->find();
			$this->assign([
				'agent' => $agent,
				'bank_info' => $bank_info
			]);
		}else{
			$this->error('抱歉，您没有操作权限');
		}
		
        if(request()->isAjax()){
            $param = input('post.');
			$insert['money'] = $param['money'];
			$insert['admin_id'] = $admin['id'];
			$insert['bank_real_name'] = $agent['bank_real_name'];
			$insert['bank_code'] = $agent['bank_code'];
			$insert['bank_name'] = $agent['bank_name'];
			$insert['create_time'] = time();
			if($agent['bank_code'] =='' || $agent['bank_name']=='' || $agent['bank_real_name']==''){
				return json(['code' => 0, 'msg' => '结算卡信息有误，请联系平台或上级代理修改']);
			}
			
			if($param['money']<100){
				return json(['code' => 0, 'msg' => '提现金额不能少于100元']);
			}
			
			if($param['money']>$agent['money']){
				return json(['code' => 0, 'msg' => '提现金额大于可提现金额']);
			}
			$insert_id = Db::name('agent_withdrawals')->insertGetId($insert);
			if ($insert_id > 0 ) {
				insertAgentLog([
					'admin_id' => $admin['id'],
					'to_user_id' => 0,
					'money' => -$param['money'],
					'desc' => '提现支出',
					'type' => 3,
					'sytype' => 0,
					'order_no' => $admin['id'].'tx'.$insert_id,
					'order_id' => $insert_id,
				]);
				writelog(session('uid'),session('username'),'用户【'.session('username').'】操作提现申请',1);       
				return json(['code' => 1, 'msg' => '操作成功，等待平台打款']);
			} else {
				writelog(session('uid'),session('username'),'用户【'.session('username').'】操作提现申请失败',2);       
				return json(['code' => -1, 'msg' => '操作失败']);
			}
        }
        return $this->fetch();
    }
	
	
	    //*********************************************代理商类型*********************************************//
    /**
     * [group 类型组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function group(){
        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['group_name'] = ['like',"%" . $key . "%"];
        }
        $group = new AgentGroupModel();
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
     * [add_group 添加类型组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function add_group(){
        if(request()->isAjax()){
            $param = input('post.');
            $group = new AgentGroupModel();
            $flag = $group->insertGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }


    /**
     * [edit_group 编辑类型组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function edit_group(){
        $group = new AgentGroupModel();
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
     * [del_group 删除类型组]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function del_group(){
        $id = input('param.id');
        $group = new AgentGroupModel();
        $flag = $group->delGroup($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [group_status 类型组状态]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function group_status(){
        $id=input('param.id');
        $status = Db::name('agent_group')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('agent_group')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        } else {
            $flag = Db::name('agent_group')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    }
}
