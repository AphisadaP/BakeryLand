<?php
session_start();
include "../connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];

    // ตรวจสอบความถูกต้องของรหัสผ่าน
    if ($password1 !== $password2) {
        echo "Passwords do not match!";
        exit;
    }

    // เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($password1, PASSWORD_DEFAULT);

    $member_dir = "../profiles/";
    $file_id = uniqid();
    $imgcus = $member_dir . basename($file_id . "_" . $_FILES["CusIMG"]["name"]);

    if (move_uploaded_file($_FILES["CusIMG"]["tmp_name"], $imgcus)) {

        $pdo->beginTransaction();

        try {
            $stmt_customer = $pdo->prepare("INSERT INTO Customer (Name, Phone, Address, RewardPoints, DateOfBirth, BirthdayDiscountEligible, CusIMG) VALUES (?, ?, ?, ?, ?, ?,?)");
            echo "1";
            $rewardPoints = 0;
            $birthdayDiscountEligible = 1;
            $stmt_customer->bindParam(1, $username);
            $stmt_customer->bindParam(2, $phone);
            $stmt_customer->bindParam(3, $address);
            $stmt_customer->bindParam(4, $rewardPoints, PDO::PARAM_INT);
            $stmt_customer->bindParam(5, $birthday);
            $stmt_customer->bindParam(6, $birthdayDiscountEligible, PDO::PARAM_INT);
            $stmt_customer->bindParam(7, $imgcus); // Use the new file name

            $stmt_customer->execute();
            $CustomerID = $pdo->lastInsertId();

            $stmt_user = $pdo->prepare("INSERT INTO User (Email ,Password ,IsAdmin ,CustomerID) VALUES (?, ?, ?, ?)");
            echo "2";
            $isadmin = 0;
            $stmt_user->bindParam(1, $email);
            $stmt_user->bindParam(2, $hashed_password);
            $stmt_user->bindParam(3, $isadmin, PDO::PARAM_INT);
            $stmt_user->bindParam(4, $CustomerID, PDO::PARAM_INT);
            $stmt_user->execute();

            $pdo->commit();
            header("Location: ../login.php");
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error uploading image.";
        exit;
    }
}
?>