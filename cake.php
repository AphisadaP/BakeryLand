<?php
session_start();
include "connect.php";
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
  <link href="./css/select.css" rel="stylesheet" type="text/css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_cart" />
  <script src="main.js"></script>
  <style>
    @media only screen and (max-width: 625px) {
      .select-img {
        width: 150px;
        height: 150px;
      }
    }

    @media only screen and (min-width: 625px) and (max-width: 800px) {
      .select-img {
        width: 150px;
        height: 150px;
      }
    }

    @media only screen and (min-width: 800px) {
      .select-img {
        width: 200px;
        height: 200px;
      }
    }

    .category-section {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .select-img {
      display: block;
      margin: 0 auto;
    }
  </style>
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
    <a class="dead" href="./cake.php">Cake</a>
    <a href="./drinks.php">Drinks</a>
    <?php
    if (empty($_SESSION["userid"])) {
      echo '<a href="./login.php">Login</a>';
    } else {
      echo '<a  href="./profile.php?CustomerID=' . ($_SESSION["cusid"]) . '">Profile</a>';
    }
    ?>
  </nav>

  <main>
    <h1>Cake</h1>
    <div class="search" id="search">
      <a href="./cart.php"><span class="material-symbols-outlined">shopping_cart</span> Your Cart</a>
      <form method="GET" action="">
        <input type="search" name="search" placeholder="Search..."
          value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Search</button>
      </form>
    </div>
    <article>
      <div class="category-section">
        <?php
        $query = "SELECT * FROM Item WHERE Category = 'Cake' AND Available = '1'";
        $params = [];

        if (isset($_GET['search']) && !empty($_GET['search'])) {
          $searchTerm = '%' . $_GET['search'] . '%'; // Wildcards for LIKE
          $query .= " AND (ItemName LIKE ? OR Description LIKE ?)";
          $params[] = $searchTerm;
          $params[] = $searchTerm;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        while ($row = $stmt->fetch()):
          $discountedPrice = null;
          $dateToday = date("Y-m-d");

          $promoStmt = $pdo->prepare("
            SELECT p.DiscountPercentage, p.AppliesToAll 
            FROM Promotion p 
            LEFT JOIN Promotionitem pi ON p.PromotionID = pi.PromotionID 
            WHERE p.StartDate <= :dateToday AND p.EndDate >= :dateToday 
            AND (pi.ItemID = :itemID OR p.AppliesToAll = 1)
        ");
          $promoStmt->execute([
            ':dateToday' => $dateToday,
            ':itemID' => $row['ItemID']
          ]);

          $promotion = $promoStmt->fetch();

          if ($promotion) {
            $discountedPrice = $row['BasePrice'] * (1 - ($promotion['DiscountPercentage'] / 100));
          }

          ?>
          <div style="padding: 15px; text-align: center">
            <a href="./detailcake.php?ItemID=<?= $row["ItemID"] ?>">
              <img class="select-img" src='./images/<?= $row["ItemIMG"] ?>' width='100'>
            </a><br>
            <?= htmlspecialchars($row["ItemName"]) ?><br>
            <?php if ($discountedPrice): ?>
              <span style="text-decoration: line-through; color:red;"><?= htmlspecialchars($row["BasePrice"]) ?>
                ฿</span><br>
              <strong><?= htmlspecialchars(number_format($discountedPrice, 2)) ?> ฿</strong><br>
            <?php else: ?>
              <?= htmlspecialchars($row["BasePrice"]) ?> ฿<br>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
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