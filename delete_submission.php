<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Check if form_id is provided
if (!isset($_GET['form_id']) || empty($_GET['form_id'])) {
    echo "Error: form_id is missing or invalid.";
    exit;
}

$form_id = $_GET['form_id'];

// Delete the form
$stmt = $pdo->prepare("DELETE FROM forms WHERE id = ?");
$stmt->execute([$form_id]);

// Redirect to the admin panel
header('Location: admin.php');
exit;
?>
