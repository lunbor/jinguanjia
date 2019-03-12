<?php

namespace app\admin\validate;
use think\Validate;

class PayAisleValidate extends Validate
{
    protected $rule = [
        ['keyname', 'unique:payAisle', '该通道已经存在']
    ];

}