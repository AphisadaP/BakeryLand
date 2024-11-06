<?php
ini_set('upload_max_filesize', '5M');
ini_set('post_max_size', '6M');
ini_set('memory_limit', '128M');

include "connect.php";
session_start();

if (!isset($_SESSION["userid"])) {
    header("Location: ../index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID = $_POST["CustomerID"];
    $name = htmlspecialchars($_POST["Name"]);
    $address = htmlspecialchars($_POST["Address"]);
    $phone = htmlspecialchars($_POST["Phone"]);
    $dateOfBirth = $_POST["DateOfBirth"];

    $uploadDir = './profiles/';
    $imagePath = null;

    if (!empty($_FILES['CusIMG']['name'])) {
        $imageName = basename($_FILES["CusIMG"]["name"]);
        $targetFile = $uploadDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["CusIMG"]["tmp_name"], $targetFile)) {
                $imagePath = $imageName;
            } else {
                echo "Error uploading image.";
                exit();
            }
        } else {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }
    }

    try {
        if ($imagePath) {
            $stmt = $pdo->prepare("UPDATE Customer SET Name = ?, Address = ?, Phone = ?, DateOfBirth = ?, CusIMG = ? WHERE CustomerID = ?");
            $stmt->execute([$name, $address, $phone, $dateOfBirth, $imagePath, $customerID]);
        } else {
            $stmt = $pdo->prepare("UPDATE Customer SET Name = ?, Address = ?, Phone = ?, DateOfBirth = ? WHERE CustomerID = ?");
            $stmt->execute([$name, $address, $phone, $dateOfBirth, $customerID]);
        }

        header("Location: profile.php?CustomerID=" . urlencode($customerID));
        exit();
    } catch (PDOException $e) {
        echo "Error updating profile: " . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT * FROM Customer WHERE CustomerID = ?");
$stmt->bindParam(1, $_GET["CustomerID"]);
$stmt->execute();
$row = $stmt->fetch();
?>

<main>
    <h1>Profile</h1>
    <article>
        <div style="display: flex; flex-direction: column; align-items: center; margin:auto; padding:10px;">
            <div>
                <img src='./profiles/<?= htmlspecialchars($row["CusIMG"]) ?>' width='200' style="border-radius: 10px;">
            </div>
            <div style="padding: 15px; text-align: center;">
                <h2><?= htmlspecialchars($row["Name"]) ?></h2>
                <div style="border: 2px solid #9b4819; padding: 10px; border-radius: 5px; display: inline-block;">
                    Address: <input type="text" value="<?= htmlspecialchars($row["Address"]) ?>" name="Address"
                        style="border: none; width: 100%;"><br>
                    Tel.: <input type="text" value="<?= htmlspecialchars($row["Phone"]) ?>" name="Phone"
                        style="border: none; width: 100%;"><br>
                    Date of Birth: <input type="date" value="<?= htmlspecialchars($row["DateOfBirth"]) ?>"
                        name="DateOfBirth" style="border: none; width: 100%;"><br>
                </div>
                <br>
                Reward Points: <?= htmlspecialchars($row["RewardPoints"]) ?><br>
                <a href="./manage/logout.php">Logout</a><br><br>
                <a href="./editprofile.php?CustomerID=<?= urlencode($_SESSION["userid"]) ?>" class="button">Edit
                    Profile</a>
            </div>
        </div>
    </article>
</main>