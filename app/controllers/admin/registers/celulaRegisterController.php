<?php
    require("../adminAuthentication.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("ERRO: Método de requisição inválido");
        header("Location: ../../view/admin/celulaRegister.php?error=invalid_method");
        exit;
    }

    // Verificação dos campos do formulário (Vazios)
    if (empty($_POST['dscCelula'])) {
        error_log("ERRO: Campos obrigatórios em branco");
        header("Location: ../../view/admin/celulaRegister.php?error=empty_fields");
        exit;
    }

    // Limpa os dados de entrada
    $dscCelula = $mysqli->real_escape_string($_POST['dscCelula']);

    // Verificação da descrição da célula
    if (strlen($dscCelula) <= 3 or strlen($dscCelula) > 100) {
        header("Location: ../../view/admin/celulaRegister.php?error=invalid_dscCelula");
        exit;
    }

    // Verifica se a célula já existe
    $stmt = $mysqli->prepare("SELECT * FROM CELULA WHERE DSC_CELULA = ?");
    $stmt->bind_param("s", $dscCelula);
    $stmt->execute();
    $sql_query = $stmt->get_result();
    if ($sql_query->num_rows > 0) {
        header("Location: ../../view/admin/celulaRegister.php?error=celula_exists");
        exit;
    }

    $sql_query->free();
    $stmt->close();

    // Insere a nova célula no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO CELULA (DSC_CELULA) VALUES (?)");
    $stmt->bind_param("s", $dscCelula);
    if ($stmt->execute()) {
        header("Location: ../../view/admin/celulaRegister.php?success=celula_registered");
    } else {
        header("Location: ../../view/admin/celulaRegister.php?error=registration_failed");
    }
    
    $stmt->close();
?>