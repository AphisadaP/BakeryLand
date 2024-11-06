<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "../connect.php";

if (!isset($_GET['token']) || !isset($_SESSION['payment_token']) || $_GET['token'] !== $_SESSION['payment_token']) {
    header("Location: ../index.php");
    exit();
}

unset($_SESSION['payment_token']);

$customerId = $_SESSION['cusid'] ?? null;
$cart = $_SESSION['cart'] ?? [];
$rewardPointsUsed = $_SESSION['rewardPoints'];
$birthdayDiscountApplied = $_SESSION['applyBirthdayDiscount'] ? 1 : 0;
$paymentStatus = 1;
$deliveryStatus = 0;
$totalAmount = $_SESSION['totalPrice'];
$finalAmount = $_SESSION['finalPrice'];

$stmt = $pdo->prepare("SELECT RewardPoints FROM Customer WHERE CustomerID = ?");
$stmt->execute([$customerId]);
$currentPoints = $stmt->fetchColumn();

if($_SESSION['applyBirthdayDiscount']){
    $stmt = $pdo->prepare("UPDATE Customer SET BirthdayDiscountEligible = 0 WHERE CustomerID = ?");
    $stmt->execute([$customerId]);
    echo 'birthday';
}

if($rewardPointsUsed > 0){
    $newPoints = $currentPoints - $rewardPointsUsed + intval($totalAmount/100);
    $stmt = $pdo->prepare("UPDATE Customer SET RewardPoints = ? WHERE CustomerID = ?");
    $stmt->execute([$newPoints, $customerId]);
    echo 'points';
}

// Get the current date and time
$orderDate = date('Y-m-d');

$stmt = $pdo->prepare("INSERT INTO `Order` (OrderDate, CustomerID, TotalAmount, BirthdayDiscountApplied, RewardPointsUsed, FinalAmount, PaymentStatus, DeliveryStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bindValue(1, $orderDate);
$stmt->bindValue(2, $customerId);
$stmt->bindValue(3, $totalAmount);
$stmt->bindValue(4, $birthdayDiscountApplied);
$stmt->bindValue(5, $rewardPointsUsed);
$stmt->bindValue(6, $finalAmount);
$stmt->bindValue(7, $paymentStatus);
$stmt->bindValue(8, $deliveryStatus);

$stmt->execute();
$orderId = $pdo->lastInsertId();
$stmt->closeCursor();

foreach ($cart as $cartItem) {
    if ($cartItem['ItemType'] === 'Regular') {
        $stmt = $pdo->prepare("INSERT INTO OrderItem (OrderID, ItemID, OptionID, Quantity, Price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bindValue(1, $orderId); 
        $stmt->bindValue(2, $cartItem['ItemID']);
        $stmt->bindValue(3, $cartItem['OptionID']);
        $stmt->bindValue(4, $cartItem['Quantity']);
        $stmt->bindValue(5, $cartItem['TotalPrice']);
        $stmt->execute();
        $stmt->closeCursor();
    } elseif ($cartItem['ItemType'] === 'SnackBox') {
        $stmt = $pdo->prepare("INSERT INTO OrderItem (OrderID, SnackBoxID, Quantity, Price) VALUES (?, ?, ?, ?)");
        $stmt->bindValue(1, $orderId);
        $stmt->bindValue(2, $cartItem['SnackBoxID']);
        $stmt->bindValue(3, 1);
        $stmt->bindValue(4, $cartItem['TotalPrice']);
        $stmt->execute();
        
        $orderItemId = $pdo->lastInsertId();
        $stmt->closeCursor();

        foreach ($cartItem['Items'] as $snackBoxItem) {
            $stmt = $pdo->prepare("INSERT INTO SnackBoxItem (OrderItemID, ItemID, Quantity) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $orderItemId);
            $stmt->bindValue(2, $snackBoxItem['ItemID']);
            $stmt->bindValue(3, $snackBoxItem['Quantity']);
            $stmt->execute();
            $stmt->closeCursor();
        }
    }
}

unset($_SESSION['cart']);
unset($_SESSION['rewardPoints']);
unset($_SESSION['applyBirthdayDiscount']);
unset($_SESSION['totalPrice']);
unset($_SESSION['finalPrice']);

header("Location: ../profile.php?CustomerID=" . $_SESSION['cusid']);
exit();
?>
