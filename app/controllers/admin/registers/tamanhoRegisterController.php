<?php
    require("../adminAuthentication.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("ERRO: Método de requisição inválido");
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_method");
        exit;
    }

    // Verificação dos campos do formulário (Vazios)
    if (empty($_POST['altura']) || empty($_POST['largura']) || empty($_POST['espessura'])) {
        error_log("ERRO: Campos obrigatórios em branco");
        header("Location: ../../view/admin/vidroRegister.php?error=empty_fields");
        exit;
    }

    // Verificação dos campos do formulário (Números válidos)
    if (!is_numeric($_POST['altura']) || $_POST['altura'] <= 0) {
        error_log("ERRO: Altura inválida");
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_altura");
        exit;
    }

    if (!is_numeric($_POST['largura']) || $_POST['largura'] <= 0) {
        error_log("ERRO: Largura inválida");
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_largura");
        exit;
    }

    if (!is_numeric($_POST['espessura']) || $_POST['espessura'] <= 0) {
        error_log("ERRO: Espessura inválida");
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_espessura");
        exit;
    }

    // Limpa os dados de entrada
    $altura = $mysqli->real_escape_string($_POST['altura']);
    $largura = $mysqli->real_escape_string($_POST['largura']);
    $espessura = $mysqli->real_escape_string($_POST['espessura']);

    // Verifica se o tamanho já existe
    $stmt = $mysqli->prepare("SELECT * FROM TAMANHO WHERE ALTURA = ? AND LARGURA = ? AND ESPESSURA = ?");
    $stmt->bind_param("iid", $altura, $largura, $espessura);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../../view/admin/vidroRegister.php?error=tamanho_exists");
        exit;
    }

    $result->free();
    $stmt->close();

    // Insere o novo tamanho no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO TAMANHO(ALTURA, LARGURA, ESPESSURA) VALUES(?, ?, ?)");
    $stmt->bind_param("iid", $altura, $largura, $espessura);
    if ($stmt->execute()) {
        header("Location: ../../view/admin/vidroRegister.php?success=tamanho_registered");
    } else {
        header("Location: ../../view/admin/vidroRegister.php?error=registration_failed");
    }

    $stmt->close();
?>