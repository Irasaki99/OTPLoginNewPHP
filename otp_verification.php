<?php
session_start();

include 'conn.php';
if (!isset($_SESSION['temp_user'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST['otp'];
    $stored_otp = $_SESSION['temp_user']['otp'];
    $user_id = $_SESSION['temp_user']['id'];

    $sql = "SELECT * FROM users WHERE id='$user_id' AND otp='$user_otp'";
    $query = mysqli_query($conn, $sql);
    $data = mysqli_fetch_array($query);

    if ($data) {
        $otp_expiry = strtotime($data['otp_expiry']);
        if ($otp_expiry >= time()) {
            $_SESSION['user_id'] = $data['id'];
            unset($_SESSION['temp_user']);
            header("Location: dashboard.php");
            exit();
        } else {
            ?>
                <script>
    alert("OTP has expired. Please try again.");
    function navigateToPage() {
        window.location.href = 'index.php';
    }
    window.onload = function() {
        navigateToPage();
    }
</script>
            <?php 
        }
    } else {
        echo "<script> alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <!--<style type="text/css">
        #container{
            border: 1px solid blue;
            width: 400px;
            margin: auto;
        }
        form{
            margin-left: 50px;
        }
        p{
            margin-left: 50px;
        }
        h1{
            margin-left: 50px;
        }
        input[type=number]{
            width: 290px;
            padding: 10px;
            margin-top: 10px;

        }
        button{
            background-color: blue;
            border: 1px solid blue;
            width: 100px;
            padding: 9px;
            margin-left: 100px;
        }
        button:hover{
            cursor: pointer;
            opacity: .9;
        }
    </style>-->
</head>
<body>
    <div class="container">
        <h1>Two-Step Verification</h1>
        <p>Enter the 6 Digit OTP Code that has been sent <br> to your email address: <?php echo $_SESSION['email']; ?></p>
        <form method="post" action="otp_verification.php">
            <label style="font-weight: bold; font-size: 18px;" for="otp">Enter OTP Code:</label><br>
            <div class="form-group">
                <input type="number" name="otp" pattern="\d{6}" placeholder="Six-Digit OTP" required class="form-control"><br><br>
            </div>
            <div class="form-btn">
            <button type="submit" class="btn btn-primary">Verify OTP</button>
            </div>
        </form>
    </div>
</body>
</html>

