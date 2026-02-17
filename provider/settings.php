<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'provider') exit('Access denied');
include '../config/db.php';
include '../config/constants.php';
include '../includes/navbar.php';

// Optional: Let provider downgrade to customer (rare, but safe)
if (isset($_POST['downgrade'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = 'customer' WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['role'] = 'customer';
    header("Location: ../customer/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="settings-menu" onclick="window.location='dashboard.php'">←</div>

    <nav class="navbar navbar-dark navbar-bg">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">← Back</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Provider Settings</h2>
        
        <div class="card p-4 mt-3">
            <h5>Account Role</h5>
            <p>You are currently a <strong>Service Provider</strong>.</p>
            <form method="POST">
                <button type="submit" name="downgrade" class="btn btn-outline-danger">
                    Downgrade to Customer
                </button>
            </form>
        </div>
    </div>
</body>
</html>