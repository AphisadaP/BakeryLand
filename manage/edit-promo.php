<?php
include "../connect.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$promotionID = !empty($_POST["promotionID"]) ? $_POST["promotionID"] : null;
$startDate = !empty($_POST["startdate"]) ? $_POST["startdate"] : null;
$endDate = !empty($_POST["enddate"]) ? $_POST["enddate"] : null;
$appliesToAll = isset($_POST["appliesToAll"]) ? 1 : 0;
$itemIDs = isset($_POST["itemIDs"]) ? $_POST["itemIDs"] : [];

// Update promotion details
$stmt = $pdo->prepare("UPDATE Promotion SET PromotionName = ?, DiscountPercentage = ?, StartDate = ?, EndDate = ?, AppliesToAll = ? WHERE PromotionID = ?");
$stmt->execute([$_POST["promoname"], $_POST["discount"], $startDate, $endDate, $appliesToAll, $promotionID]);

// Remove old items if AppliesToAll is true or if items are being updated
$stmt = $pdo->prepare("DELETE FROM Promotionitem WHERE PromotionID = ?");
$stmt->execute([$promotionID]);

// Insert into PromotionItem only if AppliesToAll is false and items are selected
if (!$appliesToAll && !empty($itemIDs)) {
    $stmt = $pdo->prepare("INSERT INTO Promotionitem (PromotionID, ItemID) VALUES (?, ?)");
    foreach ($itemIDs as $itemID) {
        $stmt->execute([$promotionID, $itemID]);
    }
}

header("Location: ../admin/managepromotion.php");
exit();
?>