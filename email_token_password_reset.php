<?php
require 'vendor/autoload.php';

include __DIR__ . '/.functions.php';
$conn = connectToDatabase();


if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $currentTime = (new DateTime())->format('Y-m-d H:i:s');

    // Retrieve and validate the token from your database
    $sql = "SELECT TOKEN_PASSWORD_RESET.* FROM TOKEN_PASSWORD_RESET
            INNER JOIN USER ON TOKEN_PASSWORD_RESET.user_id = USER.user_id
            WHERE USER.Email = ?
             AND ? < TOKEN_PASSWORD_RESET.expires_at 
             AND TOKEN_PASSWORD_RESET.status = 'active'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $currentTime);
    $stmt->execute();
    $results = $stmt->get_result();
    $isTokenValid = false;

    // Check if there is any valid token and set token status to `used`
    while ($row = $results->fetch_assoc()) {
        if ($row['token'] == $token) {
            $isTokenValid = true;

            $sql = "UPDATE TOKEN_PASSWORD_RESET SET status = 'used' WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();
            break;
        } 
    }

    if (!$isTokenValid) {
        echo 'Invalid or expired token';
        exit(3);
    } else {
        include 'page_set_new_password.html';
    }
}   
$conn->close();
?>
