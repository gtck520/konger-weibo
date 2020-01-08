<?php
namespace app\controller;

use app\BaseController;
use app\model\ImUser as ImUserModel;
use app\helper\ServerAPI;
use app\helper\PasswordHash;
class Index 
{
	
    public function index()
    {
        $hasher = new PasswordHash( 8, true );
        echo $hasher->HashPassword( trim( '7k4Bkj4Z*qvQc3iZMg' ) );
        echo '<br>';
        $hash='$P$BalV.kuhA2Kc2wkAblptnmp.83YulQ0';
        $password = '7k4Bkj4Z*qvQc3iZMg';
        $check = $hasher->CheckPassword( $password, $hash );
        echo $check;


    }

   

}
