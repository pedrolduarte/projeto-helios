<?php
    require("adminAuthentication.php");
    require("../../config/connection.php");
    
    // Define cabeçalho JSON
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método não permitido
        echo json_encode([
            "error" => true,
            "message" => "Método de requisição inválido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $orcamentoId = $_POST['orcamentoId'] ?? null;
    if (!$orcamentoId) {
        http_response_code(400); // Requisição inválida
        echo json_encode([
            "error" => true,
            "message" => "ID do orçamento não fornecido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $mysqli->prepare("UPDATE ORCAMENTOS SET STATUS = 'PENDENTE' WHERE ID_ORCAMENTO = ?");
    if (!$stmt) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro no prepare: " . $mysqli->error
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->bind_param('i', $orcamentoId);
    if (!$stmt->execute()) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro ao reabrir orçamento: " . $stmt->error
        ], JSON_UNESCAPED_UNICODE);
        $stmt->close();
        exit;
    }

    $stmt->close();
    echo json_encode([
        "error" => false,
        "message" => "Orçamento " . $orcamentoId . " reaberto com sucesso"
    ], JSON_UNESCAPED_UNICODE);
?>