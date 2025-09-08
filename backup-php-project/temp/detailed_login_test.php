<?php
// Detailed test login functionality
$loginUrl = 'http://localhost:8000/login';
$adminCategoriesUrl = 'http://localhost:8000/admin/categories';

// Initialize cURL session for login
$ch = curl_init();

// Enable cookie jar to maintain session
$cookieJar = tempnam(sys_get_temp_dir(), 'cookie');

// First, get the login page to establish session
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

echo "Getting login page...\n";
$loginPageResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Login page HTTP code: $httpCode\n";

// Now submit login form
$postData = [
    'username' => 'admin',
    'password' => 'admin123'
];

curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

echo "Submitting login form...\n";
$loginResult = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Login HTTP code: $httpCode\n";

// Check if login was successful by looking for redirect or success indicators
if (strpos($loginResult, 'Location:') !== false) {
    echo "Login appears successful (redirect detected)\n";
} else {
    echo "Login response (first 500 chars):\n";
    echo substr($loginResult, 0, 500) . "\n";
}

// Now try to access admin categories
curl_setopt($ch, CURLOPT_URL, $adminCategoriesUrl);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HEADER, false);

echo "\nAccessing /admin/categories...\n";
$categoriesPage = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Categories page HTTP code: $httpCode\n";

// Check response content
if (strpos($categoriesPage, 'Categories') !== false && strpos($categoriesPage, 'Add Category') !== false) {
    echo "SUCCESS: /admin/categories is accessible! Found Categories page content.\n";
} else if (strpos($categoriesPage, 'username') !== false && strpos($categoriesPage, 'password') !== false) {
    echo "FAILED: Still redirected to login page\n";
} else {
    echo "Response analysis:\n";
    echo "- Contains 'Categories': " . (strpos($categoriesPage, 'Categories') !== false ? 'YES' : 'NO') . "\n";
    echo "- Contains 'username': " . (strpos($categoriesPage, 'username') !== false ? 'YES' : 'NO') . "\n";
    echo "- Contains 'Dashboard': " . (strpos($categoriesPage, 'Dashboard') !== false ? 'YES' : 'NO') . "\n";
    echo "- Contains 'Admin': " . (strpos($categoriesPage, 'Admin') !== false ? 'YES' : 'NO') . "\n";
    echo "- Response length: " . strlen($categoriesPage) . " characters\n";
    
    // Show first 1000 characters of response
    echo "\nFirst 1000 characters of response:\n";
    echo substr($categoriesPage, 0, 1000) . "\n";
}

curl_close($ch);
unlink($cookieJar);
?>