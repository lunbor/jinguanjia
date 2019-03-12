<?php
use think\Db;
use Aliyun\Core\Config;  
use Aliyun\Core\Profile\DefaultProfile;  
use Aliyun\Core\DefaultAcsClient;  
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest; 

/**
 * 随机订单号
 */
function order_no(){
	return 'HD'.date('YmdHis').rand(100,999).rand(100,999); // 订单编号
}

/**
 * 字符串截取，支持中文和其他编码
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . '...' : $slice;
}



/**
 * 读取配置
 * @return array 
 */
function load_config(){
    $list = Db::name('config')->select();
    $config = [];
    foreach ($list as $k => $v) {
        $config[trim($v['name'])]=$v['value'];
    }

    return $config;
}


/**
* 验证手机号是否正确
* @author honfei
* @param number $mobile
*/
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}


/** 
 * 阿里云云通信发送短息 
 * @param string $mobile    接收手机号 
 * @param string $tplCode   短信模板ID
 * @param array  $tplParam  短信内容
 * @return array 
 */  
function sendMsg($mobile,$tplCode,$tplParam){  
    if( empty($mobile) || empty($tplCode) ) return array('Message'=>'缺少参数','Code'=>'Error');  
    if(!isMobile($mobile)) return array('Message'=>'无效的手机号','Code'=>'Error');  
      
    require_once '../extend/aliyunsms/vendor/autoload.php';  
    Config::load();             //加载区域结点配置   
    $accessKeyId = config('alisms_appkey');  
    $accessKeySecret = config('alisms_appsecret');  
    if( empty($accessKeyId) || empty($accessKeySecret) ) return array('Message'=>'请先在后台配置appkey和appsecret','Code'=>'Error'); 
    $templateParam = $tplParam; //模板变量替换  
	
	//$signName = (empty(config('alisms_signname'))?'阿里大于测试专用':config('alisms_signname'));  
	$signName = config('alisms_signname');
    //短信模板ID 
    $templateCode = $tplCode;   
    //短信API产品名（短信产品名固定，无需修改）  
    $product = "Dysmsapi";  
    //短信API产品域名（接口地址固定，无需修改）  
    $domain = "dysmsapi.aliyuncs.com";  
    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）  
    $region = "cn-hangzhou";     
    // 初始化用户Profile实例  
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);  
    // 增加服务结点  
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);  
    // 初始化AcsClient用于发起请求  
    $acsClient= new DefaultAcsClient($profile);  
    // 初始化SendSmsRequest实例用于设置发送短信的参数  
    $request = new SendSmsRequest();  
    // 必填，设置雉短信接收号码  
    $request->setPhoneNumbers($mobile);  
    // 必填，设置签名名称  
    $request->setSignName($signName);  
    // 必填，设置模板CODE  
    $request->setTemplateCode($templateCode);  
    // 可选，设置模板参数     
    if($templateParam) {
        $request->setTemplateParam(json_encode($templateParam));
    }
    //发起访问请求  
    $acsResponse = $acsClient->getAcsResponse($request);   
    //返回请求结果  
    $result = json_decode(json_encode($acsResponse),true); 

    return $result;  
}  



//生成网址的二维码 返回图片地址
function Qrcode($token, $url, $size = 8){ 
    $md5 = md5($token);
    $dir = date('Ymd'). '/' . substr($md5, 0, 10) . '/';
    $patch = 'qrcode/' . $dir;
    if (!file_exists($patch)){
        mkdir($patch, 0755, true);
    }
    $file = 'qrcode/' . $dir . $md5 . '.png';
    $fileName =  $file;
    if (!file_exists($fileName)) {

        $level = 'L';
        $data = $url;
        QRcode::png($data, $fileName, $level, $size, 2, true);
    }
    return $file;
}



/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name) {
    $result = false;
    if(is_dir($dir_name)){
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DS . $item)) {
                        delete_dir_file($dir_name . DS . $item);
                    } else {
                        unlink($dir_name . DS . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }

    return $result;
}



//时间格式化1
function formatTime($time) {
    $now_time = time();
    $t = $now_time - $time;
    $mon = (int) ($t / (86400 * 30));
    if ($mon >= 1) {
        return '一个月前';
    }
    $day = (int) ($t / 86400);
    if ($day >= 1) {
        return $day . '天前';
    }
    $h = (int) ($t / 3600);
    if ($h >= 1) {
        return $h . '小时前';
    }
    $min = (int) ($t / 60);
    if ($min >= 1) {
        return $min . '分钟前';
    }
    return '刚刚';
}


