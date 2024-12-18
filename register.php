<?php 
   session_start(); 
   require 'config.php';
   require 'connect.php';
   
   $smtpPass = $_ENV['SMTP_PASS'];
   $smtpUser = $_ENV['SMTP_USER'];

   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\Exception;
  
   
   
   // Function to send email using PHPMailer
   function sendVerificationCode($email, $code){
       global $smtpUser, $smtpPass;
       $mail = new PHPMailer(true);
       try {
           // Server settings
           $mail->isSMTP();
           $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
           $mail->SMTPAuth   = true;
           $mail->Username   = $smtpUser; // SMTP username
           $mail->Password   = $smtpPass; // SMTP password
           $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
           $mail->Port       = 587;

           // Recipients
           $mail->setFrom('no-reply@az-news.com', 'az-news');
           $mail->addAddress($email);

           // Content
           $mail->isHTML(true);
           $mail->Subject = 'Your Verification Code';
           $mail->Body    = "Your verification code is: <b>$code</b>";

           $mail->send();
           return true;
       } catch (Exception $e) {
           // Log error message
           error_log("Mail Error: " . $mail->ErrorInfo);
           return false;
       }
   }
if(isset($_POST['action'])){
    $action = $_POST['action'];

    if($action == 'sendCode'){
        $email = $_POST['email'];

        // Check if email exists in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            // Generate a random 6-digit code
            $code = rand(100000, 999999);
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_email'] = $email;

            // Send the code via email
            if(sendVerificationCode($email, $code)){
                echo 'Verification code sent to your email.';
            } else {
                echo 'Failed to send verification code. Please try again.';
            }
        }
        else{
            echo 'Email address not found.';
        }
        exit();
    }

    if($action == 'verifyCode'){
        $email = $_POST['email'];
        $code = $_POST['code'];

        if(isset($_SESSION['reset_code']) && isset($_SESSION['reset_email'])){
            if($_SESSION['reset_email'] == $email && $_SESSION['reset_code'] == $code){
                // Code is correct
                echo 'Code verified. You can now reset your password.';
            }
            else{
                echo 'Invalid verification code.';
            }
        }
        else{
            echo 'No verification code found. Please request a new one.';
        }
        exit();
    }
}

// Existing Sign Up Logic
if(isset($_POST['signUp'])){

    session_unset();
    session_destroy();
    session_start();

    $firstName = $_POST['fName'];
    $lastName  = $_POST['lName'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];
    $password  = md5($password);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $_SESSION['error'] = 'Email Address Already Exists!';
        header("Location: sign_up.php");
        exit();
    }
    else{
        $insertQuery = "INSERT INTO users(firstName, lastName, email, password)
                       VALUES ('$firstName', '$lastName', '$email', '$password')";
        if($conn->query($insertQuery) === TRUE){
            $_SESSION['success'] = 'Your account has been created successfully.';
            header("Location: sign_in.php");
            exit();
        }
        else{
            $_SESSION['error'] = 'Error: ' . $conn->error;
            header("Location: sign_up.php");
            exit();
        }
    }
}

// Existing Sign In Logic
if(isset($_POST['signIn'])){

    session_unset();
    session_destroy();
    session_start();

    $email    = $_POST['email'];
    $password = $_POST['password'];
    $password  = md5($password);
   
    $sql    = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        header("Location: homepage.php");
        exit();
    }
    else{
        $_SESSION['error'] = 'Incorrect Email or Password';
        header("Location: sign_in.php");
        exit();
    }

}

// Existing Admin Sign In Logic
if(isset($_POST['AdminSignIn'])){

    session_unset();
    session_destroy();
    session_start();
    
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $password  = md5($password);
    
    $sql    = "SELECT * FROM admin_users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
         $_SESSION['admin_email'] = $email;
         header("Location: homepage.php");
         exit();
    }
    else{
         $_SESSION['error'] = 'Incorrect Admin Email or Password';
         header("Location: admin_login.php");
         exit();
    }

}

// Reset Password Logic
if(isset($_POST['resetPassword'])){
    $email = $_POST['forgotEmail'];
    $newPassword = $_POST['newPassword'];
    $newPassword = md5($newPassword);

    // Check if the email and code are set and verified
    if(isset($_SESSION['reset_email']) && $_SESSION['reset_email'] == $email){
        // Update the user's password
        $updateQuery = "UPDATE users SET password='$newPassword' WHERE email='$email'";
        if($conn->query($updateQuery) === TRUE){
            // Unset the reset session variables
            unset($_SESSION['reset_code']);
            unset($_SESSION['reset_email']);
            $_SESSION['success'] = 'Password has been reset successfully.';
            header("Location: sign_in.php");
            exit();
        }
        else{
            $_SESSION['error'] = 'Error updating password: ' . $conn->error;
            header("Location: forget_password.php");
            exit();
        }
    }
    else{
        $_SESSION['error'] = 'Invalid password reset request.';
        header("Location: forget_password.php");
        exit();
    }
}
?>