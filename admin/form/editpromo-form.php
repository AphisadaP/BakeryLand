<?php
session_start();
include "../../connect.php";
if (!isset($_SESSION["userid"]) or $_SESSION["type"] != "1") {
    header("Location: ../../index.php");
    exit();
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
    <link href="../../css/form.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="../../main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
        <a href="./adminindex.php"><img src="../../responsive-demo-home.gif" alt="Home"></a>
        <a href="#" onClick='toggle_visibility("menu"); return false;'><img src="../../responsive-demo-menu.gif"
                alt="Menu"></a>
    </div>

    <nav id="menu">
        <a href="../adminindex.php">New Order</a>
        <a href="../managepromotion.php"><span class="material-symbols-outlined">edit</span> Promotion</a>
        <a href="../manageitem.php"><span class="material-symbols-outlined">edit</span> Item</a>
        <a href="../manageorder.php"><span class="material-symbols-outlined">edit</span> Order</a>
        <a href="../managecustomer.php"><span class="material-symbols-outlined">edit</span> Customer</a>
        <a href="../../manage/logout.php">Logout</a>
    </nav>

    <main>
        <h1>Edit Event Promotion</h1>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM Promotion WHERE PromotionID = ?");
        $stmt->bindParam(1, $_GET["PromotionID"]);
        $stmt->execute();
        $promotion = $stmt->fetch();
        ?>
        <article>
            <form class="main-form" action="../../manage/edit-promo.php" method="post" enctype="multipart/form-data">
                <div class="container">
                    <input type="hidden" name="promotionID" value="<?= $promotion["PromotionID"] ?>"><br>
                    <label for="promoname">Name:</label>
                    <input type="text" id="promoname" name="promoname"
                        value="<?= htmlspecialchars($promotion["PromotionName"]) ?>" required><br>

                    <label for="discount">Discount Percentage:</label>
                    <input type="number" id="discount" name="discount"
                        value="<?= htmlspecialchars($promotion["DiscountPercentage"]) ?>" required><br>

                    <label for="startdate">Start Date:</label>
                    <input type="date" id="startdate" name="startdate"
                        value="<?= htmlspecialchars($promotion["StartDate"]) ?>"><br>

                    <label for="enddate">End Date:</label>
                    <input type="date" id="enddate" name="enddate"
                        value="<?= htmlspecialchars($promotion["EndDate"]) ?>"><br>

                    <label for="appliesToAll">Applies to All Items:</label>
                    <input type="checkbox" id="appliesToAll" name="appliesToAll" <?= $promotion["AppliesToAll"] ? 'checked' : '' ?> onchange="toggleItemSelection()"><br>

                    <div id="itemSelection" style="display: <?= $promotion["AppliesToAll"] ? 'none' : 'block' ?>;">
                        <label for="itemIDs">Select Specific Items:</label>
                        <select id="itemIDs" name="itemIDs[]" multiple>
                            <?php
                            $stmtItems = $pdo->query("SELECT ItemID, ItemName FROM Item WHERE Available = '1'");
                            $stmtSelected = $pdo->prepare("SELECT ItemID FROM Promotionitem WHERE PromotionID = ?");
                            $stmtSelected->bindParam(1, $_GET["PromotionID"]);
                            $stmtSelected->execute();
                            $selectedItems = $stmtSelected->fetchAll(PDO::FETCH_COLUMN, 0);

                            while ($item = $stmtItems->fetch()) {
                                $selected = in_array($item["ItemID"], $selectedItems) ? 'selected' : '';
                                echo "<option value='{$item["ItemID"]}' {$selected}>{$item["ItemName"]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button class="add-button" type="submit">Edit Event Promotion</button>
            </form>
        </article>

        <script>
            function toggleItemSelection() {
                var checkbox = document.getElementById("appliesToAll");
                var itemSelection = document.getElementById("itemSelection");
                itemSelection.style.display = checkbox.checked ? "none" : "block";
            }
        </script>
        <script>
            $('option').mousedown(function (e) {
                e.preventDefault();
                var originalScrollTop = $(this).parent().scrollTop();
                $(this).prop('selected', $(this).prop('selected') ? false : true);
                var self = this;
                $(this).parent().focus();
                setTimeout(function () {
                    $(self).parent().scrollTop(originalScrollTop);
                }, 0);

                return false;
            });
        </script>

    </main>

    <footer>
        <a href="#">Sitemap</a>
        <a href="#">Contact</a>
        <a href="#">Privacy</a>
    </footer>
</body>

</html>