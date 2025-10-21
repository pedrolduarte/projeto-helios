<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Helios - Cadastro</title>
  <link rel="stylesheet" href="../../public/assets/css/login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <script src="../../public/assets/js/register.js" defer></script>
</head>
<body>

  <div class="login-container">
    <div class="login-box">
      <img src="../../public/assets/img/Sun.png" alt="Logo Helios" class="logo">
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
          <input type="text" id="nome" placeholder="Digite seu nome completo" name="completeName" required>

          <label for="email">E-mail</label>
          <input type="email" id="email" placeholder="Digite seu e-mail" name="email" required>

          <label for="cpf">CPF</label>
          <input type="text" id="cpf" placeholder="Digite seu CPF" name="cpf" required>

          <label for="senha">Senha</label>
          <input type="password" id="senha" placeholder="Crie uma senha" name="password" required>

          <button type="button" class="btn-primary" id="nextStep">Próximo</button>
        </form>

        <!-- Etapa 2 -->
        <form id="step2" class="carousel-step hidden">
          <label for="nascimento">Data de Nascimento</label>
          <input type="date" id="nascimento" name="birthDate" required>

          <label for="cep">CEP</label>
          <input type="text" id="cep" placeholder="Digite seu CEP" name="cep" required>

          <label for="numero">Número da Residência</label>
          <input type="text" id="numero" placeholder="Digite o número da residência" name="adressNumber" required>

          <label for="telefone">Número de Telefone</label>
          <input type="tel" id="telefone" placeholder="Digite seu telefone" name="phone" required>

          <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-secondary" id="prevStep">Voltar</button>
            <button type="submit" class="btn-primary">Finalizar Cadastro</button>
          </div>
        </form>
      </div>

      <p class="signup-text">Já tem uma conta? <a href="login.php">Faça login</a></p>
    </div>
  </div>

  <!-- Notificação de erro -->
  <?php if(isset($_GET['error'])): ?>
    <div class="notification error-notification" id="errorNotification">
      <div class="notification-content">
        <i class="fas fa-exclamation-circle"></i>
        <span class="notification-message">
          <?php
            switch($_GET['error']) {
              case 'invalid_name':
                echo 'Nome inválido! Deve ter mais de 5 caracteres.';
                break;
              case 'invalid_email':
                echo 'E-mail inválido! Por favor, insira um e-mail válido.';
                break;
              case 'invalid_cpf':
                echo 'CPF inválido! Verifique o número e tente novamente.';
                break;
              case 'weak_password':
                echo 'Senha fraca! Deve ter no mínimo 6 caracteres.';
                break;
              case 'invalid_birthdate':
                echo 'Data de nascimento inválida!';
                break;
              case 'underage':
                echo 'Você deve ter pelo menos 18 anos para se cadastrar.';
                break;
              case 'invalid_cep':
                echo 'CEP inválido! Deve conter 8 dígitos numéricos.';
                break;
              case 'invalid_adress_number':
                echo 'Número da residência inválido! Deve conter apenas números.';
                break;
              case 'invalid_phone':
                echo 'Telefone inválido! Deve conter entre 10 e 11 dígitos numéricos.';
                break;
              case 'server_error':
                echo 'Erro interno do servidor. Tente novamente mais tarde.';
                break;
              case 'email_taken':
                echo 'E-mail já cadastrado! Tente outro e-mail.';
                break;
              case 'empty_fields':
                echo 'Preencha todos os campos!';
                break;
              case 'invalid_method':
                echo 'Método de requisição inválido!';
                break;
              case 'cpf_taken':
                echo 'CPF já cadastrado! Tente outro CPF.';
                break;
              default:
                echo 'Erro desconhecido.';
            }
          ?>
        </span>
        <button class="close-btn" onclick="closeNotification()">&times;</button>
      </div>
      <div class="progress-bar"></div>
    </div>
  <?php endif; ?>

  <script>
    // Auto-fechar notificação após 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
      const notification = document.getElementById('errorNotification');
      if (notification) {
        setTimeout(() => {
          notification.style.display = 'none';
        }, 5000);
      }
    });

    function closeNotification() {
      const notification = document.getElementById('errorNotification');
      if (notification) {
        notification.style.display = 'none';
      }
    }

</body>
</html>
