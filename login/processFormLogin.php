<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../Lib/lib.php");
require_once("../Lib/db.php");

session_start(); // Start the session at the beginning

$flags[] = FILTER_NULL_ON_FAILURE;

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW, $flags);

if ($method == 'POST') {
    $_INPUT_METHOD = INPUT_POST;
} elseif ($method == 'GET') {
    $_INPUT_METHOD = INPUT_GET;
} else {
    echo "Invalid HTTP method (" . $method . ")";
    exit();
}

$username = filter_input($_INPUT_METHOD, 'username', FILTER_UNSAFE_RAW, $flags);
$password = filter_input($_INPUT_METHOD, 'password', FILTER_UNSAFE_RAW, $flags);

$serverName = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_UNSAFE_RAW, $flags);
$serverPort = 80;
$name = webAppName();
$baseUrl = "http://" . $serverName . ":" . $serverPort;
$baseNextUrl = $baseUrl . $name;

$idUser = isValidTP($username, $password);

if ($idUser > 0) {
    $_SESSION['username'] = $username;
    $_SESSION['id'] = $idUser;

    if (isset($_SESSION['locationAfterAuth'])) {
        $baseNextUrl = $baseUrl;
        $nextUrl = $_SESSION['locationAfterAuth'];
    } else {
        //$nextUrl = "../main/FormMainPage.php";
        $nextUrl = "../index.php";
    }
} else {
    $nextUrl = "formLogin.php";
    $_SESSION['login_error'] = "Account needs activation or invalid credentials.";
}

header("Location: " . $baseNextUrl . $nextUrl);
exit();
?>
