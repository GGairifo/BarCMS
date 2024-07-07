<?php
class Layout {
    public static function renderHeader() {
        echo "
        <header>
            <div class='logo'>
                <a href='../index.php'>
                    <img src='../login/imagens/logo.png' alt='Logo'>
                </a>
            </div>
            <div class='welcome'>Bem Vindo!</div>
            <div class='header-buttons'>
                <a href='logout.php' class='btn'>Logout</a>
            </div>
        </header>
        ";
    }

    public static function renderFooter() {
        echo "
        <footer>
            <!-- Aqui pode adicionar um rodapé, se necessário -->
        </footer>
        ";
    }
}
?>
