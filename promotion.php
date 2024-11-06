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
    <a class="dead" href="./promotion.php">Promotion</a>
    <a href="./bread.php">Bread</a>
    <a href="./cake.php">Cake</a>
    <a href="./drinks.php">Drinks</a>
    <?php
    if (empty($_SESSION["userid"])) {
      echo '<a href="./login.php">Login</a>';
    } else {
      echo '<a href="./profile.php?CustomerID=' . ($_SESSION["cusid"]) . '">Profile</a>';
    }
    ?>
  </nav>

  <main>
    <h1>Promotion</h1>
    <div class="search" id="search">
      <a href="./cart.php"><span class="material-symbols-outlined">shopping_cart</span> Your Cart</a>
      <form method="GET" action="">
        <input type="search" name="search" placeholder="Search..."
          value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Search</button>
      </form>
    </div>
    <article>
      <?php
      if (isset($_SESSION['cusid'])) {
        // Retrieve the customer's date of birth
        $customerID = $_SESSION['cusid'];

        // Query to get the customer's date of birth
        $dobStmt = $pdo->prepare("SELECT DateOfBirth, BirthdayDiscountEligible FROM Customer WHERE CustomerID = ?");
        $dobStmt->bindParam(1, $customerID);
        $dobStmt->execute();
        $customer = $dobStmt->fetch(PDO::FETCH_ASSOC);

        $birthdayPromotions = []; // Initialize array for birthday promotions
      
        if ($customer) {
          // Extract the month from the date of birth
          $dobMonth = date("m", strtotime($customer['DateOfBirth']));
          $currentMonth = date("m");

          // If the month of birth matches the current month, query birthday promotions
          if ($dobMonth == $currentMonth && $customer['BirthdayDiscountEligible'] == 1) {
            // Query to select birthday promotions
            $birthdayPromoStmt = $pdo->prepare("
              SELECT * FROM Promotion 
              WHERE IsBirthdayPromotion = 1
          ");
            $birthdayPromoStmt->execute();
            $birthdayPromotions = $birthdayPromoStmt->fetchAll(PDO::FETCH_ASSOC);
          }
        }
      }
      $dateToday = date("Y-m-d");

      // Query to select active promotions
      $promoStmt = $pdo->prepare("
    SELECT * FROM Promotion p 
    WHERE p.StartDate <= :dateToday AND p.EndDate >= :dateToday
");
      $promoStmt->execute([':dateToday' => $dateToday]);

      $activePromotions = $promoStmt->fetchAll(PDO::FETCH_ASSOC);

      // Display birthday promotions if applicable
      if (!empty($birthdayPromotions)): // Check if there are birthday promotions
        echo "<div style='display: flex; justify-content: center; margin: 10px;'>";
        foreach ($birthdayPromotions as $promotion): ?>
          <div style="padding: 15px; text-align: center; margin: 5px;">
            <h3>🎉 Happy Birthday Month! 🎉</h3><br>
            We're excited to celebrate your special month! Enjoy our exclusive offer:<br>
            Promotion: <?= htmlspecialchars($promotion["PromotionName"]) ?><br>
            Discount: <?= htmlspecialchars($promotion["DiscountPercentage"]) ?>% off<br>
            Treat yourself and enjoy your birthday gift when you shop with us! <strong>Note:</strong> You can use this
            promotion when paying.

          </div>
        <?php endforeach;
        echo "</div>";
      endif;
      ?>
      <h3>Active Promotion</h3>
      <?php
      if (empty($activePromotions)): // Check if there are no active promotions
        echo "<p style='text-align: center;'>No promotion active now.</p>";
      else: // If there are active promotions
        ?>
        <div style="display:flex;">
          <?php foreach ($activePromotions as $promotion): ?>
            <div style="padding: 15px; text-align: center; margin: 5px;">
              <strong><a
                  href="./detailpromo.php?PromotionID=<?= htmlspecialchars($promotion["PromotionID"]) ?>"><?= htmlspecialchars($promotion["PromotionName"]) ?></a></strong><br>
              Discount: <?= htmlspecialchars($promotion["DiscountPercentage"]) ?>%<br>
              <?php
              // Handle null values for StartDate and EndDate
              $startDate = $promotion["StartDate"] ? htmlspecialchars($promotion["StartDate"]) : "";
              $endDate = $promotion["EndDate"] ? htmlspecialchars($promotion["EndDate"]) : "";
              ?>
              <?= $startDate ?> - <?= $endDate ?><br>
            </div>
          <?php endforeach; ?>
        </div>
        <?php
      endif;
      ?>

      <h3>Snack Box</h3>
      <div class="category-section">
        <?php
        // Prepare the base SQL query for SnackBox
        $snackQuery = "SELECT * FROM SnackBox";
        $snackParams = [];

        // Check if there is a search term
        if (isset($_GET['search']) && !empty($_GET['search'])) {
          $searchTerm = '%' . $_GET['search'] . '%'; // Wildcards for LIKE
          $snackQuery .= " WHERE SnackBoxName LIKE ? OR Description LIKE ?";
          $snackParams[] = $searchTerm;
          $snackParams[] = $searchTerm; // Add search term for Description
        }

        // Prepare and execute the statement for SnackBox
        $snackStmt = $pdo->prepare($snackQuery);
        $snackStmt->execute($snackParams);

        while ($row = $snackStmt->fetch()): ?>
          <div style="padding: 15px; text-align: center">
            <a href="./sbdetail.php?SnackBoxID=<?= $row["SnackBoxID"] ?>">
              <img class="select-img" src='./images/<?= $row["SBIMG"] ?>' width='100'></a><br>
            <?= htmlspecialchars($row["SnackBoxName"]) ?><br>
            <?= htmlspecialchars($row["DiscountPercentage"]) ?> %<br>
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