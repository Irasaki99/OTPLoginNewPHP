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

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <style type="text/css">
        #container{
            border: 1px solid black;
            width: 450px;
            padding: 20px;
            margin-left: 400px;
            margin-top: 50px;
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
    </style>
</head>
<body>
    <div id="container">
        <form method="post" action="registration.php">
            <label for="username">Username:</label><br>
            <input type="text" name="username" placeholder="Enter Username" required><br><br>

            <label for="email">Email:</label><br>
            <input type="text" name="email" placeholder="Enter Your Email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" placeholder="Enter Password" required><br><br>
            <input type="submit" name="register" value="Register"><br><br>
            <label>Already have an account? </label><a href="index.php">Login</a>
        </form>
    </div>

</body>
</html>