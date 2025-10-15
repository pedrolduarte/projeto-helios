<?php
    if(!isset($_SESSION)){
        session_start();
    }

    if(!isset($_SESSION["accountID"])){
        die("Você não pode acessar esta página. <p><a href=\"login.php\"> Entrar </a> </p>");
    }

    if(!isset($_SESSION["clientID"])){
        die("Você não pode acessar esta página. <p><a href=\"login.php\"> Entrar </a> </p>");
    }
?>