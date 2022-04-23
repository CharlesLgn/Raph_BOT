<?php
require_once('src/php/header.php');

$lastest_log = file_get_contents("../core/logs/lastest_$UUID.log");

$cmd_time_interval = db_query($db, "SELECT `value` FROM `config` WHERE `UUID` = '$UUID' AND id = 'cmd_time_interval'")['value'];
$cmd_msg_interval  = db_query($db, "SELECT `value` FROM `config` WHERE `UUID` = '$UUID' AND id = 'cmd_msg_interval'")['value'];
$shout_interval  = db_query($db, "SELECT `value` FROM `config` WHERE `UUID` = '$UUID' AND id = 'shout_interval'")['value'];
$port = db_query($db, "SELECT `port` FROM `ports` WHERE `UUID` = '$UUID'")['port'];

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
      <title>Dashboard - Raph_BOT</title>
      <?php include("src/html/header.html"); ?>
  </head>

  <body>
    <!-- TOP Navbar -->
    <?php include("src/php/navbar.php"); ?>

    <!-- Side Navbar -->
    <?php include("src/html/sidebar.html"); ?>

    <!-- Main area -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
      <h1 class="page-header">Dashboard
        <button onclick="start_stop()" type="button" class="btn btn-success" id="btn-start-stop"><i id="ico-start-stop" class="glyphicon glyphicon-play"></i></button>
      </h1>

      <div class="row">
        <div class="col-sm-3">
          <!-- Core Connection -->
          <div class="row">
            <div class="col-sm-3 align-right">Core</div>
            <div class="col-sm-9">
              <div class="progress">
                <div class="progress-bar progress-bar-danger" role="progressbar" style="width:100%" id="core-statut">
                  <span>Disconnected</span>
                </div>
              </div> 
            </div>
          </div>

          <!-- Twitch Connection -->
          <div class="row">
            <div class="col-sm-3 align-right">Twitch</div>
            <div class="col-sm-9">
              <div class="progress">
                <div class="progress-bar progress-bar-warning" role="progressbar" style="width:100%" id="twitch-statut">
                  <span>Waiting for the core</span>
                </div>
              </div> 
            </div>
          </div>
          
        </div>
      
        <div class="col-sm-8 col-sm-offset-1">
          <!-- Auto CMD msg progress -->
          <div class="row">
            <div class="col-sm-3 align-right">Message Trigger</div>
            
            <div class="col-sm-8">
              <div class="progress">
                <div class="progress-bar progress-bar-info" role="progressbar" style="width:0%" id="auto-cmd-msg-bar">
                  <span id="auto-cmd-msg-text"></span>
                </div>
              </div> 
            </div>

            <div class="col-sm-1" id="auto-cmd-msg-counter"></span></div>
          </div>
      
          <!-- Auto CMD time progress -->
          <div class="row">
            <div class="col-sm-3 align-right">Time Trigger</div>
            
            <div class="col-sm-8">
              <div class="progress">
                <div class="progress-bar progress-bar-info" role="progressbar" style="width:0%" id="auto-cmd-time-bar">
                  <span id="auto-cmd-time-text"></span>
                </div>
              </div> 
            </div>

            <div class="col-sm-1" id="auto-cmd-time-counter"></span></div>
          </div>

          <!-- Shout progress -->
          <div class="row">
            <div class="col-sm-3 align-right">Shout Trigger</div>
            <div class="col-sm-8">
              <div class="progress">
                <div class="progress-bar progress-bar-warning" role="progressbar" style="width:0%" id="shout-bar">
                  <span id="shout-text"></span>
                </div>
              </div> 
            </div>
          </div>

        </div>
    
      <!-- Log -->
      <div class="col-sm-12">
        <h2 class="page-header">Log</h2>
        <div class="row">
          <div class="col-sm-12">
            <pre id="log" class="log"><?php echo $lastest_log?></pre>
          </div>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <?php include("src/html/footer.html"); ?>

    <script>      
      const time_interval = parseInt("<?php echo $cmd_time_interval;?>");
      const msg_interval = parseInt("<?php echo $cmd_msg_interval;?>");
      const shout_interval = parseInt("<?php echo $shout_interval;?>");
      const port = parseInt("<?php echo $port;?>");
    </script>

    <script src="src/js/socket.io-2.1.1.js"></script>
    <script src="src/js/socket-handler.js"></script>
    
    <script>
      $(document).ready(function() {
        // Active the corresponding button in the navbar
        document.getElementById("index").className="active"; 

        if(time_interval == 0){
            document.getElementById('auto-cmd-time-bar').style.width = "100%";
            document.getElementById('auto-cmd-time-bar').className = "progress-bar progress-bar-danger progress-bar-striped";
            document.getElementById('auto-cmd-time-text').innerHTML = "Disabled";
        }
        else {
            document.getElementById('auto-cmd-time-bar').style.width = "100%";
            document.getElementById('auto-cmd-time-bar').className = "progress-bar progress-bar-success progress-bar-striped";
            document.getElementById('auto-cmd-time-text').innerHTML = "Enabled (" + time_interval + ")";
            document.getElementById('auto-cmd-time-counter').innerHTML = "0";
        }

        if(msg_interval == 0){
            document.getElementById('auto-cmd-msg-bar').style.width = "100%";
            document.getElementById('auto-cmd-msg-bar').className = "progress-bar progress-bar-danger progress-bar-striped";
            document.getElementById('auto-cmd-msg-text').innerHTML = "Disabled";
        }
        else {
            document.getElementById('auto-cmd-msg-bar').style.width = "100%";
            document.getElementById('auto-cmd-msg-bar').className = "progress-bar progress-bar-success progress-bar-striped";
            document.getElementById('auto-cmd-msg-text').innerHTML = "Enabled (" + msg_interval + ")";
            document.getElementById('auto-cmd-msg-counter').innerHTML = "0";
        }

        if(shout_interval == 0){
            document.getElementById('shout-bar').style.width = "100%";
            document.getElementById('shout-bar').className = "progress-bar progress-bar-danger progress-bar-striped";
            document.getElementById('shout-text').innerHTML = "Disabled";
        }
        else {
            document.getElementById('shout-bar').style.width = "100%";
            document.getElementById('shout-bar').className = "progress-bar progress-bar-success progress-bar-striped";
            document.getElementById('shout-text').innerHTML = "Enabled (" + shout_interval + ")";
        }

      });
    </script>

</body></html>