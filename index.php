<?php
session_start();
include "connect.php";
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
  <style>
    .menu-item {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 10px;
      margin: 10px 0;
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
    <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="responsive-demo-menu.gif" alt="Menu"></a>
  </div>

  <nav id="menu">
    <a class="dead" href="./index.php">Home</a>
    <a href="./promotion.php">Promotion</a>
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
    <h1>Welcome to Bakery Land!</h1>
    <h2>Popular Menu</h2>
    <div id="result" class="popular-items"></div>
    <article>

      <p>At Bakery Land, we believe every day is a special occasion worthy of delicious treats! Dive into our wide
        selection of freshly baked goods, delightful drinks, and scrumptious cakes that will make your taste buds dance.
      </p>

      <h3>Refreshingly Delicious Drinks</h3>
      <p>Quench your thirst with our refreshing drinks, crafted to perfection. From classic lemonades to exotic
        smoothies, each sip is a burst of flavor that complements our baked delights.</p>

      <h3>Artisan Breads</h3>
      <p>Indulge in our selection of artisan breads, baked daily with love and care. Whether you're in the mood for a
        crusty baguette or a soft, fluffy loaf, our breads are perfect for any meal or as a tasty snack on their own.
      </p>

      <h3>Decadent Cakes</h3>
      <p>Celebrate life's sweet moments with our decadent cakes, perfect for birthdays, weddings, or just because! Each
        cake is made with the finest ingredients, ensuring a moist, flavorful experience with every slice.</p>

      <p>Explore our specials and promotions to enjoy these delectable treats at unbeatable prices. At Bakery Land,
        every bite is a journey of flavor, crafted just for you!</p>
    </article>
  </main>

  <footer>
    <a href="#">Sitemap</a>
    <a href="#">Contact</a>
    <a href="#">Privacy</a>
  </footer>

  <script>
    async function getDataFromJSON() {
      // ใช้ fetch เพื่อดึงข้อมูลจาก order.json
      let response = await fetch('order.json');
      let objectData = await response.json();

      objectData.sort((a, b) => b.TotalQuantityOrdered - a.TotalQuantityOrdered);

      const popularItems = objectData.slice(0, 3);

      // แสดงผลเมนูยอดนิยม
      const result = document.getElementById('result');
      popularItems.forEach((item, index) => {
        const content =
          `<img src="${item.ItemIMG}" alt="${item.ItemName}" class="menu-image" width="100" height="100" />
            <h4>${item.ItemName}</h4>
            <p>Price: ${item.Price}฿</p`;
        const li = document.createElement('li');
        li.classList.add('menu-item');
        li.innerHTML = content;
        result.appendChild(li);
      });
    }
    getDataFromJSON();
  </script>

</body>

</html>