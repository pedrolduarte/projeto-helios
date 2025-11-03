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

    // Verifica se os campos de email e senha foram preenchidos
    if (empty($_POST['email']) || empty($_POST['password'])) {
        header("Location: ../../view/login.php?error=empty_fields");
        exit;
    }

    // Verificações referente ao email
    if (strlen($_POST['email']) == 0) {
        header("Location: ../../view/login.php?error=empty_email");
        exit;
    }

    // Verificações referente a senha
    if (strlen($_POST['password']) == 0) {
        header("Location: ../../view/login.php?error=empty_password");
        exit;
    }

    // Limpa os dados de entrada
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);

    // Query para buscar o usuário no banco de dados
    $stmt = $mysqli->prepare("SELECT * FROM conta WHERE email = ?");
    if (!$stmt) {
        header("Location: ../../view/login.php?error=server_error");
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $sql_query = $stmt->get_result();
    if (!$sql_query) {
        header("Location: ../../view/login.php?error=server_error");
        exit;
    }

    if ($sql_query->num_rows == 1){
        // Busca os dados do usuário
        $user = $sql_query->fetch_assoc();

        // Verifica se a senha está correta
        if (password_verify($password, $user['SENHA_HASH'])) {
            // Define as variáveis de sessão
            $_SESSION['accountID'] = $user['ID_CONTA'];
            $_SESSION['clientID'] = $user['ID_CLIENTE'];

            if ($user['isAdmin'] == 1) {
                // Redireciona para a área de administração
                header("Location: ../../view/admin.php");    
            } else {
                // Redireciona para o dashboard
                header("Location: ../../view/dashboard.php");
            }

            // Atualizar ultimo login
            $update_stmt = $mysqli->prepare("UPDATE conta SET ULTIMO_LOGIN = NOW() WHERE ID_CONTA = ?");
            $update_stmt->bind_param("i", $user['ID_CONTA']);
            $update_stmt->execute();
            $update_stmt->close();
            exit;
        } else {
            header("Location: ../../view/login.php?error=invalid_credentials");
            exit;
        }
    } else {
        header("Location: ../../view/login.php?error=invalid_credentials");
        exit;
    }

    $stmt->close();
?>