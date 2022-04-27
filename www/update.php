<?php
require_once('src/php/header.php');

if ($_SESSION['username'] != 'admin') {
    header('Location: dashboard.php');
    exit();
}

function fix_entry($user_UUID, $table, $column, $key)
{
    global $db;
    $result = "";

    $SQL = "SELECT * FROM `$table` WHERE `$column` = '$key' AND `UUID` = '$user_UUID'";
    $data = db_query_raw($db, $SQL);
    $presence = mysqli_num_rows($data);

    $result .= "- $table, $column, $key : " . ($presence ? "OK" : "MISSING") . "\n";

    if (!$presence) {
        $result .= "\t Attempt to repair ... \n";
        db_query_no_result($db, "INSERT INTO `$table` (`UUID`, `$column`) VALUES ('$user_UUID', '$key')");
        $result .= "\t Verification of the repair ... </br>";
        $repaired = mysqli_num_rows(db_query_raw($db, $SQL));
        if ($repaired)
            $result .= "\t Repair successful \n";
        else
            $result .= "\t Repair failed \n";
    }

    return $result;
}

// Getting users
$result = "";
$users = db_query_raw($db, "SELECT username, UUID FROM `users` ORDER BY username ASC");
while ($user = mysqli_fetch_assoc($users)) {
    $result .= "Checking entry for " . $user['username'] . " - UUID : " . $user['UUID'] . "\n";

    // config
    $result .= fix_entry($user['UUID'], 'config', 'id', 'cmd_prefix');
    $result .= fix_entry($user['UUID'], 'config', 'id', 'twitch_channel');
    $result .= fix_entry($user['UUID'], 'config', 'id', 'twitch_connection_message');
    $result .= fix_entry($user['UUID'], 'config', 'id', 'cmd_time_interval');
    $result .= fix_entry($user['UUID'], 'config', 'id', 'cmd_msg_interval');
    $result .= fix_entry($user['UUID'], 'config', 'id', 'shout_interval');

    // shout
    $result .= fix_entry($user['UUID'], 'shout', 'original', '#HEADER');
    $result .= fix_entry($user['UUID'], 'shout', 'original', '#LANGUAGE');

    $result .= "\n";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update - Raph_BOT</title>
    <?php include("src/html/header.html"); ?>
</head>

<body>
    <!-- TOP Navbar -->
    <?php include("src/php/navbar.php"); ?>

    <!-- Side Navbar -->
    <?php include("src/html/sidebar.html"); ?>

    <!-- Main area -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Update and fix mandatory entries</h1>
        <div class="row">
            <div class="col-sm-12">
                <pre class="log-update"><?php echo $result ?></pre>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include("src/html/footer.html"); ?>

    <script>
        $(document).ready(function() {
            // Active the corresponding button in the navbar
            document.getElementById("update").className = "active";
        });

        function add_entry() {
            Swal.fire({
                title: "Add user",
                html: "<form id='swal-form' method='post'>" +
                    "<input type='hidden' name='action' value='add'>" +
                    "<label>Username</label><input type='text' class='form-control' name='username' required><br/>" +
                    "</form>",
                showCancelButton: true,
                showConfirmButton: confirm,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "25%",
                confirmButtonText: 'Add',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value)
                    document.getElementById('swal-form').submit();
            });
        }
    </script>

</body>

</html>