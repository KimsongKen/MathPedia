<?php
require 'vendor/autoload.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set("Asia/Bangkok");

include __DIR__ . '/.functions.php';
$conn = connectToDatabase();

/* 

*/
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];

    $dateTime = new DateTime();
    $dateTime->modify('+20 minutes');
    $currentTime_20min = $dateTime->format('Y-m-d H:i:s');

    $sql_checkEmailVerified = "SELECT User_id, Email_verified FROM USER WHERE Email = ?";
    $stmt = $conn->prepare($sql_checkEmailVerified);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows>0) {
        $stmt->bind_result($user_id, $email_verified);
        $stmt->fetch();

        // Email exists and verified
        if ($email_verified) { 
            $token = bin2hex(random_bytes(50));
            $mail = new PHPMailer(true);
            
            // insert token into TOKEN_PASSWORD_RESET
            $sql = "INSERT INTO TOKEN_PASSWORD_RESET (token, user_id, expires_at) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $token, $user_id, $currentTime_20min);
            if ($stmt->execute()) {
                echo 'successfully tokenize.<br>';
            } else {
                echo 'failed to tokenize.<br>';
                exit(3);
            }

            try {
                $current_directory = 'http://localhost/xampp/Project/CE4221-project/';
                // Server settings
                $mail->isSMTP();                                        // Enable SMTP
                $mail->Host       = 'smtp.gmail.com';                   // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                               // Enable SMTP authentication
                $mail->Username   = 'huangpongsiri@gmail.com';           // Your Gmail address
                $mail->Password   = 'blfxhpadpuenghpp';                  // 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
                $mail->Port       = 587;                                // TCP port to connect to
    
                // Recipients
                $mail->setFrom('noreply@yourwebsite.com', 'EduQuery');
                $mail->addAddress($email);                          // Add recipient (user's Gmail address)
    
                // Content
                $mail->isHTML(true);                                  // Enable HTML in email
                $mail->Subject = 'Please Verify Your Email Address';
                $mail->Body = "Click on the link to verify: <a href='{$current_directory}/page_set_new_password.php?token={$token}&email={$email}'>Verify Email</a>";
                
                // Send email
                $mail->send();
                echo 'Verification email has been sent <br>';
                echo '
                <script type="text/javascript">
                    setTimeout(function() {
                        window.close();
                        window.opener.location.href = "page_home.php";
                    }, 500);
                </script>
            ';
            } catch (Exception $e) {
                echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo 'Email is not verified. <br>';
        }
    } else {
        echo 'Email is not registered. <br>';
        exit(2);
    }
}
?>