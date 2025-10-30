<?php

    // Conexão com o .env
    require('env.php');

    $usuario = $_ENV['DB_USER'];
    $senha = $_ENV['DB_PASSWORD'];
    $database = $_ENV['DB_NAME'];
    $host = $_ENV['DB_HOST'];
    $mysqli = new mysqli($host, $usuario, $senha, $database);

    if ($mysqli->error) {
        die("Falha ao conectar ao banco de dados: " . $mysqli->error);
    }
?>