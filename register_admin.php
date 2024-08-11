<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        $error = 'Username already exists';
    } else {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database with role 'admin'
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$username, $hashedPassword]);

        // Redirect to the admin panel
        header('Location: admin.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            background-color: #1c1c1c;
            border: none;
        }
        .form-control, .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #f8f9fa;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .error {
            color: #dc3545;
        }
        .navbar, .navbar a.navbar-brand, .navbar a.btn {
            background-color: #1c1c1c;
            color: #f8f9fa;
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
            <h2 class="card-title text-center">Register Admin</h2>
            <form method="post" action="register_admin.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <?php if ($error): ?>
                <p class="error text-center mt-3"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <p class="text-center mt-3">Back to <a href="admin.php" class="text-light">Admin Panel</a>.</p>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
