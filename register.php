<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Helios - Cadastro</title>
  <link rel="stylesheet" href="Css/login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <script src="Js/register.js" defer></script>
</head>
<body>

  <div class="login-container">
    <div class="login-box">
      <img src="img/Sun.png" alt="Logo Helios" class="logo">
      <h2>Crie sua Conta</h2>

      <!-- Indicador de progresso -->
      <div class="progress-indicator">
        <div class="progress-dot active" id="dot1"></div>
        <div class="progress-dot" id="dot2"></div>
      </div>

      <!-- Container do carrossel -->
      <div class="carousel-container">
        <!-- Etapa 1 -->
        <form id="step1" class="carousel-step active">
          <label for="nome">Nome Completo</label>
          <input type="text" id="nome" placeholder="Digite seu nome completo" required>

          <label for="email">E-mail</label>
          <input type="email" id="email" placeholder="Digite seu e-mail" required>

          <label for="senha">Senha</label>
          <input type="password" id="senha" placeholder="Crie uma senha" required>

          <button type="button" class="btn-primary" id="nextStep">Próximo</button>
        </form>

        <!-- Etapa 2 -->
        <form id="step2" class="carousel-step hidden">
          <label for="nascimento">Data de Nascimento</label>
          <input type="date" id="nascimento" required>

          <label for="endereco">Endereço Completo</label>
          <input type="text" id="endereco" placeholder="Digite seu endereço" required>

          <label for="cep">CEP</label>
          <input type="text" id="cidade" placeholder="Digite seu CEP" required>

          <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-secondary" id="prevStep">Voltar</button>
            <button type="submit" class="btn-primary">Finalizar Cadastro</button>
          </div>
        </form>
      </div>

      <p class="signup-text">Já tem uma conta? <a href="login.php">Faça login</a></p>
    </div>
  </div>

</body>
</html>
