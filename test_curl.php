<?php
if (function_exists('curl_version')) {
    echo "✅ cURL is enabled!";
    echo "<br>Version: " . curl_version()['version'];
} else {
    echo "❌ cURL is NOT enabled";
}
?>