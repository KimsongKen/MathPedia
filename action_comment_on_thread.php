<?php
session_start();
include __DIR__ . '/.functions.php';

$conn = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = $_SESSION['user_id'];
    $thread_id = $_GET['thread_id'];
    $comment_content = $_POST['comment_content'];

    $sql = "INSERT INTO COMMENT (user_id, thread_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $thread_id, $comment_content);

    if ($stmt->execute()) {
        header("Location: page_thread.php?thread_id=$thread_id");
    } else {
        echo 'Failed to comment.';
    }
}

$conn->close();
?>