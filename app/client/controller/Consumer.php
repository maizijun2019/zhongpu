<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/29
 * Time: 16:23
 */

namespace app\client\controller;

use app\client\exception\RegisterException;
use app\client\model\ConsumerModel;
use app\client\utils\JSONUtil;
use Exception;
use think\admin\Controller;
use think\facade\Db;

class Consumer extends Controller
{
    /**
     * 注册
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $check_password 确认密码
     * @param string $phone 手机号码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function register(string $username,string $password,string $check_password,string $phone){
        if($this -> request -> isPost()){
            try{
                if(ConsumerModel::register($username,$password,$check_password,$phone)){
                    session_start();
                    ConsumerModel::login($username,$password);
                    JSONUtil::sendSuccess(ConsumerModel::checkLogin());
                }
            }catch (RegisterException $registerException){
                JSONUtil::sendFail($registerException -> getErrorMessage());
            }
        }
        JSONUtil::sendFail();
    }

    /**
     * 登陆
     * @param string $username 用户名
     * @param string $password 密码
     */
    public function login(string $username,string $password){
        if($this -> request -> isPost()){
            try{
                session_start();
                ConsumerModel::login($username,$password);
                JSONUtil::sendSuccess(ConsumerModel::checkLogin());
            }catch (RegisterException $registerException){
                JSONUtil::sendFail($registerException -> getErrorMessage());
            }catch (Exception $exception){

            }
        }
        JSONUtil::sendFail();
    }

    /**
     * 修改个人信息
     * @param string $nickname 联系人
     * @param string $wechat_account 微信账号
     * @param string $email_account 邮箱账号
     * @throws \think\db\exception\DbException
     */
    public function editInfo(string $nickname,string $wechat_account,string $email_account){
        if($this -> request -> isPost()){
            $consumer = ConsumerModel::checkLogin(true);
            if(!ConsumerModel::checkNickname($nickname)){
                JSONUtil::sendFail(ConsumerModel::$NICKNAME_STANDARD);
            }
            if (!ConsumerModel::checkWechatAccount($wechat_account)) {
                JSONUtil::sendFail(ConsumerModel::$WECHAT_ACCOUNT_STANDARD);
            }
            if(!ConsumerModel::checkEmailAccount($email_account)){
                JSONUtil::sendFail(ConsumerModel::$EMAIL_ACCOUNT_STANDARD);
            }
            Db::table(ConsumerModel::$TABLE_NAME)
                -> where("consumer_id",$consumer["consumer_id"])
                -> update(array(
                    "nickname" => $nickname,
                    "wechat_account" => $wechat_account,
                    "email_account" => $email_account
                ));
            JSONUtil::sendSuccess();
        }
        JSONUtil::sendFail();
    }

    /**
     * 查询用户信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info(){
        $consumer = ConsumerModel::checkLogin(true);
        JSONUtil::sendSuccess(ConsumerModel::findUser(array("consumer_id" => $consumer["consumer_id"])));
    }

    /**
     * 退出登陆
     */
    public function logout(){
        ConsumerModel::logout(true);
        JSONUtil::sendSuccess();
    }
}