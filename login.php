<?php
session_start();
include "connect.php";
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>BakeryLand</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="mobile-web-app-capable" content="yes">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="./css/login.css" rel="stylesheet" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
  <script src="main.js"></script>
</head>

<body>
  <form action="./manage/check-login.php" method="POST">
    <h1>Bakery Land</h1>
    <div class="login">
      Email: <br><input type="email" name="email" class="login-input" required><br>
      Password: <br><input type="password" name="password" class="login-input" required><br>
      <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <p style="color: red; font-size: 16px;">Wrong Email or Password</p>
      <?php endif; ?>
      <button type="submit">LOGIN</button>
      Don't have an account?<a href="register.php">Sign up</a>
    </div>
  </form>
</body>

</html>