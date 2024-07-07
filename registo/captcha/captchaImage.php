<?php

include_once( "config.php" );
include_once( "configDebug.php" );

ini_set('display_errors', 'On');
	  
error_reporting(E_ALL);

session_start();

@session_start();

$_SESSION[ 'captchal' ] = $captchaValue;

$imageCaptcha = ImageCreateFromPNG( "images/fundocaptch.png" );

$colorCaptchaRed = ImageColorAllocate($imageCaptcha, 255, 0, 0);
$colorCaptchaBlue = ImageColorAllocate($imageCaptcha, 0, 0, 255);

$fontName = "DejaVuSans-Bold.ttf";
//$fontName = "DejaVuSans.ttf";
//$fontName = "DejaVuSansMono-Bold.ttf";
//$fontName = "DejaVuSansMono.ttf";
//$fontName = "DejaVuSerif-Bold.ttf";
//$fontName = "DejaVuSerif.ttf";

//$fontName = "Vera.ttf";
//$fontName = "VeraBd.ttf";
//$fontName = "VeraBI.ttf";
//$fontName = "VeraIt.ttf";
//$fontName = "VeraMoBd.ttf";
//$fontName = "VeraMoBI.ttf";
//$fontName = "VeraMoIt.ttf";
//$fontName = "VeraMono.ttf";
//$fontName = "VeraSe.ttf";
//$fontName = "VeraSeBd.ttf";

$fontCaptcha = $fontsDirectory . $fontName;

$code1 = substr($captchaValue, 0, 4);
$code2 = substr($captchaValue, 4, 9);

/*
imagettftext(
    $imageCaptcha,          // Image
    20,                     // Font size
    -5,                     // Font angle
    40,                     // X position
    30,                     // Y position
    $colorCaptchaRed,       // Font color
    $fontCaptcha,           // Font type
    $captchaValue           // Text to write
    );
*/

@ImageTTFText(
        $imageCaptcha,      // Image
        20,                 // Font size
        -5,                 // Font angle
        40,                 // X position
        30,                 // Y position
        $colorCaptchaRed,   // Font color
        $fontCaptcha,       // Font type
        $code1              // Text to write
);

@ImageTTFText(
        $imageCaptcha,      // Image
        20,                 // Font size
        5,                  // Font angle
        120,                // X position
        30,                 // Y position
        $colorCaptchaBlue,  // Font color
        $fontCaptcha,       // Font type
        $code2              // Text to write
);

/*
  $fontCaptcha = 4;

  ImageString(
  $imageCaptcha,
  $fontCaptcha,
  15,
  15,
  $codeCaptcha,
  $corCaptcha);
 */

header( "Content-type: image/png" );

ImagePNG( $imageCaptcha /*, "nome do ficheiro de output"*/  );

ImageDestroy( $imageCaptcha );
?>
