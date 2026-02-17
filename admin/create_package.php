<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}
include '../config/db.php';

// Fetch all services for selection
$stmt = $pdo->query("
    SELECT s.service_id, s.title, s.price, s.category, u.name as provider_name
    FROM services s
    JOIN users u ON s.provider_id = u.user_id
    ORDER BY s.category, s.title
");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = '';
if ($_POST) {
    $name = trim($_POST['name']);
    $tier = $_POST['tier'];
    $description = trim($_POST['description']);
    $selected_services = $_POST['services'] ?? [];

    if (empty($name) || empty($selected_services)) {
        $msg = "Package name and at least one service are required.";
    } else {
        // Calculate total price
        $total_price = 0;
        $service_ids = [];
        foreach ($selected_services as $service_id) {
            foreach ($services as $service) {
                if ($service['service_id'] == $service_id) {
                    $total_price += $service['price'];
                    $service_ids[] = $service_id;
                    break;
                }
            }
        }

        try {
            $pdo->beginTransaction();
            
            // Insert package
            $stmt = $pdo->prepare("INSERT INTO packages (name, tier, total_price, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $tier, $total_price, $description]);
            $package_id = $pdo->lastInsertId();

            // Link services
            foreach ($service_ids as $sid) {
                $stmt = $pdo->prepare("INSERT INTO package_services (package_id, service_id) VALUES (?, ?)");
                $stmt->execute([$package_id, $sid]);
            }

            $pdo->commit();
            $msg = "Package created successfully!";
        } catch (Exception $e) {
            $pdo->rollback();
            $msg = "Error creating package.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Package</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Create New Package</h2>
        
        <?php if ($msg): ?>
            <div class="alert <?= strpos($msg, 'successfully') !== false ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="package-form">
            <div class="mb-3">
                <label class="form-label">Package Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tier</label>
                <select name="tier" class="form-select" required>
                    <option value="standard">Standard Celebration (₹5–15 lakhs)</option>
                    <option value="premium">Premium Experience (₹15–30 lakhs)</option>
                    <option value="luxury">Luxury Affair (₹30+ lakhs)</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Select Services</label>
                <div id="services-container">
                    <!-- Services will be added here -->
                </div>
                <button type="button" class="btn btn-outline-primary mt-2" id="add-service-btn">
                    + Add Service
                </button>
            </div>
            
            <div class="mb-3">
                <strong>Total Price:</strong> ₹<span id="total-price">0.00</span>
                <input type="hidden" name="total_price" id="total-price-input">
            </div>
            
            <button type="submit" class="btn btn-success">Create Package</button>
            <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>

    <!-- Service template (hidden) -->
    <template id="service-template">
        <div class="service-item mb-2 p-2 border rounded">
            <div class="d-flex align-items-center">
                <select name="services[]" class="form-select service-select me-2" required>
                    <option value="">-- Select Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['service_id'] ?>" data-price="<?= $service['price'] ?>">
                            <?= htmlspecialchars("{$service['title']} (by {$service['provider_name']}) - ₹" . number_format($service['price'], 2)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-sm btn-outline-danger remove-service">✕</button>
            </div>
        </div>
    </template>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/script.js"></script>
</body>
</html>