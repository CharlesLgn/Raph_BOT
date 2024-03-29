<?php
require_once('src/php/header.php');

// POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == "add" && !empty($_POST['key']) && !empty($_POST['reaction'])) {
        $key = strtolower(sanitise_input($db, $_POST['key']));
        $reaction = sanitise_input($db, $_POST['reaction']);
        $frequency = intval($_POST['frequency']);
        $timeout = intval($_POST['timeout']);
        db_query_no_result($db, "INSERT INTO reactions VALUES (NULL, '$UUID', '$key', '$reaction', '$frequency', '$timeout')");
    }

    if ($_POST['action'] == "del" && !empty($_POST['key'])) {
        $key = addslashes(trim($_POST['key']));
        db_query_no_result($db, "DELETE FROM `reactions` WHERE `UUID` = '$UUID' AND `key` = '$key'");
    }

    if ($_POST['action'] == "edit" && !empty($_POST['key'])) {
        $key = sanitise_input($db, $_POST['key']);
        $reaction = sanitise_input($db, $_POST['value']);
        $frequency = sanitise_input($db, $_POST['frequency']);
        $timeout = sanitise_input($db, $_POST['timeout']);
        db_query_no_result($db, "UPDATE `reactions` SET `reaction` = '$reaction', `frequency` = '$frequency', `timeout` = '$timeout' WHERE `UUID` = '$UUID' AND `key` = '$key'");
    }

    header('Location: reactions.php');
    exit();
}


$HTML = "";
$result = db_query_raw($db, "SELECT * FROM `reactions` WHERE `UUID` = '$UUID'");
while ($row = mysqli_fetch_assoc($result)) {
    $HTML .= "
    <tr>
        <td>" . $row["key"] . "</td>
        <td id='text_" . $row["key"] . "'>" . $row["reaction"] . "</td>
        <td id='freq_" . $row["key"] . "' class='text-center'>" . $row["frequency"] . "</td>
        <td id='time_" . $row["key"] . "' class='text-center'>" . $row["timeout"] . "</td>
        <td>
          <span class='pull-right'>
            <button onClick='edit_entry(\"" . $row["key"] . "\")' class='btn btn-warning' type='button'><i class='glyphicon glyphicon-pencil'></i></button>
            <button type='button' class='btn btn-danger' onclick='del_entry(\"" . $row['key'] . "\")'><i class='glyphicon glyphicon-remove'></i></button>
          </span>
        </td>
    </tr>";
}

// Count
$count = db_query($db, "SELECT COUNT(`key`) as value FROM reactions WHERE `UUID` = '$UUID'")['value'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Reactions - Raph_BOT</title>
    <?php include("src/html/header.html"); ?>
</head>

<body>
    <!-- TOP Navbar -->
    <?php include("src/php/navbar.php"); ?>

    <!-- Side bar-->
    <?php include("src/html/sidebar.html"); ?>

    <!-- Main area -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Reactions (<?php echo $count; ?>)
            <span class='pull-right'>
                <button type="button" class="btn btn-success" onclick='add_entry()'><i class="glyphicon glyphicon-plus"></i></button>
            </span>
        </h1>

        <!-- Add command -->
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th class="col-xs-2">Trigger</th>
                    <th class="col-xs-7">Reaction</th>
                    <th class="col-xs-1">Frequency (%)</th>
                    <th class="col-xs-1">Timeout (s)</th>
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
            document.getElementById("reactions").className = "active";
        });

        function add_entry() {
            Swal.fire({
                title: "Add entry",
                html: "<form id='swal-form' method='post'>" +
                    "<input type='hidden' name='action' value='add'>" +
                    "<label>Trigger</label><input type='text' class='form-control' name='key' placeholder='Trigger' required><br/>" +
                    "<label>Reaction</label><textarea class='form-control' rows='2' name='reaction'></textarea><br/>" +
                    "<label>Frequency (%)</label><input type='number' class='form-control' name='frequency' min=0 step=1 max=100 required><br/>" +
                    "<label>Timeout (s)</label><input type='number' class='form-control' name='timeout' min=0 step=1 required><br/>" +
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

        function del_entry(key) {
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
                    $.post("reactions.php", {
                        action: "del",
                        key: key
                    }, function(data) {
                        document.location.reload();
                    });
                }
            })
        }

        function edit_entry(key) {
            text = document.getElementById("text_" + key).innerText;
            freq = document.getElementById("freq_" + key).innerText;
            time = document.getElementById("time_" + key).innerText;
            Swal.fire({
                title: 'Editing : "' + key + '"',
                type: 'info',
                html: "<form id='swal-form' method='post'>" +
                    "<input type='hidden' name='action' value='edit'>" +
                    "<input type='hidden' name='key' value='" + key + "'>" +
                    "<label>Reaction</label><textarea class='form-control' rows='2' name='value'>" + text + "</textarea><br/>" +
                    "<label>Frequency (%)</label><input class='form-control' type='number' name='frequency' min=0 step=1 max=100 value=\"" + freq + "\"><br/>" +
                    "<label>Timeout (s)</label><input class='form-control' type='number' name='timeout' min=0 step=1 value=\"" + time + "\"><br/>" +
                    "</form>",
                showCancelButton: true,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "30%",
                confirmButtonText: 'Edit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value)
                    document.getElementById('swal-form').submit();
            })
        }
    </script>

</body>

</html>