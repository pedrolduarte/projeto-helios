<?php
    // Conexão com o banco de dados
    include("../config/connection.php");

    // Inicia a sessão se não estiver iniciada
    if (!isset($_SESSION)) {
        session_start();
    }

    // Verifica se o método de requisição é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/login.php?error=invalid_method");
        exit;
    }

    // Funções úteis
    function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) return false;
        
        // Verifica se não são todos iguais (111.111.111-11, etc.)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
        
        // Valida dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        
        return true;
    }

    // Verificação dos campos do formulário (Vazios)
    if (empty($_POST['completeName']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['birthDate']) || empty($_POST['cep']) || empty($_POST['adressNumber']) || empty($_POST['phone']) || empty($_POST['cpf'])) {
        error_log("ERRO: Campos obrigatórios em branco");
        header("Location: ../view/register.php?error=empty_fields");
        exit;
    }

    // Limpa os dados de entrada
    $completeName = $mysqli->real_escape_string($_POST['completeName']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $cpf = $mysqli->real_escape_string($_POST['cpf']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $birthDate = $mysqli->real_escape_string($_POST['birthDate']);
    $cep = $mysqli->real_escape_string($_POST['cep']);
    $adressNumber = $mysqli->real_escape_string($_POST['adressNumber']);
    $phone = $mysqli->real_escape_string($_POST['phone']);

    // Verificação do nome completo
    if (strlen($completeName) <= 5) {
        header("Location: ../view/register.php?error=invalid_name");
        exit;
    }

    // Verificação se email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../view/register.php?error=invalid_email");
        exit;
    }

    // Verificação do CPF
    if (!validarCPF($cpf)) {
        header("Location: ../view/register.php?error=invalid_cpf");
        exit;
    }

    // Verificação da senha (mínimo 6 caracteres)
    if (strlen($password) < 6) {
        header("Location: ../view/register.php?error=weak_password");
        exit;
    }

    // Verificação da data de nascimento (idade mínima 18 anos)
    $birthTimestamp = strtotime($birthDate);
    if ($birthTimestamp === false) {
        header("Location: ../view/register.php?error=invalid_birthdate");
        exit;
    }

    $age = (int)((time() - $birthTimestamp) / (365.25 * 24 * 60 * 60));
    if ($age < 18) {
        header("Location: ../view/register.php?error=underage");
        exit;
    }

    // Verificação do CEP (apenas números, 8 dígitos)
    if (!preg_match("/^[0-9]{8}$/", preg_replace('/[^0-9]/', '', $cep))) {
        header("Location: ../view/register.php?error=invalid_cep");
        exit;
    }

    // Verificação do número da residência (apenas números)
    if (!preg_match("/^[0-9]+$/", $adressNumber)) {
        header("Location: ../view/register.php?error=invalid_adress_number");
        exit;
    }

    // Verificação do telefone (apenas números, entre 10 e 11 dígitos)
    if (!preg_match("/^[0-9]{10,11}$/", preg_replace('/[^0-9]/', '', $phone))) {
        header("Location: ../view/register.php?error=invalid_phone");
        exit;
    }

    // Verifica se o email já está cadastrado
    $stmt = $mysqli->prepare("SELECT * FROM CONTA WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        header("Location: ../view/register.php?error=server_error");
        exit;
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../view/register.php?error=email_taken");
        exit;
    }

    // Verifica se o CPF já está cadastrado
    $stmt = $mysqli->prepare("SELECT * FROM CLIENTE WHERE CPF_CNPJ = ?");
    $stmt->bind_param("s", $cpf);
    if (!$stmt->execute()) {
        header("Location: ../view/register.php?error=server_error");
        exit;
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../view/register.php?error=cpf_taken");
        exit;
    }

    // Hash da senha
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    if ($hashedPassword === false) {
        header("Location: ../view/register.php?error=server_error");
        exit;
    }

    // Inicia a transação (todos os dados inseridos ou nenhum)
    $mysqli->begin_transaction();
    try {
        // Insert do cliente
        $costumerStmt = $mysqli->prepare("INSERT INTO CLIENTE (CPF_CNPJ, NOME_CLIENTE, DATA_NASCIMENTO) VALUES(?, ?, ?)");
        $formattedDate = date('Y-m-d', $birthTimestamp);
        $costumerStmt->bind_param("sss", $cpf, $completeName, $formattedDate);
        if (!$costumerStmt->execute()) {
            throw new Exception("Falha ao inserir cliente");
        }

        // Pega o ID do cliente recém inserido
        $costumerID = $costumerStmt->insert_id;
        $costumerStmt->close();

        // Insert do endereço (comentado temporariamente para inserir os dados necessarios primeiro)
        $costumerAdressStmt = $mysqli->prepare("INSERT INTO CLIENTE_ENDERECO(ID_CLIENTE, CEP, NUMERO) VALUES(?, ?, ?)");
        $costumerAdressStmt->bind_param("iss", $costumerID, $cep, $adressNumber);
        if (!$costumerAdressStmt->execute()) {
            throw new Exception("Falha ao inserir endereço do cliente" . $mysqli->error);
        }
        $costumerAdressStmt->close();

        // Insert da conta
        $accountStmt = $mysqli->prepare("INSERT INTO CONTA(ID_CLIENTE, EMAIL, SENHA_HASH, TELEFONE) VALUES(?, ?, ?, ?)");
        $accountStmt->bind_param("isss", $costumerID, $email, $hashedPassword, $phone);
        if (!$accountStmt->execute()) {
            throw new Exception("Falha ao inserir conta");
        }

        $accountID = $accountStmt->insert_id;
        $accountStmt->close();
        
    } catch (Exception $e) {
        $mysqli->rollback(); // Efetua o rollback em caso de erro
        // echo "Erro: Transação falhou - " . $e->getMessage();
        header("Location: ../view/register.php?error=server_error");
        exit;
    }

    // COMMIT fica FORA do try/catch (após sucesso)
    $mysqli->commit();
    
    // Redireciona para login com sucesso ou dashboard logado
    $_SESSION['accountID'] = $accountID;
    $_SESSION['clientID'] = $costumerID;
    header("Location: ../view/dashboard.php");
    exit;
?>

