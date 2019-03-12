<?php
function rel($code,$msg,$rel=[]){
	return ['code'=>$code,'msg'=>$msg,'result'=>$rel];
}

//日期相差天数
function count_days($a,$b){
    $a_dt = getdate($a);
    $b_dt = getdate($b);
    $a_new = mktime(12, 0, 0, $a_dt['mon'], $a_dt['mday'], $a_dt['year']);
    $b_new = mktime(12, 0, 0, $b_dt['mon'], $b_dt['mday'], $b_dt['year']);
    return round(abs($a_new-$b_new)/86400);
}

//DES加密
function do_mencrypt($string, $key=''){
	$key= !empty($key) ? $key : 'byzkhd2019';
	/*$input = str_replace("\n", "", $input);
	$input = str_replace("\t", "", $input);
	$input = str_replace("\r", "", $input);
	$key = substr(md5($key), 0, 24);
	$td = mcrypt_module_open('tripledes', '', 'ecb', '');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $key, $iv);
	$encrypted_data = mcrypt_generic($td, $input);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return trim(chop(base64_encode($encrypted_data)));*/
	$operation = 'E';
   	$key=md5($key); 
  	$key_length=strlen($key); 
   	$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
  	$string_length=strlen($string); 
  	$rndkey=$box=array(); 
  	$result=''; 
  	for($i=0;$i<=255;$i++){
  		$rndkey[$i]=ord($key[$i%$key_length]); 
    	$box[$i]=$i; 
  	} 
	for($j=$i=0;$i<256;$i++){ 
	    $j=($j+$box[$i]+$rndkey[$i])%256; 
	    $tmp=$box[$i]; 
	    $box[$i]=$box[$j]; 
	    $box[$j]=$tmp; 
	} 
	for($a=$j=$i=0;$i<$string_length;$i++){
	  	$a=($a+1)%256; 
	    $j=($j+$box[$a])%256; 
	    $tmp=$box[$a]; 
	    $box[$a]=$box[$j]; 
	    $box[$j]=$tmp; 
	    $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
	}
	if($operation=='D'){
		if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
			return substr($result,8); 
	    }else{
	    	return''; 
	    } 
	}else{ 
	    return str_replace('=','',base64_encode($result)); 
	} 
}
//DES解密
function do_mdecrypt($string, $key=''){
	$key= !empty($key) ? $key : 'byzkhd2019';
	$operation = 'D';
   $key=md5($key); 
  $key_length=strlen($key); 
   $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
  $string_length=strlen($string); 
  $rndkey=$box=array(); 
  $result=''; 
  for($i=0;$i<=255;$i++){ 
      $rndkey[$i]=ord($key[$i%$key_length]); 
    $box[$i]=$i; 
  } 
  for($j=$i=0;$i<256;$i++){ 
    $j=($j+$box[$i]+$rndkey[$i])%256; 
    $tmp=$box[$i]; 
    $box[$i]=$box[$j]; 
    $box[$j]=$tmp; 
  } 
  for($a=$j=$i=0;$i<$string_length;$i++){ 
    $a=($a+1)%256; 
    $j=($j+$box[$a])%256; 
    $tmp=$box[$a]; 
    $box[$a]=$box[$j]; 
    $box[$j]=$tmp; 
    $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
  } 
  if($operation=='D'){ 
    if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
      return substr($result,8); 
    }else{ 
      return''; 
    } 
  }else{ 
    return str_replace('=','',base64_encode($result)); 
  } 
}


/**
 * 验证身份证
 */
function isIdCard($number) {
	if(strlen($number) != 18) return false;
    //加权因子 
    $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码串 
    $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    //按顺序循环处理前17位 
    $sigma=0;
    for ($i = 0;$i < 17;$i++) { 
        //提取前17位的其中一位，并将变量类型转为实数 
        $b = (int) $number{$i}; 
 
        //提取相应的加权因子 
        $w = $wi[$i]; 
 
        //把从身份证号码中提取的一位数字和加权因子相乘，并累加 
        $sigma += $b * $w; 
    }
    //计算序号 
    $snumber = $sigma % 11; 
 
    //按照序号从校验码串中提取相应的字符。 
    $check_number = $ai[$snumber];
 
    if ($number{17} == $check_number) {
        return true;
    } else {
        return false;
    }
}
/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function check_mobile($mobile){
    if(preg_match('/1[12345789]\d{9}$/',$mobile))
        return true;
    return false;
}


/**
 * 写入文件
 */
function writeF(){
	$num=func_num_args();			//获取传入参数个数
	$filename=func_get_arg(0);		//获取参数的第一个值
	$filename=!empty($filename)?$filename:'demo.txt';
	$str='';
	for($i=1;$i<$num;$i++){
		if(is_array(func_get_arg($i))){
			$str.="======================================\r\n";
			foreach(func_get_arg($i) as $key=>$val){
				$str.='键名：'.$key.'==>'.$val."\r\n";
			}
			$str.="======================================\r\n";
		}elseif(is_object(func_get_arg($i))){
			$str.="**************************************\r\n";
			foreach(func_get_arg($i) as $key=>$val){
				$str.='属性：'.$key.'==>'.$val."\r\n";
			}
			$str.="**************************************\r\n";
		}else{
			$str.=func_get_arg($i)."\r\n";
		}
	}
	return file_put_contents($filename, $str, FILE_APPEND);
}
