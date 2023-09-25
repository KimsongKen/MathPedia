<?php
require 'vendor/autoload.php';

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include __DIR__ . '/.functions.php';

date_default_timezone_set("Asia/Bangkok");
$conn = connectToDatabase();  // Use the function to connect to the database

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $occupation = $_POST['occupation'];

    $dateTime = new DateTime();
    $dateTime->modify('+20 minutes');
    $currentTime_20min = $dateTime->format('Y-m-d H:i:s');

    $sql_checkEmailVerified = "SELECT email_verified FROM USER WHERE Email = ?";
    $stmt_checkEmailVerified = $conn->prepare($sql_checkEmailVerified);
    $stmt_checkEmailVerified->bind_param("s", $email);
    $stmt_checkEmailVerified->execute();
    $stmt_checkEmailVerified->store_result();
    $stmt_checkEmailVerified->bind_result($email_verified);
    $stmt_checkEmailVerified->fetch();

    if ($stmt_checkEmailVerified->num_rows>0 && $email_verified) { # email is used
        echo 'Email is already registered';
        exit(2);
    } 
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($stmt_checkEmailVerified->num_rows>0) {
        $sql = "UPDATE USER SET username=?, password_hash=?, occupation=?, dateOfBirth=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $password_hash, $occupation, $dateOfBirth, $email);
        $stmt->execute();
        delayHome();
    } else {
        $token = bin2hex(random_bytes(50));
        $mail = new PHPMailer(true);

        // TABLE: USER... 
        $sql = "INSERT INTO USER (username, email, password_hash, occupation, dateOfBirth) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $password_hash, $occupation, $dateOfBirth);
        
        $stmt->execute();

        // TABLE: USER... fetch user id for token_verification
        session_start();
        $sql = "SELECT user_id from User WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user_id_results = $stmt->get_result();
        $rows = $user_id_results->fetch_assoc();
        $user_id = $rows['user_id'];

        // TABLE: token_verification
        $sql = "INSERT INTO token_verification (token, user_id, expires_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $user_id, $currentTime_20min);
        if ($stmt->execute()) {
            echo 'successfully tokenize.<br>';
            delayHome();
        } else {
            echo 'failed to tokenize.<br>';
            delayHome();
            exit(3);
        }

        try {
            // Server settings
            $mail->isSMTP();                                        // Enable SMTP
            $mail->Host       = 'smtp.gmail.com';                   // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                               // Enable SMTP authentication
            $mail->Username   = 'huangpongsiri@gmail.com';           // Your Gmail address
            $mail->Password   = 'blfxhpadpuenghpp';                  // Your Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
            $mail->Port       = 587;                                // TCP port to connect to

            // Recipients
            $mail->setFrom('noreply@yourwebsite.com', 'EduQuery');
            $mail->addAddress($email);                          // Add recipient (user's Gmail address)

            // Content
            $mail->isHTML(true);                                  // Enable HTML in email
            $mail->Subject = 'Please Verify Your Email Address';
            $mail->Body = "Click on the link to verify: <a href='http://localhost/xampp/Project/CE4221-project//email_token_verify.php?token=" . $token . "&email=" . $email . "'>Verify Email</a>";

            // Send email
            $mail->send();
            echo 'Verification email has been sent <br>';
            delayHome();
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

?>