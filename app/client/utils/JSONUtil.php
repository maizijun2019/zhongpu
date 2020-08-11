<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/29
 * Time: 16:57
 */

namespace app\client\utils;

class JSONUtil
{
    public static $ERROR_CODE_FILED = "error_code";
    public static $ERROR_MESSAGE_FILED = "error_message";
    public static $DATA_FILED = "data";
    public static $SUCCESS_CODE = 0;//成功
    public static $FAIL_CODE = 1;//失败
    public static $NOT_LOGIN_CODE = 101;//未登录
    public static $SUCCESS_MESSAGE = "success";
    public static $FAIL_MESSAGE = "fail";
    public static $NOT_LOGIN_MESSAGE = "not_login";

    public static function baseJSON(int $error_code,string $error_message){
        return array(
            self::$ERROR_CODE_FILED => $error_code,
            self::$ERROR_MESSAGE_FILED => $error_message
        );
    }

    public static function sendSuccess($data = null,string $success_message = ""){
        $result = self::baseJSON(self::$SUCCESS_CODE,self::$SUCCESS_MESSAGE);
        if(strlen($success_message) > 0){
            $result[self::$ERROR_MESSAGE_FILED] = $success_message;
        }
        if($data !== null){
            $result[self::$DATA_FILED] = $data;
        }
        echo json_encode($result);die;
    }

    public static function sendFail(string $fail_message = "",int $fail_code = null){
        $result = self::baseJSON(self::$FAIL_CODE,self::$FAIL_MESSAGE);
        if(strlen($fail_message) > 0){
            $result[self::$ERROR_MESSAGE_FILED] = $fail_message;
        }
        if($fail_code !== null){
            $result[self::$ERROR_CODE_FILED] = $fail_code;
        }
        echo json_encode($result);die;
    }

    public static function notLogin(){
        self::sendFail(self::$NOT_LOGIN_MESSAGE,self::$NOT_LOGIN_CODE);
    }
}