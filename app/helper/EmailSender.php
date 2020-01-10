<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/8
 * Time: 14:32
 */

namespace app\helper;


class EmailSender
{
    public static function send_code($toemail)
    {
        $name='亲，您好！';//接收邮件者名称
        $subject='邮件验证码';//邮件主题
        $code=random(6);
        $content='您的验证码为：'.$code.',请于30分钟内验证。';//邮件内容
        //调用方法发送邮件
        send_mail($toemail,$name,$subject,$content);
        return $code;
    }
}