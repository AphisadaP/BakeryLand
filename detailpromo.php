<?php
session_start();
include "./connect.php";
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
    <link href="./css/detail.css" rel="stylesheet" type="text/css" />
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
        <h1>Promotion</h1>
        <div class="search" id="search">
            <form method="GET" action="">
                <input type="search" name="search" placeholder="Search..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        <article style="display: flex; justify-content: center;">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM Promotion WHERE PromotionID = ?");
            $stmt->bindParam(1, $_GET["PromotionID"]);
            $stmt->execute();
            $row = $stmt->fetch();
            ?>
            <div>
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
                            $stmt = $pdo->prepare("SELECT Item.ItemName, Item.ItemID, Item.Category, Item.Available FROM Promotionitem 
                            JOIN Item ON Promotionitem.ItemID = Item.ItemID 
                            WHERE Promotionitem.PromotionID = ?");
                            $stmt->execute([$row["PromotionID"]]);
                            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($items)):
                                foreach ($items as $item):
                                    $link = "#";
                                    if ($item["Category"] === "Cake") {
                                        $link = "detailcake.php?ItemID=" . urlencode($item["ItemID"]);
                                    } elseif ($item["Category"] === "Drinks") {
                                        $link = "detaildrink.php?ItemID=" . urlencode($item["ItemID"]);
                                    } elseif ($item["Category"] === "Bread") {
                                        $link = "detailbread.php?ItemID=" . urlencode($item["ItemID"]);
                                    }
                                    ?>
                                    <li>
                                        <?php if ($item["Available"] == 1): ?>
                                            <a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($item["ItemName"]) ?></a>
                                        <?php else: ?>
                                            <a class='dead'><?= htmlspecialchars($item["ItemName"]) ?> (Unavailable)</a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach;
                            else: ?>
                                <li>No items associated with this promotion.</li>
                            <?php endif; ?>
                        </ul>

                    <?php endif; ?>
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