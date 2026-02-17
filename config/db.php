<?php
// Cloudinary config
putenv('CLOUDINARY_URL=cloudinary://414692264414951:6iwfD8l3pDenDA8tnhKfi25Xs_M@dmqbtapai');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=samaaroh_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    die("Service unavailable. Please try again later.");
}
?>