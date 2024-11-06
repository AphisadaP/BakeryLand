<?php
include "connect.php";
session_start();

if (!isset($_SESSION["userid"]) or $_SESSION["type"] != "0") {
    header("Location: ./index.php");
    exit();
}

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
    <link href="./css/form.css" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="main.js"></script>
    <style>
        .container {
            display: flex;
            flex-direction: column;
        }
    </style>
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
        <a href="./index.php"><img src="responsive-demo-home.gif" alt="Home"></a>
        <form method="GET" action="">
            <input type="search" name="search" placeholder="Search..."
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">Search</button>
        </form>
        <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="responsive-demo-menu.gif"
                alt="Menu"></a>
    </div>

    <nav id="menu">
        <a href="./index.php">Home</a>
        <a href="./promotion.php">Promotion</a>
        <a href="./bread.php">Bread</a>
        <a href="./cake.php">Cake</a>
        <a href="./drinks.php">Drinks</a>
        <?php
        if (empty($_SESSION["userid"])) {
            echo '<a href="./login.php">Login</a>';
        } else {
            echo '<a class="dead" href="./profile.php?CustomerID=' . ($_SESSION["cusid"]) . '">Profile</a>';
        }
        ?>
    </nav>

    <?php
    $customerID = $_GET["CustomerID"];
    $stmt = $pdo->prepare("SELECT * FROM Customer WHERE CustomerID = ?");
    $stmt->bindParam(1, $customerID);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars($_POST["Name"]);
        $address = htmlspecialchars($_POST["Address"]);
        $phone = htmlspecialchars($_POST["Phone"]);
        $dateOfBirth = $row["DateOfBirth"];

        $uploadDir = './profiles/';
        $imagePath = $row["CusIMG"];

        if (!empty($_FILES['CusIMG']['name'])) {
            $imageName = basename($_FILES["CusIMG"]["name"]);
            $targetFile = $uploadDir . $imageName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES["CusIMG"]["tmp_name"], $targetFile)) {
                    $imagePath = $imageName;
                } else {
                    echo "Error uploading image. Check file permissions.";
                    error_log("Failed to move uploaded file. Target: $targetFile, Source: {$_FILES['CusIMG']['tmp_name']}");
                    exit();
                }

            } else {
                echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
                exit();
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE Customer SET Name = ?, Address = ?, Phone = ?, DateOfBirth = ?, CusIMG = ? WHERE CustomerID = ?");
            $stmt->execute([$name, $address, $phone, $dateOfBirth, $imagePath, $customerID]);

            header("Location: profile.php?CustomerID=" . urlencode($customerID));
            exit();
        } catch (PDOException $e) {
            echo "Error updating profile: " . $e->getMessage();
        }
    }
    ?>

    <main>
        <h1>Edit Profile</h1>
        <article>
            <form class="main-form" method="POST" enctype="multipart/form-data">
                </div>
                <img id="imagePreview" src='./profiles/<?= $row["CusIMG"] ?>' alt="Current Image"
                    style="max-width: 300px; margin-top: 10px;">
                </div>
                <div class="container">
                    <input type="hidden" name="itemid" value='<?= $row["ItemID"] ?>'><br>
                    Name: <input type="text" name="Name" value="<?= htmlspecialchars($row["Name"]) ?>" required>
                    Address:<br> <textarea name="Address" rows="5" cols="30"
                        required><?= htmlspecialchars($row["Address"]) ?></textarea>
                    Phone: <input type="tel" name="Phone" value="<?= htmlspecialchars($row["Phone"]) ?>">
                    Date or Birth: <?= htmlspecialchars($row["DateOfBirth"]) ?><br>
                    Image: <input type="file" name="CusIMG" accept="image/gif, image/jpeg, image/png"
                        id="imageInput"><br>
                </div>
                <button class="add-button" type="submit">Update Profile</button>
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