<?php

namespace app\admin\validate;
use think\Validate;

class AgentGroupValidate extends Validate
{
    protected $rule = [
        ['group_name', 'unique:agent_group', '会员组已经存在']
    ];

}