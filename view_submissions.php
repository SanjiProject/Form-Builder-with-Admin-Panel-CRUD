<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Get form_id from the query string
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch form details
$stmt = $pdo->prepare("SELECT title FROM forms WHERE id = :form_id");
$stmt->execute(['form_id' => $form_id]);
$form = $stmt->fetch();

if ($form_id) {
    // Fetch submissions for the specified form_id with search functionality
    $query = "SELECT s.id, s.submitted_at, s.data, u.username FROM submissions s JOIN users u ON s.user_id = u.id WHERE s.form_id = :form_id";
    
    if ($search_query) {
        $query .= " AND u.username LIKE :search_query";
    }
    
    $query .= " ORDER BY s.submitted_at DESC";
    
    $stmt = $pdo->prepare($query);
    $params = ['form_id' => $form_id];
    
    if ($search_query) {
        $params['search_query'] = "%$search_query%";
    }
    
    $stmt->execute($params);
    $submissions = $stmt->fetchAll();
}

if (isset($_POST['export']) && $_POST['export'] == 1) {
    // Export to CSV functionality
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="submissions.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Submission ID', 'Username', 'Submitted At', 'Form Data']);
    
    foreach ($submissions as $submission) {
        $data = unserialize($submission['data']);
        $data_formatted = [];
        
        foreach ($data as $key => $value) {
            if (in_array(pathinfo($value, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp'])) {
                $value = 'Image: ' . htmlspecialchars($value);
            }
            $data_formatted[] = "$key: $value";
        }
        
        fputcsv($output, [
            $submission['id'],
            $submission['username'],
            $submission['submitted_at'],
            implode('; ', $data_formatted)
        ]);
    }
    
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
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
        <!-- Form Details -->
        <?php if ($form): ?>
            <h2>Submissions for Form: <?= htmlspecialchars($form['title']) ?></h2>
        <?php else: ?>
            <h2>No Form Selected</h2>
        <?php endif; ?>

        <!-- Search and Export Forms -->
        <form method="get" action="" class="mb-4">
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($form_id) ?>">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <input type="text" class="form-control" name="search" placeholder="Search by username" value="<?= htmlspecialchars($search_query) ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <form method="post" action="" class="mb-4">
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($form_id) ?>">
            <button type="submit" name="export" value="1" class="btn btn-custom">Export to CSV</button>
            <a href="index.php" class="btn btn-danger">Home</a>
        </form>

        <!-- View Submissions -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">See Submissions</h4>
            </div>
            <div class="card-body">
                <?php if (count($submissions) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Submission ID</th>
                                <th>Username</th>
                                <th>Submitted At</th>
                                <th>Form Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><?= htmlspecialchars($submission['id']) ?></td>
                                    <td><?= htmlspecialchars($submission['username']) ?></td>
                                    <td><?= htmlspecialchars($submission['submitted_at']) ?></td>
                                    <td>
                                        <?php
                                        $data = unserialize($submission['data']);
                                        foreach ($data as $key => $value): ?>
                                            <div><strong><?= htmlspecialchars($key) ?>:</strong> 
                                                <?php if (in_array(pathinfo($value, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp'])): ?>
                                                    <a href="/uploads/<?= htmlspecialchars($value) ?>" target="_blank">View Image</a>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($value) ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No submissions available for this form.</p>
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
