<?php
include 'translateText.php'; // Include the file where translateText function is defined

$text = "Hello, world!";
$targetLanguage = "es";
$accessToken = "ya29.a0AXooCgsSoTPG9wKO4n0sTKZhXjRnTbLXBa5qS4D3fS7_Ft9-iSYsZROg63O3IUMaCbBC3pWZfl-8rhOQ8k7nI0TKlwV0v0Aobzkj6JCB3DPtCc5BGzwqeaHWvQKJ41VOa5OSH81KN4DrwQJkPGpZo1_rVSAZ4gt4mUd1aCgYKARESARMSFQHGX2MiEVOaRTl5CzAPIgdZEV8XVQ0171";

$result = translateText($text, $targetLanguage, $accessToken);

echo "Translation result:\n";
echo $result;
?>
