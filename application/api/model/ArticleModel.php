<?php
namespace app\api\model;

use think\Model;

class ArticleModel extends Model
{
    protected $name = 'article';

    /**
     * 根据条件获取列表信息
     * @param $where
     * @param $Nowpage
     * @param $limits
     */
    public function getOne($map)
    {
		$info = $this->where($map)->find(); 
		return $info;
    }
}