<?php
session_start();

include __DIR__ . '/.functions.php';

function loginUser($conn, $username, $password) {
    $sql = "SELECT * FROM USER WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            if ($row['email_verified']) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $row['user_id'];
                return 'Login successful';
            }
            else {
                return 'Please verify the email';
            }
        } else {
            return 'Invalid password';
        }
    }
    return 'User does not exist';
}

// Main Execution
try {
    $conn = connectToDatabase();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $loginStatus = loginUser($conn, $username, $password);

        if ($loginStatus === 'Login successful') {
            echo $loginStatus;
            echo '
                <p> loading... </p>
                <script type="text/javascript">
                    setTimeout(function() {
                        window.location.href = "page_home.php";
                    }, 500);
                </script>';
        } else {
            echo $loginStatus;
        }
    }
} catch (Exception $e) {
    echo '<div><h1>Error: ' . $e->getMessage() . '</h1></div>';
} finally {
    $conn->close();
}
?>
