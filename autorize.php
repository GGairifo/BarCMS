<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';

use Google\Client;

session_start();

$client = new Client();
$client->setAuthConfig('assets/apis/google/googlecredentials.json'); // Update this path
$client->setRedirectUri('http://localhost/examples-smi/Z/autorize.php'); // Update this URL if needed
$client->addScope('https://www.googleapis.com/auth/cloud-translation'); // Replace with the scope you need
$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    echo "Authorization URL: " . htmlspecialchars($authUrl) . "<br>";
    echo "<a href='" . htmlspecialchars($authUrl) . "'>Authorize</a>";
    // Uncomment the next line to debug
    // var_dump($client); exit();
} else {
    try {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
        // Save the refresh token to a secure location, e.g., a database or a secure file
        $tokenPath = 'assets/apis/google/token.json'; // Update this path
        if (file_exists($tokenPath)) {
            // Write to the file only if it exists
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            echo "Token file updated successfully.<br>";
        } else {
            throw new Exception('Token file does not exist.');
        }
        
        header('Location: ' . filter_var('http://localhost/examples-smi/Z/sucess.php', FILTER_SANITIZE_URL)); // Update this URL if needed
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        // Uncomment the next line to debug
        // var_dump($e); exit();
    }
}
?>