<?php

$flags[] = FILTER_NULL_ON_FAILURE;
    
$uri = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL, $flags);
    
if ( !isset( $_SESSION ) ) {
    session_start();
}

if ( !isset( $_SESSION[ 'username' ] ) ) {
    $_SESSION['locationAfterAuth'] = $uri;

    header( "Location: formLogin.php" );
    exit;
}
?>