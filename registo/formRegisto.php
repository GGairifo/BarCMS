<!DOCTYPE html>
<?php
    require_once("../../Lib/lib.php");

    $flags[] = FILTER_NULL_ON_FAILURE;

    $serverName = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING, $flags);

    $serverPortSSL = 443;
    $serverPort = 80;

    $name = webAppName();

    $nextUrl = "https://" . $serverName . ":" . $serverPortSSL . $name . "processFormRegisto.php";
?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <title>Authentication Using PHP</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="stylesheet" type="text/css" href="css/GlobalStyle.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../index.php">
                <img src="imagens/logo.png" alt="Logo">
            </a>
        </div>
        <div class="welcome">
            Bem Vindo!
        </div>
    </header>
    <div class="form-container">
        <form action="<?php echo $nextUrl ?>" method="POST">
            <table>
                <tr>
                    <td>User Name</td>
                    <td><input type="text" name="username" placeholder="Type your name" pattern=".{5,}" title="Username must be at least 5 characters long" required></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="email" name="email" placeholder="Type your email" required></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" placeholder="Type your password" pattern="(?=.*[a-zA-Z])(?=.*[0-9]).{8,}" title="Password must contain at least 6 letters and 2 numbers" required></td>
                </tr>
                <tr>
                    <td>Confirmar Password</td>
                    <td><input type="password" name="confirm_password" placeholder="Type your password again" pattern="(?=.*[a-zA-Z])(?=.*[0-9]).{8,}" title="Password must contain at least 6 letters and 2 numbers" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <img src="captcha/captchaImage.php"/><br>
                        <input type="text" name="captcha" id="captcha" <?php echo $value;?>><br>
                    </td>
                </tr>
            </table>
            <input type="submit" value="Registo"> <input type="reset" value="Clear">
            <button type="button" onclick="window.history.back();">Voltar</button>
        </form>
    </div>
</body>
</html>
