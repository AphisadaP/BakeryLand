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
        <h1>Your Shopping Cart</h1>
        <article style="display: flex; flex-direction: column; align-items: center;">
            <?php
            $totalPrice = 0;

            if (!empty($_SESSION['cart'])): ?>
                <table class="center">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Option</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $cartItem): ?>
                            <?php if ($cartItem['ItemType'] === 'Regular'): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cartItem['ItemName']) ?></td>
                                    <td><?= htmlspecialchars($cartItem['Quantity']) ?></td>
                                    <td><?= htmlspecialchars($cartItem['OptionValue'] ?? 'None') ?></td>
                                    <td><?= htmlspecialchars(number_format($cartItem['TotalPrice'], 2)) ?> ฿</td>
                                    <td>
                                        <?php
                                        $editLink = '';
                                        if (strpos(strtolower($cartItem['ItemCategory']), 'cake') !== false) {
                                            $editLink = "detailcake.php";
                                        } elseif (strpos(strtolower($cartItem['ItemCategory']), 'drinks') !== false) {
                                            $editLink = "detaildrink.php";
                                        } elseif (strpos(strtolower($cartItem['ItemCategory']), 'bread') !== false) {
                                            $editLink = "detailbread.php";
                                        }
                                        ?>
                                        <?php if ($editLink): ?>
                                            <a
                                                href="<?= $editLink ?>?ItemID=<?= urlencode($cartItem['ItemID']) ?>&cartitemnumber=<?= urlencode($cartItem['cartitemnumber']) ?>">Edit</a>
                                            <span style="margin: 0 5px; color: #9b4819;">|</span>
                                        <?php endif; ?>
                                        <a
                                            href="./manage/manage-cart.php?action=remove&cartitemnumber=<?= urlencode($cartItem['cartitemnumber']) ?>">Remove</a>
                                    </td>
                                </tr>
                            <?php elseif ($cartItem['ItemType'] === 'SnackBox'): ?>
                                <tr>
                                    <td colspan="5"><strong>SnackBox Name:</strong>
                                        <?= htmlspecialchars($cartItem['SnackBoxName']) ?></td>
                                </tr>
                                <?php foreach ($cartItem['Items'] as $snackBoxItem): ?>
                                    <tr>
                                        <td style="padding-left: 20px;"><?= htmlspecialchars($snackBoxItem['ItemName']) ?></td>
                                        <td><?= htmlspecialchars($snackBoxItem['Quantity']) ?></td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3"><strong>SnackBox Total Price:</strong></td>
                                    <td><?= htmlspecialchars(number_format($cartItem['TotalPrice'], 2)) ?> ฿</td>
                                    <td>
                                        <a
                                            href="sbdetail.php?SnackBoxID=<?= urlencode($cartItem['SnackBoxID']) ?>&cartitemnumber=<?= urlencode($cartItem['cartitemnumber']) ?>">Edit</a>
                                        <span style="margin: 0 5px; color: #9b4819;">|</span>
                                        <a
                                            href="./manage/manage-cart.php?action=remove&cartitemnumber=<?= urlencode($cartItem['cartitemnumber']) ?>">Remove</a>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php
                            $totalPrice += $cartItem['TotalPrice'];
                            $_SESSION['totalPrice'] = $totalPrice;
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="text-align: center; margin:10px 0 20px 0;">
                    <h3>Total: <?= number_format($totalPrice, 2) ?> ฿</h3>
                    <button onclick="window.location.href='./checkout.php';"
                        style="margin: 0 auto; display: block; padding: 5px 10px;">Check out</button>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </article>
    </main>

    <footer>
        <a href="#">Sitemap</a>
        <a href="#">Contact</a>
        <a href="#">Privacy</a>
    </footer>
</body>

</html>