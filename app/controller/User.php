<?php
namespace app\controller;

use app\BaseController;
use app\model\ImUser as ImUserModel;
use app\helper\ServerAPI;
class User extends BaseController
{
	protected static $appKey = "f682375d48c2f89c8b90863ae0fa93e0";
	protected static $appSecret = "a5f74f699a50";
    public function index()
    {
       $rs= ImUserModel::select();
       foreach ($rs as $key => $value) {
       	  echo $value['username'];
       }
     
    }

    public function login(){

    	$username=input('post.username');
    	$password=input('post.password');
    	$where=['username'=>$username,'password'=>$password];
    	$rsone=ImUserModel::where($where)->find();
    	if($rsone){
    		$data=['res'=>'200','errmsg'=>'','data'=>$rsone];
    		echo json_encode($data);
    		exit;
    	}else{

    		$data=['res'=>'400','errmsg'=>'用户不存在，或密码错误'];
    		echo json_encode($data);
    		exit;
    	}
    	
    }
    public function register()
    {

    	$username=input('post.username');
    	$password=input('post.password');
    	$nickname=input('post.nickname');

    	$where=['username'=>$username];
    	$rsone=ImUserModel::where($where)->find();
    	if($rsone){
    		$data=['res'=>'400','errmsg'=>'用户已存在'];
    		echo json_encode($data);
    		exit;
    	}

    	$accid = uniqid();
    	$token = md5($accid);
    	$insert_data=[
    		'username'=>$username,
    		'password'=>$password,
    		'nickname'=>$nickname,
    		'accid'=>$accid,
    		'token'=>$token,
    		'create_time'=>time(),
    	];

    	$id = ImUserModel::insertGetId($insert_data);
    	if($id>0){
			$p = new ServerAPI(self::$appKey,self::$appSecret,'curl');		//php curl库
			$rs=$p->createUserId($accid,$username,'{}','',$token);

    	}
		$data=['res'=>'200','errmsg'=>'成功','data'=>$insert_data];
		echo json_encode($data);
		exit;
    }

}
