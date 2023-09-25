<?php
$site = "EduQuery";
session_start();
$is_logged_in = isset($_SESSION['user_id']);

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

// Function to search for threads
function searchThreads($conn, $search_words) {
    $search_pattern = "%" . $search_words . "%";
    $sql = "SELECT * FROM THREAD 
            WHERE title LIKE ? 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_pattern);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $threads = array();
        while ($row = $result->fetch_assoc()) {
            $thread_id = $row['thread_id'];
            $threads[$thread_id] = $row;
            $threads[$thread_id]['tags'] = [];  // Initialize tags as an empty array
        }
        return $threads;
    } else {
        return null;
    }
}

// Function to fetch tags for searched threads
function fetchSearchedThreadTags($conn, $search_words, &$threads) {
    $search_pattern = "%" . $search_words . "%";
    $sql = "SELECT t.thread_id, tg.name 
            FROM thread_tags AS t 
            JOIN tags AS tg ON t.tag_id = tg.id
            JOIN THREAD AS th ON t.thread_id = th.thread_id
            WHERE th.title LIKE ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_pattern);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $thread_id = $row['thread_id'];
            $tag_name = $row['name'];

            // Only add tags to threads that exist in the passed array
            if (array_key_exists($thread_id, $threads)) {
                $threads[$thread_id]['tags'][] = $tag_name;
            }
        }
    } else {
        echo "Error fetching tags for searched threads.";
    }
}


// Main Execution
try {
    $conn = connectToDatabase();
    
    $tags = fetchTags($conn);

    $search_words = $_GET['search_words'];
    $searched_threads = searchThreads($conn, $search_words);
    fetchSearchedThreadTags($conn, $search_words, $searched_threads);
    $threads = $searched_threads;
    if (!$searched_threads) {
        // echo 'No content found';
        
    }
    
} catch (Exception $e) {
    echo '<div><h1>Error: ' . $e->getMessage() . '</h1></div>';
} finally {
    $conn->close();
}

session_abort();
include 'page_home.html';
?>
