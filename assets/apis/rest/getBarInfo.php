<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Construct the path to db.php
$dbPath = "../../../Lib/db.php";

// Include db.php
require_once($dbPath);

function sendError($message) {
    echo json_encode(["error" => $message]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['bar_id'])) {
    $barId = intval($_GET['bar_id']);
    dbConnect(ConfigFile); // Ensure this function sets the global $ligacao

    global $ligacao;
    global $configDataBase;

    if (!$ligacao) {
        sendError("Failed to connect to the database.");
    }

    mysqli_select_db($ligacao, $configDataBase->db);

    // Fetch bar details
    $barQuery = "SELECT 
                    b.bar_id,
                    b.utilizador_id,
                    b.nome,
                    b.localizacao,
                    b.descricao,
                    b.contacto,
                    AVG(c.classificacao) AS avg_score,
                    COUNT(c.id_critica) AS num_reviews
                 FROM 
                    bar b
                 LEFT JOIN 
                    critica c ON b.bar_id = c.bar_id
                 WHERE 
                    b.bar_id = ?
                 GROUP BY 
                    b.bar_id";
    $barStmt = mysqli_prepare($ligacao, $barQuery);
    if (!$barStmt) {
        sendError("Failed to prepare bar details query: " . mysqli_error($ligacao));
    }
    mysqli_stmt_bind_param($barStmt, 'i', $barId);
    mysqli_stmt_execute($barStmt);
    $barResult = mysqli_stmt_get_result($barStmt);
    $bar = mysqli_fetch_assoc($barResult);

    if (!$bar) {
        sendError("Bar not found");
    }

    // Fetch bar reviews
    $reviewsQuery = "SELECT 
                        critica.id_critica,
                        critica.utilizador_id,
                        critica.conteudo,
                        critica.data_de_publicacao,
                        critica.classificacao,
                        utilizador.user_name
                     FROM 
                        critica
                     JOIN
                        utilizador ON critica.utilizador_id = utilizador.utilizador_id
                     WHERE 
                        critica.bar_id = ?";
    $reviewsStmt = mysqli_prepare($ligacao, $reviewsQuery);
    if (!$reviewsStmt) {
        sendError("Failed to prepare reviews query: " . mysqli_error($ligacao));
    }
    mysqli_stmt_bind_param($reviewsStmt, 'i', $barId);
    mysqli_stmt_execute($reviewsStmt);
    $reviewsResult = mysqli_stmt_get_result($reviewsStmt);
    $reviews = [];
    while ($row = mysqli_fetch_assoc($reviewsResult)) {
        $reviews[] = $row;
    }

    // Fetch bar multimedia
    $multimediaQuery = "SELECT 
                           id_mult,
                           tipo,
                           url
                        FROM 
                           multimedia
                        WHERE 
                           bar_id = ?";
    $multimediaStmt = mysqli_prepare($ligacao, $multimediaQuery);
    if (!$multimediaStmt) {
        sendError("Failed to prepare multimedia query: " . mysqli_error($ligacao));
    }
    mysqli_stmt_bind_param($multimediaStmt, 'i', $barId);
    mysqli_stmt_execute($multimediaStmt);
    $multimediaResult = mysqli_stmt_get_result($multimediaStmt);
    $multimedia = [];
    while ($row = mysqli_fetch_assoc($multimediaResult)) {
        $multimedia[] = $row;
    }

    // Fetch bar schedule
    $scheduleQuery = "SELECT 
                         id_horario,
                         dia_da_semana,
                         hora_abre,
                         hora_fecho
                      FROM 
                         horario
                      WHERE 
                         bar_id = ?";
    $scheduleStmt = mysqli_prepare($ligacao, $scheduleQuery);
    if (!$scheduleStmt) {
        sendError("Failed to prepare schedule query: " . mysqli_error($ligacao));
    }
    mysqli_stmt_bind_param($scheduleStmt, 'i', $barId);
    mysqli_stmt_execute($scheduleStmt);
    $scheduleResult = mysqli_stmt_get_result($scheduleStmt);
    $schedule = [];
    while ($row = mysqli_fetch_assoc($scheduleResult)) {
        $schedule[] = $row;
    }

    // Combine all data into a single array
    $barDetails = [
        'bar' => $bar,
        'reviews' => $reviews,
        'multimedia' => $multimedia,
        'schedule' => $schedule
    ];

    echo json_encode($barDetails);

    dbDisconnect($ligacao);
} else {
    sendError("Invalid request");
}
?>
