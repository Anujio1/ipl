<?php
session_start();

// Hardcoded admin credentials (replace with secure storage in production)
$admin_username = 'fn';
$admin_password = 'fn';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fnflix Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        /* Login Form Styles */
        .login-container {
            background-color: rgba(27, 21, 21, 0.49);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #fdfdfd;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: calc(100% - 20px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 0 auto;
        }

        .login-button {
            padding: 10px 20px;
            background-color: #0088cc;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            background-color: #006699;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .error {
            color: #e74c3c;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="login-button">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
    </div>
</body>

</html>
