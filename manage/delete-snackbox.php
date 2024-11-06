<?php
include "../connect.php";

$itemid = $_GET["SnackBoxID"];

$stmt = $pdo->prepare("SELECT SBIMG FROM SnackBox WHERE SnackBoxID = ?");
$stmt->bindParam(1, $itemid);
$stmt->execute();
$result = $stmt->fetch();

if ($result) {

    $fileName = $result['ItemIMG'];
    $filePath = "../images/" . $fileName;

    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $stmt = $pdo->prepare("DELETE FROM SnackBox WHERE SnackBoxID = ?");
    $stmt->bindParam(1, $itemid);
    if ($stmt->execute()) {
        header("Location: ../admin/managepromotion.php");
        exit();
    } else {
        die('Failed to delete the item.');
    }
} else {
    die('Item not found.');
}
?>