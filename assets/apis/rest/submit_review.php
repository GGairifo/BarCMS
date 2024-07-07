<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../../../Lib/db.php");

// Check if user is logged in and their role
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    $isAdmin = ($_SESSION['tipo_utilizador'] == 'administrador');
    $isSimpatizante = ($_SESSION['tipo_utilizador'] == 'simpatizante');

    if ($isAdmin || $isSimpatizante) {
        // Check if the required fields are set in the $_POST array
        if (isset($_POST['review_text'], $_POST['classificacao'], $_POST['bar_id'])) {
            // Include necessary files and functions

            // Connect to database
            dbConnect(ConfigFile);

            // Prepare and execute query to insert review into the database
            $userId = mysqli_real_escape_string($ligacao, $userId);
            $barId = mysqli_real_escape_string($ligacao, $_POST['bar_id']);
            $content = mysqli_real_escape_string($ligacao, $_POST['review_text']);
            $rating = mysqli_real_escape_string($ligacao, $_POST['classificacao']);
            $currentDateTime = date('Y-m-d H:i:s');

            $insertQuery = "INSERT INTO smi.critica (utilizador_id, bar_id, conteudo, data_de_publicacao, classificacao) 
                            VALUES ('$userId', '$barId', '$content', '$currentDateTime', '$rating')";

            if (mysqli_query($ligacao, $insertQuery)) {
                echo "Review submitted successfully!";
            } else {
                echo "Error: " . $insertQuery . "<br>" . dbGetLastError();
            }

            // Close connection
            dbDisconnect();
        } else {
            echo "Error: Required form data not provided!";
        }
    } else {
        echo "Error: You are neither an administrator nor a simpatizante.";
    }
} else {
    echo "Error: User is not logged in!";
}
?>
