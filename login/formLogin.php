<!DOCTYPE html>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
    require_once("../../Lib/lib.php");
    
    $flags[] = FILTER_NULL_ON_FAILURE;
    
    $serverName = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_FULL_SPECIAL_CHARS, $flags);
    $serverPortSSL = 443;
    $serverPort = 80;
    $name = webAppName();
    $nextUrl = "https://" . $serverName . ":" . $serverPortSSL . $name . "processFormLogin.php";
    #$nextUrl = "http://" . $serverName . ":" . $serverPort . $name . "processFormLogin.php";
    include "../main/header_form.php"
?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <title>Authentication Using PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body>

    <div class="form-container">
        <form action="<?php echo $nextUrl ?>" method="POST">
            <table>
                <tr>
                    <td>User Name</td>
                    <td><input type="text" name="username" placeholder="Type your name" required></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" placeholder="Type your password" required></td>
                </tr>
            </table>
            <input type="submit" value="Login"> <input type="reset" value="Clear">
            <button type="button" onclick="window.history.back();">Voltar</button>
        </form>
    </div>
</body>
</html>
