<?php
session_start();
include "../connect.php";
if (!isset($_SESSION["userid"]) or $_SESSION["type"] != "1") {
    header("Location: ../index.php");
    exit();
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BakeryLand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="../css/admin.css" rel="stylesheet" type="text/css" />
    <link href="../css/order.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="../main.js"></script>
    <script>
        function confirmDelete(PromotionID) {
            var ans = confirm("Confirm remove " + PromotionID);
            if (ans == true)
                document.location = "../manage/delete-promo.php?PromotionID=" + PromotionID;
        }
    </script>
</head>

<body>

    <header>
        <div class="logo">
            <div id="mainlogo">
                <h3>Bakery Land</h3>
                <h5>since 2024</h5>
                <div class="bottom left"></div>
                <div class="bottom right"></div>
            </div>
        </div>
    </header>

    <div class="mobile_bar">
        <a href="#"><img src="../responsive-demo-home.gif" alt="Home"></a>
        <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="../responsive-demo-menu.gif"
                alt="Menu"></a>
    </div>

    <nav id="menu">
        <a href="./adminindex.php">New Order</a>
        <a href="./managepromotion.php"><span class="material-symbols-outlined">edit</span> Promotion</a>
        <a href="./manageitem.php"><span class="material-symbols-outlined">edit</span> Item</a>
        <a href="./manageorder.php"><span class="material-symbols-outlined">edit</span> Order</a>
        <a href="./managecustomer.php"><span class="material-symbols-outlined">edit</span> Customer</a>
        <a href="../manage/logout.php">Logout</a>
    </nav>

    <main>
        <h1>Order Details</h1>
        <article style="display: flex; flex-direction: column; align-items: center;">
            <?php
            $orderId = $_GET['OrderID'];

            // Fetch Order details
            $stmt = $pdo->prepare("SELECT * FROM `Order` WHERE OrderID = ?");
            $stmt->bindValue(1, $orderId);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $stmt = $pdo->prepare("SELECT Name, Phone, Address FROM Customer WHERE CustomerID = ?");
            $stmt->bindValue(1, $order['CustomerID']);
            $stmt->execute();
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $stmt = $pdo->prepare("
                SELECT oi.*, i.ItemName, s.SnackBoxName, io.OptionValue 
                FROM OrderItem oi 
                LEFT JOIN Item i ON oi.ItemID = i.ItemID 
                LEFT JOIN SnackBox s ON oi.SnackBoxID = s.SnackBoxID 
                LEFT JOIN ItemOption io ON oi.OptionID = io.OptionID 
                WHERE oi.OrderID = ?
            ");
            $stmt->bindValue(1, $orderId);
            $stmt->execute();
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $snackBoxItems = [];
            foreach ($orderItems as $item) {
                if ($item['SnackBoxID']) {
                    $stmt = $pdo->prepare("
                        SELECT sbi.*, i.ItemName 
                        FROM SnackBoxItem sbi 
                        JOIN Item i ON sbi.ItemID = i.ItemID 
                        WHERE sbi.OrderItemID = ?");
                    $stmt->bindValue(1, $item['OrderItemID']);
                    $stmt->execute();
                    $snackBoxItems[$item['OrderItemID']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                }
            }
            ?>

            <div style="margin-top: 10px;">
                Customer Name: <?= htmlspecialchars($customer['Name']) ?><br>
                Phone: <?= htmlspecialchars($customer['Phone']) ?><br>
                Address: <?= htmlspecialchars($customer['Address']) ?><br>
                Total Price: <?= number_format($order['TotalAmount'], 2) ?> ฿ <br>
                Final Price: <?= number_format($order['FinalAmount'], 2) ?> ฿ <br>
            </div>

            <table class="center" style='margin-top:20px; margin-bottom:10px;'>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Option</th>
                    <th>Total Price</th>
                </tr>

                <?php foreach ($orderItems as $orderItem): ?>
                    <?php if ($orderItem['ItemID']): ?>
                        <tr>
                            <td><?= htmlspecialchars($orderItem['ItemName'] ?? 'Unknown') ?></td>
                            <td><?= htmlspecialchars($orderItem['Quantity']) ?></td>
                            <td><?= htmlspecialchars($orderItem['OptionValue'] ?? 'N/A') ?></td>
                            <td><?= number_format($orderItem['Price'], 2) ?> ฿</td>
                        </tr>
                    <?php elseif ($orderItem['SnackBoxID']): ?>
                        <tr>
                            <td colspan="5"><strong>SnackBox Name:</strong> <?= htmlspecialchars($orderItem['SnackBoxName']) ?>
                            </td>
                        </tr>

                        <?php foreach ($snackBoxItems[$orderItem['OrderItemID']] as $snackBoxItem): ?>
                            <tr>
                                <td><?= htmlspecialchars($snackBoxItem['ItemName'] ?? 'Unknown') ?></td>
                                <td><?= htmlspecialchars($snackBoxItem['Quantity']) ?></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        <?php endforeach; ?>

                        <tr>
                            <td colspan="3"><strong>SnackBox Total Price:</strong></td>
                            <td><?= number_format($orderItem['Price'], 2) ?> ฿</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>

        </article>
    </main>
    <footer>
        <a href="#">Sitemap</a>
        <a href="#">Contact</a>
        <a href="#">Privacy</a>
    </footer>
</body>

</html>