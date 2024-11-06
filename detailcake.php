<?php
session_start();
include "connect.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dateToday = date("Y-m-d");
$discountedPrice = null;

$stmt = $pdo->prepare("SELECT * FROM Item WHERE ItemID = ?");
$stmt->bindParam(1, $_GET["ItemID"]);
$stmt->execute();
$row = $stmt->fetch();

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

$existingCartItem = null;
if (isset($_SESSION['cart']) && isset($_GET['cartitemnumber'])) {
    $cartitemnumber = $_GET['cartitemnumber'];

    foreach ($_SESSION['cart'] as $item) {
        if ($item['cartitemnumber'] === $cartitemnumber) {
            $existingCartItem = $item;
            break;
        }
    }
}


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BakeryLand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="./css/item.css" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="main.js"></script>
    <script>
        function updatePrice() {
            var basePrice = parseFloat(document.getElementById('basePrice').innerText);
            var sizeSelect = document.getElementById('cakeSize');
            var selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            var additionalCost = parseFloat(selectedOption.getAttribute('data-additional-cost')) || 0;
            var qty = parseInt(document.getElementById('qty').value) || 1;

            var totalPrice = (basePrice * additionalCost) * qty;
            document.getElementById('totalPrice').innerText = totalPrice.toFixed(2);
            document.getElementById('totalprice').value = totalPrice.toFixed(2);
        }

        function updateQtyField() {
            var sizeSelect = document.getElementById('cakeSize');
            var qtyField = document.getElementById('qty');
            if (sizeSelect.value !== "") {
                qtyField.disabled = false;
                qtyField.value = 1;
                updatePrice();
            } else {
                qtyField.disabled = true;
                document.getElementById('totalPrice').innerText = '0.00';
                document.getElementById('totalprice').value = '0.00';
            }
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
        <a href="./index.php"><img src="responsive-demo-home.gif" alt="Home"></a>
        <form method="GET" action="">
            <input type="search" name="search" placeholder="Search..."
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">Search</button>
        </form>
        <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="responsive-demo-menu.gif"
                alt="Menu"></a>
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
            echo '<a href="./profile.php?CustomerID=' . ($_SESSION["cusid"]) . '">Profile</a>';
        }
        ?>
    </nav>

    <main>
        <h1>Cake</h1>
        <div class="search" id="search">
            <form method="GET" action="">
                <input type="search" name="search" placeholder="Search..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        <article style="display: flex; justify-content: center;">
            <?php

            $optionStmt = $pdo->prepare("SELECT * FROM ItemOption WHERE OptionType = 'Cake'");
            $optionStmt->execute();
            $options = $optionStmt->fetchAll();
            ?>

            <div class="detailcake">
                <div>
                    <img src='./images/<?= $row["ItemIMG"] ?>' width='200'>
                </div>
                <div class="details">
                    <h2><?= htmlspecialchars($row["ItemName"]) ?></h2>
                    <?php if ($discountedPrice !== null): ?>
                        <span id="basePrice" style="display:none;"><?= htmlspecialchars($discountedPrice) ?></span>
                        <strong>Promotion Price: <?= htmlspecialchars($discountedPrice) ?> ฿</strong>
                        <span style="text-decoration: line-through; color:red;">Base Price:
                            <?= htmlspecialchars($row["BasePrice"]) ?> ฿</span>
                    <?php else: ?>
                        <span id="basePrice" style="display:none;"><?= htmlspecialchars($row["BasePrice"]) ?></span>
                        Base Price: <?= htmlspecialchars($row["BasePrice"]) ?> ฿<br>
                    <?php endif; ?>
                    Description: <?= htmlspecialchars($row["Description"]) ?><br>

                    <form method="post" action="./manage/manage-cart.php?action=add">
                        <input type="hidden" name="itemType" value="Regular">
                        <input type="hidden" name="cartitemnumber"
                            value="<?= isset($cartitemnumber) ? htmlspecialchars($cartitemnumber) : '' ?>">
                        <input type="hidden" name="ItemID" value="<?= htmlspecialchars($row["ItemID"]) ?>">
                        <input type="hidden" name="ItemName" value="<?= htmlspecialchars($row["ItemName"]) ?>">
                        <input type="hidden" name="Category" value="<?= htmlspecialchars($row["Category"]) ?>">
                        <label for="option">Select cake size:</label>
                        <select name="option" id="cakeSize" required onChange="updateQtyField()">
                            <option value="">-- Select cake size --</option>
                            <?php foreach ($options as $option): ?>
                                <option value="<?= htmlspecialchars($option["OptionValue"]) ?>"
                                    data-additional-cost="<?= htmlspecialchars($option["AdditionalCost"]) ?>"
                                    <?= isset($existingCartItem) && $existingCartItem['OptionValue'] == $option["OptionValue"] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($option["OptionValue"]) ?>
                                    (x<?= htmlspecialchars($option["AdditionalCost"]) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select><br>


                        <label for="qty">Quantity:</label>
                        <input type="number" id="qty" name="qty"
                            value="<?= isset($existingCartItem) ? htmlspecialchars($existingCartItem['Quantity']) : 1 ?>"
                            min="1" max="9" oninput="updatePrice()"><br><br>

                        <strong>Total: <span
                                id="totalPrice"><?= isset($existingCartItem) ? htmlspecialchars($existingCartItem['TotalPrice']) : '0.00' ?></span>
                            ฿</strong><br><br>
                        <input type="hidden" id="totalprice" name="totalPrice"
                            value="<?= isset($existingCartItem) ? htmlspecialchars($existingCartItem['TotalPrice']) : '0.00' ?>">

                        <?php
                        if (empty($_SESSION["userid"])) {
                            echo '<button type="button" onclick="window.location.href=\'./login.php\';" style="padding: 5px 10px;">Please Login</button>';
                        } else {
                            echo '<a href="cart.php?action=">Your cart (' . (isset($_SESSION['cart']) ? sizeof($_SESSION['cart']) : 0) . ')</a>';
                            echo '<button type="submit" class="buybutton">' . (isset($existingCartItem) ? 'Update cart' : 'Add to cart') . '</button>';
                        }
                        ?>
                    </form>
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