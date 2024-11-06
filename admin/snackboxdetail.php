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
    function confirmDelete(SnackBoxID) {
      var ans = confirm("Confirm remove " + SnackBoxID);
      if (ans == true)
        document.location = "../manage/delete-snackbox.php?SnackBoxID=" + SnackBoxID;
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
    <h1>Snack Box Details</h1>
    <article style="display: flex; justify-content: center;">
      <?php
      $stmt = $pdo->prepare("SELECT * FROM SnackBox WHERE SnackBoxID = ?");
      $stmt->bindParam(1, $_GET["SnackBoxID"]);
      $stmt->execute();
      $row = $stmt->fetch();
      ?>
      <div class="snackbox-detail">
        <div>
          <img src='../images/<?= $row["SBIMG"] ?>' alt="SnackBox Image">
        </div>
        <div class="details">
          <h2><?= $row["SnackBoxName"] ?></h2>
          Max Drinks: <?= $row["MaxDrinks"] ?>
          | Max Breads: <?= $row["MaxBreads"] ?>
          | Max Cakes: <?= $row["MaxCakes"] ?><br>
          Discount Percentage: <?= $row["DiscountPercentage"] ?> %<br>
          Description: <?= $row["Description"] ?><br>
          <a class="link" href="./form/editsnackbox-form.php?SnackBoxID=<?= $row["SnackBoxID"] ?>">Edit</a>
          <a class="link" href="#" onclick="confirmDelete('<?= $row["SnackBoxID"] ?>')">Delete</a>
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