<?php
session_start();
include "../../connect.php";
if (!isset($_SESSION["userid"]) or $_SESSION["type"] != "1") {
    header("Location: ../../index.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BakeryLand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="../../css/form.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="../../main.js"></script>
</head>

<body>
    <header>
        <div class="logo">
            <div id="mainlogo">
                <h3>Bakery Land</h3>
                <h5>since 2024</h5>
                <div class="bottom left"></div>
                <div class="bottom right"></div>
            </div>
        </div>
    </header>

    <div class="mobile_bar">
        <a href="./adminindex.php"><img src="../../responsive-demo-home.gif" alt="Home"></a>
        <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="../../responsive-demo-menu.gif"
                alt="Menu"></a>
    </div>

    <nav id="menu">
        <a href="../adminindex.php">New Order</a>
        <a href="../managepromotion.php"><span class="material-symbols-outlined">edit</span> Promotion</a>
        <a href="../manageitem.php"><span class="material-symbols-outlined">edit</span> Item</a>
        <a href="../manageorder.php"><span class="material-symbols-outlined">edit</span> Order</a>
        <a href="../managecustomer.php"><span class="material-symbols-outlined">edit</span> Customer</a>
        <a href="../../manage/logout.php">Logout</a>
    </nav>

    <main>
        <h1>Add Snack Box</h1>
        <article>
            <form class="main-form" action="../../manage/insert-snackbox.php" method="post"
                enctype="multipart/form-data">
                </div>
                <img id="imagePreview" src="" alt="Image Preview"
                    style="display: none; max-width: 300px; margin-top: 10px;">
                </div>
                <div class="container">
                    Name: <input type="text" name="sbname" required><br>
                    Max Drinks: <input type="number" name="maxd" required><br>
                    Max Breads: <input type="number" name="maxb" required><br>
                    Max Cakes: <input type="number" name="maxc" required><br>
                    Discount Percentage: <input type="number" name="discount" required><br>
                    Description:<br> <textarea name="des" rows="5" cols="30" required></textarea><br>
                    Image: <input type="file" name="file" accept="image/gif, image/jpeg, image/png" id="imageInput"
                        required><br>
                </div>
                <button class="add-button" type="submit">Add Snack Box</button>
            </form>
        </article>
    </main>

    <footer>
        <a href="#">Sitemap</a>
        <a href="#">Contact</a>
        <a href="#">Privacy</a>
    </footer>

    <script>
        document.getElementById('imageInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }

                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });
    </script>
</body>

</html>