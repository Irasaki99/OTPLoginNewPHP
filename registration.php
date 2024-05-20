<?php
session_start();
include 'conn.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    // Password policy: mixed characters and minimum length of 8 characters
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
        echo "<script> alert('Password must contain at least one uppercase letter, one lowercase letter, one digit, and be at least 8 characters long.'); window.history.back(); </script>";
        exit();
    }

    // Check if email is already registered
    $sql_check_email = "SELECT * FROM users WHERE email = '$email'";
    $result_check_email = mysqli_query($conn, $sql_check_email);
    if (mysqli_num_rows($result_check_email) > 0) {
        echo "<script> alert('This email is already registered.'); window.history.back(); </script>";
        exit();
    }

    // Check if passwords match
    if ($password !== $repeat_password) {
        echo "<script> alert('Passwords do not match.'); window.history.back(); </script>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        ?>
        <script>
            alert("Registration Successful.");
            window.location.href = 'index.php';
        </script>
        <?php
    } else {
        echo "<script> alert('Registration Failed. Try Again');</script>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <!--<style type="text/css">
        #container{
            border: 1px solid blue; /* Change border color to blue */
            border-radius: 10px; /* Make border edges round with a radius of 10px */
            width: 450px;
            padding: 20px;
            margin: auto;
        }
        form{
            margin-left: 50px;
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
            border-radius: 10px; /* Make border edges round with a radius of 10px */
            color: white;
            font-weight: bold;
            padding: 7px;
            margin-left: 130px;
        }
        input[type=submit]:hover{
            background-color: purple;
            cursor: pointer;
            border: 1px solid purple;
            border-radius: 10px; /* Make border edges round with a radius of 10px */
        }
    </style>-->
</head>
<body>
    <div class="container">
        <form method="post" action="registration.php">
            <label for="username">Username:</label>
            <div class="form-group">
            <input type="text" name="username" placeholder="Enter Username" class="form-control" required>
            </div>
            <label for="email">Email:</label>
            <div class="form-group">
            <input type="text" name="email" placeholder="Enter Your Email" class="form-control" required>
            </div>
            <label for="password">Password:</label>
            <div class="form-group">
            <input type="password" name="password" placeholder="Enter Password" class="form-control" required>
            </div>
            <label for="repeat_password">Repeat Password</lable>
            <div class="form-group">
            <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password" required>
            </div>
            <div class="form-btn">
            <input type="submit" name="register" value="Register" class="btn btn-primary"><br><br>
            </div>
            <label>Already have an account? </label><a href="index.php">Login</a>
        </form>
    </div>

</body>
</html>