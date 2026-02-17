<?php
function cloudinaryUpload($fileTmp, $fileName, $folder = 'samaaroh') {
    // Load Cloudinary URL from env
    $cloudinaryUrl = getenv('CLOUDINARY_URL');
    if (!$cloudinaryUrl) {
        throw new Exception("CLOUDINARY_URL not set");
    }

    // Parse URL: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
    $parts = parse_url($cloudinaryUrl);
    if (!isset($parts['host']) || !isset($parts['user']) || !isset($parts['pass'])) {
        throw new Exception("Invalid CLOUDINARY_URL format");
    }

    $cloudName = $parts['host'];
    $apiKey = $parts['user'];
    $apiSecret = $parts['pass'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloudName/image/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'file' => new CURLFile($fileTmp, 'image/' . pathinfo($fileName, PATHINFO_EXTENSION), $fileName),
        'upload_preset' => 'samaaroh', // ← You MUST create this in Cloudinary!
        'folder' => $folder
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new Exception("cURL error: " . curl_error($ch));
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        throw new Exception("Cloudinary error: " . $data['error']['message']);
    }

    return $data['secure_url'] ?? null;
}