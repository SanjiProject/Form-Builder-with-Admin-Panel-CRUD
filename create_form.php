<?php
require 'db.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle form creation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form title
    if (empty($_POST['form_title'])) {
        echo "<div class='alert alert-danger'>Error: Form title is required.</div>";
        exit;
    }

    // Insert the form into the database
    $form_title = $_POST['form_title'];
    $stmt = $pdo->prepare("INSERT INTO forms (title) VALUES (?)");
    $stmt->execute([$form_title]);
    $form_id = $pdo->lastInsertId();

    // Insert each form field into the database
    foreach ($_POST['fields'] as $field) {
        $field_name = $field['name'];
        $field_type = $field['type'];
        $is_required = isset($field['required']) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO form_fields (form_id, field_name, field_type, is_required) VALUES (?, ?, ?, ?)");
        $stmt->execute([$form_id, $field_name, $field_type, $is_required]);
    }

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form Created</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background-color: #f5f5f5; }
            .container { max-width: 600px; margin-top: 50px; }
            .alert { margin-bottom: 20px; }
            .navbar { background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
            .navbar a { color: #495057; }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="#">Form Created</a>
            </div>
        </nav>
        <div class="container text-center">
            <div class="alert alert-success">
                <h4 class="alert-heading">Form Created Successfully!</h4>
                <p>The form has been successfully created. You can now manage it.</p>
                <hr>
                <p class="mb-0"><a href="admin.php" class="btn btn-primary">Go to Admin Dashboard</a></p>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        .container { max-width: 800px; margin-top: 20px; }
        .form-control { margin-bottom: 15px; }
        .navbar { background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
        .navbar a { color: #495057; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">Create Form</a>
            <div class="ml-auto">
                <a href="admin.php" class="btn btn-primary text-white">Admin Dashboard</a>
                <a href="logout.php" class="btn btn-danger text-white ml-2">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Create a New Form</h2>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="form_title">Form Title</label>
                <input type="text" name="form_title" class="form-control" required>
            </div>

            <div id="form-fields-container">
                <div class="form-group">
                    <label for="field_name">Field Name</label>
                    <input type="text" name="fields[0][name]" class="form-control" required>
                    
                    <label for="field_type">Field Type</label>
                    <select name="fields[0][type]" class="form-control" required>
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                    </select>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fields[0][required]" id="required_0">
                        <label class="form-check-label" for="required_0">Required</label>
                    </div>
                </div>
            </div>

            <button type="button" id="add-field" class="btn btn-secondary btn-block">Add Another Field</button>
            <button type="submit" class="btn btn-primary btn-block">Create Form</button>
        </form>

        <div class="text-center mt-3">
            <a href="admin.php" class="btn btn-secondary">Back to Admin Dashboard</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        let fieldCount = 1;

        $('#add-field').click(function() {
            $('#form-fields-container').append(`
                <div class="form-group">
                    <label for="field_name">Field Name</label>
                    <input type="text" name="fields[${fieldCount}][name]" class="form-control" required>
                    
                    <label for="field_type">Field Type</label>
                    <select name="fields[${fieldCount}][type]" class="form-control" required>
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                    </select>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fields[${fieldCount}][required]" id="required_${fieldCount}">
                        <label class="form-check-label" for="required_${fieldCount}">Required</label>
                    </div>
                </div>
            `);
            fieldCount++;
        });
    </script>
</body>
</html>
