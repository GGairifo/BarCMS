<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once("../../../Lib/db.php");

function sendError($message) {
    echo json_encode(["error" => $message]);
    exit();
}

function getDistrictFromPostalCode($address) {
    // Extract the postal code from the address
    preg_match('/\b\d{4}-\d{3}\b/', $address, $matches);
    if (!empty($matches)) {
        $postalCode = $matches[0];
        // Define the postal code ranges for each district
        $districtPostalCodes = [
            "Lisboa" => ["1000", "1999"],
            "Porto" => ["4000", "4999"],
            "Setúbal" => ["2900", "2999"],
            "Braga" => ["4700", "4799"],
            "Aveiro" => ["3800", "3899"],
            "Coimbra" => ["3000", "3099"],
            "Faro" => ["8000", "8999"],
            "Leiria" => ["2400", "2499"],
            "Santarém" => ["2000", "2099"],
            "Viseu" => ["3500", "3599"],
            "Viana do Castelo" => ["4900", "4999"],
            "Castelo Branco" => ["6000", "6099"],
            "Évora" => ["7000", "7099"],
            "Guarda" => ["6300", "6399"],
            "Beja" => ["7800", "7899"],
            "Bragança" => ["5300", "5399"],
            "Portalegre" => ["7300", "7399"],
            "Vila Real" => ["5000", "5099"],
            "Açores (Ponta Delgada)" => ["9500", "9599"],
            "Madeira (Funchal)" => ["9000", "9099"]
        ];

        // Extract the first four digits of the postal code
        $prefix = substr($postalCode, 0, 4);

        // Check each district's postal code range to determine the district
        foreach ($districtPostalCodes as $district => $range) {
            $min = $range[0];
            $max = $range[1];
            if ($prefix >= $min && $prefix <= $max) {
                return $district;
            }
        }
    }

    // Return "Unknown District" if no matching district is found
    return "Unknown District";
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $district = isset($_GET['district']) ? $_GET['district'] : '';
    $name = isset($_GET['name']) ? $_GET['name'] : '';

    dbConnect(ConfigFile);

    global $ligacao;
    global $configDataBase;

    if (!$ligacao) {
        sendError("Failed to connect to the database.");
    }

    mysqli_select_db($ligacao, $configDataBase->db);

    // Prepare the SQL query
    $query = "SELECT 
                b.bar_id,
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
              WHERE 1=1 ";

    $params = [];
    $types = "";

    if (!empty($district)) {
        // Check if the district is a postal code in the format "nnnn-nnn"
        $query .= "AND b.localizacao LIKE ? ";
        $params[] = "%$district%";
        $types .= "s";
    }

    if (!empty($name)) {
        // Check if the name contains the given string
        $query .= "AND b.nome LIKE ? ";
        $params[] = "%$name%";
        $types .= "s";
    }

    $query .= "GROUP BY b.bar_id";

    // Bind parameters and execute the query
    $stmt = mysqli_prepare($ligacao, $query);
    if (!empty($types)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        sendError("Error executing the query: " . mysqli_error($ligacao));
    }

    $bars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Determine district based on the address
        $row['district'] = getDistrictFromPostalCode($row['localizacao']);
        $bars[] = $row;
    }

    echo json_encode($bars);
}