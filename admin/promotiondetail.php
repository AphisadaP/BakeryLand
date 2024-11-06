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
    <h1>Promotion Details</h1>
    <article style="display: flex; justify-content: center;">
      <?php
      $stmt = $pdo->prepare("SELECT * FROM Promotion WHERE PromotionID = ?");
      $stmt->bindParam(1, $_GET["PromotionID"]);
      $stmt->execute();
      $row = $stmt->fetch();
      ?>
      <div class="snackbox-detail">
        <div class="details">
          <h2><?= $row["PromotionName"] ?></h2>
          StartDate: <?= $row["StartDate"] ?>
          | EndDate: <?= $row["EndDate"] ?><br>
          Discount Percentage: <?= $row["DiscountPercentage"] ?> %<br>
          Items include:
          <?php if ($row["AppliesToAll"] == 1): ?>
            All items<br>
          <?php else: ?>
            <ul>
              <?php
              $stmt = $pdo->prepare("SELECT Item.ItemName FROM Promotionitem 
                                    JOIN Item ON Promotionitem.ItemID = Item.ItemID 
                                    WHERE Promotionitem.PromotionID = ?");
              $stmt->execute([$row["PromotionID"]]);
              $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

              if (!empty($items)):
                foreach ($items as $item): ?>
                  <li><?= htmlspecialchars($item["ItemName"]) ?></li>
                <?php endforeach;
              else: ?>
                <li>No items associated with this promotion.</li>
              <?php endif; ?>
            </ul>
          <?php endif; ?>
          <a class="link" href="./form/editpromo-form.php?PromotionID=<?= $row["PromotionID"] ?>">Edit</a>
          <a class="link" href="#" onclick="confirmDelete('<?= $row["PromotionID"] ?>')">Delete</a>
        </div>
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