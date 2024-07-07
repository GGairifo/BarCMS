<?php

session_start();

if ($lang_code == "pt") {
    require_once("../lang/lang_pt.php");
} elseif ($lang_code == "fr") {
    require_once("../lang/lang_fr.php");
} else {
    require_once("../lang/lang_en.php");
}

require_once("../Lib/lib.php");
require_once("../Lib/db.php");
require_once("processFormMainPage.php");

// Handle form submission
$updateMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['user_name'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $userType = $_POST['user_type'];
    $active = $_POST['active'];

    dbConnect(ConfigFile);
    mysqli_select_db($GLOBALS['ligacao'], $GLOBALS['configDataBase']->db);

    $query = "UPDATE utilizador SET user_name = ?, password = ?, email = ?, tipo_utilizador = ?, active = ? WHERE utilizador_id = ?";
    $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
    if (!$stmt) {
        die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
    }
    mysqli_stmt_bind_param($stmt, 'ssssii', $username, $password, $email, $userType, $active, $userId);

    // Verificação de sucesso da execução
    if (mysqli_stmt_execute($stmt)) {
        $updateMessage = "Update successful!";
    } else {
        $updateMessage = "Error: " . mysqli_stmt_error($stmt);
    }

    dbDisconnect();
}

// Handle bar deletion
$deleteMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bar_id'])) {
    $barId = $_POST['delete_bar_id'];

    dbConnect(ConfigFile);
    mysqli_select_db($GLOBALS['ligacao'], $GLOBALS['configDataBase']->db);

    // Excluir registros da tabela multimedia relacionados ao bar_id
    $queryDeleteMultimedia = "DELETE FROM multimedia WHERE bar_id = ?";
    $stmtDeleteMultimedia = mysqli_prepare($GLOBALS['ligacao'], $queryDeleteMultimedia);
    if (!$stmtDeleteMultimedia) {
        die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
    }
    mysqli_stmt_bind_param($stmtDeleteMultimedia, 'i', $barId);

    if (mysqli_stmt_execute($stmtDeleteMultimedia)) {
        // Após excluir os registros de multimedia, agora podemos excluir o bar
        $queryDeleteBar = "DELETE FROM bar WHERE bar_id = ?";
        $stmtDeleteBar = mysqli_prepare($GLOBALS['ligacao'], $queryDeleteBar);
        if (!$stmtDeleteBar) {
            die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
        }
        mysqli_stmt_bind_param($stmtDeleteBar, 'i', $barId);

        if (mysqli_stmt_execute($stmtDeleteBar)) {
            $deleteMessage = "Bar deleted successfully!";
        } else {
            $deleteMessage = "Error: " . mysqli_stmt_error($stmtDeleteBar);
        }
    } else {
        $deleteMessage = "Error: " . mysqli_stmt_error($stmtDeleteMultimedia);
    }

    dbDisconnect();
}

$users = getAllUsers();
$bars = getAllBars();
$is_admin = false;
$is_simpatizante = false;

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

include "header_other.php";

// Display user details in a table
echo '<table border="1">';
echo '<tr><th>User ID</th><th>Username</th><th>Email</th><th>Password</th><th>User Type</th><th>Active</th><th>Action</th></tr>';

foreach ($users as $user) {
    echo '<tr>';
    echo '<td>' . $user['utilizador_id'] . '</td>';
    echo '<td>' . $user['user_name'] . '</td>';
    echo '<td>' . $user['email'] . '</td>';
    echo '<td>' . $user['password'] . '</td>'; // Displaying password (NOT recommended)
    echo '<td>' . $user['tipo_utilizador'] . '</td>';
    echo '<td>' . $user['active'] . '</td>';
    echo '<td>';
    echo '<form method="post">';
    echo '<input type="text" name="user_name" value="' . $user['user_name'] . '" placeholder="Username"><br>';
    echo '<input type="text" name="password" value="' . $user['password'] . '"  placeholder="Password"><br>'; 
    echo '<input type="text" name="email" value="' . $user['email'] . '" placeholder="Email"><br>';
    echo '<select name="user_type">';
    echo '<option value="convidado" ' . ($user['tipo_utilizador'] == 'convidado' ? 'selected' : '') . '>Convidado</option>';
    echo '<option value="utilizador" ' . ($user['tipo_utilizador'] == 'utilizador' ? 'selected' : '') . '>Utilizador</option>';
    echo '<option value="simpatizante" ' . ($user['tipo_utilizador'] == 'simpatizante' ? 'selected' : '') . '>Simpatizante</option>';
    echo '<option value="administrador" ' . ($user['tipo_utilizador'] == 'administrador' ? 'selected' : '') . '>Administrador</option>';
    echo '</select><br>';
    echo '<input type="text" name="active" value="' . $user['active'] . '" placeholder="Active"><br>';
    echo '<input type="hidden" name="user_id" value="' . $user['utilizador_id'] . '">';
    echo '<button type="submit">Save</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';

echo '<p>' . $updateMessage . '</p>';

// Display bar details in a table
echo '<table border="1">';
echo '<tr><th>Bar ID</th><th>Owner ID</th><th>Bar Name</th><th>Location</th><th>Description</th><th>Contact</th><th>Action</th></tr>';

foreach ($bars as $bar) {
    echo '<tr>';
    echo '<td>' . $bar['bar_id'] . '</td>';
    echo '<td>' . $bar['utilizador_id'] . '</td>';
    echo '<td>' . $bar['nome'] . '</td>';
    echo '<td>' . $bar['localizacao'] . '</td>';
    echo '<td>' . $bar['descricao'] . '</td>';
    echo '<td>' . $bar['contacto'] . '</td>';
    echo '<td>';
    echo '<form method="post">';
    echo '<input type="hidden" name="delete_bar_id" value="' . $bar['bar_id'] . '">';
    echo '<button type="submit">Delete</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';

echo '<p>' . $deleteMessage . '</p>';
?>
