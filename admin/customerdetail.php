<?php
session_start();
include "../connect.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and is an admin
if (!isset($_SESSION["userid"]) || $_SESSION["type"] != "1") {
    header("Location: ../index.php");
    exit();
}

// Check if CustomerID is provided in the URL
if (!isset($_GET['CustomerID'])) {
    header("Location: managecustomer.php"); // Redirect if no CustomerID is provided
    exit();
}

$customerID = $_GET['CustomerID'];

try {
    $stmt = $pdo->prepare("SELECT * FROM Customer WHERE CustomerID = :customerID");
    $stmt->bindParam(':customerID', $customerID);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        echo '<h3>No Customer Found</h3>';
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM `Order` WHERE CustomerID = :customerID");
    $stmt->bindParam(':customerID', $customerID);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Database error: ' . htmlspecialchars($e->getMessage());
    exit();
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Customer Detail - BakeryLand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/admin.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Radley:ital@0;1&display=swap" rel="stylesheet">
    <style>
        .content-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 800px;
        }

        .customer-detail,
        .order-list {
            margin-bottom: 20px;
        }

        .customer-detail {
            display: flex;
            align-items: center;
        }

        .customer-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            margin-right: 20px;
        }

        .button-container {
            margin-top: 20px;
            text-align: center;
        }

        .order-list h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        .customer-info p {
            display: flex;
            align-items: center;
            width: 100%;
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

    <nav id="menu">
        <a href="./adminindex.php">New Order</a>
        <a href="./managepromotion.php"><span class="material-symbols-outlined">edit</span> Promotion</a>
        <a href="./manageitem.php"><span class="material-symbols-outlined">edit</span> Item</a>
        <a href="./manageorder.php"><span class="material-symbols-outlined">edit</span> Order</a>
        <a href="./managecustomer.php"><span class="material-symbols-outlined">edit</span> Customer</a>
        <a href="../manage/logout.php">Logout</a>
    </nav>

    <main>
        <h1>Customer Detail</h1>
        <article style="display: flex; justify-content: center;">
            <div class="content-wrapper">
                <div class="customer-detail">
                    <img src='../profiles/<?= htmlspecialchars($customer['CusIMG'] ?? 'default_image.png'); ?>'
                        alt="<?php echo htmlspecialchars($customer['Name']); ?>" class="customer-image">

                    <div class="customer-info">
                        <p><strong>Name : </strong> <?php echo htmlspecialchars($customer['Name']); ?></p>
                        <p><strong>Phone : </strong> <?php echo htmlspecialchars($customer['Phone']); ?></p>
                        <p><strong>Address : </strong> <?php echo htmlspecialchars($customer['Address']); ?></p>
                        <p><strong>Reward Points : </strong> <?php echo htmlspecialchars($customer['RewardPoints']); ?>
                        </p>
                        <p><strong>Date of Birth : </strong> <?php echo htmlspecialchars($customer['DateOfBirth']); ?>
                        </p>
                        <p><strong>Birthday Discount Eligible : </strong>
                            <?php echo htmlspecialchars($customer['BirthdayDiscountEligible']); ?></p>
                    </div>


                </div>

                <div class="button-container">
                    <a href="managecustomer.php"><button type="button">Back to Customer List</button></a><br><br>
                </div>

                <div class="order-list">
                    <h3>Orders for Customer: <span><?php echo htmlspecialchars($customer["Name"]); ?></span></h3>
                    <table class='center'>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Delivery Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="5">No orders found for this customer.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><a
                                                href="./orderdetail.php?OrderID=<?= htmlspecialchars($order['OrderID']); ?>"><?php echo htmlspecialchars($order['OrderID']); ?></a>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                                        <td><?php echo htmlspecialchars($order['TotalAmount']); ?></td>
                                        <td><?php echo htmlspecialchars($order['PaymentStatus']); ?></td>
                                        <td><?php echo htmlspecialchars($order['DeliveryStatus']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </article>
    </main>

    <footer>
        <a href="#">Sitemap</a>
        <a href="#">Contact Us</a>
        <a href="#">Privacy Policy</a>
    </footer>

</body>

</html>