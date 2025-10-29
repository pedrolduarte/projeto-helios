<?php
    
    // Conexão com o banco de dados
    require("../../config/connection.php");
    
    // Inicia a sessão se não estiver iniciada
    if (!isset($_SESSION)) {
        session_start();
    }

    // Verifica se o método de requisição é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../../view/login.php?error=invalid_method");
        exit;
    }

    if (empty($_POST['email'])) {
        header("Location: ../../view/recoverAcc.php?error=empty_email");
        exit;
    }

    // Funções úteis
    function sendEmail($to, $subject, $message, $headers) {
        return mail($to, $subject, $message, $headers);
    }

    // Limpa os dados de entrada
    $email = $mysqli->real_escape_string($_POST['email']);

    // Verifica se o email existe no banco de dados
    $stmt = $mysqli->prepare("SELECT ID_CONTA FROM CONTA WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header("Location: ../../view/recuperarsenha.php?error=email_not_found");
        exit;
    }

    $row = $result->fetch_assoc();
    $accountID = $row['ID_CONTA'];
    $result->free();
    $stmt->close();

    // Verifica se já existe um token ativo para esse email
    $stmt = $mysqli->prepare("SELECT TOKEN_HASH, DATA_EXPIRACAO, USADO FROM RECUPERACAO_SENHA WHERE ID_CONTA = ? AND USADO = 0 AND DATA_EXPIRACAO > NOW()");
    $stmt->bind_param("i", $accountID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Já existe um token ativo, vai retornar para a pagina de recuperação e vai encaminhar para a aba de colocar o token
        header("Location: ../../view/recuperarsenha.php?error=token_active");
        exit;
    }

    $stmt->close();

    // Gera um token de 6 digitos
    $token = bin2hex(random_bytes(3)); // 6 caracteres hexadecimais

    // Hash do token
    $secret = 'helios_sistema_solar_secret_key_2025_ultra_secure!@#$%';
    $tokenHash = hash_hmac('sha256', $token, $secret);

    // Define o tempo de expiração do token
    $dataCriacao = date("Y-m-d H:i:s");
    $dataExpiracao = date("Y-m-d H:i:s", strtotime("+" . (getenv('TOKEN_EXPIRATION_HOURS') ?: 1) . " hours"));

    // Insere o token no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO RECUPERACAO_SENHA (ID_CONTA, TOKEN_HASH, DATA_CRIACAO, DATA_EXPIRACAO) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $accountID, $tokenHash, $dataCriacao, $dataExpiracao);
    if (!$stmt->execute()) {
        header("Location: ../../view/login.php?error=server_error");
        exit;
    }

    $stmt->close();

    // Envia o email com o token
    $to = $email;
    $subject = "🔐 Recuperação de Senha - Sistema Helios";
    
    // Carrega template do email e substitui o token
    $templatePath = "../../templates/email_recuperacao.html";
    $message = file_get_contents($templatePath);
    $message = str_replace('{{TOKEN}}', $token, $message);
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Sistema Helios <noreply@helios.com>\r\n";
    $mail = sendEmail($to, $subject, $message, $headers);
    if (!$mail) {
        header("Location: ../../view/recuperarsenha.php?error=email_failed");
        exit;
    }

    // Redireciona para a página de recuperação de senha com sucesso
    header("Location: ../../view/recuperarsenha.php?success=token_sent");
    exit;
?>
