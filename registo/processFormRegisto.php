<?php
require_once("../../Lib/lib.php");
require_once("../../Lib/db.php");
require_once("../../Lib/lib-mail-v2.php");
require_once("../../Lib/HtmlMimeMail.php");

session_start();

include_once("captcha/xxx.php");

try {
    // Verificação do CAPTCHA
    if ($_SESSION['captchal'] == $_POST['captcha']) {

        $flags[] = FILTER_NULL_ON_FAILURE;
        
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW, $flags);
        
        if ($method == 'POST') {
            $_INPUT_METHOD = INPUT_POST;
        } elseif ($method == 'GET') {
            echo "Este script deve ser chamado via POST.";
            exit();
        } else {
            echo "Método HTTP inválido (" . $method . ")";
            exit();
        }

        $username = filter_input($_INPUT_METHOD, 'username', FILTER_SANITIZE_STRING, $flags);
        $password = filter_input($_INPUT_METHOD, 'password', FILTER_SANITIZE_STRING, $flags);
        $email = filter_input($_INPUT_METHOD, 'email', FILTER_SANITIZE_EMAIL, $flags);

        $tipoUtilizador = 'utilizador';
        $token = @substr(md5(time()), 0, 9);
        $active = 0;

        if (empty($username) || empty($password) || empty($email)) {
            echo "Todos os campos são obrigatórios.";
            echo "Username: " . htmlspecialchars($username) . "<br>";
            echo "Password: " . htmlspecialchars($password) . "<br>";
            echo "Email: " . htmlspecialchars($email) . "<br>";
            exit();
        }

        // Verificar se o nome de usuário já existe no banco de dados
        dbConnect(ConfigFile);
        $dataBaseName = $GLOBALS['configDataBase']->db;
        mysqli_select_db($GLOBALS['ligacao'], $dataBaseName);
        $query = "SELECT COUNT(*) AS count FROM `$dataBaseName`.`utilizador` WHERE user_name = ?";
        $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a declaração: " . mysqli_error($GLOBALS['ligacao']));
        }
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        if ($count > 0) {
            echo "O nome de usuário já existe. Por favor, escolha outro.<br>";
            echo "<a href='formRegisto.php'>Voltar para a página de registro</a>";
            exit();
        }

        // Verificar se há algum usuário administrador registrado
        $queryAdmin = "SELECT COUNT(*) AS count FROM `$dataBaseName`.`utilizador`";
        $stmtAdmin = mysqli_query($GLOBALS['ligacao'], $queryAdmin);
        $resultAdmin = mysqli_fetch_assoc($stmtAdmin);
        mysqli_free_result($stmtAdmin);

        //$resultAdmin['count'] = '0';

        if (empty($resultAdmin['count'])) {
            $tipoUtilizador = 'administrador';
        }


        // Criptografar a senha antes de armazenar no banco de dados
        $md5Password = substr(md5($password), 0, 9);
        $hashedPassword = $md5Password;

        $serverName = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_UNSAFE_RAW, $flags);
        if ($serverName === null) {
            throw new Exception("SERVER_NAME não está definido.");
        }

        $serverPort = 80;
        $name = webAppName();
        $baseUrl = "http://" . $serverName . ":" . $serverPort;
        $baseNextUrl = $baseUrl . $name;

        dbConnect(ConfigFile);
        $dataBaseName = $GLOBALS['configDataBase']->db;
        mysqli_select_db($GLOBALS['ligacao'], $dataBaseName);

        $query = "INSERT INTO `$dataBaseName`.`utilizador` (user_name, password, email, tipo_utilizador, token, active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a declaração: " . mysqli_error($GLOBALS['ligacao']));
        }

        mysqli_stmt_bind_param($stmt, 'sssssi', $username, $hashedPassword, $email, $tipoUtilizador, $token, $active);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            $queryString = "SELECT * FROM `$dataBaseName`.`email-accounts`";
            $queryResult = mysqli_query($GLOBALS['ligacao'], $queryString);
            $record      = mysqli_fetch_array($queryResult);
            $smtpServer  = $record['smtpServer'];
            $port        = intval($record['port']);
            $useSSL      = boolval($record['useSSL']);
            $timeout     = intval($record['timeout']);
            $loginName   = $record['loginName'];
            $password    = $record['password'];
            $fromEmail   = $record['email'];
            $fromName    = $record['displayName'];
            mysqli_free_result($queryResult);
            dbDisconnect();

            $activationUrl  = "http://" . $serverName . webAppName() . "validateAccount.php";
            $activationLink = "http://" . $serverName . webAppName() . "/activate.php?token=" . $token;
            $MessageHTML = <<<EOD
            <html>
                <body>
                    <p>Dear $username,
                    <p>Welcome to our website.</p>
                    <a href="$activationLink">Activate your account</a>
                </body>
            </html>
            EOD;

            $mail = new HtmlMimeMail();
            $mail->add_html($MessageHTML, $activationLink);
            $mail->build_message();
            $mail->send(
                $smtpServer,
                $useSSL,
                $port,
                $loginName,
                $password,
                $username,
                $email, 
                $fromName, 
                $fromEmail,
                "Welcome to our site!",
                "X-Mailer: Html Mime Mail Class"
            );
            echo "O registro foi realizado com sucesso!";
        } else {
            echo "Erro ao realizar o registo usuário: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
        dbDisconnect();

    } else {
        echo "<h1>Error - Code is incorrect</h1>";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
