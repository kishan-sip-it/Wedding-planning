<?php include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }
include '../config/db.php';
include '../config/constants.php';


$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$customers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'provider'");
$providers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> 

    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-bg-primary p-3 text-center">
                    <h5>Customers</h5>
                    <h2><?= $customers ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success p-3 text-center">
                    <h5>Providers</h5>
                    <h2><?= $providers ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-info p-3 text-center">
                    <h5>Total Orders</h5>
                    <h2><?= $total_orders ?></h2>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <a href="add_admin.php" class="btn btn-outline-primary">➕ Add New Admin</a>
        </div>
    </div>
</body>
</html>