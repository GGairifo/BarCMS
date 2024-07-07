<!DOCTYPE html>
<?php
session_start();

$lang_code = $_GET['lang_code'] ?? 'en';
switch ($lang_code) {
    case 'pt':
        require_once("../lang/lang_pt.php");
        break;
    case 'fr':
        require_once("../lang/lang_fr.php");
        break;
    default:
        require_once("../lang/lang_en.php");
        break;
}

require_once("../Lib/lib.php");
require_once("../Lib/db.php");
require_once("../main/processFormMainPage.php");

$users = getAllUsers();
$is_admin = false;
$is_simpatizante = false;
$is_utilizador = false;
if(isset($_SESSION['id'])) {
    foreach ($users as $user) {
        if ($_SESSION['id'] == $user['utilizador_id']) {
            $is_admin = $user['tipo_utilizador'] == 'administrador';
            $is_simpatizante = $user['tipo_utilizador'] == 'simpatizante';
            $is_utilizador =  $user['tipo_utilizador'] == 'utilizador';;
            break;
        }
    }
}

include "../main/header_other.php";
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMw3QRiQ6wY24B4XyS9xEjT+6VRMSgq3uFA/U5A" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            padding: 20px;
            margin:0px;
            background-color: #f4f4f4;
            color: #3E3434;
        }
        #searchResults {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #searchResults ul {
            list-style: none;
            padding: 0;
        }
        #searchResults li {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        #searchResults li:last-child {
            border-bottom: none;
        }
        #searchResults h3 {
            margin: 0 0 10px;
            font-size: 1.2em;
        }
        #searchResults p {
            margin: 5px 0;
            color: #555;
        }
        #searchResults a {
            text-decoration: none;
            color: #333;
        }
        #searchResults a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Search Results</h2>
    <div id="searchResults"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function getUrlParameter(name) {
                name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            var district = getUrlParameter('district');
            var name = getUrlParameter('name');

            $.ajax({
                url: '../assets/apis/rest/searchBars.php',
                type: 'GET',
                data: {
                    district: district,
                    name: name
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        $('#searchResults').html('<p>Error: ' + response.error + '</p>');
                    } else {
                        var html = '<ul>';
                        $.each(response, function(index, bar) {
                            html += '<li>' +
                                '<a href="bar_page.php?bar_id=' + bar.bar_id + '">' +
                                '<h3>' + bar.nome + '</h3>' +
                                '</a>' +
                                '<p>Location: ' + bar.localizacao + '</p>' +
                                '<p>Average Rating: ' + (bar.avg_score === null ? '0' : parseFloat(bar.avg_score).toFixed(1)) + '</p>' +
                                '</li>';
                        });
                        html += '</ul>';
                        $('#searchResults').html(html);
                    }
                },
                error: function(xhr, status, error) {
                    $('#searchResults').html('<p>Error: ' + error + '</p>');
                }
            });
        });
    </script>
</body>
</html>
