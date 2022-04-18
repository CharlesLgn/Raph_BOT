<?php
require_once('src/php/header.php');
require_once('src/php/functions.php');

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "add" && !empty($_POST['username'])){
        $username = sanitise_input($db, $_POST['username']);

        // Add user in users table
        $user_UUID = guidv4();
        db_query_no_result($db, "INSERT INTO `users` VALUES ('$user_UUID', '$username', NULL)");

        // Setting up config table
        db_query_no_result($db, "INSERT INTO `config` (`#`, `UUID`, `id`, `value`) VALUES
            (NULL, '$user_UUID', 'twitch_channel', 'Your twitch channel'),
            (NULL, '$user_UUID', 'cmd_prefix', '!'),
            (NULL, '$user_UUID', 'twitch_connection_message', 'Your welcome message'),
            (NULL, '$user_UUID', 'cmd_time_interval', '0'),
            (NULL, '$user_UUID', 'cmd_msg_interval', '0');");

        // Setting up ports table
        db_query_no_result($db, "INSERT INTO `ports` VALUES ('$user_UUID', NULL)");

        header("Location: users.php");
        exit();
    }

    exit();
}


// Listing
$data = db_query_raw($db, "SELECT * FROM `users` ORDER BY username ASC");
$list = "";
while($row = mysqli_fetch_assoc($data)) {
    $list .= "
    <tr>
        <td>".$row["username"]."</td>
        <td>".$row["UUID"]."</td>
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
                  <th class="col-xs-7">UUID</th>
                  <th></th>
              </tr>
            </thead>
          <tbody>
            <?php echo $list; ?>
          </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include("src/html/footer.html"); ?>

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