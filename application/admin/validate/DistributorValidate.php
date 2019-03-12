<?php

namespace app\admin\validate;
use think\Validate;

class DistributorValidate extends Validate
{
    protected $rule = [
        ['account', 'unique:distributor', '账号已经存在']
    ];

}