<?php
    
    // Conexão com o banco de dados - usando caminho absoluto
    require(__DIR__ . "/../../config/connection.php");

    // Função para destruir a sessão do administrador
    function destroyAdminSession() {
        // Remove todas as variáveis de sessão relacionadas ao administrador
        unset($_SESSION['accountID']);
        unset($_SESSION['clientID']);
        session_destroy();
        header("Location: ../view/login.php");
        exit;
    }

    if (!isset($_SESSION)) {
        session_start();
    }

    // Verifica se a sessão do administrador está ativa
    if (!isset($_SESSION['accountID']) || empty($_SESSION['accountID'])) {
        destroyAdminSession();
    }
    
    if (!isset($_SESSION['clientID']) || empty($_SESSION['clientID'])) {
        destroyAdminSession();
    }

    // Verifica se o usuário é um administrador
    $accountID = $_SESSION['accountID'];
    $stmt = $mysqli->prepare("SELECT ID_CONTA FROM CONTA WHERE ID_CONTA = ? AND IsAdmin = 1");
    $stmt->bind_param("i", $accountID);
   
    if (!$stmt->execute()) {
        destroyAdminSession();
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        destroyAdminSession();
    }

    $result->free();
    $stmt->close();
?>