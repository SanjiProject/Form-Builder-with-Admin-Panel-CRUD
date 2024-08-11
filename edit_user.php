<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';
$userId = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    // Basic validation
    if ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } elseif (!empty($password)) {
        // Update the user in the database with a hashed password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $role, $hashedPassword, $userId]);
        header('Location: user_list.php');
        exit;
    } else {
        // Update the user in the database without changing the password
        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $role, $userId]);
        header('Location: user_list.php');
        exit;
    }
}

// Fetch the user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: user_list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title text-center">Edit User</h2>
            <form method="post" action="edit_user.php?id=<?= htmlspecialchars($userId) ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirm New Password:</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Leave blank to keep current password">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update</button>
            </form>

            <?php if ($error): ?>
                <p class="error text-center mt-3"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <p class="text-center mt-3">Back to <a href="user_list.php">User List</a>.</p>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
