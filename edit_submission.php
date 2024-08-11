<?php
require 'db.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form update
    if (!isset($_POST['submission_id']) || empty($_POST['submission_id'])) {
        echo "<div class='alert alert-danger'>Error: submission_id is missing or invalid.</div>";
        exit;
    }

    $submission_id = $_POST['submission_id'];
    $form_id = $_POST['form_id'];

    // Fetch existing submission data
    $stmt = $pdo->prepare("SELECT data FROM submissions WHERE id = ? AND user_id = ?");
    $stmt->execute([$submission_id, $_SESSION['user_id']]);
    $submission = $stmt->fetch();

    if (!$submission) {
        echo "<div class='alert alert-danger'>Error: Submission not found.</div>";
        exit;
    }

    // Unserialize existing data
    $existing_data = unserialize($submission['data']);
    $form_fields = $existing_data; // Default to existing data

    // Handle file uploads
    $uploaded_images = [];
    foreach ($_FILES as $key => $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $timestamp = microtime(true);
            $file_name = 'file_' . str_replace('.', '', $timestamp) . '.' . $file_ext;
            $upload_file = $upload_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $upload_file)) {
                $uploaded_images[$key] = $file_name;
            } else {
                echo "<div class='alert alert-danger'>Error uploading image: " . htmlspecialchars($file['name']) . "</div>";
                exit;
            }
        }
    }

    // Update form fields with new data
    foreach ($_POST as $key => $value) {
        if ($key !== 'submission_id' && $key !== 'form_id') {
            $form_fields[$key] = $value;
        }
    }

    // Replace existing image data with newly uploaded images
    foreach ($uploaded_images as $key => $image_name) {
        $form_fields[$key] = $image_name;
    }

    // Serialize the updated form fields
    $form_fields_serialized = serialize($form_fields);

    // Update the submission data in the database
    $stmt = $pdo->prepare("UPDATE submissions SET data = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$form_fields_serialized, $submission_id, $_SESSION['user_id']]);

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Submission Updated</title>
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
                <a class="navbar-brand" href="#">Submission Updated</a>
            </div>
        </nav>
        <div class="container text-center">
            <div class="alert alert-success">
                <h4 class="alert-heading">Submission Updated Successfully!</h4>
                <p>Your submission has been successfully updated. Thank you!</p>
                <hr>
                <p class="mb-0"><a href="user.php" class="btn btn-primary">Go to Dashboard</a></p>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';
    exit;
}

// Check if submission_id is provided
if (!isset($_GET['submission_id']) || empty($_GET['submission_id'])) {
    echo '<div class="alert alert-danger">Error: submission_id is missing or invalid.</div>';
    exit;
}

$submission_id = $_GET['submission_id'];

// Fetch the submission data
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = ? AND user_id = ?");
$stmt->execute([$submission_id, $_SESSION['user_id']]);
$submission = $stmt->fetch();

if (!$submission) {
    echo '<div class="alert alert-danger">Error: Submission not found.</div>';
    exit;
}

// Fetch the form fields
$form_id = $submission['form_id'];
$stmt = $pdo->prepare("SELECT * FROM form_fields WHERE form_id = ?");
$stmt->execute([$form_id]);
$form_fields = $stmt->fetchAll();

// Unserialize the submission data
$data = unserialize($submission['data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Submission</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin-top: 20px;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .form-group img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
        .navbar {
            background-color: #e9ecef;
            border-bottom: 1px solid #dee2e6;
        }
        .navbar a {
            color: #495057;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">Edit Submission</a>
            <div class="ml-auto">
                <a href="index.php" class="btn btn-primary text-white">Home</a>
                <a href="logout.php" class="btn btn-danger text-white ml-2">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Edit Submission</h2>
        <p class="text-center">Submission ID: <?= htmlspecialchars($submission['id']) ?></p>

        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="submission_id" value="<?= htmlspecialchars($submission['id']) ?>">
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($form_id) ?>">

            <?php foreach ($form_fields as $field): ?>
                <div class="form-group">
                    <label><?= htmlspecialchars($field['field_name']) ?></label>

                    <?php if ($field['field_type'] === 'text'): ?>
                        <input type="text" name="<?= htmlspecialchars($field['field_name']) ?>" 
                               class="form-control" 
                               value="<?= htmlspecialchars($data[$field['field_name']] ?? '') ?>" 
                               <?= $field['is_required'] ? 'required' : '' ?>>

                    <?php elseif ($field['field_type'] === 'image'): ?>
                        <?php if (isset($data[$field['field_name']]) && !empty($data[$field['field_name']])): ?>
                            <img src="<?= htmlspecialchars($data[$field['field_name']]) ?>" alt="Current Image">
                        <?php endif; ?>
                        <input type="file" name="<?= htmlspecialchars($field['field_name']) ?>" 
                               class="form-control-file" 
                               <?= $field['is_required'] ? 'required' : '' ?>>
                    
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-block">Update Submission</button>
        </form>

        <div class="text-center mt-3">
            <a href="user.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
