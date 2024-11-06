<?php
session_start();
include "connect.php";

if (!isset($_SESSION["userid"])) {
    header("Location: ../index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BakeryLand - Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="./css/main.css" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <script src="main.js"></script>
    <style>
        .checkout{
            text-align: center;
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
        <h1>Checkout</h1>

        <?php
        $customerID = $_SESSION['cusid'];

        $stmt = $pdo->prepare("SELECT * FROM Customer WHERE CustomerID = ?");
        $stmt->execute([$customerID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if it's the birthday month and discount is eligible
        $dobMonth = date("m", strtotime($row['DateOfBirth']));
        $currentMonth = date("m");
        $isBirthDay = ($dobMonth == $currentMonth && $row['BirthdayDiscountEligible'] == 1);

        $totalPrice = $_SESSION['totalPrice'];

        $isChecked = isset($_SESSION['applyBirthdayDiscount']) && $_SESSION['applyBirthdayDiscount'];
        $storedRewardPoints = isset($_SESSION['rewardPoints']) ? $_SESSION['rewardPoints'] : 0;
        $finalPrice = isset($_SESSION['finalPrice']) ? (float)$_SESSION['finalPrice'] : (float)$totalPrice;
        ?>
        <article>

            <div class="checkout" style="display: flex; justify-content: center; padding:10px;">
                <div style="padding: 15px; width: 100%; max-width: 600px;">
                    <h2>Customer Details</h2>
                    Name: <?= htmlspecialchars($row["Name"]) ?><br>
                    Address: <?= htmlspecialchars($row["Address"]) ?><br>
                    Reward Points: <?= $row["RewardPoints"] ?><br>

                    <h2>Order Summary</h2>
                    Total Price: <?= number_format($totalPrice, 2) ?> ฿<br>
                    Your Points Earned: <?= intval($totalPrice / 100) ?> point(s).<br><br>

                    <form method="post" action="./manage/stripe-checkout.php">
                        <?php if ($isBirthDay): ?>
                            <label>
                                <input type="checkbox" name="applyBirthdayDiscount" id="applyBirthdayDiscount"
                                    onchange="calculateTotal()" <?php echo $isChecked ? 'checked' : ''; ?>> Use Birthday
                                Discount (50% off)
                            </label><br><br>
                        <?php endif; ?>

                        <label for="rewardPoints">Use Reward Points (10 points = 100 ฿ discount):</label>
                        <button type="button" style="width: 30px;" onclick="decrementPoints()">-</button>
                        <input type="number" style="width: 60px;" id="rewardPoints" name="rewardPoints" min="0" step="10" 
                               oninput="calculateTotal()" value="<?= $storedRewardPoints ?>" 
                               <?php echo $isChecked ? 'disabled' : ''; ?>>
                        <button type="button" style="width: 30px;" onclick="incrementPoints()">+</button><br>
                        <span id="rewardMessage" style="color: red;">You can't discount below 100 ฿ and need at least
                            200 ฿ total to apply reward points.</span><br>

                        <h4>Final Price: <span id="final-price"><?= isset($_SESSION['finalPrice']) ? number_format($finalPrice, 2) : number_format($totalPrice, 2) ?> ฿</span></h4>
                        <input type="hidden" id="finalPriceInput" name="finalPrice" value="<?= number_format($finalPriceInputValue, 2) ?>">
                        <button type="submit">Confirm Purchase</button>
                    </form>

                    <script>
                        function calculateTotal() {
                            let totalPrice = <?= $totalPrice ?>;
                            let pointshave = <?= json_encode($row["RewardPoints"]); ?>;
                            let finalPrice = totalPrice;

                            // Reference to the checkbox and reward points input
                            const birthdayDiscountCheckbox = document.getElementById("applyBirthdayDiscount");
                            const rewardPointsInput = document.getElementById("rewardPoints");
                            const minPayment = 100; // Minimum amount to be paid

                            // Calculate maximum reward points that can be used
                            const maxRewardDiscount = Math.floor((totalPrice - minPayment) / 100) // 10 points = 100 Baht
                            const maxPointsCanUse = Math.min((pointshave / 10),maxRewardDiscount)
                            const maxRewardPoints = Math.max(0, Math.floor(maxPointsCanUse)) * 10;
                            
                            console.log(maxPointsCanUse);
                            console.log(maxRewardDiscount);
                            console.log(maxRewardPoints);

                            // Check if birthday discount is selected
                            if (birthdayDiscountCheckbox && birthdayDiscountCheckbox.checked) {
                                finalPrice *= 0.5; // Apply 50% discount

                                // Disable reward points input if birthday discount is applied
                                if (rewardPointsInput) {
                                    rewardPointsInput.value = "0";
                                    rewardPointsInput.disabled = true; // Disable input
                                }
                            } else {
                                // Enable the reward points input if birthday discount is not applied
                                if (rewardPointsInput) {
                                    rewardPointsInput.disabled = false;

                                    // Get the value of the reward points entered by the user
                                    let rewardPoints = parseFloat(rewardPointsInput.value) || 0;

                                    // Round down to the nearest multiple of 10
                                    rewardPoints = Math.floor(rewardPoints / 10) * 10;

                                    // Limit reward points to max allowable points
                                    const allowedRewardPoints = Math.min(rewardPoints, maxRewardPoints);
                                    const rewardDiscount = (allowedRewardPoints / 10) * 100; // 10 points = 100 Baht
                                    finalPrice -= rewardDiscount;

                                    // Update the input value to reflect the maximum allowed if user entered more
                                    rewardPointsInput.value = allowedRewardPoints;
                                }
                            }

                            // Ensure final price is not negative
                            finalPrice = Math.max(finalPrice, 0);

                            // Update the display
                            document.getElementById("final-price").textContent = finalPrice.toFixed(2) + ' ฿';
                            document.getElementById("finalPriceInput").value = finalPrice.toFixed(2);
                        }

                        // Add event listener to the checkbox
                        document.getElementById("applyBirthdayDiscount").onchange = function () {
                            calculateTotal(); // Recalculate total whenever the checkbox state changes
                        };

                        function incrementPoints() {
        const rewardPointsInput = document.getElementById("rewardPoints");
        const maxRewardPoints = <?= json_encode($row["RewardPoints"]); ?>;
        let currentPoints = parseInt(rewardPointsInput.value) || 0;

        if (currentPoints + 10 <= maxRewardPoints) {
            rewardPointsInput.value = currentPoints + 10;
            calculateTotal();
        }
    }

    function decrementPoints() {
        const rewardPointsInput = document.getElementById("rewardPoints");
        let currentPoints = parseInt(rewardPointsInput.value) || 0;

        if (currentPoints - 10 >= 0) {
            rewardPointsInput.value = currentPoints - 10;
            calculateTotal();
        }
    }

    document.getElementById("applyBirthdayDiscount").onchange = function () {
        calculateTotal();
    };

                        // Initial call to set the correct state on page load
                        window.onload = function () {
                            calculateTotal();

                            // Ensure reward points input is enabled/disabled based on the checkbox
                            const birthdayDiscountCheckbox = document.getElementById("applyBirthdayDiscount");
                            const rewardPointsInput = document.getElementById("rewardPoints");

                            if (birthdayDiscountCheckbox && birthdayDiscountCheckbox.checked) {
                                rewardPointsInput.disabled = true; // Disable if the birthday discount is checked
                            } else {
                                rewardPointsInput.disabled = false; // Enable if the birthday discount is not checked
                            }
                        };
                    </script>


        </article>

    </main>

    <footer>
        <a href="#">Sitemap</a>
        <a href="#">Contact</a>
        <a href="#">Privacy</a>
    </footer>
</body>

</html>