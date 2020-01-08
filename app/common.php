<?php
// 应用公共文件
/**
 * Notes:发送邮件
 * @param string $tomail 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @throws phpmailerException
 */
function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {

    $mail = new PHPMailer\PHPMailer\PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = config('email.host'); // SMTP 服务器
    $mail->Port = config('email.port');                  // SMTP服务器的端口号
    $mail->Username = config('email.username');    // SMTP服务器用户名
    $mail->Password = config('email.password');     // SMTP服务器密码，这里是你开启SMTP服务时生成密码
    $mail->SetFrom(config('email.username'), 'KONGER');
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}
/**
2      * 方法一：获取随机字符串
3      * @param number $length 长度
4      * @param string $type 类型
5      * @param number $convert 转换大小写
6      * @return string 随机字符串
7      */
   function random($length = 6, $type = 'string', $convert = 0)
   {
      $config = array(
           'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
          'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
         'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
     );

   if (!isset($config[$type]))
           $type = 'string';
       $string = $config[$type];

      $code = '';
     $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
           $code .= $string{mt_rand(0, $strlen)};
      }
    if (!empty($convert)) {
           $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
  }
  return $code;
 }