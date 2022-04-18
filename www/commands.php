<?php
require_once('src/php/header.php');

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if($_POST['action'] == "add" && !empty($_POST['command'])){
    $command = strtolower(addslashes(trim($_POST['command'])));
    $text = addslashes(trim($_POST['text']));
    db_query_no_result($db, "INSERT INTO `commands` VALUES (NULL, '$UUID', '$command', '$text', 0)");
  }

  if($_POST['action'] == "del" && !empty($_POST['command'])){
    $command = addslashes(trim($_POST['command']));
    db_query_no_result($db, "DELETE FROM `commands` WHERE `UUID` = '$UUID' AND `command` = '$command'");
  }

  if($_POST['action'] == "edit" && !empty($_POST['command'])){
    $command = addslashes(trim($_POST['command']));
    $text = addslashes(trim($_POST['text']));
    $auto = isset($_POST['auto']) ? 1 : 0;
    db_query_no_result($db, "UPDATE `commands` SET `text` = '$text', `auto` = '$auto' WHERE `UUID` = '$UUID' AND `command` = '$command'");
  }

  header('Location: commands.php');
  exit();
}


$HTML = "";
$result = db_query_raw($db, "SELECT * FROM commands WHERE `UUID` = '$UUID'");
while($row = mysqli_fetch_assoc($result)) {
    $HTML .= "
    <tr>
        <td>".$row["command"]."</td>
        <td><input type='checkbox' class='checkbox' ".($row['auto'] ? "checked" : "")." disabled></td>
        <td id='value_".$row["command"]."'>".$row["text"]."</td>
        <td>
          <span class='pull-right'>
            <button onClick='edit_entry(\"".$row["command"]."\", \"".$row["auto"]."\")' class='btn btn-warning' type='button'><i class='glyphicon glyphicon-pencil'></i></button>
            <button type='button' class='btn btn-danger' onclick='del_entry(\"".$row['command']."\")'><i class='glyphicon glyphicon-remove'></i></button>
          </span>
        </td>
    </tr>";
}

// Count
$count = db_query($db, "SELECT COUNT(`command`) as value FROM commands WHERE `UUID` = '$UUID'")['value'];

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Commands - Raph_BOT</title>
    <?php include("src/html/header.html"); ?>
  </head>

  <body>
    <!-- TOP Navbar -->
    <?php include("src/php/navbar.php"); ?>

    <!-- Side bar-->
    <?php include("src/html/sidebar.html"); ?> 

    <!-- Main area -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
      <h1 class="page-header">Commands (<?php echo $count;?>)
        <div class='pull-right'>
          <button type="button" class="btn btn-success" onclick='add_entry()'><i class="glyphicon glyphicon-plus"></i></button>
        </div>
      </h1>

      <!-- Add command -->
      <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th class="col-xs-2">Command</th>
                <th class="col-xs-1">Auto</th>
                <th class="col-xs-8">Text</th>
                <th class="col-xs-1"></th>
            </tr>
          </thead>
        <tbody>
            <?php echo $HTML; ?>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <?php include("src/html/footer.html"); ?>

    <script>
      $(document).ready(function() {
        // Active the corresponding button in the navbar
        document.getElementById("commands").className="active"; 
      });

      function add_entry(){
        Swal.fire({
            title: "Add entry",
            html:   "<form id='swal-form' method='post'>"+
                    "<input type='hidden' name='action' value='add'>"+
                    "<label>Command</label><input type='text' class='form-control' name='command' placeholder='Command' required><br/>"+
                    "<label>Text</label><textarea class='form-control' rows='2' name='text'></textarea>"+
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

      function del_entry(key){
        Swal.fire({
          title: "Delete '" + key + "' ?",
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText: 'No',
          focusCancel: true
        }).then((result) => {
          if (result.value) {
              $.post("commands.php", { action : "del", command: key }, function(data){
                  document.location.reload();
              }); 
          }
        })
      }

      function edit_entry(key, auto){
        value = document.getElementById("value_" + key).innerText;

        if(auto == 1)
          checkbox = "checked";
        else
          checkbox = "";

        Swal.fire({
            title: 'Editing : "' + key + '"',
            type: 'info',
            html: "<form id='swal-form' method='post'>"+
                  "<input type='hidden' name='action' value='edit'>"+
                  "<input type='hidden' name='command' value='" + key + "'>"+
                  "<label>Text</label><textarea class='form-control' rows='2' name='text'>"+ value +"</textarea>"+
                  "<label>Auto</label><input class='form-control' type='checkbox' name='auto' " + checkbox + ">"+
                  "</form>",
            showCancelButton: true,
            focusConfirm: false,
            allowOutsideClick: false,
            width: "30%",
            confirmButtonText: 'Edit',
            cancelButtonText: 'Cancel'
        }).then((result) =>{
            if(result.value)
                document.getElementById('swal-form').submit();
        })
      }
    </script>

</body></html>