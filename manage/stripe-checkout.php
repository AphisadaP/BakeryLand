<?php
require '../vendor/autoload.php'; // Load Stripe's library
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up Stripe with your secret key
\Stripe\Stripe::setApiKey('sk_test_51P0gH3P1YxfDww8b5sEsuc8oBvyXexMHE9p0KEKxoiRBorY7oYjRYOGl5vZcKOFHCXaFTy5HhC9XknmNqkjbYt3800HWr3CuKB');

$useBirthdayDiscount = isset($_POST['applyBirthdayDiscount']) ? true : false;
$rewardPoints = $_POST['rewardPoints'] ?? 0;
$rewardPointValue = 10;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['applyBirthdayDiscount'] = isset($_POST['applyBirthdayDiscount']) ? true : false;
    $_SESSION['rewardPoints'] = isset($_POST['rewardPoints']) ? intval($_POST['rewardPoints']) : 0;
    $_SESSION['finalPrice'] = (float) $_POST['finalPrice'];
}

// Generate a unique token for payment verification
$paymentToken = bin2hex(random_bytes(16)); // Creates a secure, random token
$_SESSION['payment_token'] = $paymentToken;

// Initialize the line_items array
$line_items = [];

foreach ($_SESSION['cart'] as $cartItem) {
    if ($cartItem['ItemType'] === 'Regular') {
        // Add regular item details
        $line_items[] = [
            'price_data' => [
                'currency' => 'thb',
                'product_data' => [
                    'name' => $cartItem['ItemName'],
                    'description' => $cartItem['OptionValue'] ?? 'No options',
                ],
                'unit_amount' => $cartItem['TotalPrice'] / $cartItem['Quantity'] * 100, // Stripe accepts amounts in cents
            ],
            'quantity' => $cartItem['Quantity'],
        ];
    } elseif ($cartItem['ItemType'] === 'SnackBox') {
        // Add SnackBox item details
        $line_items[] = [
            'price_data' => [
                'currency' => 'thb',
                'product_data' => [
                    'name' => $cartItem['SnackBoxName'],
                    'description' => 'Snack Box with custom items',
                ],
                'unit_amount' => $cartItem['TotalPrice'] * 100,
            ],
            'quantity' => 1, // Assuming one SnackBox per entry
        ];
    }
}

$sessionData = [
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'http://202.44.40.193/~cs6530383/project/BakeryLand/manage/insert-order.php?token=' . $paymentToken,
    'cancel_url' => 'http://202.44.40.193/~cs6530383/project/BakeryLand/checkout.php',
];

// If the birthday discount is applicable, create a coupon and add it to the session
if ($useBirthdayDiscount) {
    // Create a 50% off coupon
    $coupon = \Stripe\Coupon::create([
        'percent_off' => 50,
        'duration' => 'once', // Use 'once' for a single-use coupon
        'name' => 'Birthday Discount',
    ]);

    // Add the discount to session data
    $sessionData['discounts'] = [
        [
            'coupon' => $coupon->id,
        ]
    ];
}

if ($rewardPoints > 0) {
    // Calculate the fixed discount amount based on the reward points
    $fixedDiscountAmount = $rewardPoints * $rewardPointValue * 100;

    $coupon = \Stripe\Coupon::create([
        'amount_off' => $fixedDiscountAmount,
        'duration' => 'once',
        'name' => 'Reward Points Discount',
        'currency' => 'thb',
    ]);

    $sessionData['discounts'] = [
        [
            'coupon' => $coupon->id,
        ]
    ];
}

// Create the Stripe Checkout session with the session data
$checkout_session = \Stripe\Checkout\Session::create($sessionData);

// Redirect to Stripe's checkout page
header("Location: " . $checkout_session->url);
exit();
