<?php
session_start(); // Start the session

// Default language code
$lang_code = isset($_SESSION['lang_code']) ? $_SESSION['lang_code'] : "pt";

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include language files based on selected language
if ($lang_code == "pt") {
    require_once("../lang/lang_pt.php");
} elseif ($lang_code == "fr") {
    require_once("../lang/lang_fr.php");
} else {
    require_once("../lang/lang_en.php");
}

// Handle language switching
if (isset($_GET['lang']) && ($_GET['lang'] == 'pt' || $_GET['lang'] == 'en' || $_GET['lang'] == 'fr')) {
    $_SESSION['lang_code'] = $_GET['lang']; // Set the session variable
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <header>
        <div class="logo-title">
            <img src="../assets/images/logo.png" alt="Logo">
            <h1><?php echo $lang['title']; ?></h1>
        </div>
        
        <div class="welcome-message">
            <p><?php echo $lang['welcome_message']; ?></p>
        </div>
        
        <nav>
            <ul>
                <li>
                    <div class="lang-menu">
                        <a href="?lang=pt" class="<?php echo $lang_code == 'pt' ? 'active' : ''; ?>">PT</a> | 
                        <a href="?lang=en" class="<?php echo $lang_code == 'en' ? 'active' : ''; ?>">ENG</a> |
                        <a href="?lang=fr" class="<?php echo $lang_code == 'fr' ? 'active' : ''; ?>">FR</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
