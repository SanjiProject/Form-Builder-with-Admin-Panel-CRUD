<?php
$host = 'localhost'; // Your database host
$db = 'form_builder'; // Your database name
$user = 'form_builder'; // Your database username
$pass = 'WjPYbnxwpTwJaGFn'; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
