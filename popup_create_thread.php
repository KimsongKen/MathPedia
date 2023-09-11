<?php
session_start();

include __DIR__ . '/.functions.php';
$conn = connectToDatabase();


if (!isset($_SESSION['user_id'])) {
    header("Location: page_login.php");
    exit(2);
}

$tags = array();

// Fetch all tags
$sql_tags = "SELECT name FROM tags";
$results_tags = $conn->query($sql_tags);
while ($row = $results_tags->fetch_assoc()) {
    $tags[] = $row['name'];
}

session_abort();
include 'popup_create_thread.html';
?>

