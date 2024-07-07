<!DOCTYPE html>
<?php

if ($lang_code == "pt") {
    require_once("../lang/lang_pt.php");
} elseif ($lang_code == "fr") {
    require_once("../lang/lang_fr.php");
} else {
    require_once("../lang/lang_en.php");
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
            if ($user['tipo_utilizador'] == 'administrador') {
                $is_admin = true;
            }
            if ($user['tipo_utilizador'] == 'simpatizante') {
                $is_simpatizante = true;
            }
            if ($user['tipo_utilizador'] == 'utilizador') {
                $is_utilizador = true;
            }
            break;
        }
    }
}


include "../main/header_other.php"
?>
<html>
<head>
    <meta charset='utf-8'>
    <title>Criar Bar</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .form-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #3E3434;
        }

        .form-container form table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-container form table tr td {
            padding: 10px 0;
        }

        .form-container form table tr td label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-container form table tr td input[type="text"],
        .form-container form table tr td textarea,
        .form-container form table tr td input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container form table tr td input[type="submit"],
        .form-container form table tr td button {
            background-color: #3E3434;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container form table tr td input[type="submit"]:hover,
        .form-container form table tr td button:hover {
            background-color: #2e2828;
        }

        .form-container form table tr td input[type="submit"]:active,
        .form-container form table tr td button:active {
            background-color: #1e1919;
        }

        .form-container form table tr td[colspan="2"] {
            text-align: center;
        }

        .form-container form table tr td textarea {
            height: 100px;
        }

        .form-container form table tr td input[type="file"] {
            padding: 3px;
        }
    </style>
</head>
<body>
    <div class="form-page">
        <div class="form-container">
            <h2>Criação de Bar</h2>
            <form method="POST" action="processCreateBar.php" enctype="multipart/form-data">
                <input type="hidden" name="utilizador_id" value="<?php echo $_SESSION['id']; ?>">
                <table>
                    <tr>
                        <td><label for="bar_name">Nome do Bar:</label></td>
                        <td><input type="text" id="bar_name" name="bar_name" required></td>
                    </tr>
                    <tr>
                        <td><label for="bar_location">Localização:</label></td>
                        <td><input type="text" id="bar_location" name="bar_location" required></td>
                    </tr>
                    <tr>
                        <td><label for="bar_description">Descrição:</label></td>
                        <td><textarea id="bar_description" name="bar_description"></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="bar_contact">Contacto:</label></td>
                        <td><input type="text" id="bar_contact" name="bar_contact" required></td>
                    </tr>
                    <tr>
                        <td><label for="bar_images">Imagens:</label></td>
                        <td><input type="file" id="bar_images" name="bar_images[]" multiple required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Criar Bar"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><button type="button" onclick="window.history.back();">Voltar</button></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>
