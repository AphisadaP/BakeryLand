<?php
session_start();
include "../connect.php";

if (isset($_SESSION["userid"]) && $_SESSION["type"] == "1" && isset($_GET['OrderID'])) {
    $orderID = $_GET['OrderID'];

    $stmt = $pdo->prepare("UPDATE `Order` SET DeliveryStatus = '1' WHERE OrderID = :orderID");
    $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: adminindex.php");
        exit();
    } else {
        echo "Error updating order status.";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>