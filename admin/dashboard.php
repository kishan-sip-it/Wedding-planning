<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}
include '../config/db.php';

// Fetch packages
$stmt = $pdo->query("
    SELECT p.*, 
           GROUP_CONCAT(
               CONCAT(s.title, '|', s.price, '|', u.name) 
               SEPARATOR '||'
           ) AS service_details
    FROM packages p
    LEFT JOIN package_services ps ON p.package_id = ps.package_id
    LEFT JOIN services s ON ps.service_id = s.service_id
    LEFT JOIN users u ON s.provider_id = u.user_id
    GROUP BY p.package_id
    ORDER BY 
        CASE p.tier 
            WHEN 'standard' THEN 1 
            WHEN 'premium' THEN 2 
            WHEN 'luxury' THEN 3 
        END
");
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard - Samaaroh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Pre-Built Wedding Packages</h2>
        
        <?php if (empty($packages)): ?>
            <div class="alert alert-info">No packages available yet.</div>
        <?php else: ?>
            <div class="row">
            <?php foreach ($packages as $pkg): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5><?= htmlspecialchars($pkg['name']) ?></h5>
                                <span class="badge bg-<?= $pkg['tier']=='luxury'?'danger':($pkg['tier']=='premium'?'warning':'info') ?>">
                                    <?= ucfirst($pkg['tier']) ?> Package
                                </span>
                            </div>
                            <p class="text-muted"><?= htmlspecialchars($pkg['description']) ?></p>
                            
                            <div class="mt-3">
                                <strong>Included Services:</strong>
                                <ul class="mt-2 mb-3">
                                <?php
                                if (!empty($pkg['service_details'])) {
                                    $serviceGroups = explode('||', $pkg['service_details']);
                                    $servicesList = [];
                                    foreach ($serviceGroups as $group) {
                                        $parts = explode('|', $group);
                                        if (count($parts) >= 3) {
                                            list($title, $price, $provider) = $parts;
                                            echo "<li>{$title} (by {$provider}) - ₹" . number_format($price, 2) . "</li>";
                                            $servicesList[] = ['title' => $title, 'price' => (float)$price, 'provider' => $provider];
                                        }
                                    }
                                } else {
                                    echo "<li>No services configured</li>";
                                }
                                ?>
                                </ul>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price fw-bold">Total: ₹<?= number_format($pkg['total_price'], 2) ?></div>
                                <button 
                                    class="btn btn-success"
                                    onclick="alert('Package selected! (Demo mode)')"
                                >
                                    Select Package
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>