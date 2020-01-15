<?php
namespace app\controller;

use app\BaseController;
use app\model\WpUser as WpUserModel;
use app\model\KgEmail as KgEmailModel;
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
    	$post['username']=input('post.username');
        $post['password']=input('post.password');
        $post['email']=input('post.email');
        $post['code']=input('post.code');

        //验证输入
        $validate = new \app\validate\User;
        if (!$validate->check($post)) {
            $data=['res'=>'400','msg'=>$validate->getError()];
            echo json_encode($data);
            exit;
        }

        $where=['user_login'=>$post['username'],'user_email'=>$post['email']];
        $wp_user=WpUserModel::whereOr($where)->find();
        if($wp_user){
            $data=['res'=>'400','msg'=>'用户名或邮箱已注册！'];
            echo json_encode($data);
            exit;
        }
        $where1=['email'=>$post['email'],'code'=>$post['code']];
        $user_email=KgEmailModel::where($where1)->order('id desc')->find();//取最近一条发送记录
        if($user_email){
            $now=time();
            $exp=$now-$user_email['exp_time'];
            if($exp>0){
                $data=['res'=>'400','msg'=>'该验证码已过期，请重新发送'];
                echo json_encode($data);
                exit;
            }
        }else{
            $data=['res'=>'400','msg'=>'验证码错误！请重试'];
            echo json_encode($data);
            exit;
        }
        $hasher = new PasswordHash( 8, true );
        $password_hash = $hasher->HashPassword( trim(  $post['password'] ) );
        $insert_data['user_login']=$post['username'];
        $insert_data['user_pass']=$password_hash;
        $insert_data['user_nicename']=$post['username'];
        $insert_data['user_email']=$post['email'];
        $insert_data['user_registered']=date('Y-m-d H:i:s');
        $insert_data['display_name']=$post['username'];
        $uid=WpUserModel::insert($insert_data);
        $data=['res'=>'200','msg'=>'成功','data'=>$uid];
        echo json_encode($data);
    }
    public function  email()
    {
        $email=input('post.email');
        $where=['email'=>$email];
        $user_email=KgEmailModel::where($where)->order('id desc')->find();//取最近一条发送记录
        $now=time();
        if($user_email){
            $interval= config('email.interval')-($now-$user_email['add_time']);
            if($interval>0){
                $data=['res'=>'400','msg'=>'验证码已发送，请'.$interval.'秒后重新发送'];
                echo json_encode($data);
                exit;
            }
        }
        $insert_data['code']=EmailSender::send_code($email);
        $insert_data['add_time']=$now;
        $insert_data['exp_time']=$now+config('email.expire');
        $insert_data['email']=$email;
        KgEmailModel::insert($insert_data);
        $data=['res'=>'200','msg'=>'成功','data'=>''];
        echo json_encode($data);

    }

}
