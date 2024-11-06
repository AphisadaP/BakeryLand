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

// Move the uploaded file to the target directory
if (!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
    die('File upload failed. Please try again.');
}

// Prepare to insert the item into the database
$available = isset($_POST["stock"]) ? 1 : 0;

$stmt = $pdo->prepare("INSERT INTO Item (ItemName, Category, BasePrice, Description, ItemIMG, Available) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bindParam(1, $_POST["itemname"]);
$stmt->bindParam(2, $_POST["type"]);
$stmt->bindParam(3, $_POST["bprice"]);
$stmt->bindParam(4, $_POST["des"]);
$stmt->bindParam(5, basename($targetFilePath)); // Use the new file name
$stmt->bindParam(6, $available);

$stmt->execute();
$itemid = $pdo->lastInsertId();

header("Location: ../admin/manageitem.php");
exit();
?>