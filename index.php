    <?php
    require 'db.php';
    session_start();

    $error = ''; // Initialize the error variable

    if (isset($_SESSION['username'])) {
        // Redirect to admin panel if the user is already logged in
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin.php');
            exit;
        }
        // Redirect to user page if the user is already logged in
        else {
            header('Location: user.php');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Fetch user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];  // Store the user's ID in the session

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: user.php');
            }
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .login-container {
                max-width: 400px;
                width: 100%;
                padding: 20px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }
            .login-container h2 {
                margin-bottom: 20px;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-control {
                border-radius: 5px;
                border: 1px solid #ced4da;
            }
            .btn-primary {
                background-color: #007bff;
                border: none;
            }
            .btn-primary:hover {
                background-color: #0056b3;
            }
            .error-message {
                color: red;
            }
            .login-image {
                width: 100%;
                height: auto;
                display: block;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>

    <div class="login-container">
        <img src="images/login.webp" alt="Login Image" class="login-image">
        <h2 class="text-center"><b>Mac Manpower Login</b></h2>

        <form method="post" action="index.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <?php if ($error): ?>
            <p class="error-message text-center mt-3"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    </body>
    </html>
