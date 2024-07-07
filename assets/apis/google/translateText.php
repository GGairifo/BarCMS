<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

function translateText($text, $targetLanguage, $accessToken) {
    $url = "https://translation.googleapis.com/language/translate/v2";

    $data = array(
        'q' => $text,
        'target' => $targetLanguage,
        'format' => 'text',
    );
    $data_string = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ));
    
    $response = curl_exec($ch);
    if ($response === false) {
        // cURL error occurred
        echo json_encode(array("error" => "cURL error occurred: " . curl_error($ch)));
        curl_close($ch);
        return;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        // HTTP error occurred
        echo json_encode(array(
            "error" => "HTTP error occurred: " . $httpCode,
            "response" => $response,
            "request" => $data_string,
            "url" => $url,
            "headers" => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            )
        ));
        curl_close($ch);
        return;
    }

    curl_close($ch);

    $responseDecoded = json_decode($response, true);

    if (isset($responseDecoded['data']['translations'][0]['translatedText'])) {
        $translatedText = $responseDecoded['data']['translations'][0]['translatedText'];
        return json_encode(array("translatedText" => $translatedText));
    } else {
        // Translation not found in response
        return json_encode(array("error" => "Translation not found in response"));
    }
}

// Call the function
if ((isset($_POST['text']) && isset($_POST['targetLanguage']) && isset($_POST['access_token'])) ||
    (isset($_GET['text']) && isset($_GET['targetLanguage']) && isset($_GET['access_token']))) {

    $text = isset($_POST['text']) ? $_POST['text'] : $_GET['text'];
    $targetLanguage = isset($_POST['targetLanguage']) ? $_POST['targetLanguage'] : $_GET['targetLanguage'];
    $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : $_GET['access_token'];
    
    $translatedText = translateText($text, $targetLanguage, $accessToken);
    echo $translatedText;
} else {
    echo json_encode(array("error" => "Missing required parameters"));
}
?>
