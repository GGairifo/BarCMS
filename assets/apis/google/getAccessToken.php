<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../../vendor/autoload.php';

use Google\Client;

function getAccessToken() {
    $client = new Client();
    $client->setAuthConfig('googlecredentials.json');

    // Load the refresh token from the secure location
    $tokenPath = 'token.json';
    if (!file_exists($tokenPath)) {
        http_response_code(500);
        echo json_encode(array("error" => "Refresh token not found."));
        exit;
    }

    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken();
        $newAccessToken = $client->getAccessToken();
        // Save the new access token including the refresh token back to the secure location
        file_put_contents($tokenPath, json_encode($newAccessToken));
    }

    // Return the access token as JSON
    $accessTokenString = $client->getAccessToken()['access_token'];
    return json_encode(array("access_token" => $accessTokenString));
}

// Set the response header to indicate JSON content type
header('Content-Type: application/json');

// Call the function and echo the JSON result
echo getAccessToken();

?>
