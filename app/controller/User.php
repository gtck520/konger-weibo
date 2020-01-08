<?php
namespace app\controller;

use app\BaseController;
use app\model\WpUser as WpUserModel;
use app\helper\ServerAPI;
use app\helper\PasswordHash;
use app\service\User as UserService;
use app\helper\EmailSender;
class User extends BaseController
{
    public function login(){
        $hasher = new PasswordHash( 8, true );
    	$username=input('post.username');
    	$password=input('post.password');
    	$where=['user_login'=>$username,'user_email'=>$username];
        $wp_user=WpUserModel::whereOr($where)->find();
        $check = $hasher->CheckPassword( $password, $wp_user['user_pass'] );
        if($check){
            $token=UserService::getJwtToken($wp_user['ID'],$wp_user['user_email']);
            $backuser=['userID'=>$wp_user['ID'],'user_email'=>$wp_user['user_email'],'token'=>$token];
            $data=['res'=>'200','msg'=>'成功','data'=>$backuser];
            echo json_encode($data);
        }else{
            $data=['res'=>'400','msg'=>'用户不存在或密码错误'];
            echo json_encode($data);
            exit;
        }
    }
    public function register()
    {
    	$username=input('post.username');
    	$password=input('post.password');
    	$email=input('post.email');
        $code=input('post.code');
        $where=['user_login'=>$username,'user_email'=>$email];
        $wp_user=WpUserModel::whereOr($where)->find();
        if($wp_user){
            $data=['res'=>'400','msg'=>'用户已存在！'];
            echo json_encode($data);
            exit;
        }
        $hasher = new PasswordHash( 8, true );
        $password_hash = $hasher->HashPassword( trim( $password ) );




    }
    public function  email()
    {
        $username=input('post.username');

        EmailSender::send_code($username);
    }

}
