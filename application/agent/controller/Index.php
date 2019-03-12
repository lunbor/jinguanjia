<?php
namespace app\qudao\controller;

use think\Controller;
use think\Db;
use org\Verify;

class Index extends Controller{

    public function login()
    {
        return $this->fetch('login');
    }
	
	
	/**
     * 登录操作
     * @return
     */
    public function doLogin(){
        $username = input("param.username");
        $password = input("param.password");
		
        $code = input("param.code"); 
		
		if (!$username) {
			return json(['code' => -5, 'url' => '', 'msg' => '请输入账号']);
		}
		
		
		if (!$password) {
			return json(['code' => -5, 'url' => '', 'msg' => '请输入密码']);
		}
		
        $verify = new Verify();

		if (!$code) {
			return json(['code' => -4, 'url' => '', 'msg' => '请输入验证码']);
		}
		if (!$verify->check($code)) {
			return json(['code' => -4, 'url' => '', 'msg' => '验证码错误']);
		}


        $hasUser = Db::name('distributor')->where('account', $username)->find();
        if(empty($hasUser)){
            return json(['code' => -1, 'url' => '', 'msg' => '渠道商不存在']);
        }

        if(md5(md5($password) . config('auth_key')) != $hasUser['password']){
            return json(['code' => -2, 'url' => '', 'msg' => '账号或密码错误']);
        }

        if(1 != $hasUser['status']){
            return json(['code' => -6, 'url' => '', 'msg' => '该账号被禁用']);
        }

        session('qd_id', $hasUser['qd_id']);         //渠道ID

        //更新渠道商状态
        $param = [
            'login_num' => $hasUser['login_num'] + 1,
            'last_login_ip' => request()->ip(),
            'last_login_time' => time(),
            'token' => md5($hasUser['account'] . $hasUser['password'])
        ];

        Db::name('distributor')->where('qd_id', $hasUser['qd_id'])->update($param);

        return json(['code' => 1, 'url' => url('info/nxInfo'), 'msg' => '登录成功！']);
    }
	
	
	 /**
     * 验证码
     * @return
     */
    public function checkVerify(){
        $verify = new Verify();
        $verify->imageH = 49;
        $verify->imageW = 140;
		    $verify->codeSet = '0123456789';
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontSize = 18;
        return $verify->entry();
    }
	
	
	/**
     * 退出登录
     * @return
     */
    public function loginOut(){
        session(null);
        $this->redirect('index/login');
    }
}
