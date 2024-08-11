<?php
require 'db.php';
session_start();

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit;
}

// Check if submission_id and form_id are provided
if (!isset($_POST['submission_id'], $_POST['form_id']) || empty($_POST['submission_id']) || empty($_POST['form_id'])) {
    echo "Error: submission_id or form_id is missing or invalid.";
    exit;
}

$submission_id = $_POST['submission_id'];
$form_id = $_POST['form_id'];

// Check if the submission exists and belongs to the current user
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = ? AND user_id = ?");
$stmt->execute([$submission_id, $_SESSION['user_id']]);
$submission = $stmt->fetch();

if (!$submission) {
    echo "Error: Submission not found or you do not have permission to edit it.";
    exit;
}

// Fetch the form fields
$stmt = $pdo->prepare("SELECT * FROM form_fields WHERE form_id = ?");
$stmt->execute([$form_id]);
$form_fields = $stmt->fetchAll();

// Initialize the data array
$data = [];

// Process form fields
foreach ($form_fields as $field) {
    $field_name = $field['field_name'];

    if ($field['field_type'] === 'text') {
        $data[$field_name] = $_POST[$field_name] ?? null;
    } elseif ($field['field_type'] === 'image') {
        if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $filename = basename($_FILES[$field_name]['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_file)) {
                $data[$field_name] = $target_file;
            } else {
                echo "Error: Failed to upload the image.";
                exit;
            }
        } else {
            // Keep existing image if no new file is uploaded
            if (isset($submission['data'][$field_name])) {
                $data[$field_name] = $submission['data'][$field_name];
            } else {
                $data[$field_name] = null;
            }
        }
    }
}

// Serialize the data array before storing it
$stmt = $pdo->prepare("UPDATE submissions SET data = ? WHERE id = ? AND user_id = ?");
$stmt->execute([serialize($data), $submission_id, $_SESSION['user_id']]);

// Display success message and countdown
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <script type="text/javascript">
        let countdown = 3;
        function updateCountdown() {
            document.getElementById('countdown').innerText = countdown;
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
    <p>Submission updated successfully! Redirecting to the homepage in <span id="countdown">3</span> seconds...</p>
</body>
</html>
