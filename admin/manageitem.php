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
  <style>
    @media only screen and (max-width: 625px) {
      .product-img {
        width: 150px;
        height: 150px;
      }
    }

    @media only screen and (min-width: 625px) and (max-width: 800px) {
      .logo {
        display: none;
      }

      .product-img {
        width: 150px;
        height: 150px;
      }

    }

    @media only screen and (min-width: 800px) {
      .product-img {
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

    .product-img {
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
    <a href="./adminindex.php"><img src="../responsive-demo-home.gif" alt="Home"></a>
    <form method="GET" action="">
      <input type="search" name="search" placeholder="Search..."
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button type="submit">Search</button>
    </form>
    <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="../responsive-demo-menu.gif"
        alt="Menu"></a>
  </div>

  <nav id="menu">
    <a href="./adminindex.php">New Order</a>
    <a href="./managepromotion.php"><span class="material-symbols-outlined">edit</span> Promotion</a>
    <a class="dead" href="./manageitem.php"><span class="material-symbols-outlined">edit</span> Item</a>
    <a href="./manageorder.php"><span class="material-symbols-outlined">edit</span> Order</a>
    <a href="./managecustomer.php"><span class="material-symbols-outlined">edit</span> Customer</a>
    <a href="../manage/logout.php">Logout</a>
  </nav>

  <main>
    <h1>Manage Item</h1>
    <div class="search" id="search">
      <form method="GET" action="">
        <input type="search" name="search" placeholder="Search..."
          value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Search</button>
      </form>
    </div>
    <article>
      <button class="add-button" onclick="window.location.href='./form/additem-form.php'">Add New Item</button>

      <?php
      $categories = ['Bread', 'Cake', 'Drinks'];

      foreach ($categories as $category): ?>
        <h3><?= htmlspecialchars($category) ?></h3>
        <div class="category-section">
          <?php
          $query = "SELECT * FROM Item WHERE Category = ?";
          $params = [$category];

          if (isset($_GET['search']) && !empty($_GET['search'])) {
            $searchTerm = '%' . $_GET['search'] . '%'; // Wildcards for LIKE
            $query .= " AND (ItemName LIKE ? OR Description LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
          }

          $stmt = $pdo->prepare($query);
          $stmt->execute($params);

          while ($row = $stmt->fetch()): ?>
            <div style="padding: 15px; text-align: center">
              <a href="./itemdetail.php?ItemID=<?= $row["ItemID"] ?>">
                <img class="product-img" src='../images/<?= $row["ItemIMG"] ?>' width='100'>
              </a><br>
              <?= htmlspecialchars($row["ItemName"]) ?><br>
              <?= htmlspecialchars($row["BasePrice"]) ?> ฿<br>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endforeach; ?>
    </article>
  </main>

  <footer>
    <a href="#">Sitemap</a>
    <a href="#">Contact</a>
    <a href="#">Privacy</a>
  </footer>
</body>

</html>