//时间格式化2
function pincheTime($time) {
     $today  =  strtotime(date('Y-m-d')); //今天零点
      $here   =  (int)(($time - $today)/86400) ; 
      if($here==1){
          return '明天';  
      }
      if($here==2) {
          return '后天';  
      }
      if($here>=3 && $here<7){
          return $here.'天后';  
      }
      if($here>=7 && $here<30){
          return '一周后';  
      }
      if($here>=30 && $here<365){
          return '一个月后';  
      }
      if($here>=365){
          $r = (int)($here/365).'年后'; 
          return   $r;
      }
     return '今天';
}


function getRandomString($len, $chars=null){
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }  
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];  
    }
    return $str;
}


function random_str($length){
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
 
    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++)
    {
        $rand = mt_rand(0, $arr_len-1);
        $str.=$arr[$rand];
    }
 
    return $str;
}


/**
 * 写入提现日志
 */
function insertMoneyLog($data) {
	$get_money_log = array(
		'get_id' => $data['get_id'],
		'msg' => $data['msg'],
		'ctime' => time(),
		'status' => $data['status'],
	);
	Db::name('get_money_log')->insert($get_money_log);
	return true;
}


/**
 * 积分新增
 */
function insertCreditValLog($data) {
	if($data['val']<=0) return;
	$credit_val_log = array(
		'order_no' => $data['order_no'],
		'user_id' => $data['user_id'],
		'form_userid' => $data['form_userid'],
		'ctime' => time(),
		'type' => $data['type'],
		'val' => $data['val'],
	);
	Db::name('credit_val_log')->insert($credit_val_log);
	Db::name('member')->where('id',$data['user_id'])->setInc('integral',$data['val']);
	return true;
}


/**
 * 代理商
 */
function insertAgentLog($data) {
	if($data['money']==0) return;
	$agent_account_log = array(
		'admin_id' => $data['admin_id'],
		'to_user_id' => $data['to_user_id'],
		'money' => $data['money'],
		'change_time' => time(),
		'desc' => $data['desc'],
		'type' => $data['type'],
		'sytype' => $data['sytype'],
		'order_no' => $data['order_no'],
		'order_id' => $data['order_id'],
	);
	Db::name('agent_account_log')->insert($agent_account_log);
	Db::name('agent')->where(array('admin_id'=>$data['admin_id']))->setInc('money',$data['money']);
	if($data['sytype']>0){
		Db::name('agent')->where(array('admin_id'=>$data['admin_id']))->setInc('z_money',$data['money']);
	}
	return true;
}


/**
 * 获取手续费率
 $type 1刷卡,2空卡还款,3垫资还款
 */
function getFee($user_id,$type) {
	$config = load_config();            //加载区域结点配置   
	$res = [];
	$user = Db::name("member")->where(['id'=>$user_id])->find();
	
		if($user && $user['group_id'] == 2){
			//vip
			$res['pay_rate'] = $config['vip_pay_rate'];
			$res['pay_dfee'] = $config['pay_dfee'];
				
			$res['repay_rate'] = $config['vip_rate'];
			$res['repay_dfee'] = $config['dfee'];
				
			$res['dz_rate'] = $config['vip_dz_fee'];
			$res['dz_dfee'] = $config['dz_dfee'];
		}else{
			$res['pay_rate'] = $config['pay_rate'];
			$res['pay_dfee'] = $config['pay_dfee'];
			
			$res['repay_rate'] = $config['rate'];
			$res['repay_dfee'] = $config['dfee'];
			
			$res['dz_rate'] = $config['dz_fee'];
			$res['dz_dfee'] = $config['dz_dfee'];
		}
	//查询是否代理
	$agent = Db::name("agent")->where(['user_id'=>$user_id])->find();
	if($agent){
		//如果是代理商,查看代理类型
		$agent_group = Db::name("agent_group")->where(['id'=>$agent['group_id']])->find();
		if($agent_group){
			if($user && $user['group_id'] == 2 && $config['vip_pay_rate'] < $agent_group['pay_rate']){
				//vip
				$res['pay_rate'] = $config['vip_pay_rate'];
				$res['pay_dfee'] = $config['pay_dfee'];
				
				$res['repay_rate'] = $config['vip_rate'];
				$res['repay_dfee'] = $config['dfee'];
				
				$res['dz_rate'] = $config['vip_dz_fee'];
				$res['dz_dfee'] = $config['dz_dfee'];
			}else{
				$res['pay_rate'] = $agent_group['pay_rate'];
				$res['pay_dfee'] = $agent_group['pay_dfee'];
				
				$res['repay_rate'] = $agent_group['repay_rate'];
				$res['repay_dfee'] = $agent_group['repay_dfee'];
				
				$res['dz_rate'] = $agent_group['dz_rate'];
				$res['dz_dfee'] = $agent_group['dz_dfee'];
			}
		}
	}
	
	$return = [];
	if($type==1){
		//刷卡
		$return['rate'] = $res['pay_rate'];
		$return['dfee'] = $res['pay_dfee'];
	}elseif($type==2){
		//还款
		$return['rate'] = $res['repay_rate'];
		$return['dfee'] = $res['repay_dfee'];
	}elseif($type==3){
		//垫资
		$return['rate'] = $res['dz_rate'];
		$return['dfee'] = $res['dz_dfee'];
	}
	return $return;
}


