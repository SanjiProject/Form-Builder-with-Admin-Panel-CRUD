<?php
require 'db.php'; // Adjust path as needed
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['form_id']) || empty($_GET['form_id'])) {
        echo "<div class='alert alert-danger'>Error: form_id is missing or invalid.</div>";
        exit;
    }

    $form_id = $_GET['form_id'];

    // Check if the user has already submitted this form
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE form_id = ? AND user_id = ?");
    $stmt->execute([$form_id, $_SESSION['user_id']]);
    $existing_submission = $stmt->fetch();

    if ($existing_submission) {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Form Submission Error</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f5f5f5; }
                .container { max-width: 600px; margin-top: 50px; }
                .alert { margin-bottom: 20px; }
                .navbar { background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
                .navbar a { color: #495057; }
            </style>
            <script type="text/javascript">
                let countdown = 5;
                function updateCountdown() {
                    document.getElementById("countdown").innerText = countdown;
                    countdown--;
                    if (countdown < 0) {
                        window.location.href = "index.php";
                    } else {
                        setTimeout(updateCountdown, 1000);
                    }
                }
                window.onload = updateCountdown;
            </script>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container">
                    <a class="navbar-brand" href="#">Form Submission Error</a>
                </div>
            </nav>
            <div class="container text-center">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">Form Already Submitted!</h4>
                    <p>You have already submitted this form. You will be redirected to the homepage shortly.</p>
                    <hr>
                    <p class="mb-0">Redirecting in <span id="countdown">5</span> seconds...</p>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
        exit;
    }

    // Fetch the form fields based on the form_id
    $stmt = $pdo->prepare("SELECT * FROM form_fields WHERE form_id = ?");
    $stmt->execute([$form_id]);
    $form_fields = $stmt->fetchAll();

    if (!$form_fields) {
        echo "<div class='alert alert-danger'>Error: No fields found for the given form.</div>";
        exit;
    }

    // Display the form
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Submit Form</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background-color: #f5f5f5; }
            .container { max-width: 800px; margin-top: 20px; }
            .form-control-file { margin-bottom: 15px; }
            .form-group img { max-width: 100%; height: auto; margin-bottom: 15px; }
            .navbar { background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
            .navbar a { color: #495057; }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="#">Submit Form</a>
                <div class="ml-auto">
                    <a href="index.php" class="btn btn-primary text-white">Home</a>
                    <a href="logout.php" class="btn btn-danger text-white ml-2">Logout</a>
                </div>
            </div>
        </nav>
        <div class="container">
            <h2 class="text-center">Submit Form</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="form_id" value="' . htmlspecialchars($form_id) . '">';
    
    foreach ($form_fields as $field) {
        echo '<div class="form-group">';
        echo '<label>' . htmlspecialchars($field['field_name']) . '</label>';
        
        if ($field['field_type'] === 'text') {
            echo '<input type="text" name="' . htmlspecialchars($field['field_name']) . '" class="form-control" ' . ($field['is_required'] ? 'required' : '') . '>';
        } elseif ($field['field_type'] === 'image') {
            echo '<input type="file" name="' . htmlspecialchars($field['field_name']) . '" class="form-control-file" ' . ($field['is_required'] ? 'required' : '') . '>';
        }
        
        echo '</div>';
    }
    
    echo '<button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
        <div class="text-center mt-3">
            <a href="user.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['form_id']) || empty($_POST['form_id'])) {
        echo "<div class='alert alert-danger'>Error: form_id is missing or invalid.</div>";
        exit;
    }

    $form_id = $_POST['form_id'];

    // Check if the user has already submitted this form
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE form_id = ? AND user_id = ?");
    $stmt->execute([$form_id, $_SESSION['user_id']]);
    $existing_submission = $stmt->fetch();

    if ($existing_submission) {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Form Submission Error</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f5f5f5; }
                .container { max-width: 600px; margin-top: 50px; }
                .alert { margin-bottom: 20px; }
                .navbar { background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
                .navbar a { color: #495057; }
            </style>
            <script type="text/javascript">
                let countdown = 5;
                function updateCountdown() {
                    document.getElementById("countdown").innerText = countdown;
                    countdown--;
                    if (countdown < 0) {
                        window.location.href = "index.php";
                    } else {
                        setTimeout(updateCountdown, 1000);
                    }
                }
                window.onload = updateCountdown;
            </script>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container">
                    <a class="navbar-brand" href="#">Form Submission Error</a>
                </div>
            </nav>
            <div class="container text-center">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">Form Already Submitted!</h4>
                    <p>You have already submitted this form. You will be redirected to the homepage shortly.</p>
                    <hr>
                    <p class="mb-0">Redirecting in <span id="countdown">5</span> seconds...</p>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
        exit;
    }

    // Handle file uploads
    $uploaded_images = [];
    foreach ($_FILES as $key => $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $timestamp = microtime(true); // Current Unix timestamp with microseconds
            $file_name = 'file_' . str_replace('.', '', $timestamp) . '.' . $file_ext; // Generate a unique file name
            $upload_file = $upload_dir . $file_name;

            // Move the uploaded file to the /uploads/ directory
            if (move_uploaded_file($file['tmp_name'], $upload_file)) {
                $uploaded_images[$key] = $file_name;
            } else {
                echo "<div class='alert alert-danger'>Error uploading image: " . htmlspecialchars($file['name']) . "</div>";
                exit;
            }
        }
    }

    // Prepare form fields for storage
    $form_fields = [];
    foreach ($_POST as $key => $value) {
        if ($key !== 'form_id') {
            $form_fields[$key] = $value;
        }
    }

    // Add image paths to the form fields
    foreach ($uploaded_images as $key => $image_name) {
        $form_fields[$key] = $image_name; // Store only the file name
    }

    // Serialize the form fields with the new image filenames
    $form_fields_serialized = serialize($form_fields);

    // Save the submission data to the database
    $stmt = $pdo->prepare("INSERT INTO submissions (form_id, user_id, data) VALUES (?, ?, ?)");
    $stmt->execute([$form_id, $_SESSION['user_id'], $form_fields_serialized]);

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form Submitted</title>
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
                <a class="navbar-brand" href="#">Form Submitted</a>
            </div>
        </nav>
        <div class="container text-center">
            <div class="alert alert-success">
                <h4 class="alert-heading">Form Submitted Successfully!</h4>
                <p>Your form has been successfully submitted. Thank you!</p>
                <hr>
                <p class="mb-0"><a href="index.php" class="btn btn-primary">Go to Homepage</a></p>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';
}
?>
