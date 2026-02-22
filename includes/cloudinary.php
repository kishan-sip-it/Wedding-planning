    <?php
    function cloudinaryUpload($fileTmp, $fileName, $folder = 'samaaroh') {

    $cloudinaryUrl = getenv('CLOUDINARY_URL');
    if (!$cloudinaryUrl) {
        throw new Exception("CLOUDINARY_URL not set");
    }

    $parts = parse_url($cloudinaryUrl);
    if (!isset($parts['host'], $parts['user'], $parts['pass'])) {
        throw new Exception("Invalid CLOUDINARY_URL format");
    }

    $cloudName = $parts['host'];

    $ch = curl_init();
    if ($ch === false) {
        throw new Exception("Failed to initialize cURL");
    }

    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloudName/image/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'file' => new CURLFile($fileTmp, 'image/' . pathinfo($fileName, PATHINFO_EXTENSION), $fileName),
        'upload_preset' => 'samaaroh',
        'folder' => $folder
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL error: " . $error);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Cloudinary request failed with HTTP code " . $httpCode);
    }

    $data = json_decode($response, true);

    if (isset($data['error'])) {
        throw new Exception("Cloudinary error: " . $data['error']['message']);
    }

    return $data['secure_url'] ?? null;
}