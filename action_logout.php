<?php
session_start();
$_SESSION['user_id'] = array();
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

session_destroy();

header("Location: page_home.php");
exit();
?>