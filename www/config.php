<?php
require_once('src/php/header.php');

// POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Edit
    if ($_POST['action'] == "edit" && !empty($_POST['id'])) {
        $id = sanitise_input($db, $_POST['id']);
        $value = sanitise_input($db, $_POST['value']);
        db_query_no_result($db, "UPDATE `config` SET `value` = '$value' WHERE `id` = '$id' and `UUID` = '$UUID'");

        $_SESSION["alert"] = ["info", "Restart the bot to apply this change", false];
    }

    header('Location: config.php');
    exit();
}


// Listing
$data = db_query_raw($db, "SELECT * FROM config WHERE `UUID` = '$UUID' ORDER BY id");
$list = "";
while ($row = mysqli_fetch_assoc($data)) {
    $list .= "
    <tr>
        <td>" . $row["id"] . "</td>
        <td id='value_" . $row["id"] . "'>" . $row["value"] . "</td>
        <td><button onClick='edit_entry(\"" . $row["id"] . "\")' class='btn btn-warning pull-right' type='button'><i class='glyphicon glyphicon-pencil'></i></button></td>
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
        <h1 class="page-header">Configuration</h1>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th class="col-xs-2">Id</th>
                    <th class="col-xs-7">Value</th>
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
            document.getElementById("core").className = "active";
        });

        function edit_entry(key) {
            value = document.getElementById("value_" + key).innerText;

            Swal.fire({
                title: 'Editing : "' + key + '"',
                type: 'info',
                html: "<form id='swal-form' method='post'>" +
                    "<input type='hidden' name='action' value='edit'>" +
                    "<input type='hidden' name='id' value='" + key + "'>" +
                    "<input class='form-control' type='text' name='value' value=\"" + value + "\"></form>",
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