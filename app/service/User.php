<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/8
 * Time: 11:02
 */
namespace app\service;
use \Firebase\JWT\JWT; //导入JWT
class User
{
    /**
     * Notice:获取jwt的token信息
     * Date  :2019/3/12
     */
    public static function getJwtToken($userId,$email)
    {
        $key     = config('jwt.key');
        $jwtData = [
            'lat' => config('jwt.lat'),
            'nbf' => config('jwt.nbf'),
            'exp' => config('jwt.exp'),
            'uid' => $userId,
            'email' => $email, //可以加入自己想要获得的用户信息参数
        ];

        $jwtToken = JWT::encode($jwtData, $key);

        return $jwtToken;

    }

    public static function checkJwtToken($token)
    {
        $key  = config('jwt.key');
        $info = JWT::decode($token, $key,['HS256']);
        return $info;
    }


}