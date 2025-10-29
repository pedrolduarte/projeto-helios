<?php
    require("../adminAuthentication.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=invalid_method");
        exit;
    }

    // Verificação dos campos do formulário (Vazios)
    if (empty($_POST['ipCaixaConexao']) || 
        empty($_POST['didCaixaConexao']) || 
        empty($_POST['espessuraCaboCaixaConexao']) ||
        empty($_POST['comprimentoCaixaConexao']) ||
        empty($_POST['tipoConexaoCaixaConexao'])) {

        error_log("ERRO: Campos obrigatórios em branco");
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=empty_fields");
        exit;
    }

    // Limpa os dados de entrada
    $ipCaixaConexao = $mysqli->real_escape_string($_POST['ipCaixaConexao']);
    $didCaixaConexao = $mysqli->real_escape_string($_POST['didCaixaConexao']);
    $espessuraCaboCaixaConexao = $mysqli->real_escape_string($_POST['espessuraCaboCaixaConexao']);
    $comprimentoCaixaConexao = $mysqli->real_escape_string($_POST['comprimentoCaixaConexao']);
    $tipoConexaoCaixaConexao = $mysqli->real_escape_string($_POST['tipoConexaoCaixaConexao']);

    if (!is_numeric($ipCaixaConexao)) {
        error_log("ERRO: IP da Caixa de Conexão inválido");
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=invalid_ip");
        exit;
    }

    if (!is_numeric($didCaixaConexao)) {
        error_log("ERRO: DID da Caixa de Conexão inválido");
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=invalid_did");
        exit;
    }

    if (!is_numeric($espessuraCaboCaixaConexao) || $espessuraCaboCaixaConexao <= 0) {
        error_log("ERRO: Espessura do Cabo inválida");
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=invalid_espessura_cabo");
        exit;
    }

    if (!is_numeric($comprimentoCaixaConexao) || $comprimentoCaixaConexao <= 0) {
        error_log("ERRO: Comprimento da Caixa de Conexão inválido");
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=invalid_comprimento");
        exit;
    }

    // Inserção no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO caixa_conexao (ip, did, espessura_cabo, comprimento, tipo_conexao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiids", $ipCaixaConexao, $didCaixaConexao, $espessuraCaboCaixaConexao, $comprimentoCaixaConexao, $tipoConexaoCaixaConexao);
    if ($stmt->execute()) {
        header("Location: ../../view/admin/caixaConexaoRegister.php?success=caixa_conexao_registered");
    } else {
        error_log("ERRO: Falha ao registrar Caixa de Conexão - " . $stmt->error);
        header("Location: ../../view/admin/caixaConexaoRegister.php?error=registration_failed");
    }

    $stmt->close();
?>