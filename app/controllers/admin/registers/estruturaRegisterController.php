<?php
    require("../adminAuthentication.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("ERRO: Método de requisição inválido");
        header("Location: ../../view/admin/estruturaRegister.php?error=invalid_method");
        exit;
    }

    // Verificação dos campos do formulário (Vazios)
    if (empty($_POST['dscEstrutura'])) {
        error_log("ERRO: Campos obrigatórios em branco");
        header("Location: ../../view/admin/estruturaRegister.php?error=empty_fields");
        exit;
    }

    // Limpa os dados de entrada
    $dscEstrutura = $mysqli->real_escape_string($_POST['dscEstrutura']);

    // Verificação da descrição da estrutura
    if (strlen($dscEstrutura) < 5 || strlen($dscEstrutura) > 100) {
        header("Location: ../../view/admin/estruturaRegister.php?error=invalid_dscEstrutura");
        exit;
    }

    // Verifica se a estrutura já existe
    $stmt = $mysqli->prepare("SELECT * FROM ESTRUTURA WHERE DESCRICAO_ESTRUTURA = ?");
    $stmt->bind_param("s", $dscEstrutura);
    $stmt->execute();
    $sql_query = $stmt->get_result();
    if ($sql_query->num_rows > 0) {
        header("Location: ../../view/admin/estruturaRegister.php?error=estrutura_exists");
        exit;
    }

    $sql_query->free();
    $stmt->close();

    // Insere a nova estrutura no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO ESTRUTURA (DESCRICAO_ESTRUTURA) VALUES (?)");
    $stmt->bind_param("s", $dscEstrutura);
    if ($stmt->execute()) {
        header("Location: ../../view/admin/estruturaRegister.php?success=estrutura_registered");
    } else {
        header("Location: ../../view/admin/estruturaRegister.php?error=registration_failed");
    }

    $stmt->close();
?>