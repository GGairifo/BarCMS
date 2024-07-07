<?php
    session_start();
    session_destroy();

    echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"2; URL=formLogin.php\">";
?>