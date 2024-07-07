<?php
    include_once( "ensureAuth.php" );
    include_once( "header.php" );
?>


        <p>Verifique o seu mail para que a sua conta seja validad!!!</p>
<?php
    @session_start();
    
    include( "./files/xpto.php" );
    echo "\t\t$myVar\n\t\t<br>\n";

    include_once( "session.inc" );
    include_once( "footer.php" );
?>