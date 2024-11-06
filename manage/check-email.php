<?php

include "../connect.php";

$stmt = $pdo->prepare("SELECT Email FROM User");
$stmt->execute();

$takenEmail = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

if (in_array($_GET["email"], $takenEmail)) {
    echo "This email address has already been used.";
}

?>