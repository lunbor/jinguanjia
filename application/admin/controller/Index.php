<?php

namespace app\admin\controller;
use think\Config;
use think\Loader;
use think\Db;

class Index extends Base{
	protected $groupid = 4;
	
    public function index(){
        return $this->fetch('/index');
    }


    /**
     * [indexPage 后台首页]
     * @return [type] [description]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function indexPage(){
		//代理商
		$admin = Db::name('admin')->where('id',session('uid'))->find();
	
          //今日新增会员
        $today = strtotime(date('Y-m-d 00:00:00'));//今天开始日期     
        $map['create_time'] = array('egt', $today);
		if($admin['groupid'] == $this->groupid){
		   $map['agent_id'] =  $admin['id'];
		}
        $member = Db::name('member')->where($map)->count();
        $this->assign('member', $member);
		
		$map0 = [];
		if($admin['groupid'] == $this->groupid){
		   $map0['agent_id'] =  $admin['id'];
		}
        $member_z = Db::name('member')->where($map0)->count();
        $this->assign('member_z', $member_z);

        $today = strtotime(date('Y-m-d 00:00:00'));//今天开始日期     
        $map8['last_login_time'] = array('egt', $today);
		if($admin['groupid'] == $this->groupid){
		   $map8['agent_id'] =  $admin['id'];
		}
        $member_hy = Db::name('member')->where($map8)->count();
        $this->assign('member_hy', $member_hy);
        


        $today = strtotime(date('Y-m-d 00:00:00'));//今天开始日期     
        $map2['ctime'] = array('egt', $today);
        $map2['status'] = 4;
		if($admin['groupid'] == $this->groupid){
		   $map2['admin_id'] =  $admin['id'];
		}
        $get_money = Db::name('get_money')->where($map2)->sum("money");
        $this->assign('get_money', $get_money);

        $today = strtotime(date('Y-m-d 00:00:00'));//今天开始日期     
        $map3['exetime'] = array('egt', $today);
        $map3['status'] = 2;
		if($admin['groupid'] == $this->groupid){
		   $map3['agent_id'] =  $admin['id'];
		}
        $repay_list = Db::name('repay_list')->where($map3)->sum("money");
        $this->assign('repay_list', $repay_list/100);




        $map4['status'] = 4;
		if($admin['groupid'] == $this->groupid){
		   $map4['admin_id'] =  $admin['id'];
		}
        $get_money_z = Db::name('get_money')->where($map4)->sum("money");
        $this->assign('get_money_z', $get_money_z);

        $map5['status'] = 2;
		if($admin['groupid'] == $this->groupid){
		   $map5['agent_id'] =  $admin['id'];
		}
        $repay_list_z = Db::name('repay_list')->where($map5)->sum("money");
        $this->assign('repay_list_z', $repay_list_z/100);

        $info = array(
            'web_server' => $_SERVER['SERVER_SOFTWARE'],
            'onload'     => ini_get('upload_max_filesize'),
            'think_v'    => THINK_VERSION,
            'phpversion' => phpversion(),
        );
		
		if($admin['groupid'] == $this->groupid){
			$agent = Db::name('agent')->where(array('admin_id'=>$admin['id']))->find();
			$this->assign('is_agent', $agent);
		}else{
			$this->assign('is_agent', 0);
		}

        $this->assign('info',$info);
        return $this->fetch('index');
    }



    /**
     * [userEdit 修改密码]
     * @return [type] [description]
     * @author [汤汤] [1582978230@qq.com]
     */
    public function editpwd(){
        if(request()->isAjax()){
            $param = input('post.');
            $user=Db::name('admin')->where('id='.session('uid'))->find();
            if(md5(md5($param['old_password']) . config('auth_key'))!=$user['password']){
               return json(['code' => -1, 'url' => '', 'msg' => '旧密码错误']);
            }else{
                $pwd['password']=md5(md5($param['password']) . config('auth_key'));
                Db::name('admin')->where('id='.$user['id'])->update($pwd);
                session(null);
                cache('db_config_data',null);//清除缓存中网站配置信息
                return json(['code' => 1, 'url' => 'index/index', 'msg' => '密码修改成功']);
            }
        }
        return $this->fetch();
    }


    /**
     * 清除缓存
     */
    public function clear() {
        if (delete_dir_file(CACHE_PATH) && delete_dir_file(TEMP_PATH)) {
            return json(['code' => 1, 'msg' => '清除缓存成功']);
        } else {
            return json(['code' => 0, 'msg' => '清除缓存失败']);
        }
    }

}
