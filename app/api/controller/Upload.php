<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/9
 * Time: 13:11
 */

namespace app\api\controller;

use think\admin\Controller;

class Upload extends Controller
{

    public function index(){
        $now_time = time();
        $path = $this -> uploadFile("upload/".date("Y-m-d",$now_time)."/{$now_time}/","file");
        if($path !== false){
//            $preview_path = $path;
            $preview_name = $_FILES["file"]["name"];
//            $suffix_name = explode(".",$preview_name);
//            if(sizeof($suffix_name) > 1){
//                $preview_path = str_replace(".".$suffix_name[sizeof($suffix_name) - 1],
//                    ".".strtolower($suffix_name[sizeof($suffix_name) - 1]),$path);
//            }
            if(strlen($preview_name) > 20){
                $preview_name = mb_substr($preview_name,0,10)."...";
            }
            echo json_encode(array(
                "code" => 1,
                "message" => "success",
                "path" => SERVER_NAME.$path,
                "name" => $_FILES["file"]["name"],
//                "preview_path" => SERVER_NAME.$preview_path,
                "preview_name" => $preview_name
            ));die;
        }
        echo json_encode(array("code" => 0,"message" => "fail"));die;
    }

    private function uploadFile(string $path,string $file){
        if($_FILES){
            if ($_FILES[$file]["error"] <= 0) {
                set_time_limit(0);//设置无超时时间

                //创建路径文件夹
                $dir = iconv("UTF-8", "GBK", $path);
                if(file_exists($dir) == false){
                    mkdir ($dir,0777,true);
                }
                //保存文件
                $path .= "{$_FILES[$file]["name"]}";
                move_uploaded_file($_FILES[$file]["tmp_name"],$path);
                return $path;
            }
        }
        return false;
    }
}