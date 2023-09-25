<?php
// to validate email when registered.
require 'vendor/autoload.php';

include __DIR__ . '/.functions.php';
$conn = connectToDatabase();


if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $currentTime = (new DateTime())->format('Y-m-d H:i:s');

    $sql = "SELECT TOKEN_VERIFICATION.* FROM TOKEN_VERIFICATION
            INNER JOIN USER ON TOKEN_VERIFICATION.user_id = USER.user_id
            WHERE USER.Email = ?
            AND ? < TOKEN_VERIFICATION.expires_at 
            AND TOKEN_VERIFICATION.status = 'active'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $currentTime);
    $stmt->execute();
    $results = $stmt->get_result();
    $isTokenValid = false;

    while ($row = $results->fetch_assoc()) {
        if ($row['token'] == $token) {
            $isTokenValid = true;

            $sql = "UPDATE TOKEN_VERIFICATION SET status = 'used' WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();
            break;
        } 
    }

    if ($isTokenValid) {
        $sql = "UPDATE USER SET email_verified = TRUE WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            echo "Email verified successfully!\n";
            delayHome();
        } else {
            echo "Failed to verify email.\n";
        }
    } else {
        echo "Invalid or expired token.\n";
    }

    $stmt->close();
}   
$conn->close();
?>
