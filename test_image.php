<?php
include 'config/db.php';

// Get one service with an image
$stmt = $pdo->query("SELECT * FROM services WHERE image_url IS NOT NULL LIMIT 1");
$service = $stmt->fetch();

if (!$service) {
    die("No services with images found in database.");
}

echo "<h2>Database Image URL:</h2>";
echo "<p><strong>Raw value:</strong> " . htmlspecialchars($service['image_url']) . "</p>";
echo "<p><strong>Length:</strong> " . strlen($service['image_url']) . " characters</p>";
echo "<p><strong>Has spaces?</strong> " . (strpos($service['image_url'], ' ') !== false ? 'YES' : 'NO') . "</p>";

echo "<h2>Image Preview:</h2>";
echo "<p><strong>Using /samaaroh/ prefix:</strong></p>";
echo '<img src="/samaaroh/' . htmlspecialchars($service['image_url']) . '" style="max-width: 300px; border: 3px solid red;">';

echo "<h2>Direct Link Test:</h2>";
echo '<p><a href="/samaaroh/' . htmlspecialchars($service['image_url']) . '" target="_blank">Click to open image directly</a></p>';

echo "<h2>Browser Console:</h2>";
echo "<p>Open browser DevTools (F12) → Console tab → Look for red errors</p>";
?>