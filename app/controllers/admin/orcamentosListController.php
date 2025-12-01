<?php
    require("adminAuthentication.php");
    require("../../config/connection.php");

    // Define cabeçalho JSON
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Método não permitido
        echo json_encode([
            "error" => true,
            "message" => "Método de requisição inválido"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    function getEnderecoName($enderecoCep, $enderecoNumber) {
        $cleanCep = preg_replace('/\D/', '', $enderecoCep);
        $url = "https://viacep.com.br/ws/{$cleanCep}/json/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data['logradouro'])) {
            return $data['logradouro'] . ", " . $enderecoNumber . ", " . $data['localidade'];
        } else {
            return "Endereço não encontrado";
        }
    }

    $orcamentosList = [];
    try {
        $stmt = $mysqli->prepare("
            SELECT
                ORC.ID_ORCAMENTO, CLI.NOME_CLIENTE, END.CEP AS ENDERECO_CEP, END.NUMERO AS ENDERECO_NUMERO, ORC.STATUS
            FROM 
                ORCAMENTOS ORC
                INNER JOIN CLIENTE CLI ON ORC.ID_CLIENTE = CLI.ID_CLIENTE
                LEFT JOIN CLIENTE_ENDERECO END ON CLI.ID_CLIENTE = END.ID_CLIENTE
            ORDER BY 
                ORC.ID_ORCAMENTO DESC
        ");

        if (!$stmt) {
            throw new Exception("Erro no prepare: " . $mysqli->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro na execução da query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orcamentosList[] = [
                "idOrcamento" => (int)$row['ID_ORCAMENTO'],
                "nomeCliente" => $row['NOME_CLIENTE'],
                "endereco" => getEnderecoName($row['ENDERECO_CEP'], $row['ENDERECO_NUMERO']),
                "status" => $row['STATUS']
            ];
        }

        $stmt->close();
        echo json_encode([
            "error" => false,
            "orcamentos" => $orcamentosList
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro ao buscar orçamentos: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
?>  