<?php 
session_start();
require_once('src/php/db.php');

$db = db_connect();

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = addslashes(trim($_POST['username']));
    $password = addslashes(trim($_POST['password']));
    $row = db_query_raw($db, "SELECT `password` FROM `users` WHERE `username` = '$username'");

    if(mysqli_num_rows($row)){
        $row = mysqli_fetch_assoc($row);
        // Password Reset
        if(empty($row["password"]) && $password == "0"){
            $_SESSION['username'] = $username;
            $_SESSION['login'] = true;
            $_SESSION['pass-reset'] = true;
            header('Location: ./password.php');
            exit();
        }

        // PASS
        if(password_verify($password, $row['password'])){
            $_SESSION['username'] = $username;
            $_SESSION['login'] = true;
            header('Location: ./dashboard.php'); 
            exit();
        }
    }
    else{
        $_SESSION["alert"] = ["error", "Username doesn't exist", false];
        header('Location: ./index.php');
        exit();
    }

    $_SESSION["alert"] = ["error", "Incorrect password", false];
    header('Location: ./index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
      <?php include("src/html/header.html"); ?>
      <title>Login - Raph_BOT</title>
  </head>
  <body>
  <div class="main col-lg-8 col-lg-offset-2 col-md-12">
        <!-- Titre -->
        <center><h2><b>Raph_BOT</b></h2></center>

        <!-- Login -->
        <div class="login-form">
            <form action="" method="post">
                <h2 class="text-center">Please login to access your dashboard</h2>       
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Log in</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <?php 
        require_once("src/html/footer.html");
        require_once("src/php/alert.php");
    ?>
</body>
</html>