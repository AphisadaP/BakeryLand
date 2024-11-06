<?php 
session_start();
include "../connect.php";

function getItemDetails($itemID) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT ItemName, BasePrice FROM Item WHERE ItemID = ?");
    $stmt->bindParam(1, $itemID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addOrUpdateSnackBoxItem(&$snackBoxItems, $itemID, $itemDetails, $optionID) {
    foreach ($snackBoxItems as &$item) {
        if ($item['ItemID'] === $itemID && $item['OptionID'] === $optionID) {
            $item['Quantity'] += 1;
            return;
        }
    }
    $snackBoxItems[] = [
        'ItemID' => $itemID,
        'ItemName' => $itemDetails['ItemName'],
        'OptionID' => $optionID,
        'Quantity' => 1
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add') {
    $itemType = $_POST['itemType'] ?? null;
    $cartitemnumber = $_POST['cartitemnumber'] ?? null;

    if ($itemType === 'Regular') {
        $itemID = $_POST['ItemID'];
        $itemCategory = $_POST['Category'];
        $itemName = $_POST['ItemName'];
        $optionValue = $_POST['option'] ?? null;
        $quantity = $_POST['qty'];
        $totalPrice = $_POST['totalPrice'];

        $optionID = null;
        if ($optionValue) {
            $stmt = $pdo->prepare("SELECT OptionID FROM ItemOption WHERE OptionValue = ?");
            $stmt->bindParam(1, $optionValue);
            $stmt->execute();
            $option = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($option) {
                $optionID = $option['OptionID'];
            }
        }

        if ($cartitemnumber) {
            foreach ($_SESSION['cart'] as &$cartItem) {
                if ($cartItem['cartitemnumber'] === $cartitemnumber) {
                    $cartItem['OptionValue'] = $optionValue;
                    $cartItem['OptionID'] = $optionID;
                    $cartItem['Quantity'] = (int)$quantity;
                    $cartItem['TotalPrice'] = (float)$totalPrice;
                    break;
                }
            }
        } else {
            $_SESSION['cart'][] = [
                'cartitemnumber' => uniqid(),
                'ItemType' => $itemType,
                'ItemName' => $itemName,
                'ItemCategory'=> $itemCategory,
                'ItemID' => (int)$itemID,
                'OptionValue' => $optionValue,
                'OptionID' => $optionID,
                'Quantity' => (int)$quantity,
                'TotalPrice' => (float)$totalPrice
            ];
        }
    }

if ($itemType === 'SnackBox') {
    $snackBoxID = $_POST['SnackBoxID'];
    $snackBoxName = $_POST['SnackBoxName'];
    $snackBoxItems = [];

    if (isset($_POST['drinks'])) {
        foreach ($_POST['drinks'] as $drinkItemID) {
            if (!empty($drinkItemID)) {
                $itemDetails = getItemDetails($drinkItemID);
                addOrUpdateSnackBoxItem($snackBoxItems, $drinkItemID, $itemDetails, 1); // OptionID fixed at 1 for drinks
            }
        }
    }

    if (isset($_POST['breads'])) {
        foreach ($_POST['breads'] as $breadItemID) {
            if (!empty($breadItemID)) {
                $itemDetails = getItemDetails($breadItemID);
                addOrUpdateSnackBoxItem($snackBoxItems, $breadItemID, $itemDetails, null); // No specific OptionID for breads
            }
        }
    }

    if (isset($_POST['cakes'])) {
        foreach ($_POST['cakes'] as $cakeItemID) {
            if (!empty($cakeItemID)) {
                $itemDetails = getItemDetails($cakeItemID);
                addOrUpdateSnackBoxItem($snackBoxItems, $cakeItemID, $itemDetails, 4); // OptionID fixed at 4 for cakes
            }
        }
    }

    $totalSnackBoxPrice = $_POST['totalPrice'];
    $cartUpdated = false;

    // Check if the SnackBox already exists in the cart
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['cartitemnumber'] === $_POST['cartitemnumber'] && $cartItem['ItemType'] === $itemType) {
            // Update existing cart item
            $cartItem['Items'] = $snackBoxItems;
            $cartItem['TotalPrice'] = $totalSnackBoxPrice;
            $cartUpdated = true;
            break;
        }
    }

    // If not updated, add a new item to the cart
    if (!$cartUpdated) {
        $_SESSION['cart'][] = [
            'cartitemnumber' => uniqid(),
            'ItemType' => $itemType,
            'SnackBoxID' => $snackBoxID,
            'SnackBoxName' => $snackBoxName,
            'Items' => $snackBoxItems,
            'TotalPrice' => $totalSnackBoxPrice
        ];
    }
}
}

// Handle remove item action
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['cartitemnumber'])) {
    $cartitemnumberToRemove = $_GET['cartitemnumber'];
    foreach ($_SESSION['cart'] as $index => $cartItem) {
        if ($cartItem['cartitemnumber'] === $cartitemnumberToRemove) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

header('Location: ../cart.php');
exit();
