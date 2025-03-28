<?php
// Get the image URL from the query parameter
$imageUrl = isset($_GET['url']) ? $_GET['url'] : '';

if (empty($imageUrl)) {
    header('HTTP/1.0 400 Bad Request');
    exit('No URL provided');
}

// Fetch the image
$imageData = file_get_contents($imageUrl);

if ($imageData === false) {
    header('HTTP/1.0 404 Not Found');
    exit('Failed to fetch image');
}

// Get the content type from the response headers
$headers = get_headers($imageUrl);
foreach ($headers as $header) {
    if (preg_match('/^Content-Type:\s*(image\/\w+)/i', $header, $matches)) {
        header('Content-Type: ' . $matches[1]);
        break;
    }
}

// Output the image data
echo $imageData;
?> 