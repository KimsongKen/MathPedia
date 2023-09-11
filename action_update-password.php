<?php
require 'vendor/autoload.php';
include __DIR__ . '/.functions.php';
$conn = connectToDatabase();


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new-password'];

    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $sql_updatePassword = "UPDATE USER SET Password_hash = ? WHERE Email = ?";
    $stmt = $conn->prepare($sql_updatePassword);
    $stmt->bind_param("ss", $password_hash, $email);
    if ($stmt->execute()) {
        echo "Password updated successfully. <br>";
        echo '
            <script type="text/javascript">
                setTimeout(function() {
                    window.location.href = "page_home.php";
                }, 500);
            </script>
        ';
    } else {
        echo "Error: " . $stmt->error;
    }
}
$stmt->close();
$conn->close();
?>