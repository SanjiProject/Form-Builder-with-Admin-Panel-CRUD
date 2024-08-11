<?php
require 'db.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit;
}

// Fetch all forms
$stmt = $pdo->query("SELECT * FROM forms ORDER BY created_at DESC");
$forms = $stmt->fetchAll();

// Fetch the user's submissions
$stmt = $pdo->prepare("SELECT submissions.id, forms.title, submissions.submitted_at 
                       FROM submissions 
                       JOIN forms ON submissions.form_id = forms.id 
                       WHERE submissions.user_id = ? 
                       ORDER BY submissions.submitted_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$submissions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border: 1px solid #dee2e6;
        }
        .card-header {
            background-color: #e9ecef;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }
        .table th {
            background-color: #e9ecef;
            color: #495057;
        }
        .table tbody tr:nth-of-type(odd) {
            background-color: #ffffff;
        }
        .table tbody tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }
        .btn-custom {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #5a6268;
            color: white;
        }
        .btn-warning {
            background-color: #ffc107;
            color: black;
            border: none;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .navbar {
            background-color: #e9ecef;
            border-bottom: 1px solid #dee2e6;
        }
        .navbar a {
            color: #495057;
        }
        .navbar a.btn-home {
            background-color: #6c757d;
            color: white;
        }
        .navbar a.btn-home:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">User Dashboard</a>
            <div class="ml-auto">
                <a href="index.php" class="btn btn-primary text-white">Home</a>
                <a href="logout.php" class="btn btn-danger text-white ml-2">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                    </div>
                    <div class="card-body">
                        <!-- Available Forms Section -->
                        <div class="mb-4">
                            <h3 class="text-center">Available Forms</h3>
                            <br>
                            <?php if (count($forms) > 0): ?>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Form ID</th>
                                            <th>Title</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($forms as $form): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($form['id']) ?></td>
                                                <td><?= htmlspecialchars($form['title']) ?></td>
                                                <td><?= htmlspecialchars($form['created_at']) ?></td>
                                                <td>
                                                    <a href="submit_form.php?form_id=<?= htmlspecialchars($form['id']) ?>" class="btn btn-custom">Submit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center">No forms available at the moment.</p>
                            <?php endif; ?>
                        </div>

                        <!-- User Submissions Section -->
                        <div class="mb-4">
                            <h3 class="text-center">Your Submitted Submissions</h3>
                            <br>
                            <?php if (count($submissions) > 0): ?>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Submission ID</th>
                                            <th>Form Title</th>
                                            <th>Submitted At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($submission['id']) ?></td>
                                                <td><?= htmlspecialchars($submission['title']) ?></td>
                                                <td><?= htmlspecialchars($submission['submitted_at']) ?></td>
                                                <td>
                                                    <a href="edit_submission.php?submission_id=<?= htmlspecialchars($submission['id']) ?>" class="btn btn-warning">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center">You have not submitted any forms yet.</p>
                            <?php endif; ?>
                        </div>
<br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
