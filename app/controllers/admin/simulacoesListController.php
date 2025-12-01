<?php

    // Requirements
    require("../../config/connection.php");
    require("adminAuthentication.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Método não permitido
        echo json_encode([
            "error" => true,
            "message" => "Método de requisição inválido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $simulacoes = [];
    try {
        $stmt = $mysqli->prepare(
            "SELECT 
                ID_SIMULACAO, CONSUMO_MEDIO, TARIFA_MEDIA, COBERTURA, AREA_MINIMA, VALOR_APROXIMADO 
            FROM 
                SIMULACOES
            ORDER BY
                ID_SIMULACAO DESC"
        );

        if ($stmt === false) {
            throw new Exception("Erro no prepare: " . $mysqli->error);
        }

        if ($stmt->execute() === false) {
            throw new Exception("Erro na execução da query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $simulacoes[] = [
                "id" => (int)$row['ID_SIMULACAO'],
                "consumo" => (float)$row['CONSUMO_MEDIO'],
                "tarifa" => (float)$row['TARIFA_MEDIA'],
                "cobertura" => (float)$row['COBERTURA'],
                "area" => (float)$row['AREA_MINIMA'],
                "valor" => (float)$row['VALOR_APROXIMADO']
            ];
        }

        $stmt->close();
        echo json_encode([
            "error" => false,
            "data" => $simulacoes
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro ao buscar simulações: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
?>