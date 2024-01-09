<?php

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "app_users";
$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Something went wrong;");
}

?>
