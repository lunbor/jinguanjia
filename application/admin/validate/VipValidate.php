<?php

namespace app\admin\validate;
use think\Validate;

class VipValidate extends Validate
{
    protected $rule = [
        ['title', 'unique:vip', '类型已经存在']
    ];

}