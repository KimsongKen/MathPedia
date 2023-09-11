<?php 
session_start();
$is_logged_in = isset($_SESSION['user_id']);

$site = "EduQuery";

include __DIR__ . '/.functions.php';

function fetchTags($conn) {
    $tags = array();
    $sql = "SELECT name FROM tags";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row['name'];
        }
    } else {
        echo "Error fetching tags.";
    }
    return $tags;
}

function fetchThreads($conn) {
    $threads = array();
    $sql = "SELECT * FROM THREAD ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $thread_id = $row['thread_id'];
            $threads[$thread_id] = ['title' => $row['title'],
                                    'vote_count' => $row['vote_count'],
                                    'tags' => []];
        }
    } else {
        echo "Error fetching threads.";
    }
    return $threads;
}

function fetchThreadTags($conn, &$threads) {
    $sql = "SELECT t.thread_id, tg.name 
            FROM thread_tags AS t 
            JOIN tags AS tg ON t.tag_id = tg.id";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $thread_id = $row['thread_id'];
            $tag_name = $row['name'];
            $threads[$thread_id]['tags'][] = $tag_name;
        }
    } else {
        echo "Error fetching thread tags.";
    }
}

// Main Execution
try {
    $conn = connectToDatabase();
    $tags = fetchTags($conn);
    $threads = fetchThreads($conn);
    fetchThreadTags($conn, $threads);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
session_abort();
include 'page_home.html';
?>
