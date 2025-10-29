<?php
    require("../adminAuthentication.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("ERRO: Método de requisição inválido");
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_method");
        exit;
    }

    // Verificação dos campos do formulário (Vazios)
    if (empty($_POST['tipoVidro']) || empty($_POST['espessuraVidro'])) {
        error_log("ERRO: Campos obrigatórios em branco");
        header("Location: ../../view/admin/vidroRegister.php?error=empty_fields");
        exit;
    }

    // Limpa os dados de entrada
    $tipoVidro = $mysqli->real_escape_string($_POST['tipoVidro']);
    $espessuraVidro = $mysqli->real_escape_string($_POST['espessuraVidro']);

    // Verificação do tipo de vidro
    if (strlen($tipoVidro) <= 3 or strlen($tipoVidro) > 50) {
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_tipoVidro");
        exit;
    }

    // Verificação da espessura do vidro
    if (!is_numeric($espessuraVidro) || $espessuraVidro <= 0 || $espessuraVidro > 100) {
        header("Location: ../../view/admin/vidroRegister.php?error=invalid_espessuraVidro");
        exit;
    }

    // Verifica se o vidro já existe
    $stmt = $mysqli->prepare("SELECT * FROM VIDRO WHERE TIPO_VIDRO = ? AND ESPESSURA_VIDRO = ?");
    $stmt->bind_param("sd", $tipoVidro, $espessuraVidro);
    $stmt->execute();
    $sql_query = $stmt->get_result();
    if ($sql_query->num_rows > 0) {
        header("Location: ../../view/admin/vidroRegister.php?error=vidro_exists");
        exit;
    }

    $sql_query->free();
    $stmt->close();

    // Insere o novo vidro no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO VIDRO (TIPO_VIDRO, ESPESSURA_VIDRO) VALUES (?, ?)");
    $stmt->bind_param("sd", $tipoVidro, $espessuraVidro);
    if ($stmt->execute()) {
        header("Location: ../../view/admin/vidroRegister.php?success=vidro_registered");
    } else {
        header("Location: ../../view/admin/vidroRegister.php?error=registration_failed");
    }

    $stmt->close();
    $mysqli->close();
?>