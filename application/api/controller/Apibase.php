<?php
namespace app\api\controller;
use app\api\model\MemberModel;
use think\Controller;
use think\Db;
class Apibase extends Controller{
	protected $user_id=0;
	protected $_user=[];
	protected $_data=[];		//接口数据

	public function _initialize(){
		header("Content-Type:text/html; charset=utf-8");	//设置程序编码
		header('Access-Control-Allow-Origin:*');  			// 指定允许其他域名访问
		header('Access-Control-Allow-Methods:GET,POST,OPTIONS');  	// 响应类型  
		header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization,userId');
		header('Access-Control-Expose-Headers:Authorization,userId');
		header('Access-Control-Allow-Credentials: true');
		if(request()->isOptions()){
			header("HTTP/1.1 200 Ok");
			exit;
		}
		
        $config = load_config();

        config($config);
		
		$this->_doData();	//参数处理
	}
    //作用：1、获取用户访问的模块、控制器、操作，2、将传递过来的json数据转化成数组，3、获取用户ip
	private function _doData(){
		$a=strtoupper(request()->action());	//获取访问的操作
		$minActList=[						//需要检测cookie中是否存在手机号码的操作列表 
				'PUTREAL',			//实名认证
				'LOGINPWD',			//设置密码（重置密码）
				'ADDBANK',			//绑定银行卡号
				'ADDCREDIT',		//绑定信用卡
				'SEEBANK',			//查看银行卡列表
				'SEECREDIT',		//查看信用卡列表
				'EDITCREDIT',		//修改信用卡账单与还款日
				'PRODUCEPROGRAM',	//创建任务
				'EXECUTEPROGRAM',	//执行任务
			];
		
		$json=$this->request->post('data','','htmlspecialchars_decode');
		$data=json_decode($json,true);
		$this->_data=$data;
		
			
		//验证登录状态
		if(in_array($a, $minActList) && !request()->isOptions() && !input('?server.HTTP_USERID') && !isset($data['openid'])){
			header("HTTP/1.1 401 Nologin first login");
			echo json_encode(['code' => -99, 'msg' => '缺少用户主键信息，请重新登录', 'result' => '']);
	        exit;
		}
		
		$userMD = new MemberModel();
		if(input('?server.HTTP_USERID')){
//			$key=config('byzk.KEY');		//后期由前段传递
			$user_id=do_mdecrypt(input('server.HTTP_USERID'));
			$this->_user=$userMD->getOne(['id'	=> $user_id]);
			if(!empty($this->_user)) $this->user_id=$user_id;
		}elseif(isset($data['openid'])){
			$user_id=do_mdecrypt($data['openid']);
			$this->_user=$userMD->getOne(['id'	=> $user_id]);
			if(!empty($this->_user)) $this->user_id=$user_id;
		}
	}
}