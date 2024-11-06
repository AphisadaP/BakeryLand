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
    <link href="./css/register.css" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="main.js"></script>
    <style>
        .denied {
            font-size: 14px;
            margin-left: 10px;
            margin-top: 1px;
            color: red;
        }
    </style>

    <script>
        var xmlHttp;

        function checkUsername() {
            xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = showUsernameStatus;

            var name = document.getElementById("name").value;
            var url = "manage/check-name.php?name=" + name;
            xmlHttp.open("GET", url);
            xmlHttp.send();
        }

        function checkEmail() {
            xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = showEmailStatus;

            var email = document.getElementById("email").value;
            var url = "manage/check-email.php?email=" + email;
            xmlHttp.open("GET", url);
            xmlHttp.send();
        }

        function showUsernameStatus() {
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                if (xmlHttp.responseText == "Username not available.") {
                    var text = "Username not available.";
                    document.getElementById("result1").className = "denied";
                    document.getElementById("result1").innerHTML = text;
                }
            } else {
                document.getElementById("result1").innerHTML = "";
                document.getElementById("result1").className = "";
            }
        }

        function showEmailStatus() {
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                if (xmlHttp.responseText == "This email address has already been used.") {
                    var text = "This email address has already been used.";
                    document.getElementById("result2").className = "denied";
                    document.getElementById("result2").innerHTML = text;
                }
            } else {
                document.getElementById("result2").innerHTML = "";
                document.getElementById("result2").className = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            function validatePassword() {
                const passwordInput = document.querySelector('input[name="password1"]');
                const confirmPasswordInput = document.querySelector('input[name="password2"]');
                const errorMessage = document.getElementById("passwordError");

                if (passwordInput.value !== confirmPasswordInput.value) {
                    errorMessage.textContent = "Passwords do not match.";
                } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(passwordInput.value)) {
                    errorMessage.textContent = "Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and be at least 8 characters long.";
                } else {
                    errorMessage.textContent = "";
                }
            }

            document.querySelector('input[name="password1"]').addEventListener('input', validatePassword);
            document.querySelector('input[name="password2"]').addEventListener('input', validatePassword);
        });
    </script>

</head>

<body>
    <form action="manage/insert-register.php" method="POST" enctype="multipart/form-data">
        <h1>Bakery Land</h1>
        <div class="register">
            <div class="register-detail">
                <div class="register-box">
                    <span class="title">Username:</span>
                    <div class="box">
                        <input type="name" name="name" id="name" class="register-input" placeholder="Username" required
                            onblur="checkUsername()">
                        <span id="result1" class="denied"></span>
                    </div>
                </div>

                <div class="register-box">
                    <span class="title">Profile:</span>
                    <div class="box">
                        <input type="file" id="CusIMG" name="CusIMG" class="register-input"
                            accept="image/jpg,image/jpeg,image/png" required>
                    </div>
                </div>

                <div class="register-box">
                    <span class="title">Email:</span>
                    <div class="box">
                        <input type="email" name="email" id="email" class="register-input" placeholder="Email" required
                            onblur="checkEmail()">
                        <span id="result2" class="denied"></span>
                    </div>
                </div>

                <div class="register-box">
                    <span class="title">Phone Number:</span>
                    <div class="box">
                        <input type="tel" name="phone" class="register-input" placeholder="Phone Number" required>
                    </div>
                </div>

                <div class="register-box">
                    <span class="title">Address:</span>
                    <div class="box">
                        <textarea name="address" class="register-text" placeholder="Address" required></textarea>
                    </div>
                </div>

                <div class="register-box">
                    <span class="title">Birthday:</span>
                    <div class="box">
                        <input type="date" name="birthday" class="register-input" required>
                    </div>
                </div>
                <div class="register-box">
                    <span class="title">Password:</span>
                    <div class="box">
                        <input type="password" name="password1" class="register-input" placeholder="Password"
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$"
                            title="Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and be at least 8 characters long"
                            required>
                    </div>
                </div>

                <div class="register-box">
                    <span class="title">Confirm Password:</span>
                    <div class="box">
                        <input type="password" name="password2" class="register-input" placeholder="Confirm Password"
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$"
                            title="Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and be at least 8 characters long"
                            required>
                    </div>
                </div>

                <div id="passwordError" class="denied" style="width: 100%;"></div>
                <button type="submit">Register</button>
            </div>
        </div>
    </form>

</body>

</html>