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

    $clientesList = [];
    try {
        $stmt = $mysqli->prepare("
            SELECT 
                CLI.*, CON.EMAIL, CON.TELEFONE, CE.CEP AS ENDERECO_CEP, CE.NUMERO AS ENDERECO_NUMERO
            FROM 
                CLIENTE CLI
                LEFT JOIN CONTA CON ON CLI.ID_CLIENTE = CON.ID_CLIENTE
                LEFT JOIN CLIENTE_ENDERECO CE ON CLI.ID_CLIENTE = CE.ID_CLIENTE
        ");

        if (!$stmt) {
            throw new Exception("Erro no prepare: " . $mysqli->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro na execução da query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $clientesList[] = [
                "idCliente" => (int)$row['ID_CLIENTE'],
                "nomeCliente" => $row['NOME_CLIENTE'],
                "cpfCnpj" => $row['CPF_CNPJ'],
                "email" => $row['EMAIL'],
                "telefone" => $row['TELEFONE'],
                "enderecoCep" => $row['ENDERECO_CEP'],
                "enderecoNumero" => $row['ENDERECO_NUMERO'],
                "dataNascimento" => $row['DATA_NASCIMENTO']
            ];
        }

        $stmt->close();
        echo json_encode([
            "error" => false,
            "clientes" => $clientesList
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode([
            "error" => true,
            "message" => "Erro ao buscar clientes: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
?>