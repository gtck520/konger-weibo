<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/8
 * Time: 17:30
 */

namespace app\validate;

use think\Validate;
class User extends Validate
{
    protected $rule =   [
        'username'  => 'require|max:25',
        'password'  => 'require|alphaNum|min:6|max:25',
        'email' => 'email',
        'code' => 'require',
        'accept'=>'accepted',
    ];

    protected $message  =   [
        'username.require' => '用户名必须',
        'username.max'     => '用户名最多不能超过25个字符',
        'password.require'   => '密码必须',
        'password.alphaNum'  => '密码必须为字母与数字的组合',
        'password.min'  => '密码至少6个字符',
        'password.max'  => '密码最多不能超过25个字符',
        'email'        => '邮箱格式错误',
        'code'        => '验证码为必须',
    ];
    // 自定义验证规则
    protected function checkName($value,$rule,$data=[])
    {
        return $rule == $value ? true : '名称错误';
    }

}