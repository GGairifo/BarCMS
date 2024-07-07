<?php
    if ( !isset($_SESSION) ) {
      session_start();
    }

    require_once( "../../Lib/lib.php" );
    require_once( "../../Lib/db.php" );

    $user = $_SESSION['username'];
    $id = $_SESSION['id'];
    $userRole = getRole($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <title>Authentication Using PHP</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <link rel="stylesheet" type="text/css" href="../../Styles/GlobalStyle.css">
    </head>
    
    <body>
        <div style="font-weight:bold;text-align:right"><?php echo "$user, $userRole";?></div>
        <hr>