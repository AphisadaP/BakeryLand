<?php
include "connect.php";
session_start();
if (!isset($_SESSION["userid"]) or $_SESSION["type"] != "0") {
  header("Location: ./index.php");
  exit();
}
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
  <link href="./css/main.css" rel="stylesheet" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
  <script src="main.js"></script>
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
    <a href="./index.php"><img src="responsive-demo-home.gif" alt="Home"></a>
    <form method="GET" action="">
      <input type="search" name="search" placeholder="Search..."
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button type="submit">Search</button>
    </form>
    <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="responsive-demo-menu.gif" alt="Menu"></a>
  </div>

  <nav id="menu">
    <a href="./index.php">Home</a>
    <a href="./promotion.php">Promotion</a>
    <a href="./bread.php">Bread</a>
    <a href="./cake.php">Cake</a>
    <a href="./drinks.php">Drinks</a>
    <?php
    if (empty($_SESSION["userid"])) {
      echo '<a href="./login.php">Login</a>';
    } else {
      echo '<a class="dead" href="./profile.php?CustomerID=' . ($_SESSION["cusid"]) . '">Profile</a>';
    }
    ?>
  </nav>
  <?php
  $orderStmt = $pdo->prepare("SELECT * FROM `Order` WHERE CustomerID = ? ORDER BY OrderDate DESC, OrderID DESC LIMIT 1");
  $orderStmt->bindParam(1, $_GET["CustomerID"]);
  $orderStmt->execute();
  $latestOrder = $orderStmt->fetch();

  if ($latestOrder) {
    $orderId = $latestOrder['OrderID'];
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
  }
  ?>
  <main>
    <h1>Profile</h1>
    <article style="display: flex; justify-content: center;">
      <?php
      $stmt = $pdo->prepare("SELECT * FROM Customer WHERE CustomerID = ?");
      $stmt->bindParam(1, $_GET["CustomerID"]);
      $stmt->execute();
      $row = $stmt->fetch();
      ?>

      <div style="display: flex; margin:auto; padding:10px;">
        <div>
          <img src='./profiles/<?= $row["CusIMG"] ?>' width='200' height='200'>
        </div>
        <div style="padding: 15px">
          <h2><?= $row["Name"] ?></h2>
          Address: <?= $row["Address"] ?><br>
          Tel.: <?= $row["Phone"] ?><br>
          Date of Birth: <?= $row["DateOfBirth"] ?><br>
          Reward Points: <?= $row["RewardPoints"] ?><br><br>

          <div style="display: flex; gap: 10px;">
            <button onclick="window.location.href='./cart.php';" style="padding: 5px;">Cart</button>
            <button onclick="window.location.href='./editprofile.php?CustomerID=<?= $row['CustomerID'] ?>';"
              style="padding: 5px;">Edit</button>
            <button onclick="window.location.href='./manage/logout.php';" style="padding: 5px;">Logout</button>
          </div>

        </div>
      </div>

      <div>
        <h3>Latest Order</h3>
        <?php if ($latestOrder): ?>
          <table class="center latest-order-table" style="margin-bottom: 20px;">
            <tr>
              <th>Order Date</th>
              <th>Birthday Discount Applied</th>
              <th>Reward Points Used</th>
              <th>Final Amount</th>
              <th>Delivery Status</th>
            </tr>
            <tr>
              <td data-label="Order Date"><?= htmlspecialchars($latestOrder['OrderDate']) ?></td>
              <td data-label="Birthday Discount Applied">
                <?= htmlspecialchars($latestOrder['BirthdayDiscountApplied'] ? 'Yes' : 'No') ?></td>
              <td data-label="Reward Points Used"><?= htmlspecialchars($latestOrder['RewardPointsUsed']) ?></td>
              <td data-label="Final Amount"><?= number_format($latestOrder['FinalAmount'], 2) ?> ฿</td>
              <td data-label="Delivery Status">
                <?= htmlspecialchars($latestOrder['DeliveryStatus'] === 0 ? 'Preparing' : 'Delivered') ?></td>
            </tr>
          </table>

          <table class="center latest-order-table" style="margin-bottom: 20px;">
            <tr>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Option</th>
              <th>Total Price</th>
            </tr>

            <?php foreach ($orderItems as $item): ?>
              <?php if ($item['ItemID']): ?>
                <tr>
                  <td data-label="Item Name"><?= htmlspecialchars($item['ItemName'] ?? 'Unknow') ?></td>
                  <td data-label="Quantity"><?= htmlspecialchars($item['Quantity']) ?></td>
                  <td data-label="Option"><?= htmlspecialchars($item['OptionValue'] ?? 'N/A') ?></td>
                  <td data-label="Total Price"><?= number_format($item['Price'], 2) ?></td>
                </tr>

              <?php elseif ($item['SnackBoxID']): ?>
                <tr>
                  <td colspan="4"><strong>SnackBox Name:</strong> <?= htmlspecialchars($item['SnackBoxName']) ?></td>
                </tr>

                <?php foreach ($snackBoxItems[$item['OrderItemID']] as $snackBoxItem): ?>
                  <tr>
                    <td><?= htmlspecialchars($snackBoxItem['ItemName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($snackBoxItem['Quantity']) ?></td>
                    <td>-</td>
                    <td>-</td>
                  </tr>
                <?php endforeach; ?>
                <tr>
                  <td colspan="3"><strong>SnackBox Total Price:</strong></td>
                  <td><?= number_format($item['Price'], 2) ?> ฿</td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </table>

        <?php else: ?>
          <p>No orders found for this customer.</p>
        <?php endif; ?>

      </div>
    </article>
  </main>

  <footer>
    <a href="#">Sitemap</a>
    <a href="#">Contact</a>
    <a href="#">Privacy</a>
  </footer>

</body>

</html>