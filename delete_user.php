<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$userId = $_GET['id'] ?? '';

if ($userId) {
    // Delete the user from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    // Redirect to the user list
    header('Location: user_list.php');
    exit;
}
?>
