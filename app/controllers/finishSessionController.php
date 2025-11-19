<?php

    if (!isset($_SESSION)){
        session_start();
    }

    // Destrói todas as variáveis de sessão
    session_destroy();
    
    // Redireciona para o index
    header("Location: ../../public/index.php");
?>