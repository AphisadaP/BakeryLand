<?php
include "../connect.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$itemId = $_POST["itemid"];

// Fetch existing image path from the database
$stmt = $pdo->prepare("SELECT ItemIMG FROM Item WHERE ItemID = ?");
$stmt->bindParam(1, $itemId);
$stmt->execute();
$row = $stmt->fetch();

$oldImagePath = "../images/" . $row['ItemIMG'];

$targetDir = "../images/";
$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;

// Check if the form was submitted and a new image is uploaded
if (!empty($fileName)) {
    // Check if old image exists and delete it
    if (file_exists($oldImagePath)) {
        unlink($oldImagePath);
    }

    // Check for existing file with the same name and change the name if necessary
    $fileCounter = 1;
    while (file_exists($targetFilePath)) {
        $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $targetFilePath = $targetDir . $fileNameWithoutExt . '_' . $fileCounter . '.' . $fileExtension;
        $fileCounter++;
    }

    // Check directory permissions and move the uploaded file
    if (!is_dir($targetDir) || !is_writable($targetDir)) {
        die('Target directory does not exist or is not writable.');
    }

    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        die('File upload failed. Please try again.');
    }
}

$available = isset($_POST["stock"]) ? 1 : 0;

if (!empty($fileName)) {
    $stmt = $pdo->prepare("UPDATE Item SET ItemName=?, Category=?, BasePrice=?, Description=?, ItemIMG=?, Available=? WHERE ItemID=?");
    $stmt->bindParam(1, $_POST["itemname"]);
    $stmt->bindParam(2, $_POST["type"]);
    $stmt->bindParam(3, $_POST["bprice"]);
    $stmt->bindParam(4, $_POST["des"]);
    $stmt->bindParam(5, basename($targetFilePath)); // Use the new file name
    $stmt->bindParam(6, $available);
    $stmt->bindParam(7, $itemId);
} else {
    $stmt = $pdo->prepare("UPDATE Item SET ItemName=?, Category=?, BasePrice=?, Description=?, Available=? WHERE ItemID=?");
    $stmt->bindParam(1, $_POST["itemname"]);
    $stmt->bindParam(2, $_POST["type"]);
    $stmt->bindParam(3, $_POST["bprice"]);
    $stmt->bindParam(4, $_POST["des"]);
    $stmt->bindParam(5, $available);
    $stmt->bindParam(6, $itemId);
}

if (!$stmt->execute()) {
    die("Database update failed: " . implode(", ", $stmt->errorInfo()));
}

header("Location: ../admin/itemdetail.php?ItemID=" . $itemId);
exit();
?>