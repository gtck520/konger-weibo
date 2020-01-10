<?php
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );
Loader::import('my\Test', EXTEND_PATH);
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
//wordpress cookie生成
function is_ssl() {
    if ( isset( $_SERVER['HTTPS'] ) ) {
        if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
            return true;
        }

        if ( '1' == $_SERVER['HTTPS'] ) {
            return true;
        }
    } elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
        return true;
    }
    return false;
}
function apply_filters( $tag, $value ) {
    global $wp_filter, $wp_current_filter;

    $args = func_get_args();

    // Do 'all' actions first.
    if ( isset( $wp_filter['all'] ) ) {
        $wp_current_filter[] = $tag;
        _wp_call_all_hook( $args );
    }

    if ( ! isset( $wp_filter[ $tag ] ) ) {
        if ( isset( $wp_filter['all'] ) ) {
            array_pop( $wp_current_filter );
        }
        return $value;
    }

    if ( ! isset( $wp_filter['all'] ) ) {
        $wp_current_filter[] = $tag;
    }

    // Don't pass the tag name to WP_Hook.
    array_shift( $args );

    $filtered = $wp_filter[ $tag ]->apply_filters( $value, $args );

    array_pop( $wp_current_filter );

    return $filtered;
}
    function wp_set_auth_cookie( $user_id, $remember = false, $secure = '', $token = '' ) {
        $WP_Session_Tokens = new app\helper\WpSessionTokens($user_id);
        if ( $remember ) {
            /**
             * Filters the duration of the authentication cookie expiration period.
             *
             * @since 2.8.0
             *
             * @param int  $length   Duration of the expiration period in seconds.
             * @param int  $user_id  User ID.
             * @param bool $remember Whether to remember the user login. Default false.
             */
            $expiration = time() + apply_filters( 'auth_cookie_expiration', 14 * DAY_IN_SECONDS, $user_id, $remember );

            /*
             * Ensure the browser will continue to send the cookie after the expiration time is reached.
             * Needed for the login grace period in wp_validate_auth_cookie().
             */
            $expire = $expiration + ( 12 * HOUR_IN_SECONDS );
        } else {
            /** This filter is documented in wp-includes/pluggable.php */
            $expiration = time() + apply_filters( 'auth_cookie_expiration', 2 * DAY_IN_SECONDS, $user_id, $remember );
            $expire     = 0;
        }

        if ( '' === $secure ) {
            $secure = is_ssl();
        }

        // Front-end cookie is secure when the auth cookie is secure and the site's home URL is forced HTTPS.
        $secure_logged_in_cookie = $secure && 'https' === parse_url( get_option( 'home' ), 0 );

        /**
         * Filters whether the connection is secure.
         *
         * @since 3.1.0
         *
         * @param bool $secure  Whether the connection is secure.
         * @param int  $user_id User ID.
         */
        $secure = apply_filters( 'secure_auth_cookie', $secure, $user_id );

        /**
         * Filters whether to use a secure cookie when logged-in.
         *
         * @since 3.1.0
         *
         * @param bool $secure_logged_in_cookie Whether to use a secure cookie when logged-in.
         * @param int  $user_id                 User ID.
         * @param bool $secure                  Whether the connection is secure.
         */
        $secure_logged_in_cookie = apply_filters( 'secure_logged_in_cookie', $secure_logged_in_cookie, $user_id, $secure );

        if ( $secure ) {
            $scheme           = 'secure_auth';
        } else {
            $scheme           = 'auth';
        }

        if ( '' === $token ) {
            $manager = $WP_Session_Tokens::get_instance( $user_id );
            $token   = $manager->create( $expiration );
        }

        $auth_cookie      = wp_generate_auth_cookie( $user_id, $expiration, $scheme, $token );
        $logged_in_cookie = wp_generate_auth_cookie( $user_id, $expiration, 'logged_in', $token );
        $data['auth_cookie']=$auth_cookie;
        $data['logged_in_cookie']=$logged_in_cookie;
        return $data;

    }