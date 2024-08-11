<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['form_id']) || empty($_GET['form_id'])) {
    echo "Error: form_id is missing or invalid.";
    exit;
}

$form_id = $_GET['form_id'];

// Initialize search query
$search_query = '';
$search_field = 'username';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
}

if (isset($_GET['field']) && !empty($_GET['field'])) {
    $search_field = $_GET['field'];
}

// Fetch submissions for the given form ID along with the username
$sql = "
    SELECT submissions.*, users.username 
    FROM submissions 
    JOIN users ON submissions.user_id = users.id 
    WHERE form_id = ?
";

// Add search condition if search query is provided
if ($search_query) {
    $sql .= " AND $search_field LIKE ?";
}

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
if ($search_query) {
    $stmt->execute([$form_id, '%' . $search_query . '%']);
} else {
    $stmt->execute([$form_id]);
}
$submissions = $stmt->fetchAll();

// Fetch the form fields to use as table headers
$stmt = $pdo->prepare("SELECT field_name FROM form_fields WHERE form_id = ?");
$stmt->execute([$form_id]);
$form_fields = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "<h2>Submissions for Form ID $form_id</h2>";

// Search form
echo '<form method="get" action="">
    <input type="hidden" name="form_id" value="' . htmlspecialchars($form_id) . '">
    <input type="text" name="search" placeholder="Search" value="' . htmlspecialchars($search_query) . '">
    <select name="field">
        <option value="username"' . ($search_field === 'username' ? ' selected' : '') . '>Username</option>
        <option value="submitted_at"' . ($search_field === 'submitted_at' ? ' selected' : '') . '>Submitted At</option>
        <option value="id"' . ($search_field === 'id' ? ' selected' : '') . '>Submission ID</option>
        <!-- Add options for other form fields dynamically if needed -->
    </select>
    <button type="submit">Search</button>
</form>';

if (count($submissions) > 0) {
    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<thead><tr><th>Submission ID</th><th>Username</th><th>Submitted At</th>";

    // Print table headers for form fields
    foreach ($form_fields as $field_name) {
        echo "<th>" . htmlspecialchars($field_name) . "</th>";
    }

    echo "</tr></thead>";
    echo "<tbody>";

    // Print each submission's data
    foreach ($submissions as $submission) {
        $submission_data = unserialize($submission['data']);

        echo "<tr>";
        echo "<td>" . htmlspecialchars($submission['id']) . "</td>";
        echo "<td>" . htmlspecialchars($submission['username']) . "</td>";
        echo "<td>" . htmlspecialchars($submission['submitted_at']) . "</td>";

        foreach ($form_fields as $field_name) {
            echo "<td>" . htmlspecialchars($submission_data[$field_name] ?? 'N/A') . "</td>";
        }

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No submissions found for this form.</p>";
}
?>
