<?php
include __DIR__ . '/.functions.php';

$conn = connectToDatabase();
$email = $_GET['email'];
$token = $_GET['token'];

$stmt = $conn->prepare("SELECT t.token
                        FROM USER u 
                        INNER JOIN token_password_reset t ON u.user_id = t.user_id
                        WHERE t.token = ? and u.email = ? and t.status = 'active'");
$stmt->bind_param("ss", $token, $email);
$stmt->execute();
$results = $stmt->get_result();

$isTokenValid = false;
while ($row = $results->fetch_assoc()) {
    if ($row['token'] == $token) {
        $isTokenValid = true;

        $sql = "UPDATE token_password_reset SET status = 'used' WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
        break;
    }
}  
if ($isTokenValid == FALSE) {
    echo '
    <script type="text/javascript">
        setTimeout(function() {
            window.location.href = "page_home.php";
        }, 500);
    </script>
    ';
    echo '<script>alert("invalid or expired token");</script>';
}

include 'page_set_new_password.html';
?>