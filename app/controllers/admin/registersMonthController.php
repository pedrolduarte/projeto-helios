<?php
    require("adminAuthentication.php"); // Comentado temporariamente para teste
    require("../../config/connection.php");

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

    $cadastrosMensais = [];
    try {
        $currentYear = date('Y');
        
        // Verificar se prepare foi bem-sucedido
        $stmt = $mysqli->prepare("
            SELECT 
                MONTH(DATA_CRIACAO) AS MES, COUNT(*) AS TOTAL 
            FROM 
                CONTA 
            WHERE 
                YEAR(DATA_CRIACAO) = ? 
            GROUP BY 
                MONTH(DATA_CRIACAO) 
            ORDER BY 
                MONTH(DATA_CRIACAO)"
        );
        
        if (!$stmt) {
            throw new Exception("Erro no prepare: " . $mysqli->error);
        }
        
        $stmt->bind_param('i', $currentYear);
        if (!$stmt->execute()) {
            throw new Exception("Erro na execução da query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $cadastrosMensais[] = [
                "mes" => getMonthName($row['MES']), // Usar o mês real da query
                "total" => (int)$row['TOTAL']
            ];
        } 

        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro ao buscar dados de cadastros mensais " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        "error" => false,
        "data" => $cadastrosMensais
    ], JSON_UNESCAPED_UNICODE);
?>