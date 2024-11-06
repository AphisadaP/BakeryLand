<?php

include "../connect.php";

$stmt = $pdo->prepare("SELECT Name FROM Customer");
$stmt->execute();

$takenUsernames = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

if (in_array($_GET["name"], $takenUsernames)) {
    echo "Username not available.";
}

?>