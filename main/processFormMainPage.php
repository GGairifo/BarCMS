<?php
require_once("../Lib/db.php");

function getAllUsers() {
    dbConnect(ConfigFile);
    mysqli_select_db($GLOBALS['ligacao'], $GLOBALS['configDataBase']->db);

    $query = "SELECT utilizador_id, user_name, password, email, tipo_utilizador, active FROM utilizador";
    $result = mysqli_query($GLOBALS['ligacao'], $query);

    if (!$result) {
        echo "Error: " . mysqli_error($GLOBALS['ligacao']);
        exit();
    }

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    dbDisconnect();

    return $users;
}

function getAllBars() {
    dbConnect(ConfigFile);
    mysqli_select_db($GLOBALS['ligacao'], $GLOBALS['configDataBase']->db);

    $query = "SELECT * FROM bar";
    $result = mysqli_query($GLOBALS['ligacao'], $query);
    if (!$result) {
        die('mysqli error: ' . mysqli_error($GLOBALS['ligacao']));
    }

    $bars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bars[] = $row;
    }

    dbDisconnect();
    return $bars;
}
?>