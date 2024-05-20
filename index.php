<?php
session_start();
include 'conn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $_SESSION['email'] = $email;

    $sql = "SELECT * FROM users WHERE email='$email'";
    $query = mysqli_query($conn, $sql);
    $data = mysqli_fetch_array($query);

    if ($data) {
        // Check if the account is locked
        if ($data['locked_until'] && strtotime($data['locked_until']) > time()) {
            ?>
            <script>
                alert("Your account is locked. Please try again after 3 minutes.");
                window.location.href = 'index.php';
            </script>
            <?php
            exit();
        }

        if (password_verify($password, $data['password'])) {
            $otp = rand(100000, 999999);
            $otp_expiry = date("Y-m-d H:i:s", strtotime("+3 minute"));
            $subject= "Your OTP for Login";
            $message="Your OTP is: $otp";

            $sql_reset_attempts = "UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL, locked_until = NULL WHERE id=".$data['id'];
            mysqli_query($conn, $sql_reset_attempts);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'irfan.ito.99@gmail.com'; //host email 
            $mail->Password = 'fdevayzyczadkmln'; // app password of your host email
            $mail->Port = 465;
            $mail->SMTPSecure = 'ssl';
            $mail->isHTML(true);
            $mail->setFrom('irfan.ito.99@gmail.com', 'One Time Password');//Sender's Email & Name
            $mail->addAddress($email); //Receiver's Email and Name
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();

            $sql1 = "UPDATE users SET otp='$otp', otp_expiry='$otp_expiry' WHERE id=".$data['id'];
            $query1 = mysqli_query($conn, $sql1);

            $_SESSION['temp_user'] = ['id' => $data['id'], 'otp' => $otp];
            header("Location: otp_verification.php");
            exit();
        } else {
            // Failed login attempt
            $failed_attempts = isset($data['failed_attempts']) ? $data['failed_attempts'] + 1 : 1;
            $last_failed_attempt = date("Y-m-d H:i:s");

            if ($failed_attempts >= 3) {
                // Lock the account if failed attempts exceed a threshold
                $locked_until = date("Y-m-d H:i:s", strtotime("+3 minute"));
                $sql_update_attempts = "UPDATE users SET failed_attempts = $failed_attempts, last_failed_attempt = '$last_failed_attempt', locked_until = '$locked_until' WHERE email='$email'";
                mysqli_query($conn, $sql_update_attempts);
                ?>
                <script>
                    alert("Your account has been locked due to multiple failed login attempts. Please try again after 3 minutes.");
                    window.location.href = 'index.php';
                </script>
                <?php
                exit();
            } else {
                // Update failed attempts
                $sql_update_attempts = "UPDATE users SET failed_attempts = $failed_attempts, last_failed_attempt = '$last_failed_attempt' WHERE email='$email'";
                mysqli_query($conn, $sql_update_attempts);
                ?>
                <script>
                    alert("Invalid Email or Password. Please try again.");
                    window.location.href = 'index.php';
                </script>
                <?php
                exit();
            }
        }
    } else {
        ?>
        <script>
            alert("Invalid Email or Password. Please try again.");
            window.location.href = 'index.php';
        </script>
        <?php
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
<!--<style type="text/css">
        #container{
            
            border: 1px solid blue;
            border-radius: 10px; /* Make border edges round with a radius of 10px */
            width: 440px;
            padding: 20px;
            margin: auto;
        }
        input[type=text],input[type=password]{
            width: 300px;
            height: 20px;
            padding: 10px;
        }
        label{
            font-size: 20px;
            font-weight: bold;
        }
        form{
            margin-left: 50px;
        }
        a{
            text-decoration: none;
            font-weight: bold;
            font-size: 21px;
            color: blue;
        }
        a:hover{
            cursor: pointer;
            color: purple;
        }
        input[type=submit]{
            width: 70px;
            background-color: blue;
            border: 1px solid blue;
            color: white;
            font-weight: bold;
            padding: 7px;
            margin-left: 130px;
        }
        input[type=submit]:hover{
            background-color: purple;
            cursor: pointer;
            border: 1px solid purple;
        }
    </style>-->
</head>
<body>
    <div class="container">
        <form method="post" action="index.php">
            <label for="email">Email</label>
            <div class="form-group">
            <input class="form-control" type="text" name="email" placeholder="Enter Your Email" required>
            </div>
            <label for="password">Password:</label>
            <div class="form-group">
            <input class="form-control" type="password" name="password" placeholder="Enter Your Password" required>
            </div>
            <div class="form-btn">
            <input class="btn btn-primary" type="submit" name="login" value="Login">
            </div> 
            <div class="col-auto">
            <label>Don't have an account? </label><a href="registration.php">Sign Up</a>
            </div>
        </form>
    </div>
</body>
</html>
