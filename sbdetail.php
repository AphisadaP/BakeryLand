<?php
session_start();
include "connect.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

echo '<script>';
if ($existingCartItem) {
    echo 'console.log("Existing cart item found:", ' . json_encode($existingCartItem) . ');';
} else {
    echo 'console.log("No existing cart item found for cartitemnumber: ' . htmlspecialchars($cartitemnumber) . '");';
}
echo '</script>';

?>



<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BakeryLand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="./css/promotion.css" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="main.js"></script>
    <script>
        function updatePrice(discountPercentage) {
            let drinkPrices = Array.from(document.querySelectorAll('.drink-select')).map(el => parseFloat(el.selectedOptions[0].dataset.price) || 0);
            let breadPrices = Array.from(document.querySelectorAll('.bread-select')).map(el => parseFloat(el.selectedOptions[0].dataset.price) || 0);
            let cakePrices = Array.from(document.querySelectorAll('.cake-select')).map(el => parseFloat(el.selectedOptions[0].dataset.price) || 0);

            let totalPrice = [...drinkPrices, ...breadPrices, ...cakePrices].reduce((acc, price) => acc + price, 0);

            totalPrice = totalPrice * ((100 - discountPercentage) / 100);
            document.getElementById('totalPrice').innerText = totalPrice.toFixed(2);
            document.getElementById('totalprice').value = totalPrice.toFixed(2);
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
        <h1>SnackBox</h1>
        <article style="display: flex; justify-content: center;">

            <?php
            $stmt = $pdo->prepare("SELECT * FROM SnackBox WHERE SnackBoxID = ?");
            $stmt->bindParam(1, $_GET["SnackBoxID"]);
            $stmt->execute();
            $snackbox = $stmt->fetch();
            $discountPercentage = $snackbox["DiscountPercentage"];
            ?>

            <div class="detailcake">
                <div>
                    <img src='./images/<?= $snackbox["SBIMG"] ?>' width='200'>
                </div>
                <div class="details">
                    <h2><?= htmlspecialchars($snackbox["SnackBoxName"]) ?></h2>
                    Description: <?= htmlspecialchars($snackbox["Description"]) ?><br><br>

                    <form method="post" action="./manage/manage-cart.php?action=add">
                        <input type="hidden" name="itemType" value="SnackBox">
                        <input type="hidden" name="SnackBoxName"
                            value="<?= htmlspecialchars($snackbox["SnackBoxName"]) ?>">
                        <input type="hidden" name="SnackBoxID" value="<?= htmlspecialchars($snackbox["SnackBoxID"]) ?>">
                        <input type="hidden" name="cartitemnumber"
                            value="<?= isset($cartitemnumber) ? htmlspecialchars($cartitemnumber) : '' ?>">
                        <?php
                        $existingItems = $existingCartItem ? $existingCartItem['Items'] : [];
                        $existingDrinks = [];
                        $existingBreads = [];
                        $existingCakes = [];

                        foreach ($existingItems as $item) {
                            if ($item['OptionID'] === 1) {
                                for ($i = 0; $i < $item['Quantity']; $i++) {
                                    $existingDrinks[] = $item;
                                }
                            } elseif ($item['OptionID'] === null) {
                                for ($i = 0; $i < $item['Quantity']; $i++) {
                                    $existingBreads[] = $item;
                                }
                            } elseif ($item['OptionID'] === 4) {
                                for ($i = 0; $i < $item['Quantity']; $i++) {
                                    $existingCakes[] = $item;
                                }
                            }
                        }

                        // For Drinks
                        for ($i = 1; $i <= $snackbox["MaxDrinks"]; $i++):
                            $drinkSelected = isset($existingDrinks[$i - 1]) ? $existingDrinks[$i - 1] : null;
                            ?>
                            <label for="drink-<?= $i ?>">Select Drink
                                (<?= $i ?>/<?= htmlspecialchars($snackbox["MaxDrinks"]) ?>):</label>
                            <select id="drink-<?= $i ?>" name="drinks[]" class="drink-select" required
                                onchange="updatePrice(<?= $discountPercentage ?>)">
                                <option value="">-- Select a drink --</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM Item WHERE Category = 'Drinks' AND Available = '1'");
                                while ($row = $stmt->fetch()) {
                                    $selected = $drinkSelected && $drinkSelected['ItemID'] == $row["ItemID"] ? 'selected' : '';
                                    echo "<option value='{$row["ItemID"]}' data-price='{$row["BasePrice"]}' $selected>{$row["ItemName"]} - {$row["BasePrice"]} ฿</option>";
                                }
                                ?>
                            </select><br><br>
                        <?php endfor; ?>

                        <!-- For Breads -->
                        <?php
                        for ($i = 1; $i <= $snackbox["MaxBreads"]; $i++):
                            $breadSelected = isset($existingBreads[$i - 1]) ? $existingBreads[$i - 1] : null;
                            ?>
                            <label for="bread-<?= $i ?>">Select Bread
                                (<?= $i ?>/<?= htmlspecialchars($snackbox["MaxBreads"]) ?>):</label>
                            <select id="bread-<?= $i ?>" name="breads[]" class="bread-select" required
                                onchange="updatePrice(<?= $discountPercentage ?>)">
                                <option value="">-- Select a bread --</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM Item WHERE Category = 'Bread' AND Available = '1'");
                                while ($row = $stmt->fetch()) {
                                    $selected = $breadSelected && $breadSelected['ItemID'] == $row["ItemID"] ? 'selected' : '';
                                    echo "<option value='{$row["ItemID"]}' data-price='{$row["BasePrice"]}' $selected>{$row["ItemName"]} - {$row["BasePrice"]} ฿</option>";
                                }
                                ?>
                            </select><br><br>
                        <?php endfor; ?>

                        <!-- For Cakes -->
                        <?php
                        for ($i = 1; $i <= $snackbox["MaxCakes"]; $i++):
                            $cakeSelected = isset($existingCakes[$i - 1]) ? $existingCakes[$i - 1] : null;
                            ?>
                            <label for="cake-<?= $i ?>">Select Cake
                                (<?= $i ?>/<?= htmlspecialchars($snackbox["MaxCakes"]) ?>):</label>
                            <select id="cake-<?= $i ?>" name="cakes[]" class="cake-select" required
                                onchange="updatePrice(<?= $discountPercentage ?>)">
                                <option value="">-- Select a cake --</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM Item WHERE Category = 'Cake' AND Available = '1'");
                                while ($row = $stmt->fetch()) {
                                    $selected = $cakeSelected && $cakeSelected['ItemID'] == $row["ItemID"] ? 'selected' : '';
                                    echo "<option value='{$row["ItemID"]}' data-price='{$row["BasePrice"]}' $selected>{$row["ItemName"]} - {$row["BasePrice"]} ฿</option>";
                                }
                                ?>
                            </select><br><br>
                        <?php endfor; ?>



                        <!-- Display Total Price -->
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