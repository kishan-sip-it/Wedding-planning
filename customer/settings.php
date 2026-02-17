<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'customer') exit('Access denied');
include '../config/db.php';

if (isset($_POST['become_provider'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = 'provider' WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['role'] = 'provider';
    header("Location: ../provider/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <nav class="navbar navbar-dark navbar-bg">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">← Back</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Account Settings</h2>
        
        <div class="card p-4 mt-4">
            <h5>Become a Service Provider</h5>
            <p class="text-muted">Offer your services on Samaaroh and start receiving bookings!</p>
            <form method="POST">
                <button type="submit" name="become_provider" class="btn btn-outline-primary">
                    Apply Now
                </button>
            </form>
        </div>
    </div>
</body>
</html>