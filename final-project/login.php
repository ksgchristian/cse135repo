<?php
session_start();

$users = [
    "superadmin" => [
        "password" => "SuperAdmin123!",
        "role" => "super_admin"
    ],
    "analyst1" => [
        "password" => "Analyst123!",
        "role" => "analyst"
    ],
    "viewer1" => [
        "password" => "Viewer123!",
        "role" => "viewer"
    ],
    "grader" => [
        "password" => "grader",
        "role" => "super_admin"
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        isset($users[$username]) &&
        $users[$username]['password'] === $password
    ) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $users[$username]['role'];

        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        form {
            max-width: 300px;
        }
        input {
            display: block;
            width: 100%;
            margin-bottom: 12px;
            padding: 8px;
        }
        button {
            padding: 8px 16px;
        }
    </style>
</head>
<body>
    <h1>MVP Analytics Backend Login</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>
</html>
