<?php
namespace app\api\model;

use think\Model;

class BannerModel extends Model
{
    protected $name = 'banner';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getAdAll($map, $limits)
    {
		$map['status'] = 1;
		$map['closed'] = 0;
		$now_date = date('Y-m-d');
		$map['start_date'] = ['elt',$now_date];
		$map['end_date'] = ['egt',$now_date];
		$map['images'] = ['neq',''];
		$site = input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME');
		$list = $this->field("images,link_url,title")->where($map)->order('orderby desc')->limit($limits)->select(); 
		foreach ($list as $k => $v){
			if($v['images']!=''){
				$list[$k]['images']  = $site.'/uploads/images/'.str_replace('\\','/',$v['images']);
			}
		}
		return $list;
    }
}