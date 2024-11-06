<?php
include "../connect.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$targetDir = "../images/";
$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;
$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

if (!is_dir($targetDir)) {
    die('Target directory does not exist.');
}

if (!is_writable($targetDir)) {
    die('Target directory is not writable.');
}

$fileCounter = 1;
while (file_exists($targetFilePath)) {
    $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
    $targetFilePath = $targetDir . $fileNameWithoutExt . '_' . $fileCounter . '.' . $fileType;
    $fileCounter++;
}

if (!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
    die('File upload failed. Please try again.');
}

$stmt = $pdo->prepare("INSERT INTO SnackBox (SnackBoxName, MaxDrinks, MaxBreads, MaxCakes, DiscountPercentage, Description, SBIMG) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bindParam(1, $_POST["sbname"]);
$stmt->bindParam(2, $_POST["maxd"]);
$stmt->bindParam(3, $_POST["maxb"]);
$stmt->bindParam(4, $_POST["maxc"]);
$stmt->bindParam(5, $_POST["discount"]);
$stmt->bindParam(6, $_POST["des"]);
$stmt->bindParam(7, basename($targetFilePath));

$stmt->execute();
$itemid = $pdo->lastInsertId();

header("Location: ../admin/managepromotion.php");
exit();
?>