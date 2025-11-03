<?php
    // Requirements
    require("../protect.php");
    require("../../config/connection.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../../view/dashboard.php?error=invalid_method");
        error_log("ERRO: Método de requisição inválido em consumoRegisterController.php");
        exit;
    }

    // Limpar os dados de entrada
    $anoConsumo = $mysqli->real_escape_string($_POST['ano_consumo']);
    $mesConsumo = $mysqli->real_escape_string($_POST['mes_consumo']);
    $consumo_kwh = $mysqli->real_escape_string($_POST['consumo_kwh']);

    // Verifica se os campos estão vazios
    if (empty($anoConsumo) || empty($mesConsumo) || empty($consumo_kwh)) {
        header("Location: ../../view/dashboard.php?error=empty_fields");
        exit;
    }

    if (!is_numeric($anoConsumo) || !is_numeric($mesConsumo) || !is_numeric($consumo_kwh)) {
        header("Location: ../../view/dashboard.php?error=invalid_input");
        exit;
    }

    $clientID = $_SESSION['clientID'];
    $stmt = $mysqli->prepare("SELECT * FROM CONSUMO WHERE ID_CLIENTE = ? AND ANO = ? AND MES = ?");
    $stmt->bind_param("iii", $clientID, $anoConsumo, $mesConsumo);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $insertStmt = $mysqli->prepare("INSERT INTO CONSUMO (ID_CLIENTE, ANO, MES, CONSUMO_KWH) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("iiii", $clientID, $anoConsumo, $mesConsumo, $consumo_kwh);
        if ($insertStmt->execute()) {
            header("Location: ../../view/dashboard.php?success=consumo_added");
            exit;
        } else {
            header("Location: ../../view/dashboard.php?error=server_error");
            error_log("ERRO: Falha ao inserir consumo no banco de dados em consumoRegisterController.php - " . $mysqli->error);
            exit;
        }
    } else {
        $updateStmt = $mysqli->prepare("UPDATE CONSUMO SET CONSUMO_KWH = ? WHERE ID_CLIENTE = ? AND ANO = ? AND MES = ?");
        $updateStmt->bind_param("iiii", $consumo_kwh, $clientID, $anoConsumo, $mesConsumo);
        if ($updateStmt->execute()) {
            header("Location: ../../view/dashboard.php?success=consumo_updated");
            exit;
        } else {
            header("Location: ../../view/dashboard.php?error=server_error");
            error_log("ERRO: Falha ao atualizar consumo no banco de dados em consumoRegisterController.php - " . $mysqli->error);
            exit;
        }
    }
?>