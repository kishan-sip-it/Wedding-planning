<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'admin') exit('Access denied');
include '../config/db.php';
include '../config/constants.php';
include '../includes/navbar.php';
$msg = '';
if ($_POST) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
    if ($stmt->execute([$email])) {
        $msg = "✅ User promoted to admin!";
    } else {
        $msg = "❌ User not found. Make sure they are registered first.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <nav class="navbar navbar-dark navbar-bg">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">← Dashboard</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Add New Admin</h2>
        
        <?php if ($msg): ?>
            <div class="alert <?= strpos($msg, '✅') !== false ? 'alert-success' : 'alert-warning' ?> mt-3">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <p class="mt-3">Enter the email of an existing user to promote them to admin.</p>
        
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="User Email" required>
            </div>

            <button type="submit" class="btn btn-primary" disabled>
    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
    Promote to Admin
</button>
        </form>
    </div>
</body>
</html>