<?php
$site = "EduQuery";
include __DIR__ . '/.functions.php';
$conn = connectToDatabase();


/*
    fetch thread details using thread_id.
    result: $thread_title, $thread_content
*/
$thread_id = $_GET['thread_id'];

$sql_thread = "SELECT * FROM THREAD WHERE thread_id = ?";
$stmt_thread = $conn->prepare($sql_thread);
$stmt_thread->bind_param("i", $thread_id);
$stmt_thread->execute();
$result_thread = $stmt_thread->get_result();

if ($result_thread->num_rows > 0) {
    $row = $result_thread->fetch_assoc();
    $thread_title = $row['title'];
    $thread_content_md = $row['content'];
} else {
    echo "Thread does not exist";
    exit(2);
}

require 'Parsedown.php';
$Parsedown = new Parsedown();
$thread_content_html = $Parsedown->text($thread_content_md);

$stmt_thread->close();

/*
    fetch thread's comments.
    results: $comments
*/
$sql_comments = "SELECT * FROM COMMENT WHERE thread_id = ?";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("i", $thread_id);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();

$comments = array();
while ($row = $result_comments->fetch_assoc()) {
    $comments[] = $row;
}
$stmt_comments->close();

$conn->close();

include 'page_thread.html';
?>