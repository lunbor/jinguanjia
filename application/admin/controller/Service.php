<?php

namespace app\admin\controller;
use app\admin\model\PayAisleModel;
use app\admin\model\DistributorModel;
use app\admin\model\MemberGroupModel;
use think\Db;

class Service extends Base{


	protected $withdraw_status = [
		'0' => '申请中',
		'1' => '已通过',
		'2' => '已拒绝',
	];
	
	protected $distributor_status = [
		'0' => '禁用',
		'1' => '正常',
	];
	
	protected $current_type = [
		'0' => '本金还款',
		'1' => '空卡垫资',
		'2' => '精养卡',
	];
	
	protected $dz_type = [
		'0' => '未垫资',
		'1' => '垫资',
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
		'5' => '已失败',
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

    //*********************************************通道管理*********************************************//
    /**
     * [payAisle 通道管理]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function payAisle(){
        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['keyname'] = ['like',"%" . $key . "%"];
        }
        $payAisle = new PayAisleModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');
        $lists = $payAisle->getPayAisleByWhere($map, $Nowpage, $limits);
		$page = $lists->render();
		$this->assign('page', $page);
		$this->assign('count', $lists->total());
        $this->assign('val', $key);
		$this->assign('list', $lists);
        return $this->fetch();
    }
	
	 /**
     * [payAisle 添加及修改通道管理]
     * @author [汤汤] [1582978230@qq.com]
     */	
	public function editPayTypeBlock(){
		$payAisle = new PayAisleModel();
		if(request()->isGet()){
			if(input('?get.block_id') && !empty(input('block_id'))){		//修改原有标签块
				$block=$payAisle->getOnePayAisle(input('block_id'));
			
				if(input('?get.act') && input('act')=='del'){	//删除标签块
					if(!empty($block['head_pic'])) unlink('uploads/'.$block['head_pic']);
					
					$res = $payAisle->delPayAisle(input('block_id'));
					return json($res);
				}
				
				$block['tit_block']=json_decode($block['tit_block'],true);
				return $this->fetch('',['block'=>$block]);
			}else{						//添加新标签块
				return $this->fetch('',['block'=>[]]);
			}
		}elseif(request()->isPost()){
			if(input('?post.block_id') && !empty(input('block_id'))){		//处理修改的数据
				$data=input('post.');
				$block=$data['block'];
				$block_id=$data['block_id'];
				$old_img=$data['old_img'];
				$head_pic=$data['head_pic'];
				if(!empty($old_img) && $old_img!=$head_pic){
					@unlink('uploads/'.$old_img);
				}
				unset($data['block']);
				unset($data['file']);
				unset($data['old_img']);
				$data['tit_block']=json_encode($block);
			
				$res = $payAisle->editPayAisle($data);
				return json($res);
			}else{							//处理新增的数据
				$data=input('post.');
				$data['tit_block']= !empty($data['block']) ? json_encode($data['block']) : '';
				
				unset($data['block']);
				unset($data['file']);
				unset($data['old_img']);
				$res = $payAisle->insertPayAisle($data);
				return json($res);
			}
		}
	}
	
	 //*********************************************收款订单*********************************************//
    /**
     * [payAisle 收款订单]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function credit_card_vocher(){
		$key = input('key');
		$qd_id = input('qd_id');
		$status = input('status');
        $map = [];
        if($key&&$key!==""){
            $map['m.mobile'] = ['like',"%" . $key . "%"];
        }
		if($qd_id&&$qd_id!==""){
			$map['w.qd_id'] = $qd_id;
		}
		if($status&&$status!==""){
			$map['w.status'] = $status;
		}elseif($status==="0"){
			$map['w.status'] = $status;
		}
		$pageSize = config('list_rows');
		$list = Db::name('get_money')
			->alias('w')
			->field('w.*,m.realname,m.mobile')
			->join('member m', 'w.user_id=m.id', 'LEFT')
			->where($map)
			->order('w.get_id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
			
		$distributor = new DistributorModel();
		$data = [
			'list' => $list,
			'qd_id' => $qd_id,
			'val' => $key,
			'sea_status' => $status,
			'status' =>$this->card_status,
			'distributor' => $distributor->getDistributor()
		];
		return $this->fetch('', $data);		
	}
	
	//*********************************************还款计划*********************************************//
    /**
     * [payAisle 还款计划]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function repay_program(){
		$key = input('key');
		$qd_id = input('qd_id');
		$status = input('status');
		$current = input('current');
        $map = [];
        if($key&&$key!==""){
            $map['m.mobile'] = ['like',"%" . $key . "%"];
        }
		if($qd_id&&$qd_id!==""){
			$map['w.qd_id'] = $qd_id;
		}
		if($status&&$status!==""){
			$map['w.status'] = $status;
		}elseif($status==="0"){
			$map['w.status'] = $status;
		}
		
		if($current&&$current!==""){
			$map['w.current'] = $current;
		}elseif($current==="0"){
			$map['w.current'] = $current;
		}
		
		$pageSize = config('list_rows');
		$list = Db::name('repay_program')
			->alias('w')
			->field('w.*,m.realname,m.mobile')
			->join('member m', 'w.user_id=m.id', 'LEFT')
			->where($map)
			->order('w.pro_id desc')
			->paginate($pageSize,false,
			[
			   'type'     => 'Bootstrap',
			   'var_page' => 'page',
				//使用jqery 无刷新分页
				//'path'=> '/',
				//'query' => input(),
			    'path'=>'javascript:AjaxPage([PAGE]);',
			   //第一种方法，使用数组方式传入参数
				// 'query' => ['status'=>$status],
			// 第二种方法，使用函数助手传入参数
			   //'query' => request()->param(),
			 ]
			);
		$distributor = new DistributorModel();
		$data = [
			'list' => $list,
			'qd_id' => $qd_id,
			'val' => $key,
			'sea_status' => $status,
			'sea_current' => $current,
			'status' =>$this->repay_status,
			'distributor' => $distributor->getDistributor(),
			'currentType' => $this->current_type,
			'dzType' => $this->dz_type,
		];
		return $this->fetch('', $data);			
	}

	/**
     * [payAisle 还款详情]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function repay_list(){
		$map = [];
		$map['repay_id'] = input('id');
		$pageSize = config('list_rows');
		$list = Db::name('repay_list')
			->where($map)
			->order('id asc')
			->paginate($pageSize);
		$data = [
			'list' => $list,
			'status' =>$this->repay_list_status,
			'type' =>$this->repay_list_type,
		];
		return $this->fetch('', $data);		
	}
	
	//*********************************************渠道商*********************************************//
	/**
     * [payAisle 渠道商]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function distributor(){
		$key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['phone|account|nickname'] = ['like',"%" . $key . "%"];
        }
		$pageSize = config('list_rows');
		$list = Db::name('distributor')
			->where($map)
			->order('qd_id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
		$data = [
			'val' => $key,	
			'list' => $list,
			'status' =>$this->distributor_status,
		];
		return $this->fetch('', $data);			
	}
	
	
	
	/**
     * [payAisle 添加渠道商]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function add_distributor(){
		if(request()->isAjax()){
            $param = input('post.');
			$param['password'] = md5(md5($param['password']) . config('auth_key'));
            $distributor = new DistributorModel();
            $flag = $distributor->insertDistributor($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();	
	}
	
	/**
     * 编辑渠道商
     * @author [汤汤] [1582978230@qq.com]
     */
    public function edit_distributor(){
        $distributor = new DistributorModel();
        if(request()->isAjax()){
            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            $flag = $distributor->editDistributor($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
		
		$id = input('param.id');
		
		$site = config('siteUrl');
		$shareUrl  = $site.'/#/?qdId='.$id;
		$bank_info = Db::name('bank_info')->where(array('status'=>1))->select();
       
        $this->assign([
			'shareUrl' => $shareUrl,
            'distributor' => $distributor->getOne($id),
			'bank_info' => $bank_info
        ]);
        return $this->fetch();
    }
	
	
	/**
     * [distributor_member 渠道商推广会员]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function distributor_member(){
		$id = input('param.id');
		$map['qd_id'] = $id;//代理商
		
		$key = input('key');
		if($key&&$key!==""){
            $map['account'] = ['like',"%" . $key . "%"];
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
	
	//*********************************************垫资警报*********************************************//
	/**
     * [repay_warn 垫资警报]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function repay_warn(){
		$key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['l.order_no'] = ['like',"%" . $key . "%"];
        }
		$map['p.is_dz'] = 1;
		$map['l.type'] = 1;
		$map['l.status'] = 3;
		$map['l.a_money'] = ['gt',0];
		//SELECT p.is_dz,l.* FROM `think_repay_list` as l left join think_repay_program as p on p.pro_id = l.repay_id  where p.is_dz=1 and  l.type=1 and l.status=3 ;
		$pageSize = config('list_rows');
		$list = Db::name('repay_list')
			->alias('l')
			->field('l.*,m.realname,m.mobile,cc.bank_name,p.credit_code,tm.realname as tj_name,m.invite_id,tm.mobile as tj_mobile')
			->join('member m', 'l.user_id=m.id', 'LEFT')
			->join('member tm', 'm.invite_id=tm.id', 'LEFT')
			->join('repay_program p','p.pro_id = l.repay_id','LEFT')
			->join('credit_card cc', 'cc.credit_id=p.credit_id', 'LEFT')
			->where($map)
			->order('l.list_id desc')
			->paginate($pageSize,false,['path'=>'javascript:AjaxPage([PAGE]);',]);
		$data = [
			'val' => $key,	
			'list' => $list,
			'status' =>$this->repay_list_status,
			'type' =>$this->repay_list_type,
		];
		return $this->fetch('', $data);			
	}
	
	/**
     * [repay_afresh 重新扣款]
     * @author [汤汤] [1582978230@qq.com]
     */
	public function repay_afresh(){
	 	$list_id = input('id');
		if($list_id<=0) return $this->error('参数错误');
		$map['list_id'] = $list_id;
		$map['type'] = 1;
		$map['status'] = 3;
		$map['a_money'] = ['gt',0];
		$repay_list = Db::name('repay_list')->where($map)->find();
		if(!$repay_list) return $this->error('状态不允许扣款');
		
		$update = [
				'resp_msg' => NULL,
				'resp' => NULL,
				'order_no' => order_no(),
				'status' => 1,
		];
		Db::name('repayList')->where(['list_id'=>$repay_list['list_id']])->update($update);
		
		Db::name('repay_program')->where(['pro_id'=>$repay_list['repay_id']])->update(['status'=>2]);
		
		return $this->success('操作成功！');
		
	}
	
	
}
