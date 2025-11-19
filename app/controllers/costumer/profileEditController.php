<?php
    // Requirements
    require("../protect.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../../view/dashboard.php?error=invalid_method");
        error_log("ERRO: Método de requisição inválido em consumoRegisterController.php");
        exit;
    }

    if (empty($_POST['nome_completo']) || empty($_POST['email']) || empty($_POST['telefone']) || empty($_POST['cep']) || empty($_POST['numero'])) {
        header("Location: ../../view/dashboard.php?error=empty_fields");
        exit;
    }

    // Limpar os dados de entrada
    $nomeCompleto = $mysqli->real_escape_string($_POST['nome_completo']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $telefone = $mysqli->real_escape_string($_POST['telefone']);
    $cep = $mysqli->real_escape_string($_POST['cep']);
    $numero = $mysqli->real_escape_string($_POST['numero']);

    $clientID = $_SESSION['clientID'];
    $mysqli->begin_transaction();
    try {
        $stmt = $mysqli->prepare("UPDATE CLIENTE SET NOME_CLIENTE = ? WHERE ID_CLIENTE = ?");
        $stmt->bind_param("si", $nomeCompleto, $clientID);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("UPDATE CONTA SET EMAIL = ?, TELEFONE = ? WHERE ID_CLIENTE = ?");
        $stmt->bind_param("ssi", $email, $telefone, $clientID);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("UPDATE CLIENTE_ENDERECO SET CEP = ?, NUMERO = ? WHERE ID_CLIENTE = ?");
        $stmt->bind_param("ssi", $cep, $numero, $clientID);
        $stmt->execute();
        $stmt->close();

        $mysqli->commit();
        header("Location: ../../view/dashboard.php?success=profile_updated");
    } catch (Exception $e) {
        $mysqli->rollback();
        header("Location: ../../view/dashboard.php?error=server_error");
        error_log("ERRO: Falha ao atualizar perfil do cliente em profileEditController.php - " . $e->getMessage());
        exit;
    }
?>