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

    // Get current URL parameters
    $query_params = $_GET;
    // Remove the 'lang' parameter as it's already processed
    unset($query_params['lang']);
    // Rebuild the query string
    $query_string = http_build_query($query_params);
    // Redirect to the same page with the original query parameters
    header("Location: {$_SERVER['PHP_SELF']}?" . $query_string);
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<header>
        <div class="logo-title" id="logo-title">
            <img src="../assets/images/logo.png" alt="Logo" id = "logo">
            <h1><?php echo $lang['title']; ?></h1>
        </div>
        
        <div class="search-bar">
            <select name="district" id="districtSelect">
                <option value=""><?php echo $lang['select_district']; ?></option>
                <option value="Aveiro">Aveiro</option>
                <option value="Beja">Beja</option>
                <option value="Braga">Braga</option>
                <option value="Bragança">Bragança</option>
                <option value="Castelo Branco">Castelo Branco</option>
                <option value="Coimbra">Coimbra</option>
                <option value="Évora">Évora</option>
                <option value="Faro">Faro</option>
                <option value="Guarda">Guarda</option>
                <option value="Leiria">Leiria</option>
                <option value="Lisboa">Lisboa</option>
                <option value="Portalegre">Portalegre</option>
                <option value="Porto">Porto</option>
                <option value="Santarém">Santarém</option>
                <option value="Setúbal">Setúbal</option>
                <option value="Viana do Castelo">Viana do Castelo</option>
                <option value="Vila Real">Vila Real</option>
                <option value="Viseu">Viseu</option>55
                <option value="current_location"><?php echo $lang['current_location']; ?></option>
            </select>
            <input type="text" id="searchInput" name="search" placeholder="<?php echo $lang['search_by_name']; ?>">
            <button id="searchButton" type="button"><i class="fas fa-search"></i></button>
        </div>
        <nav>
            <ul>
            <?php if ($is_admin): ?>
                <li><a href="?manage=<?php echo $show_table ? '0' : '1'; ?>" class='btn'>Management</a></li>
            <?php endif; ?>

                <?php if ($is_admin || $is_simpatizante): ?>
                    <li><a href="createBar.php" class='btn'>Criar Bar</a></li>
                <?php endif; ?>
                <?php if ($is_admin || $is_simpatizante ||$is_utilizador): ?>
                    <li><a href='logout.php' class='btn'>Logout</a></li>
                <?php endif; ?>

                <?php if (!$is_admin && !$is_simpatizante && !$is_utilizador): ?>

                    <li><a href="../login/formLogin.php" class="user-icon"><?php echo $lang['login']; ?></a></li>
                    <li><a href="../registo/formRegisto.php" class="user-icon"><?php echo $lang['register']; ?></a></li>
                <?php endif; ?>
                <li>
                <div class="lang-menu">
                    <a href="?lang=pt<?php echo isset($_GET['bar_id']) ? '&bar_id=' . $_GET['bar_id'] : ''; ?>" class="<?php echo $lang_code == 'pt' ? 'active' : ''; ?>">PT</a> |
                    <a href="?lang=en<?php echo isset($_GET['bar_id']) ? '&bar_id=' . $_GET['bar_id'] : ''; ?>" class="<?php echo $lang_code == 'en' ? 'active' : ''; ?>">ENG</a> |
                    <a href="?lang=fr<?php echo isset($_GET['bar_id']) ? '&bar_id=' . $_GET['bar_id'] : ''; ?>" class="<?php echo $lang_code == 'fr' ? 'active' : ''; ?>">FR</a>
                </div>
            </li>
            </ul>
        </nav>
        
        
    </header>
    <script>
document.getElementById("logo-title").addEventListener("click", function() {
    window.location.href = "../index.php";
});

$(document).ready(function() {
        $('#searchButton').click(function() {
            var district = $('#districtSelect').val();
            var name = $('#searchInput').val();
            // Construct the URL with search parameters
            var url = 'search_results.php?district=' + encodeURIComponent(district) + '&name=' + encodeURIComponent(name);
            // Redirect to the search results pagerch
            window.location.href = url;
        });
    });
</script>