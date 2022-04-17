<?php 
session_start();

$core_directory = dirname(__DIR__)."/../../core/";
$UUID = $_SESSION['UUID'];

if(isset($_POST) && $_POST['action'] == "start"){
    //shell_exec('> '.$core_directory.'lastest.log');

    //shell_exec('screen -dmS Raph_BOT-'.$UUID.' clear;  node '.$core_directory.'core.js '.$UUID.' > '.$core_directory.'debug.log 2>&1');

    exec('node '.$core_directory.'core.js '.$UUID.' > '.$core_directory.'debug-'.$UUID.'.log 2>&1');
}

exit();
?>