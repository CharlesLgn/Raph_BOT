<?php
require_once('src/php/header.php');

if($_SESSION['username'] != 'admin'){
    header('Location: dashboard.php');
    exit();
}

function fix_entry($user_UUID, $table, $column, $key){ 
    global $db;

    $SQL = "SELECT * FROM `$table` WHERE `$column` = '$key' AND `UUID` = '$user_UUID'";
    $data = db_query_raw($db, $SQL);
    $presence = mysqli_num_rows($data);

    echo "- $table, $column, $key : " . ($presence ? "OK" : "MISSING") . "\n";

    if(!$presence){
        echo "\t Attempt to repair ... \n";
        db_query_no_result($db, "INSERT INTO `$table` (`UUID`, `$column`) VALUES ('$user_UUID', '$key')");
        echo "\t Verification of the repair ... </br>";
        $repaired = mysqli_num_rows(db_query_raw($db, $SQL));
        if($repaired)
            echo "\t Repair successful \n";
        else
            echo "\t Repair failed \n";
    }

    return $presence;
}

echo "<h2>Auto updating entries</h2>";

// Auto updating table with missing entry

// Getting users
$users = db_query_raw($db, "SELECT username, UUID FROM `users` ORDER BY username ASC");
while($user = mysqli_fetch_assoc($users)) {
    echo "<h4>Checking entry for " . $user['username'] . " - UUID : " . $user['UUID'] . "</h4>";
    echo "<pre>";

    // config
    fix_entry($user['UUID'], 'config', 'id', 'cmd_prefix');
    fix_entry($user['UUID'], 'config', 'id', 'twitch_channel');
    fix_entry($user['UUID'], 'config', 'id', 'twitch_connection_message');
    fix_entry($user['UUID'], 'config', 'id', 'cmd_time_interval');
    fix_entry($user['UUID'], 'config', 'id', 'cmd_msg_interval');
    fix_entry($user['UUID'], 'config', 'id', 'shout_interval');

    // shout
    fix_entry($user['UUID'], 'shout', 'original', '#HEADER');
    fix_entry($user['UUID'], 'shout', 'original', '#LANGUAGE');



    echo "</pre>";
}


echo "<h2>Done<h2>";

?>