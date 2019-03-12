<?php

namespace app\qudao\controller;
use think\Controller;

class Base extends Controller{
  
    public function _initialize(){

        if(!session('qd_id')){
            $this->redirect('index/login');
        }

    }
}
