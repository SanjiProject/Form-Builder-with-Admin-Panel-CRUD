<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #f8f9fa;
        }
        .container {
            max-width: 1000px;
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            background-color: #1c1c1c;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #f8f9fa;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-info {
            background-color: #007bff;
            border-color: #007bff;
            color: #f8f9fa;
        }
        .btn-info:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        table {
            color: #f8f9fa;
        }
        th, td {
            text-align: center;
        }
        .navbar, .navbar a.navbar-brand, .navbar a.btn {
            background-color: #1c1c1c;
            color: #f8f9fa;
        }
        .navbar a.btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .navbar a.btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">Admin Panel</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    <br>
    <div class="container">
        <div class="card">
            <h2 class="card-title text-center">User List</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-white">ID</th>
                        <th class="text-white">Username</th>
                        <th class="text-white">Role</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-white"><?= htmlspecialchars($user['id']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-info btn-sm text-white">Edit</a>
                                <a href="delete_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-danger btn-sm text-white" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
