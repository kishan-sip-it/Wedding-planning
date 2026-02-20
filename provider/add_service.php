<?php
include '../includes/auth_check.php';
if ($_SESSION['role'] !== 'provider') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';
include '../includes/cloudinary.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $tier = $_POST['package_tier'];
    $image_url = null;

    if (!empty($_FILES['image']['name'])) {
        $image_url = cloudinaryUpload(
            $_FILES['image']['tmp_name'],
            $_FILES['image']['name']
        );
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO services 
            (provider_id, title, description, price, category, package_tier, image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $desc,
            $price,
            $category,
            $tier,
            $image_url
        ]);

        $msg = "✅ Service added successfully!";

    } catch (Exception $e) {
        $msg = "❌ Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Service | Samaaroh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Add New Service</h2>

        <?php if ($msg): ?>
            <div class="alert <?= strpos($msg, '✅') !== false ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Service Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="decor">Decoration</option>
                        <option value="catering">Catering</option>
                        <option value="photography">Photography</option>
                        <option value="entertainment">Entertainment</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label>Package Tier</label>
                <select name="package_tier" class="form-control" required>
                    <option value="standard">Standard Celebration (₹5–15 lakhs)</option>
                    <option value="premium">Premium Experience (₹15–30 lakhs)</option>
                    <option value="luxury">Luxury Affair (₹30+ lakhs)</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Service Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="form-control">
                <div class="form-text">JPEG/PNG/GIF only. Max 5MB.</div>
            </div>

            <button type="submit" class="btn btn-success">Add Service</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>