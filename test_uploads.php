<?php
// Test Cloudinary upload
$cloudName = 'dmqbtapai';
$apiKey = '414692264414951';
$apiSecret = '6iwfD8l3pDenDA8tnhKfi25Xs_M';

// Simulate file upload
$data = [
    'file' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Placeholder_Person.jpg/120px-Placeholder_Person.jpg',
    'upload_preset' => 'samaaroh'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloudName/image/upload");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo "<h2>Cloudinary Response:</h2>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$data = json_decode($response, true);
if (isset($data['secure_url'])) {
    echo "<h3>Image uploaded successfully!</h3>";
    echo '<img src="' . $data['secure_url'] . '" style="max-width:300px;">';
} else {
    echo "<h3 style='color:red'>Upload failed!</h3>";
}
?>