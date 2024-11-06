<?php
include "../connect.php";

$promotionID = $_GET["PromotionID"];

// Prepare a statement to check if the promotion exists
$stmt = $pdo->prepare("SELECT * FROM Promotion WHERE PromotionID = ?");
$stmt->bindParam(1, $promotionID);
$stmt->execute();
$result = $stmt->fetch();

if ($result) {
    // First, delete associated PromotionItems
    $stmt = $pdo->prepare("DELETE FROM Promotionitem WHERE PromotionID = ?");
    $stmt->bindParam(1, $promotionID);
    $stmt->execute();

    // Now delete the promotion
    $stmt = $pdo->prepare("DELETE FROM Promotion WHERE PromotionID = ?");
    $stmt->bindParam(1, $promotionID);

    if ($stmt->execute()) {
        header("Location: ../admin/managepromotion.php");
        exit();
    } else {
        die('Failed to delete the promotion.');
    }
} else {
    die('Promotion not found.');
}
?>