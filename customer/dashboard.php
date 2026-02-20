    <?php
    include '../includes/auth_check.php';
    if ($_SESSION['role'] !== 'customer') { 
        header("Location: ../login.php"); 
        exit(); 
    }
    include '../config/db.php';

    $stmt = $pdo->query("
        SELECT 
            s.service_id, s.title, s.price, s.category, s.package_tier, s.image_url,
            u.name AS provider_name
        FROM services s
        JOIN users u ON s.provider_id = u.user_id
        WHERE s.is_available = 1
        ORDER BY s.package_tier, s.category, s.title
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customer Dashboard - Samaaroh</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../assets/style.css">
    </head>
    <body>
        <?php include '../includes/navbar.php'; ?>

        <div class="container mt-4">
            <h2 class="mb-4">Plan Your Perfect Event</h2>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn btn-outline-primary" data-mode="individual">
                    🛒 Build Your Own
                </button>
                <button class="btn btn-outline-success" data-mode="package">
                    🎁 Pre-Built Packages
                </button>
                <button id="clear-cart" class="btn btn-outline-danger ms-auto">Clear Selection</button>
            </div>

            <div class="alert alert-info mb-4" id="cart-summary">
                <strong>Loading cart...</strong>
            </div>

            <div id="individual-services">
                <?php if (!empty($services)): ?>
                    <section class="mb-5">
                        <h3 class="mb-4 pb-2 border-bottom">✨ Standard Celebration (₹5–15 lakhs)</h3>
                        <div class="row">
                        <?php foreach ($services as $service):
                            if ($service['package_tier'] !== 'standard') continue; ?>
                            <div class="col-md-4 mb-4">
                                <div class="service-card card h-100">
                                    <div class="position-relative">
                                        <?php if (!empty($service['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($service['image_url']) ?>">
 
                                                class="service-image" 
                                                alt="<?= htmlspecialchars($service['title']) ?>">
                                        <?php else: ?>
                                            <div class="service-image bg-light d-flex align-items-center justify-content-center">
                                                <span class="text-muted">No Image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="category-badge"><?= ucfirst($service['category']) ?></span>
                                        </div>
                                        <h5 class="fw-bold"><?= htmlspecialchars($service['title']) ?></h5>
                                        <p class="provider">by <?= htmlspecialchars($service['provider_name']) ?></p>
                                        <div class="mt-auto">
                                            <div class="price">₹<?= number_format($service['price'], 2) ?></div>
                                            <button 
                                                class="btn btn-add add-service w-100 mt-2"
                                                data-id="<?= (int)$service['service_id'] ?>"
                                                data-title="<?= htmlspecialchars($service['title']) ?>"
                                                data-price="<?= (float)$service['price'] ?>"
                                                data-provider="<?= htmlspecialchars($service['provider_name']) ?>"
                                            >
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="mb-5">
                        <h3 class="mb-4 pb-2 border-bottom">💎 Premium Experience (₹15–30 lakhs)</h3>
                        <div class="row">
                        <?php foreach ($services as $service):
                            if ($service['package_tier'] !== 'premium') continue; ?>
                            <div class="col-md-4 mb-4">
                                <div class="service-card card h-100">
                                    <div class="position-relative">
                                        <?php if (!empty($service['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($service['image_url']) ?>">
 
                                                class="service-image" 
                                                alt="<?= htmlspecialchars($service['title']) ?>">
                                        <?php else: ?>
                                            <div class="service-image bg-light d-flex align-items-center justify-content-center">
                                                <span class="text-muted">No Image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="category-badge"><?= ucfirst($service['category']) ?></span>
                                        </div>
                                        <h5 class="fw-bold"><?= htmlspecialchars($service['title']) ?></h5>
                                        <p class="provider">by <?= htmlspecialchars($service['provider_name']) ?></p>
                                        <div class="mt-auto">
                                            <div class="price">₹<?= number_format($service['price'], 2) ?></div>
                                            <button 
                                                class="btn btn-add add-service w-100 mt-2"
                                                data-id="<?= (int)$service['service_id'] ?>"
                                                data-title="<?= htmlspecialchars($service['title']) ?>"
                                                data-price="<?= (float)$service['price'] ?>"
                                                data-provider="<?= htmlspecialchars($service['provider_name']) ?>"
                                            >
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="mb-5">
                        <h3 class="mb-4 pb-2 border-bottom">👑 Luxury Affair (₹30+ lakhs)</h3>
                        <div class="row">
                        <?php foreach ($services as $service):
                            if ($service['package_tier'] !== 'luxury') continue; ?>
                            <div class="col-md-4 mb-4">
                                <div class="service-card card h-100">
                                    <div class="position-relative">
                                        <?php if (!empty($service['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($service['image_url']) ?>">
 
                                                class="service-image" 
                                                alt="<?= htmlspecialchars($service['title']) ?>">
                                        <?php else: ?>
                                            <div class="service-image bg-light d-flex align-items-center justify-content-center">
                                                <span class="text-muted">No Image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="category-badge"><?= ucfirst($service['category']) ?></span>
                                        </div>
                                        <h5 class="fw-bold"><?= htmlspecialchars($service['title']) ?></h5>
                                        <p class="provider">by <?= htmlspecialchars($service['provider_name']) ?></p>
                                        <div class="mt-auto">
                                            <div class="price">₹<?= number_format($service['price'], 2) ?></div>
                                            <button 
                                                class="btn btn-add add-service w-100 mt-2"
                                                data-id="<?= (int)$service['service_id'] ?>"
                                                data-title="<?= htmlspecialchars($service['title']) ?>"
                                                data-price="<?= (float)$service['price'] ?>"
                                                data-provider="<?= htmlspecialchars($service['provider_name']) ?>"
                                            >
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </section>
                <?php else: ?>
                    <div class="alert alert-warning">No services available yet.</div>
                <?php endif; ?>
            </div>

            <div id="package-section" class="d-none">
                <h3 class="mb-4">🎁 Pre-Built Wedding Packages</h3>
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
                                            $servicesList = [];
                                        }
                                        ?>
                                        </ul>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="price fw-bold">Total: ₹<?= number_format($pkg['total_price'], 2) ?></div>
                                        <button 
                                            class="btn btn-success add-package"
                                            data-package='<?= json_encode([
                                                'id' => $pkg['package_id'],
                                                'name' => $pkg['name'],
                                                'services' => $servicesList
                                            ]) ?>'
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

            <div class="card mt-4">
                <div class="card-header">
                    <strong>Your Selection</strong>
                </div>
                <div class="card-body" id="cart-items">
                    <p class="text-muted">Loading cart...</p>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/script.js"></script>
    </body>
    </html>