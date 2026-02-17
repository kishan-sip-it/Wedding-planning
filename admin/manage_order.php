<?php include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'admin') exit('Access denied');
include '../config/db.php';

$stmt = $pdo->query("
    SELECT o.order_id, o.total_amount, o.status, o.created_at, 
           u.name as customer_name, o.event_type
    FROM orders o
    JOIN users u ON o.customer_id = u.user_id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-purple" style="background-color: #6A1B9A;">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand">← Dashboard</a>
        <span class="text-white">Admin | <a href="../logout.php" class="text-white">Logout</a></span>
    </div>
</nav>

<div class="container mt-4">
    <h2>Orders</h2>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Customer</th><th>Event</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['event_type']) ?></td>
                <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                <td><span class="badge bg-<?= $order['status']=='confirmed'?'success':'warning' ?>"><?= $order['status'] ?></span></td>
                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>