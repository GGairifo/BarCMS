<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');



// Construct the path to db.php
$dbPath = "../../../Lib/db.php";

// Include db.php
require_once($dbPath);



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $conn = dbConnect(ConfigFile);

    mysqli_select_db($GLOBALS['ligacao'], $GLOBALS['configDataBase']->db);
    $query = "SELECT 
                b.bar_id,
                b.utilizador_id,
                b.nome AS nome,
                b.localizacao,
                b.descricao,
                b.contacto,
                AVG(c.classificacao) AS avg_score,
                COUNT(c.id_critica) AS num_reviews,
                m.url AS image_url
              FROM 
                bar b
              LEFT JOIN 
                critica c ON b.bar_id = c.bar_id
              LEFT JOIN
                multimedia m ON b.bar_id = m.bar_id AND m.tipo = 'imagem'
              GROUP BY 
                b.bar_id";
    $result = mysqli_query($GLOBALS['ligacao'], $query);

    if (!$result) {
        echo json_encode(["error" => mysqli_error($conn)]);
    } else {
        $bars = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $bars[] = $row;
        }
        echo json_encode($bars);
    }

    dbDisconnect($conn);
}
?>
