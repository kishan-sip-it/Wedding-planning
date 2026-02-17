<?php
// Minimal, no constants required — uses relative paths
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-bg">
    <div class="container">
        <a class="navbar-brand" href="../customer/dashboard.php">Samaaroh</a>

        <div class="d-flex align-items-center gap-2">
            <div class="avatar" style="width:36px;height:36px;border-radius:50%;background:#e0e0e0;display:flex;align-items:center;justify-content:center;font-weight:600;color:#6A1B9A;font-size:0.85rem;">
                <?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?>
            </div>
            <span class="text-white d-none d-md-inline">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>

            <div class="dropdown">
                <a class="btn btn-link text-white text-decoration-none dropdown-toggle p-0" 
                   href="#" role="button" data-bs-toggle="dropdown">
                    ⋮
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($_SESSION['role'] === 'customer'): ?>
                        <li><a class="dropdown-item" href="../customer/settings.php">⚙️ Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../customer/settings.php#become-provider">🚀 Become Provider</a></li>
                    <?php elseif ($_SESSION['role'] === 'provider'): ?>
                        <li><a class="dropdown-item" href="../provider/dashboard.php">🏠 Dashboard</a></li>
                        <li><a class="dropdown-item" href="../provider/add_service.php">➕ Add Service</a></li>
                    <?php elseif ($_SESSION['role'] === 'admin'): ?>
                        <li><a class="dropdown-item" href="../admin/dashboard.php">📊 Admin</a></li>
                        <li><a class="dropdown-item" href="../admin/add_admin.php">➕ Add Admin</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>