<?php

    // Conexão com o banco de dados
    require("../../config/connection.php");

    // Inicia a sessão se não estiver iniciada
    if (!isset($_SESSION)) {
        session_start();
    }

    // Verifica se o método de requisição é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../../view/login.php?error=invalid_method");
        exit;
    }

    if (empty($_POST['token']) || empty($_POST['new_password'])) {
        header("Location: ../../view/recuperarsenha.php?error=empty_fields");
        exit;
    }

    // Limpa os dados de entrada
    $token = $mysqli->real_escape_string($_POST['token']);
    $newPassword = $mysqli->real_escape_string($_POST['new_password']);

    // Hash do token
    $secret = 'test_secret_for_dev_only';
    $tokenHash = hash_hmac('sha256', $token, $secret);

    // Verifica se o token é valido
    $stmt = $mysqli->prepare("SELECT ID_CONTA FROM RECUPERACAO_SENHA WHERE TOKEN_HASH = ? AND USADO = 0 AND DATA_EXPIRACAO > NOW()");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($result->num_rows === 0) {
        header("Location: ../../view/recuperarsenha.php?error=invalid_token");
        exit;
    }

    $accountID = $row['ID_CONTA'];
    $result->free();
    $stmt->close();

    // Hash da nova senha
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    // Inicia transação (ambas operações ou nenhuma)
    $mysqli->begin_transaction();
    try {
        // Atualiza a senha na tabela CONTA
        $stmt = $mysqli->prepare("UPDATE CONTA SET SENHA_HASH = ? WHERE ID_CONTA = ?");
        $stmt->bind_param("si", $newPasswordHash, $accountID);
        if (!$stmt->execute()) {
            throw new Exception("Falha ao atualizar senha");
        }
        $stmt->close();

        // Marca o token como usado
        $stmt = $mysqli->prepare("UPDATE RECUPERACAO_SENHA SET USADO = 1 WHERE TOKEN_HASH = ?");
        $stmt->bind_param("s", $tokenHash);
        if (!$stmt->execute()) {
            throw new Exception("Falha ao invalidar token");
        }
        $stmt->close();
    } catch (Exception $e) {
        // Rollback em caso de erro
        $mysqli->rollback();
        header("Location: ../../view/login.php?error=server_error");
        exit;
    }
    
    $mysqli->commit();

    // Redireciona para a página de login com sucesso
    header("Location: ../../view/login.php?success=password_updated");
    exit;
