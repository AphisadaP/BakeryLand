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
    <a class="dead" href="./adminindex.php">New Order</a>
    <a href="./managepromotion.php"><span class="material-symbols-outlined">edit</span> Promotion</a>
    <a href="./manageitem.php"><span class="material-symbols-outlined">edit</span> Item</a>
    <a href="./manageorder.php"><span class="material-symbols-outlined">edit</span> Order</a>
    <a href="./managecustomer.php"><span class="material-symbols-outlined">edit</span> Customer</a>
    <a href="../manage/logout.php">Logout</a>
  </nav>

  <main>
    <h1>New Order</h1>
    <article>
      <?php
      $stmt = $pdo->prepare("
                SELECT 
                    o.OrderID,
                    o.OrderDate,
                    c.Name AS CustomerName,
                    o.BirthdayDiscountApplied,
                    o.RewardPointsUsed,
                    o.FinalAmount
                FROM 
                    `Order` o
                JOIN 
                    Customer c ON o.CustomerID = c.CustomerID
                WHERE 
                    o.DeliveryStatus = '0'
                ORDER BY 
                    o.OrderID; ");
      $stmt->execute();

      // Check if there are any orders
      if ($stmt->rowCount() > 0) {
        echo "<table class='center' border='1' style='margin-top:10px; margin-bottom:10px;'>";
        echo "<tr>
        <th>Order ID</th>
        <th>Order Date</th>
        <th>Customer Name</th>
        <th>Birthday Discount Applied</th>
        <th>Reward Points Used</th>
        <th>Final Amount</th>
        <th>Action</th>
        </tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo "<tr>";
          echo "<td>{$row['OrderID']}</a></td>";
          echo "<td>{$row['OrderDate']}</td>";
          echo "<td>{$row['CustomerName']}</td>";
          echo "<td>" . ($row['BirthdayDiscountApplied'] ? 'Yes' : 'No') . "</td>";
          echo "<td>{$row['RewardPointsUsed']}</td>";
          echo "<td>" . number_format($row['FinalAmount'], 2) . "</td>";
          echo "<td>
          <a class='detail' href='orderdetail.php?OrderID={$row['OrderID']}'>View Detail</a> |
          <a class='update' href='update-status.php?OrderID={$row['OrderID']}'>Update Status</a>
        </td>";
          echo "</tr>";
        }
      } else {
        echo "<h3>No New Order...</h3>";
      }
      echo "</table>";
      ?>

    </article>
  </main>
  <footer>
    <a href="#">Sitemap</a>
    <a href="#">Contact</a>
    <a href="#">Privacy</a>
  </footer>
</body>

</html>