/**
 * 结算收益
 $type 1刷卡,2空卡还款,3垫资还款
 */
function jiesuanProfit($user_id,$type,$money,$order_no,$order_id) {
	$user = Db::name("member")->where(['id'=>$user_id])->find();
	if($user){
		//是否直推会员收益
		
		if($user['agent_id']>0){
			//判断自己是否代理商
			$yj_profit = 0;
			$user_agent = Db::name("agent")->alias('a')
			->field('a.*,ag.pay_profit,ag.repay_profit,ag.repay_profit_jt,ag.dz_profit,ag.dz_profit_jt')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->where(['a.user_id'=>$user_id])->find();
			if($user_agent && $user_agent['group_id'] > 1){
				//如果是自己代理商，需要减去代理商自己的收益
				if($type == 1){
					$yj_profit = $user_agent['pay_profit'];
				}elseif($type == 2){
					$yj_profit = $user_agent['repay_profit'];
				}elseif($type == 3){
					$yj_profit = $user_agent['dz_profit'];
				}
			}
		
			//代理商
			$agent = Db::name("agent")->where(['admin_id'=>$user['agent_id']])->find();
			if($agent && $agent['user_id'] == $user['invite_id']){
				//直推
				jiesuanAgent($agent['user_id'],$money,$type,$user_id,$order_no,$order_id,1,$yj_profit);
			}elseif($agent){
				jiesuanAgent($agent['user_id'],$money,$type,$user_id,$order_no,$order_id,2,$yj_profit);
			}
		}
		
		//代理分润
		/*if($user['path']!=''){
			$prev_user= Db::name("member")->where(['id'=>['IN',$user['path']]])->order('id desc')->find();
			foreach ($prev_user as $k => $v) {
			 	if($k == 0){
					//一级
					jiesuanAgent($prev_user['id'],$money,$type,$user_id,$order_no,$order_id,1);
				}else{
					jiesuanAgent($prev_user['id'],$money,$type,$user_id,$order_no,$order_id,2);
				}
			}
		}*/
    }
	return true;
}

