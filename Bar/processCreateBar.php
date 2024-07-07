<?php
require_once("../Lib/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utilizadorId = $_POST['utilizador_id'];
    $barName = htmlspecialchars($_POST['bar_name']);
    $barLocation = htmlspecialchars($_POST['bar_location']);
    $barDescription = htmlspecialchars($_POST['bar_description']);
    $barContact = htmlspecialchars($_POST['bar_contact']);

    // Diretório de upload
    $target_dir = "uploads/";

    // Extensões permitidas
    $allowed_image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowed_video_extensions = ['mp4'];

    // Verificar os ficheiros enviados
    foreach ($_FILES["bar_images"]["name"] as $key => $file_name) {
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_extension, array_merge($allowed_image_extensions, $allowed_video_extensions))) {
            echo "Desculpe, apenas ficheiros JPG, JPEG, PNG, GIF, e MP4 são permitidos.";
            exit();
        }
    }

    dbConnect(ConfigFile);
    mysqli_select_db($GLOBALS['ligacao'], $GLOBALS['configDataBase']->db);

    // Verificar o ID do último bar criado para o utilizador
    $query = "SELECT MAX(bar_id) AS max_bar_id FROM bar WHERE utilizador_id = ?";
    $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
    if (!$stmt) {
        die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
    }
    mysqli_stmt_bind_param($stmt, 'i', $utilizadorId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $maxBarId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $newBarId = $maxBarId + 1;

    // Inserir os dados do bar na tabela 'bar'
    $query = "INSERT INTO bar (bar_id, utilizador_id, nome, localizacao, descricao, contacto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
    if (!$stmt) {
        die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
    }
    mysqli_stmt_bind_param($stmt, 'iissss', $newBarId, $utilizadorId, $barName, $barLocation, $barDescription, $barContact);

    if (mysqli_stmt_execute($stmt)) {
        echo "Bar created successfully!";
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);

    // Processar cada imagem ou vídeo
    foreach ($_FILES["bar_images"]["name"] as $key => $file_name) {
        $target_file = $target_dir . basename($file_name);
        $uploadOk = 1;
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar se o ficheiro é uma imagem ou um vídeo permitido
        if (in_array($file_extension, $allowed_image_extensions)) {
            $check = getimagesize($_FILES["bar_images"]["tmp_name"][$key]);
            if ($check === false) {
                echo "O ficheiro " . $file_name . " não é uma imagem válida.";
                $uploadOk = 0;
            }
        } elseif (!in_array($file_extension, $allowed_video_extensions)) {
            echo "O ficheiro " . $file_name . " não é um vídeo válido.";
            $uploadOk = 0;
        }

        // Verificar se o ficheiro já existe
        if (file_exists($target_file)) {
            echo "Desculpe, o ficheiro " . $file_name . " já existe.";
            $uploadOk = 0;
        }

        // Verificar o tamanho do ficheiro
        if ($_FILES["bar_images"]["size"][$key] > 50000000) { // 50MB de limite
            echo "Desculpe, o ficheiro " . $file_name . " é muito grande.";
            $uploadOk = 0;
        }

        // Verificar se $uploadOk está a 0 devido a um erro
        if ($uploadOk == 0) {
            echo "Desculpe, o ficheiro " . $file_name . " não foi carregado.";
        } else {
            // Se tudo estiver ok, tentar carregar o ficheiro
            if (move_uploaded_file($_FILES["bar_images"]["tmp_name"][$key], $target_file)) {
                echo "O ficheiro " . htmlspecialchars(basename($file_name)) . " foi carregado.";

                // Inserir os dados da imagem ou vídeo na tabela 'multimedia'
                $query = "INSERT INTO multimedia (bar_id, tipo, url) VALUES (?, ?, ?)";
                $media_type = in_array($file_extension, $allowed_image_extensions) ? 'imagem' : 'video';
                $stmt = mysqli_prepare($GLOBALS['ligacao'], $query);
                if (!$stmt) {
                    die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
                }
                mysqli_stmt_bind_param($stmt, 'iss', $newBarId, $media_type, $target_file);

                if (mysqli_stmt_execute($stmt)) {
                    echo ucfirst($media_type) . " " . $file_name . " inserida com sucesso.";
                } else {
                    echo "Erro ao inserir " . $media_type . " " . $file_name . ": " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Desculpe, ocorreu um erro ao carregar o ficheiro " . $file_name . ".";
            }
        }
    }

    // Fechar a conexão
    dbDisconnect();

    header("Location: ../index.php");
    exit();
}
?>