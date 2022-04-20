<?php 
session_start();

$core_directory = dirname(__DIR__)."/../../core";
$UUID = $_SESSION['UUID'];

if(isset($_POST) && $_POST['action'] == "start"){
    //node $core_directory/core.js $UUID > $core_directory/logs/debug_$UUID.log 2>&1

    $command = "node $core_directory/core.js $UUID > $core_directory/logs/debug_$UUID.log 2>&1";

    echo shell_exec("screen -dmS Raph_BOT-$UUID bash -c '$command'");
}
    
    

exit();
?>