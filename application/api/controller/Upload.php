<?php
namespace app\api\controller;
use think\Controller;
use app\api\model\MemberModel;
use think\File;
use think\Db;
use think\Request;
use lib\Curl;
class Upload extends Apibase{
	//图片上传
    /*public function upload(){
    	$path=!empty(input('path')) ? input('path') : 'images';
       $file = request()->file('file');
       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/'.$path);
       if($info){
            echo $path. DS .$info->getSaveName();
        }else{
            echo $file->getError();
        }
    }*/
	
	    //会员头像上传
    public function uploadcard(){
	   $user_id=$this->user_id;
	   $data=$this->_data;
	   if($user_id <= 0){
	   	   return rel(-1,'登录信息失效','');
	   }
	   try{
		   $file = request()->file('file');
		   if(!$file){
		   		return rel(-1,'图像无法识别','');
		   }
		   $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/card/'.$user_id.'');
			if($info){
				$card_img = $info->getSaveName();	
				
				if(isset($data['side'])){
					$imgurl = ROOT_PATH . 'public' . DS . 'uploads/card/'.$user_id.'/'.$card_img;
					$imgurl2 = ROOT_PATH . 'public' . DS . 'uploads/card/'.$user_id.'/'.$card_img;
					//$this->compressedImage($imgurl,$imgurl2);
					$rel['juhedata'] = $this->juheIdimage($imgurl2,$data['side']);
					//$rel['juhedata'] =  '';
				    if(is_array($rel['juhedata'])){
						if($rel['juhedata']['error_code'] == 0 || $rel['juhedata']['error_code'] == 228706){
							if($data['side'] == 'front' && (!isset($rel['juhedata']['result']['realname']) || $rel['juhedata']['result']['realname'] =='')){
								 return rel(-1,'身份证正面识别失败,请上传清晰的照片');
							}
							
							
							
							if($data['side'] == 'back' && (!isset($rel['juhedata']['result']['end']) || $rel['juhedata']['result']['end'] =='')){
								 return rel(-1,'身份证反面识别失败,请上传清晰的照片');
							}
							
							//会计费
							$insert=[
								'user_id'		=> $user_id,
								'side'	=> $data['side'],
								'juhedata'		=> json_encode($rel['juhedata']),
								'ctime'			=> time()
							];
							Db::name('juhe')->insert($insert);
						}
					}else{
						 return rel(-1,'身份证识别失败');
					}
				}
				$rel['juhe'] = 1;
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$rel['card_img'] = $site.'/uploads/card/'.$user_id.'/'.$card_img;
				$rel['img_url'] = $card_img;
				return rel(1,'上传成功',$rel);
			}else{
				 return rel(-1,$file->getError(),'');
			}
		}catch (Exception $e) {
				return rel(-1,$e->getMessage(),'');
		}
    }

    //会员头像上传
    public function uploadface(){
	   $user_id=$this->user_id;
	   if($user_id <= 0){
	   	   return rel(-1,'登录信息失效','');
	   }
	   try{
		   $file = request()->file('file');
		   if(!$file){
		   		return rel(-1,'图像无法识别','');
		   }
		   $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/face');
			if($info){
				$head_img = $info->getSaveName();
				
				//压缩图片
				$imgurl = ROOT_PATH . 'public' . DS . 'uploads/face/'.$head_img;
				$this->compressedImage($imgurl,$imgurl);
				
				$param['head_img'] = $head_img;
				$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
				$head_img  = $site.'/uploads/face/'.str_replace('\\','/',$head_img);
				
				$memberMD = new MemberModel();
				$map['id'] = $user_id;
				$memberMD->edit($map,$param);
				
				return rel(1,'头像保存成功',$head_img);
			}else{
				 return rel(-1,$file->getError(),'');
			}
		}catch (Exception $e) {
				return rel(-1,$e->getMessage(),'');
		}
    }
	
	
	/*public function yzimg(){
		$imgurl = ROOT_PATH . 'public' . DS . 'uploads/card/212068/20181229/c8080418b4785ed1307f289706fe52ad.jpg';
		$imgurl2 = ROOT_PATH . 'public' . DS . 'uploads/card/212068/20181229/c8080418b4785ed1307f289706fe52ad.jpg';
		$this->compressedImage($imgurl,$imgurl2);
		$this->juheIdimage($imgurl2,'front');
	}*/
	
	//身份证图片识别
	protected function juheIdimage($imgurl,$side){
		$image = $this->base64EncodeImage($imgurl);
		$url = 'http://apis.juhe.cn/idimage/verify';
		$reqData = [
			'key' => '1f99dff734d00d6a1e56aed2f133eddb',
			'image' => $image,
			'side' => $side, 
		];
		$rel = Curl::http_curl($url,$reqData);
		$rel1 = json_decode($rel,true);
		return $rel1;
	}
	
	
	protected function base64EncodeImage ($image_file) {
		$base64_image = '';
		$image_info = getimagesize($image_file);
		$image_data = fread(fopen($image_file, 'r'), filesize($image_file));
		$base64_image = chunk_split(base64_encode($image_data));//'data:' . $image_info['mime'] . ';base64,' . 
		return $base64_image;
	}
	
	
	/**
   * desription 压缩图片
   * @param sting $imgsrc 图片路径
   * @param string $imgdst 压缩后保存路径
   */
  protected function compressedImage($imgsrc, $imgdst) {
    list($width, $height, $type) = getimagesize($imgsrc);
    $new_width = $width;//压缩后的图片宽
    $new_height = $height;//压缩后的图片高
    if($width >= 1200){
      $per = 1200 / $width;//计算比例
      $new_width = $width * $per;
      $new_height = $height * $per;
    }
    switch ($type) {
      case 1:
        $giftype = check_gifcartoon($imgsrc);
        if ($giftype) {
          header('Content-Type:image/gif');
          $image_wp = imagecreatetruecolor($new_width, $new_height);
          $image = imagecreatefromgif($imgsrc);
          imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
          //90代表的是质量、压缩图片容量大小
          imagejpeg($image_wp, $imgdst, 90);
          imagedestroy($image_wp);
          imagedestroy($image);
        }
        break;
      case 2:
        header('Content-Type:image/jpeg');
        $image_wp = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefromjpeg($imgsrc);
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //90代表的是质量、压缩图片容量大小
        imagejpeg($image_wp, $imgdst, 90);
        imagedestroy($image_wp);
        imagedestroy($image);
        break;
      case 3:
        header('Content-Type:image/png');
        $image_wp = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefrompng($imgsrc);
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //90代表的是质量、压缩图片容量大小
        imagejpeg($image_wp, $imgdst, 90);
        imagedestroy($image_wp);
        imagedestroy($image);
        break;
    }
  }

}