//收益划账，$is_jt类型(1直接收益,2间接收益,3提现支出)，$type收益类型(1收款收益,2还款收益,3垫资收益)
function jiesuanAgent($agent_user_id,$money,$type,$user_id,$order_no,$order_id,$is_jt,$yj_profit) {
	$agent = Db::name("agent")->alias('a')
			->field('a.*,ag.pay_profit,ag.repay_profit,ag.repay_profit_jt,ag.dz_profit,ag.dz_profit_jt')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->where(['a.user_id'=>$agent_user_id])->find();
	if(!$agent){
		return false;
	}
	if($type == 1){
		$pay_profit = $agent['pay_profit'] - $yj_profit;
		$profit_money = round($money * $pay_profit / 100,2);
		$yj_profit = $agent['pay_profit'];
		$desc = '刷卡分润';
	}elseif($type == 2){
		if($is_jt == 1){
			$repay_profit = $agent['repay_profit'] - $yj_profit;
			$profit_money = round($money * $repay_profit / 100,2);
			$yj_profit = $agent['repay_profit'];
			$desc = '直推还款分润';
		}else{
			$repay_profit_jt = $agent['repay_profit_jt'] - $yj_profit;
			$profit_money = round($money * $repay_profit_jt / 100,2);
			$yj_profit = $agent['repay_profit_jt'];
			$desc = '间推还款分润';
		}
	}elseif($type == 3){
		if($is_jt == 1){
			$dz_profit = $agent['dz_profit'] - $yj_profit;
			$profit_money = round($money * $dz_profit / 100,2);
			$yj_profit = $agent['dz_profit'];
			$desc = '直推垫资分润';
		}else{
			$dz_profit_jt = $agent['dz_profit_jt'] - $yj_profit;
			$profit_money = round($money * $dz_profit_jt / 100,2);
			$yj_profit = $agent['dz_profit_jt'];
			$desc = '间推垫资分润';
		}
	}else{
		return false;
	}
	
	if($profit_money<0){
		//return false;
	}
		if($profit_money > 0){
			insertAgentLog([
				'admin_id' => $agent['admin_id'],
				'to_user_id' => $user_id,
				'money' => $profit_money,
				'desc' => $desc,
				'type' => $is_jt,
				'sytype' => $type,
				'order_no' => $order_no,
				'order_id' => $order_id,
			]); 
		}
		
		if($agent['prev_id']<=0){return true;}
		
		$max_id = Db::name("agent_group")->max('id');
		//判断上级
		if($agent['group_id'] == $max_id){
			//如果是顶级代理商，则上级无收益
			return true;
		}else{
			//上级代理商
			$prev_agent = Db::name("agent")->alias('a')
			->field('a.*,ag.pay_profit,ag.repay_profit,ag.repay_profit_jt,ag.dz_profit,ag.dz_profit_jt')->join('agent_group ag', 'ag.id=a.group_id', 'LEFT')->where(['a.admin_id'=>$agent['prev_id']])->find();
			if($prev_agent){
				if($prev_agent['group_id'] == $agent['group_id']){
					if($agent['group_id'] > 1){//普通代理不给平级分润
						//平级
						$config = load_config();            //加载区域结点配置
						if($config['levelProfit'] > 0){
							$levelProfit = $profit_money * $config['levelProfit'] / 100;
							if($profit_money > 0){
								insertAgentLog([
									'admin_id' => $prev_agent['admin_id'],
									'to_user_id' => $user_id,
									'money' => $levelProfit,
									'desc' => '平级分润',
									'type' => 2,
									'sytype' => $type,
									'order_no' => $order_no,
									'order_id' => $order_id,
								]); 
							}
						}   
					}   
				}else{
					if($prev_agent['group_id'] > $agent['group_id']){	
						//循环拿上级收益
						jiesuanAgent($prev_agent['user_id'],$money,$type,$user_id,$order_no,$order_id,2,$yj_profit);
					}
				}
			}
			
		}
		 
		
		return true;

}

//推荐代理后给上级收益，$prev_id上级代理
function shareProfit($admin_id,$prev_id,$group_id){
	$config = load_config();            //加载区域结点配置
	$prev_agent = Db::name("agent")->where(['admin_id'=>$prev_id])->find();
	if(!$prev_agent) return false;
	$agent_group_fee = Db::name("agent_group")->where(['id'=>$group_id])->value('fee');
	$money = 0;
	if($prev_agent['group_id'] == 2 && $group_id == 2){
			//代理商推荐代理商推荐费
			$money  = round($agent_group_fee * $config['dtjdFee'] / 100 , 2);
			if($prev_agent['prev_id']>0){
				//有上级
				$prev_prev_agent = Db::name("agent")->where(['admin_id'=>$prev_agent['prev_id']])->find();
				if($prev_prev_agent && $prev_prev_agent['group_id'] == 3){
					$money2  = round($agent_group_fee * $config['dtjdYyFee'] / 100 , 2);
					if($money2 > 0){
						insertAgentLog([
							'admin_id' => $prev_prev_agent['admin_id'],
							'to_user_id' => $admin_id,
							'money' => $money2,
							'desc' => '间接推荐费',
							'type' => 5,
							'sytype' => 4,
							'order_no' => 'HD'.time().$prev_id.'tj'.$admin_id,
							'order_id' => $admin_id,
						]);
					} 
				}
			}
	}elseif($prev_agent['group_id'] == 3 && $group_id == 2){
		//运营中心推荐代理商推荐费
		$money  = round($agent_group_fee * $config['ytjdFee'] / 100 , 2);
	}elseif($prev_agent['group_id'] == 3 && $group_id == 3){
		//运营中心推荐代理商推荐费
		$money  = round($agent_group_fee * $config['ytjyFee'] / 100 , 2);
	}
	
			if($money>0){
				insertAgentLog([
					'admin_id' => $prev_agent['admin_id'],
					'to_user_id' => $admin_id,
					'money' => $money,
					'desc' => '推荐费',
					'type' => 5,
					'sytype' => 4,
					'order_no' => 'HD'.time().$prev_id.'tj'.$admin_id,
					'order_id' => $admin_id,
				]); 
			}
	return true;
}