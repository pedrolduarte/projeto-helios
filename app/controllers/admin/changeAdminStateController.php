<?php
    require("../admin/adminAuthentication.php");
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

    $clientId = $_POST['clientId'] ?? null;
    if (!$clientId) {
        http_response_code(400); // Requisição inválida
        echo json_encode([
            "error" => true,
            "message" => "ID do cliente não fornecido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $stmt = $mysqli->prepare(
            "UPDATE 
                CONTA
            SET
                IsAdmin = NOT IsAdmin
            WHERE 
                ID_CLIENTE = ?"
        );

        if (!$stmt) {
            throw new Exception("Erro no prepare: " . $mysqli->error);
        }

        $stmt->bind_param('i', $clientId);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao alterar estado de administrador: " . $stmt->error);
        }

        $stmt->close();
        echo json_encode([
            "error" => false,
            "message" => "Estado de administrador do cliente " . $clientId . " alterado com sucesso"
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro ao alterar estado de administrador: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
?>