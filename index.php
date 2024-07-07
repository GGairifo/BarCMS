<?php
// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start the session

// Default language code
$lang_code = isset($_SESSION['lang_code']) ? $_SESSION['lang_code'] : "pt";

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Include language files based on selected language
if ($lang_code == "pt") {
    require_once("lang/lang_pt.php");
} elseif ($lang_code == "fr") {
    require_once("lang/lang_fr.php");
} else {
    require_once("lang/lang_en.php");
}


// Handle language switching
if (isset($_GET['lang']) && ($_GET['lang'] == 'pt' || $_GET['lang'] == 'en' || $_GET['lang'] == 'fr')) {
    $_SESSION['lang_code'] = $_GET['lang']; // Set the session variable
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}



require_once("../Lib/lib.php");
require_once("../Lib/db.php");
require_once("main/processFormMainPage.php");
$users = getAllUsers();


$is_admin = false;
$is_simpatizante = false;
$is_utilizador= false;
if(isset($_SESSION['id'])) {
    foreach ($users as $user) {
        if ($_SESSION['id'] == $user['utilizador_id']) {
            if ($user['tipo_utilizador'] == 'administrador') {
                $is_admin = true;
                $_SESSION['tipo_utilizador'] = 'administrador';
            }
            if ($user['tipo_utilizador'] == 'simpatizante') {
                $is_simpatizante = true;
                $_SESSION['tipo_utilizador'] = 'simpatizante';
            }
            if ($user['tipo_utilizador'] == 'utilizador') {
                $is_utilizador= true;
                $_SESSION['tipo_utilizador'] = 'utilizador';
            }
            break;
        }
    }
}





require_once("main/header.php");


?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>
<body>
    
    


    <main>
        <h3><?php echo $lang['title_homepage']; ?></h3>
        <div class="card-container" id="barList">
        </div>
    </main>

  
    <script>


$(document).ready(function() {
    $.ajax({
        url: "assets/apis/google/getAccessToken.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            console.log("Access Token:", response);
            var accessToken = response.access_token;
            console.log("Access Token:", accessToken);

            loadBarRatingsAndTranslateDescriptions(accessToken);
        },
        error: function(xhr, status, error) {
            console.error("Error getting access token:", error);
        }
    });
});

function loadBarRatingsAndTranslateDescriptions(accessToken) {
    $.ajax({
        url: "assets/apis/rest/getBarRatings.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.error) {
                console.error("Error getting bar ratings:", response.error);
            } else {
                console.log("Bar ratings loaded successfully:", response);
                var barList = $("#barList");
                
                // Map each bar to a promise that resolves when its translation is done
                var translationPromises = response.map(function(bar) {
                    return new Promise(function(resolve, reject) {
                        translate(bar.descricao, "<?php echo $lang_code; ?>", accessToken, function(translatedDescription) {
                            console.log("Translating:", translatedDescription);
                            resolve({ bar: bar, translatedDescription: translatedDescription });
                        });
                    });
                });

                // Wait for all translation promises to resolve
                Promise.all(translationPromises).then(function(translations) {
                    // Sort bars by average score in descending order
                    translations.sort((a, b) => b.bar.avg_score - a.bar.avg_score);
                    
                    // Clear existing content from barList
                    barList.empty();

                    // Display all bars with translated descriptions
                    translations.forEach(function(translation) {
                        var bar = translation.bar;
                        var translatedDescription = translation.translatedDescription;
                        
                        // Round the average score to one decimal place
                        var roundedAvgScore = Math.round(bar.avg_score * 10) / 10;

                        var imageUrl = bar.image_url ? "Bar/" + bar.image_url : "Bar/uploads/default.jpg"; // Check if image exists, if not, use default.jpg
                        var html = "<div class='card'>";
                        html += "<a href='Bar/bar_page.php?bar_id=" + bar.bar_id + "'>"; // Link to the bar page
                        html += "<h2>" + bar.nome + "</h2>";
                        html += "<img src='" + imageUrl + "' alt='Bar Image'>";
                        html += "<p><strong><?php echo $lang['avg']; ?></strong> " + roundedAvgScore.toFixed(1) + "</p>"; // Display rounded average score
                        html += "<p><strong><?php echo $lang['nreviews']; ?></strong> " + bar.num_reviews + "</p>";
                        html += "<p>" + translatedDescription + "</p>";
                        html += "<p>" + bar.localizacao + "</p>";
                        html += "<p style='margin-bottom: 10px;'>" + bar.contacto + "</p>"; // Add margin-bottom to the last paragraph
                        html += "</a>"; // Close the anchor tag

                        html += "</div>";

                        barList.append(html);
                    });
                });

            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading bar ratings:", error);
        }
    });
}



function translate(text, targetLanguage, accessToken, callback) {
    console.log("Translating:", text, "to", targetLanguage);
    $.ajax({
        url: "assets/apis/google/translateText.php",
        type: "POST",
        dataType: "json",
        data: {
            text: text,
            targetLanguage: targetLanguage,
            access_token: accessToken
        },
        success: function(response) {
            if (response.error) {
                console.error("Translation Error:", response.error);
            } else {
                var translatedText = response.translatedText;
                console.log("Translated Text:", translatedText);
                callback(translatedText);
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText); // Log the full response for detailed debugging

            console.error("Error translating text:", error); // Add a closing parenthesis here

        }
    });
}
</script>


    
<footer class="footer">
    <p>&copy; 2024 Barometro</p>
</footer>

</body>
</html>
