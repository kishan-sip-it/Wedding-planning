<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'provider') { 
    header("Location: ../login.php"); 
    exit(); 
}
include '../config/db.php';

$stmt = $pdo->prepare("
    SELECT service_id, title, price, category, package_tier, image_url, is_available
    FROM services 
    WHERE provider_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>My Services</h2>
        <a href="add_service.php" class="btn btn-success mb-3">+ Add New Service</a>
        
        <?php if (empty($services)): ?>
            <div class="alert alert-info">You haven't added any services yet.</div>
        <?php else: ?>
            <div class="row">
            <?php foreach ($services as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <?php if (!empty($service['image_url'])): ?>
                                <img src="/samaaroh/<?= htmlspecialchars(trim($service['image_url'])) ?>" 
                                     class="service-image" 
                                     alt="<?= htmlspecialchars($service['title']) ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                            <?php endif; ?>
                            <?php if (empty($service['image_url']) || !empty($service['image_url'])): ?>
                                <div class="service-image bg-light d-flex align-items-center justify-content-center" style="<?= !empty($service['image_url']) ? 'display:none;' : '' ?>">
                                    <span class="text-muted">No Image</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <span class="badge bg-<?= $service['package_tier']=='luxury'?'danger':($service['package_tier']=='premium'?'warning':'info') ?>">
                                <?= ucfirst($service['package_tier']) ?>
                            </span>
                            <h5 class="mt-2"><?= htmlspecialchars($service['title']) ?></h5>
                            <p class="text-muted"><?= ucfirst($service['category']) ?></p>
                            <h6 class="text-primary">₹<?= number_format($service['price'], 2) ?></h6>
                            <p>Status: <?= $service['is_available'] ? 'Available' : 'Unavailable' ?></p>
                            
                            <!-- EDIT BUTTON -->
                            <div class="mt-2">
                                <a href="edit_service.php?id=<?= $service['service_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    ✏️ Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>