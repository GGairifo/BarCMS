<?php

$ch = curl_init('http://localhost:8000/translate.php');
$data = array(
    'text' => 'Hello, world!',
    'targetLanguage' => 'es',
    'access_token' => 'ya29.a0AXooCgsSoTPG9wKO4n0sTKZhXjRnTbLXBa5qS4D3fS7_Ft9-iSYsZROg63O3IUMaCbBC3pWZfl-8rhOQ8k7nI0TKlwV0v0Aobzkj6JCB3DPtCc5BGzwqeaHWvQKJ41VOa5OSH81KN4DrwQJkPGpZo1_rVSAZ4gt4mUd1aCgYKARESARMSFQHGX2MiEVOaRTl5CzAPIgdZEV8XVQ0171'
);
$data_string = json_encode($data);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
));

$response = curl_exec($ch);
if ($response === false) {
    // cURL error occurred
    echo "cURL error occurred: " . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        // HTTP error occurred
        echo "HTTP error occurred: " . $httpCode;
    } else {
        // Successful response
        echo $response;
    }
}

curl_close($ch);

?>
