<?php
// Using cURL to send a HEAD request
$ch = curl_init('https://www.youtube.com/');
curl_setopt($ch, CURLOPT_NOBODY, true);        // HEAD request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response      = curl_exec($ch);
$httpCode      = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
$contentType   = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Status: $httpCode\n";
echo "Content-Length: $contentLength bytes\n";
echo "Content-Type: $contentType\n";
?>
