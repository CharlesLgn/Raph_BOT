<?php
require_once('src/php/header.php');
require_once('src/php/functions.php');

if($_SESSION['username'] != 'admin'){
    header('Location: dashboard.php');
    exit();
}

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "add" && !empty($_POST['username'])){
        $username = sanitise_input($db, $_POST['username']);

        // Add user in users table
        $user_UUID = guidv4();
        db_query_no_result($db, "INSERT INTO `users` VALUES ('$user_UUID', '$username', NULL)");

        // Setting up ports table
        db_query_no_result($db, "INSERT INTO `ports` VALUES ('$user_UUID', NULL)");

        // Alert
        $_SESSION['alert'] = ["info", "Go to the 'Update' tab to add mandatory entries to that user", false];

        header("Location: users.php");
        exit();
    }

    exit();
}


// Listing
$data = db_query_raw($db, "SELECT * FROM `users` ORDER BY username ASC");
$list = "";
while($row = mysqli_fetch_assoc($data)) {
    $res = shell_exec("screen -S Raph_BOT-".$row['UUID']." -Q select . ; echo $?");
    $res = intval(substr($res, strlen($res) - 2, 1));

    if($res){ // 1 is not active
        $text = "Offline";
        $color = "danger";
    }
    else{
        $text = "Online";
        $color = "success";
    }

    $list .= "
    <tr>
        <td>".$row["username"]."</td>
        <td>".$row["UUID"]."</td>
        <td>
            <div class='progress'>
                <div class='progress-bar progress-bar-$color' role='progressbar' style='width:100%'>
                <span>$text</span>
                </div>
            </div> 
        </td>
        <td></td>
    </tr>";
}

?>

<!DOCTYPE html>
<html>
    <head>
      <title>Configuration - Raph_BOT</title>
      <?php include("src/html/header.html"); ?>
    </head>

    <body>
    <!-- TOP Navbar -->
    <?php include("src/php/navbar.php"); ?>

    <!-- Side Navbar -->        
    <?php include("src/html/sidebar.html"); ?>

    <!-- Main area -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Users
            <div class='pull-right'>
            <button type="button" class="btn btn-success" onclick='add_entry()'><i class="glyphicon glyphicon-plus"></i></button>
            </div>
        </h1>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th class="col-xs-2">Username</th>
                    <th class="col-xs-4">UUID</th>
                    <th class="col-xs-2">Status</th>
                    <th></th>
                </tr>
                </thead>
            <tbody>
                <?php echo $list; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php 
        include("src/html/footer.html"); 
        require_once("src/php/alert.php");
    ?>

    <script>
        $(document).ready(function() {
            // Active the corresponding button in the navbar
            document.getElementById("users").className="active"; 
        });

        function add_entry(){
            Swal.fire({
                title: "Add user",
                html:   "<form id='swal-form' method='post'>"+
                        "<input type='hidden' name='action' value='add'>"+
                        "<label>Username</label><input type='text' class='form-control' name='username' required><br/>"+
                        "</form>",
                showCancelButton: true,
                showConfirmButton: confirm,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "25%",
                confirmButtonText: 'Add',
                cancelButtonText: 'Cancel'
            }).then((result) =>{
                if(result.value)
                    document.getElementById('swal-form').submit();
            });
        }
    </script>

</body>
</html>