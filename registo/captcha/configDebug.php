<?php
    $debug = false;

    if ( $debug==false ) {
        $captchaValue = @substr(md5(time()), 0, 9);
    }
    else {
      //$captchaValue = @substr(md5("abcdfeghi"), 0, 9);
      $captchaValue = "abcdefghi";
    }
?>
