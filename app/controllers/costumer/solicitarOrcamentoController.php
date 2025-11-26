<?php
    // Requirements
    require("../protect.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../../view/noCostumerDashboard.php?error=invalid_method");
        exit;
    }

    $clientID = $_SESSION['clientID'];

    $stmt = $mysqli->prepare(
        "SELECT ID_ORCAMENTO FROM ORCAMENTOS
        WHERE ID_CLIENTE = ? AND STATUS = 'PENDENTE'
        ORDER BY DATA_CRIACAO DESC LIMIT 1"
    );

    if (!$stmt) {
        header("Location: ../../view/noCostumerDashboard.php?error=server_error");
        exit;
    }

    $stmt->bind_param("i", $clientID);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        // Já existe um orçamento pendente
        header("Location: ../../view/noCostumerDashboard.php?error=existing_orcamento");
        exit;
    }

    // Inserir novo orçamento
    $stmt = $mysqli->prepare(
        "INSERT INTO ORCAMENTOS (ID_CLIENTE) VALUES (?)"
    );
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    header("Location: ../../view/noCostumerDashboard.php?success=orcamento_created");
    exit;
?>