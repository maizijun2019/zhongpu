<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/29
 * Time: 16:24
 */

namespace app\client\model;

use app\client\exception\RegisterException;
use app\client\utils\JSONUtil;
use Exception;
use think\App;
use think\facade\Db;

class ConsumerModel
{
    public static $TABLE_NAME = "zp_consumer";
    public static $NAME_STANDARD = '用户名长度在6-18位之间,仅允许使用字母、数字、下划线或邮箱';
    public static $PASSWORD_STANDARD = '密码长度在6-32位之间,不支持非法占位符与中文';
    public static $PHONE_STANDARD = "请填写正确的手机号码";
    public static $NICKNAME_STANDARD = "联系人名称限制10位中文字数之内";
    public static $WECHAT_ACCOUNT_STANDARD = "微信号不能含有中文且限制50位长度之内";
    public static $EMAIL_ACCOUNT_STANDARD = "请输入正确的邮箱";

    /**
     * 登陆密码加密
     * @param string $password
     * @return string
     */
    public static function encodePassword(string $password){
        $length = strlen($password);
        return md5("{$length}{$password}{$length}");
    }

    /**
     * 检测用户名
     * @param string $username
     * @return bool true：通过，false：不通过
     */
    public static function checkUsername(string $username){
        if(preg_match('/^[\w]{6,18}$/',$username)){
            return true;
        }
        if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$username)){
            return true;
        }
        return false;
    }

    /**
     * 检测密码
     * @param string $password
     * @return bool true：通过，false：不通过
     */
    public static function checkPassword(string $password){
        if(!preg_match('/^[\s\x{4e00}-\x{9fa5}]+$/u',$password)){
            if(strlen($password) >= 6 && strlen($password) <= 32){
                return true;
            }
        }
        return false;
    }

    /**
     * 检测手机号码
     * @param string $phone
     * @return bool true：通过，false：不通过
     */
    public static function checkPhone(string $phone){
        if(preg_match('/^[\w]{11}$/',$phone)){
            return true;
        }
        return false;
    }

    /**
     * 检测联系人名称
     * @param string $nickname
     * @return bool true：通过，false：不通过
     */
    public static function checkNickname(string $nickname){
        if(strlen($nickname) <= 30){
            return true;
        }
        return false;
    }

    /**
     * 检测微信账号
     * @param string $wechat_account
     * @return bool true：通过，false：不通过
     */
    public static function checkWechatAccount(string $wechat_account){
        if (!preg_match('/[\x{4e00}-\x{9fa5}]/u',$wechat_account) > 0 && strlen($wechat_account) <= 50) {
            return true;
        }
        return false;
    }

    /**
     * 检测邮箱账号
     * @param string $email_account
     * @return bool true：通过，false：不通过
     */
    public static function checkEmailAccount(string $email_account){
        if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email_account)){
            return true;
        }
        return false;
    }

    /**
     * 注册
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $check_password 确认密码
     * @param string $phone 手机号码
     * @return bool
     * @throws RegisterException
     */
    public static function register(string $username,string $password,string $check_password,string $phone){
        if(!self::checkUsername($username)){
            throw new RegisterException(self::$NAME_STANDARD);
        }
        if($password !== $check_password){
            throw new RegisterException("两次密码不一致");
        }
        if(!self::checkPassword($password)){
            throw new RegisterException(self::$PASSWORD_STANDARD);
        }
        if(!self::checkPhone($phone)){
            throw new RegisterException(self::$PHONE_STANDARD);
        }
        try{
            $insert = Db::table(self::$TABLE_NAME) -> insert(array(
                "username" => $username,
                "password" => self::encodePassword($password),
                "phone" => $phone,
                "create_time" => time()
            ));
            if($insert > 0){
                return true;
            }
        }catch (Exception $e){
            if($e -> getMessage() === "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '{$username}' for key 'username'"){
                throw new RegisterException("该用户名已存在");
            }
        }
        return false;
    }

    /**
     * 查找单个用户
     * @param array $where 查询条件
     * @param array $field 查询字段
     * @param bool $lock 是否行锁
     * @return array|null|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function findUser(array $where,array $field = array("consumer_id","username","password","nickname","phone","wechat_account","email_account","service_provider","create_time"),bool $lock = false){
        $sql = Db::table(self::$TABLE_NAME) -> where($where);
        if(sizeof($field) > 0){
            $sql = $sql -> field($field);
        }
        if($lock){
            $sql = $sql -> lock($lock);
        }
        return $sql -> find();
    }

    /**
     * 登陆
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $session_start 是否开启session会话
     * @throws RegisterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function login(string $username,string $password,bool $session_start = false){
        $consumer = self::findUser(array("username" => $username));
        if(!$consumer || $consumer["password"] !== self::encodePassword($password)){
            throw new RegisterException("用户名或密码错误");
        }
        unset($consumer["password"]);
        if($session_start){
            session_start();
        }
        $_SESSION['consumer'] = $consumer;
    }

    /**
     * 获取session会话
     * @param bool $session_start 是否开启session会话
     * @return null
     */
    public static function getSessionConsumer(bool $session_start = false){
        if($session_start){
            session_start();
        }
        if(isset($_SESSION['consumer'])){
            return $_SESSION['consumer'];
        }
        return null;
    }

    /**
     * 去除登陆状态
     * @param bool $session_start 是否开启session会话
     */
    public static function logout(bool $session_start = false){
        if($session_start){
            session_start();
        }
        if(isset($_SESSION['consumer'])){
            unset($_SESSION['consumer']);
        }
    }

    /**
     * 检测登陆
     * @param bool $session_start 是否开启session会话
     * @return null
     */
    public static function checkLogin(bool $session_start = false){
        $consumer = self::getSessionConsumer($session_start);
        if($consumer !== null){
            return $consumer;
        }
        JSONUtil::notLogin();
    }
}