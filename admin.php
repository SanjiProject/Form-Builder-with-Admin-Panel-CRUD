<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Fetch all forms
$stmt = $pdo->query("SELECT * FROM forms ORDER BY created_at DESC");
$forms = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dark Theme</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for Dark Theme -->
    <style>
        body {
            background-color: #121212;
            color: #f8f9fa;
        }
        .navbar, .card-header, .btn-custom {
            background-color: #1c1c1c;
            color: #f8f9fa;
        }
        .navbar a.navbar-brand, .navbar a.btn {
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
        .table thead th {
            background-color: #333333;
            color: #f8f9fa;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #2c2c2c;
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #1c1c1c;
        }
        .table td {
            color: #f8f9fa; /* White text for table cells */
        }
        .btn-custom {
            background-color: #007bff;
            color: #f8f9fa;
            border: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            color: #f8f9fa;
        }
        .btn-info {
            background-color: #007bff;
            border-color: #007bff;
            color: #f8f9fa;
        }
        .btn-info:hover {
            background-color: #0056b3;
            border-color: #004085;
            color: #f8f9fa;
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
        .card {
            background-color: #1c1c1c;
            color: #f8f9fa;
            border: none;
        }
        .container {
            margin-top: 20px;
        }
        /* Make form input text white */
        input.form-control, textarea.form-control, select.form-control {
            background-color: #2c2c2c;
            color: #f8f9fa;
            border: 1px solid #555555;
        }
        input.form-control::placeholder, textarea.form-control::placeholder {
            color: #cccccc;
        }
        input.form-control:focus, textarea.form-control:focus, select.form-control:focus {
            background-color: #333333;
            color: #f8f9fa;
            border-color: #777777;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
<br>
    <div class="container">

        <!-- Menu Section -->
        <div class="card mb-4">
            <div class="card-header text-center">
                <h4 class="mb-0">Admin Panel Menu</h4>
            </div>
            <div class="card-body text-center">
                <a href="create_form.php" class="btn btn-primary">Create Form</a>
                <a href="register_admin.php" class="btn btn-success">Register Admin</a>
                <a href="user_list.php" class="btn btn-danger">User List</a>
            </div>
        </div>

        <!-- View Database Section -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">See Submissions</h4>
            </div>
            <div class="card-body">
                <?php if (count($forms) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Form ID</th>
                                <th>Title</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forms as $form): ?>
                                <tr>
                                    <td><?= htmlspecialchars($form['id']) ?></td>
                                    <td><?= htmlspecialchars($form['title']) ?></td>
                                    <td><?= htmlspecialchars($form['created_at']) ?></td>
                                    <td>
                                        <a href="view_submissions.php?form_id=<?= htmlspecialchars($form['id']) ?>" class="btn btn-info btn-sm">View Submissions</a>
                                        <a href="delete_submission.php?form_id=<?= htmlspecialchars($form['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this form?');">Delete Form</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No forms available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
