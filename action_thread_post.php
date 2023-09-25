<?php
session_start();

include __DIR__ . '/.functions.php';

function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: quick_login.php");
        exit;
    }
}

function insertThread($conn, $user_id, $title, $content) {
    $sql = "INSERT INTO THREAD (user_id, title, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $title, $content);
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

function manageTags($conn, $tagsArray, $thread_id) {
    foreach ($tagsArray as $tag) {
        $stmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $tag_id = $row['id'];
        } else {
            $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            $tag_id = $conn->insert_id;
        }

        $stmt = $conn->prepare("INSERT INTO thread_tags (thread_id, tag_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $thread_id, $tag_id);
        if ($stmt->execute() === FALSE) {
            throw new Exception("Error inserting into thread_tags: " . $stmt->error);
        }
    }
}

// Main Execution
try {
    $conn = connectToDatabase();
    redirectIfNotLoggedIn();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $user_id = $_SESSION['user_id'];
        $content = $_POST['content'];
        $title = $_POST['title'];
        // $preview_image = $_POST['preview_image'];
        $tagsString = $_POST['tags'];
        $tagsArray = explode(',', $tagsString);

        $thread_id = insertThread($conn, $user_id, $title, $content);
        
        if ($thread_id) {
            manageTags($conn, $tagsArray, $thread_id);
            echo '<p> loading... </p>
                  <script type="text/javascript">
                      setTimeout(function() {
                          window.location.href = "page_home.php";
                      }, 200);
                  </script>';
        } else {
            echo "Error: Failed to insert the thread.";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
