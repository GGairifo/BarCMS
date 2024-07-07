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
        throw new Exception('Refresh token not found.');
    }

    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
    echo "Access Token: " . $accessToken['access_token'] . "<br>";

    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken();
        $newAccessToken = $client->getAccessToken();
        // Save the new access token including the refresh token back to the secure location
        file_put_contents($tokenPath, json_encode($newAccessToken));
    }

    return $client->getAccessToken();
}


echo getAccessToken();

?>
