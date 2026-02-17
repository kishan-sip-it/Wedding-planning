<?php
session_start();
include 'config/db.php';

$error = '';
if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT user_id, name, role, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        session_regenerate_id(true);

        switch ($user['role']) {
            case 'admin': header("Location: admin/dashboard.php"); break;
            case 'provider': header("Location: provider/dashboard.php"); break;
            default: header("Location: customer/dashboard.php");include '../includes/navbar.php';
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Samaaroh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2 class="text-center">Login to Samaaroh</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="text-center mt-3">
                    Don’t have an account? <a href="register.php">Register</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>