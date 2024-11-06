<?php 
include "../connect.php"; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$itemId = $_POST["sbid"];

// Fetch existing image path from the database
$stmt = $pdo->prepare("SELECT SBIMG FROM SnackBox WHERE SnackBoxID = ?");
$stmt->bindParam(1, $itemId);
$stmt->execute();
$row = $stmt->fetch();

$oldImagePath = "../images/" . $row['SBIMG'];

// Define target directory
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

if (!empty($fileName)) {
    $stmt = $pdo->prepare("UPDATE SnackBox SET SnackBoxName=?, MaxDrinks=?, MaxBreads=?, MaxCakes=?, DiscountPercentage=?, Description=?, SBIMG=? WHERE SnackBoxID=?");
    $stmt->bindParam(1, $_POST["sbname"]);
    $stmt->bindParam(2, $_POST["maxd"]);
    $stmt->bindParam(3, $_POST["maxb"]);
    $stmt->bindParam(4, $_POST["maxc"]);
    $stmt->bindParam(5, $_POST["discount"]);
    $stmt->bindParam(6, $_POST["des"]);
    $stmt->bindParam(7, basename($targetFilePath));
    $stmt->bindParam(8, $itemId);
} else {
    $stmt = $pdo->prepare("UPDATE SnackBox SET SnackBoxName=?, MaxDrinks=?, MaxBreads=?, MaxCakes=?, DiscountPercentage=?, Description=? WHERE SnackBoxID=?");
    $stmt->bindParam(1, $_POST["sbname"]);
    $stmt->bindParam(2, $_POST["maxd"]);
    $stmt->bindParam(3, $_POST["maxb"]);
    $stmt->bindParam(4, $_POST["maxc"]);
    $stmt->bindParam(5, $_POST["discount"]);
    $stmt->bindParam(6, $_POST["des"]);
    $stmt->bindParam(7, $itemId);
}

if (!$stmt->execute()) {
    die("Database update failed: " . implode(", ", $stmt->errorInfo()));
}

header("Location: ../admin/snackboxdetail.php?SnackBoxID=" . $itemId);
exit();
?>
