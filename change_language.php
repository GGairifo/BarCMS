<?php
session_start();

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang_code'] = $lang; // Update session variable with language code
}

$redirectUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php'; // Redirect to the previous page or default to index.php
header("Location: $redirectUrl");
exit();
?>
