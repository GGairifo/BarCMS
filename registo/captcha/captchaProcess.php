<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <title>Image Processing</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <link rel="stylesheet" type="text/css" href="../Styles/GlobalStyle.css">
    </head>

    <body>
<?php

ini_set('display_errors', 'On');
	  
error_reporting(E_ALL);

session_start();

include_once( "xxx.php" );

@session_start();

header( 'Content-Type: text/html; charset=utf-8' );

if ($_SESSION['captchal'] == $_POST['captcha']) {
    echo "<h1>Ok - Code is correct</h1>";
}
else {
    echo "<h1>Error - Code is incorrect</h1>";
}
?>

    </body>
</html>