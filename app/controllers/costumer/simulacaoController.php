<?php
    // Iniciar buffer de saída
    ob_start();

    // Requirements
    require("../protect.php");
    require("../../config/connection.php");
    require("../../config/env.php");

    // Limpar qualquer output anterior
    ob_clean();

    // Definir cabeçalho para JSON 
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Método não permitido
        echo json_encode([
            "error" => true,
            "message" => "Método de requisição inválido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $consumoMedio = isset($_GET['consumoMedio']) ? (float)$_GET['consumoMedio'] : NULL;
        $tarifaMedia = isset($_GET['tarifaMedia']) ? (float)$_GET['tarifaMedia'] : NULL;
        $coberturaDesejada = isset($_GET['coberturaDesejada']) ? (float)$_GET['coberturaDesejada'] : NULL;

        if (is_null($consumoMedio) || is_null($tarifaMedia) || is_null($coberturaDesejada) ||
            $consumoMedio <= 0 || $tarifaMedia <= 0 || $coberturaDesejada <= 0 || $coberturaDesejada > 100) {
            http_response_code(400); // Requisição inválida
            echo json_encode([
                "error" => true,
                "message" => "Parâmetros inválidos ou ausentes"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Cálculos da simulação
        $QTD_Paineis = ceil(($consumoMedio * ($coberturaDesejada / 100)) / $_ENV['PRODUCAO_PAINEL']);
        $potenciaInstalada = $QTD_Paineis * ($_ENV['PRODUCAO_PAINEL'] / 30); // kWp, assumindo cada painel tem 330W
        $prodMensal = $_ENV['PRODUCAO_PAINEL'] * $QTD_Paineis;
        $areaMinima = $_ENV['AREA_BASE'] + ($QTD_Paineis * $_ENV['AREA_PAINEL']);
        $custoTotal = $_ENV['VALOR_BASE'] + ($QTD_Paineis * $_ENV['VALOR_PAINEL']);
        $economiaAnual = $prodMensal * $tarifaMedia * 12;
        $payback = ($custoTotal / $economiaAnual);

        // Inserindo Simulação banco de dados
        $clientID = $_SESSION['clientID'];
        $stmt = $mysqli->prepare(
            "INSERT INTO 
                SIMULACOES (ID_CLIENTE, CONSUMO_MEDIO, TARIFA_MEDIA, COBERTURA, AREA_MINIMA, VALOR_APROXIMADO)
            VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            http_response_code(500); // Erro interno do servidor
            echo json_encode([
                "error" => true,
                "message" => "Erro ao preparar consulta de inserção"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt->bind_param("iddddi", 
            $clientID, 
            $consumoMedio, 
            $tarifaMedia, 
            $coberturaDesejada, 
            $areaMinima, 
            $custoTotal
        );

        if (!$stmt->execute()) {
            http_response_code(500); // Erro interno do servidor
            echo json_encode([
                "error" => true,
                "message" => "Erro ao executar consulta de inserção"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $mysqli->commit();
        $stmt->close();

        // Resposta JSON
        echo json_encode([
            "error" => false,
            "data" => [
                "quantidade_paineis" => $QTD_Paineis,
                "potencia_instalada_kwp" => round($potenciaInstalada, 2),
                "producao_mensal_kwh" => round($prodMensal, 2),
                "area_minima_m2" => round($areaMinima, 1),
                "custo_total_r$" => round($custoTotal, 2),
                "economia_anual_r$" => round($economiaAnual, 2),
                "payback_anos" => round($payback, 2)
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Exception $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "[ERROR] " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
?>