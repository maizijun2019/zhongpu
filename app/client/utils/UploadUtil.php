<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 16:51
 */

namespace app\client\utils;

class UploadUtil
{
    public static function uploadFile(string $path,string $file){
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