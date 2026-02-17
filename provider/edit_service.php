<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'provider') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

// Get service ID from URL
$service_id = $_GET['id'] ?? null;
if (!$service_id) {
    die("Service ID required.");
}

// Fetch service (ensure it belongs to current provider)
$stmt = $pdo->prepare("
    SELECT * FROM services 
    WHERE service_id = ? AND provider_id = ?
");
$stmt->execute([$service_id, $_SESSION['user_id']]);
$service = $stmt->fetch();

if (!$service) {
    die("Service not found or access denied.");
}

// Handle form submission
$msg = '';
if ($_POST) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $tier = $_POST['package_tier'];

    // Update without image first
    $image_url = $service['image_url']; // keep existing

    if (!empty($_FILES['image']['name'])) {
        // === CLOUDINARY UPLOAD (same as add_service.php) ===
        function cloudinaryUpload($fileTmp, $fileName) {
            $cloudName = 'dmqbtapai';
            $apiKey = '414692264414951';
            $apiSecret = '6iwfD8l3pDenDA8tnhKfi25Xs_M';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloudName/image/upload");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'file' => new CURLFile($fileTmp, 'image/' . pathinfo($fileName, PATHINFO_EXTENSION), $fileName),
                'upload_preset' => 'samaaroh'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            return $data['secure_url'] ?? null;
        }

        $newImageUrl = cloudinaryUpload($_FILES['image']['tmp_name'], $_FILES['image']['name']);
        if ($newImageUrl) {
            $image_url = $newImageUrl;
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE services 
            SET title = ?, description = ?, price = ?, category = ?, package_tier = ?, image_url = ?
            WHERE service_id = ? AND provider_id = ?
        ");
        $stmt->execute([$title, $desc, $price, $category, $tier, $image_url, $service_id, $_SESSION['user_id']]);
        $msg = "✅ Service updated successfully!";
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();
    } catch (Exception $e) {
        $msg = "❌ Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service | Samaaroh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Edit Service</h2>

        <?php if ($msg): ?>
            <div class="alert <?= strpos($msg, '✅') !== false ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Service Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($service['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($service['description']) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $service['price'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="decor" <?= $service['category']=='decor'?'selected':'' ?>>Decoration</option>
                        <option value="catering" <?= $service['category']=='catering'?'selected':'' ?>>Catering</option>
                        <option value="photography" <?= $service['category']=='photography'?'selected':'' ?>>Photography</option>
                        <option value="entertainment" <?= $service['category']=='entertainment'?'selected':'' ?>>Entertainment</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label>Package Tier</label>
                <select name="package_tier" class="form-control" required>
                    <option value="standard" <?= $service['package_tier']=='standard'?'selected':'' ?>>Standard Celebration (₹5–15 lakhs)</option>
                    <option value="premium" <?= $service['package_tier']=='premium'?'selected':'' ?>>Premium Experience (₹15–30 lakhs)</option>
                    <option value="luxury" <?= $service['package_tier']=='luxury'?'selected':'' ?>>Luxury Affair (₹30+ lakhs)</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Current Image</label><br>
                <?php if (!empty($service['image_url'])): ?>
                    <img src="<?= htmlspecialchars($service['image_url']) ?>" style="max-height: 150px; border: 1px solid #ddd;">
                <?php else: ?>
                    <span class="text-muted">No image uploaded</span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label>Replace Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="form-control">
                <div class="form-text">Leave blank to keep current image.</div>
            </div>

            <button type="submit" class="btn btn-primary">Update Service</button>
            <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>