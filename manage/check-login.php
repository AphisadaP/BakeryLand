<?php
include "../connect.php";

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $_POST["email"];
$password = $_POST["password"];

$stmt = $pdo->prepare("SELECT * FROM User WHERE Email = ?");
$stmt->bindParam(1, $_POST["email"]);
$stmt->execute();
$row = $stmt->fetch();

if ($row) {
  if (password_verify($password, $row["Password"])) {
    $_SESSION["userid"] = $row["UserID"];
    $_SESSION["type"] = $row["IsAdmin"];
    $_SESSION["cusid"] = $row["CustomerID"];

    if ($_SESSION["type"] == "1") {
      header("Location: ../admin/adminindex.php");
    } else {
      // Check for birthday discount eligibility
      $stmt = $pdo->prepare("SELECT DateOfBirth, BirthdayDiscountEligible, BirthdayDiscountLastReset FROM Customer WHERE CustomerID = ?");
      $stmt->execute([$row["CustomerID"]]);
      $user = $stmt->fetch();

      $dobMonth = date("m", strtotime($user['DateOfBirth']));
      $currentMonth = date('m');
      $currentDate = date('Y-m-d'); // Current date for tracking purposes

      // Check if it's the user's birthday month and discount hasn't been reset this month
      if (
        $dobMonth == $currentMonth &&
        $user['BirthdayDiscountEligible'] == 0 &&
        (empty($user['BirthdayDiscountLastReset']) || date('m', strtotime($user['BirthdayDiscountLastReset'])) != $currentMonth)
      ) {

        // Reset eligibility
        $stmt = $pdo->prepare("UPDATE Customer SET BirthdayDiscountEligible = 1, BirthdayDiscountLastReset = ? WHERE CustomerID = ?");
        $stmt->execute([$currentDate, $row["CustomerID"]]);
      }

      header("Location: ../index.php");
    }


  } else {
    header("Location: ../login.php?error=1");
  }

} else {
  header("Location: ../login.php?error=1");
}
