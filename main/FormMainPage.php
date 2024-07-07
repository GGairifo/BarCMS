<!DOCTYPE html>
<?php
// Habilitar a exibição de erros PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../Lib/lib.php");
require_once("../../Lib/db.php");
require_once("processFormMainPage.php");

session_start();
$users = getAllUsers();
$is_admin = false;
$is_simpatizante = false;
foreach ($users as $user) {
    if ($_SESSION['id'] == $user['utilizador_id']) {
        if ($user['tipo_utilizador'] == 'administrador') {
            $is_admin = true;
        }
        if ($user['tipo_utilizador'] == 'simpatizante') {
            $is_simpatizante = true;
        }
        break;
    }
}
// Lógica para atualizar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
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
        echo "Update successful!";
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

    dbDisconnect();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Lógica para mostrar ou esconder a tabela de gerenciamento
$show_table = false;
if (isset($_GET['manage']) && $is_admin) {
    $show_table = true;
}

?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <title>User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
</head>
<body>
    <header>
    <div class="logo-title">
            <img src="assets/images/logo.png" alt="Logo">
            <h1><?php echo $lang['title']; ?></h1>
        </div>
        <div class='welcome'>Bem Vindo!</div>
        <div class='header-buttons'>
            <?php if ($is_admin): ?>
                <a href="?manage=<?php echo $show_table ? '0' : '1'; ?>" class='btn'>Management</a>
            <?php endif; ?>
            <?php if ($is_admin || $is_simpatizante): ?>
                <a href="../Bar/createBar.php" class='btn'>Criar Bar</a>
            <?php endif; ?>
            <a href='logout.php' class='btn'>Logout</a>
        </div>
    </header>

    <?php if ($show_table): ?>
        <div class="form-container">
            <table border="1">
                <tr>
                    <th>User Name</th>
                    <th>Password</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <form method="POST" action="">
                            <input type="hidden" name="user_id" value="<?php echo $user['utilizador_id']; ?>">
                            <td><input type="text" name="username" value="<?php echo $user['user_name']; ?>"></td>
                            <td><input type="text" name="password" value="<?php echo $user['password']; ?>"></td>
                            <td><input type="text" name="email" value="<?php echo $user['email']; ?>"></td>
                            <td>
                                <select name="user_type">
                                    <option value="convidado" <?php echo $user['tipo_utilizador'] == 'convidado' ? 'selected' : ''; ?>>Convidado</option>
                                    <option value="utilizador" <?php echo $user['tipo_utilizador'] == 'utilizador' ? 'selected' : ''; ?>>Utilizador</option>
                                    <option value="simpatizante" <?php echo $user['tipo_utilizador'] == 'simpatizante' ? 'selected' : ''; ?>>Simpatizante</option>
                                    <option value="administrador" <?php echo $user['tipo_utilizador'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </td>
                            <td>
                                <select name="active">
                                    <option value="1" <?php echo $user['active'] == 1 ? 'selected' : ''; ?>>1</option>
                                    <option value="0" <?php echo $user['active'] == 0 ? 'selected' : ''; ?>>0</option>
                                </select>
                            </td>
                            <td><input type="submit" value="Edit"></td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
