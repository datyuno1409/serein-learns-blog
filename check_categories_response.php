<?php
// Check what's actually being returned from /admin/categories
$adminCategoriesUrl = 'http://localhost:8000/admin/categories';

// Initialize cURL session
$ch = curl_init();

// Enable cookie jar to maintain session
$cookieJar = tempnam(sys_get_temp_dir(), 'cookie');

// First login
$loginUrl = 'http://localhost:8000/login';
$postData = [
    'username' => 'admin',
    'password' => 'admin123'
];

curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects automatically
curl_setopt($ch, CURLOPT_HEADER, true);

echo "Logging in...\n";
$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Login HTTP code: $httpCode\n";

// Check for redirect location
if (preg_match('/Location: (.+)/', $loginResponse, $matches)) {
    echo "Login redirects to: " . trim($matches[1]) . "\n";
}

// Now access categories page
curl_setopt($ch, CURLOPT_URL, $adminCategoriesUrl);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);

echo "\nAccessing /admin/categories...\n";
$categoriesResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Categories HTTP code: $httpCode\n";

// Check for redirect
if (preg_match('/Location: (.+)/', $categoriesResponse, $matches)) {
    echo "Categories page redirects to: " . trim($matches[1]) . "\n";
    
    // Follow the redirect manually
    $redirectUrl = trim($matches[1]);
    if (strpos($redirectUrl, 'http') !== 0) {
        $redirectUrl = 'http://localhost:8000' . $redirectUrl;
    }
    
    curl_setopt($ch, CURLOPT_URL, $redirectUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    echo "Following redirect to: $redirectUrl\n";
    $finalResponse = curl_exec($ch);
    
    echo "Final response analysis:\n";
    echo "- Contains 'Categories': " . (strpos($finalResponse, 'Categories') !== false ? 'YES' : 'NO') . "\n";
    echo "- Contains 'Dashboard': " . (strpos($finalResponse, 'Dashboard') !== false ? 'YES' : 'NO') . "\n";
    echo "- Response length: " . strlen($finalResponse) . " characters\n";
} else {
    // No redirect, check the response directly
    $body = substr($categoriesResponse, strpos($categoriesResponse, "\r\n\r\n") + 4);
    echo "Direct response analysis:\n";
    echo "- Contains 'Categories': " . (strpos($body, 'Categories') !== false ? 'YES' : 'NO') . "\n";
    echo "- Contains 'Dashboard': " . (strpos($body, 'Dashboard') !== false ? 'YES' : 'NO') . "\n";
    echo "- Response length: " . strlen($body) . " characters\n";
    
    echo "\nFirst 1000 characters:\n";
    echo substr($body, 0, 1000) . "\n";
}

curl_close($ch);
unlink($cookieJar);
?>