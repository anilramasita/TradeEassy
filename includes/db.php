<?php
// includes/db.php

$host = 'sql101.infinityfree.com';
$db = 'if0_38251626_trading_db';
$user = 'if0_38251626';
$password = 'kEnvamoZXbYVVKa';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
