<?php
$host = 'localhost'; // Your database host
$db = 'databasename'; // Your database name
$user = 'databaseuser'; // Your database username
$pass = 'password'; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
