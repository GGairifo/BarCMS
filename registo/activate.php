<?php
require_once("../../Lib/lib.php");
require_once("../../Lib/db.php");

try {
    dbConnect(ConfigFile);
    $dataBaseName = $GLOBALS['configDataBase']->db;
    mysqli_select_db($GLOBALS['ligacao'], $dataBaseName);

    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

    if (empty($token)) {
        throw new Exception("Token não fornecido.");
    }

    $query = "SELECT * FROM `$dataBaseName`.`utilizador` WHERE token = ?";
    $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
    if (!$stmt) {
        throw new Exception("Erro ao preparar a declaração: " . mysqli_error($GLOBALS['ligacao']));
    }

    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "Debug: Token found, updating account to active.<br>";
        // Atualizar o campo `active` para 1
        $updateQuery = "UPDATE `$dataBaseName`.`utilizador` SET active = 1 WHERE token = ?";
        $updateStmt = mysqli_prepare($GLOBALS['ligacao'], $updateQuery);
        if (!$updateStmt) {
            throw new Exception("Erro ao preparar a declaração de atualização: " . mysqli_error($GLOBALS['ligacao']));
        }

        mysqli_stmt_bind_param($updateStmt, 's', $token);
        mysqli_stmt_execute($updateStmt);

        if (mysqli_stmt_affected_rows($updateStmt) > 0) {
            echo "Debug: Account activated successfully.<br>";
            header("Location: ../../login/formLogin.php");
        } else {
            echo "<h1>Erro ao ativar a conta. Por favor, tente novamente.</h1>";
        }

        mysqli_stmt_close($updateStmt);
    } else {
        echo "<h1>Token inválido.</h1>";
    }

    mysqli_stmt_close($stmt);
    dbDisconnect();
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
