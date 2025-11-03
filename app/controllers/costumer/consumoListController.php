<?php
    // Evitar qualquer output acidental
    ob_start();
    
    // Requirements
    require("../protect.php");
    require("../../config/connection.php");
    
    // Limpar qualquer output anterior
    ob_clean();
    
    // Define o cabeçalho para JSON
    header('Content-Type: application/json; charset=utf-8');

    // Funções úteis
    function getMonthName($monthNumber) {
        $months = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];
        return $months[$monthNumber] ?? 'Mês inválido';
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Método não permitido
        echo json_encode([
            "error" => true,
            "message" => "Método de requisição inválido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $clientID = $_SESSION['clientID'];
        if (!isset($clientID)) {
            http_response_code(401); // Não autorizado
            echo json_encode([
                "error" => true,
                "message" => "Usuário não autenticado"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');
        $actualYear = (int)date('Y');
        if ($ano < 2000 || $ano > $actualYear) {
            http_response_code(400); // Requisição inválida
            echo json_encode([
                "error" => true,
                "message" => "Ano inválido"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Query para buscar dados do consumo (estrutura correta da tabela)
        $stmt = $mysqli->prepare(
            "SELECT 
                MES as mes,
                CONSUMO_KWH as consumo,
                (CONSUMO_KWH * 0.71) as economia
            FROM CONSUMO 
            WHERE ID_CLIENTE = ? AND ANO = ?
            ORDER BY MES"
        );
        
        // Verificar se o prepare funcionou
        if (!$stmt) {
            throw new Exception("Erro na query SQL: " . $mysqli->error);
        }
        
        $stmt->bind_param("ii", $clientID, $ano);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = [
                "mes" => getMonthName($row['mes']),
                "mes_numero" => (int)$row['mes'],
                "consumo" => (float)$row['consumo'],
                "economia" => round((float)$row['economia'], 2)
            ];
        }

        $result->free();
        $stmt->close();

        error_log("Dados encontrados para ano $ano, cliente $clientID: " . count($dados) . " registros");
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        // Log do erro
        error_log("Erro no consumoListController: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "error" => true,
            "message" => "Erro ao buscar dados de consumo: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
?>