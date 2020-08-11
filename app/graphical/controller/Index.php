<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/22
 * Time: 09:17
 */

namespace app\graphical\controller;

use think\admin\Controller;

class Index extends Controller
{
    public function index(){
        $this -> title = "数据显示";
        $this -> fetch("index");
    }
}