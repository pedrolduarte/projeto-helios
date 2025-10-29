<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Helios - Login</title>
  <link rel="stylesheet" href="../../public/assets/css/login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>

  <div class="login-container">
    <div class="login-box">
      <img src="../../public/assets/img/Sun.png" alt="Logo Helios" class="logo">
      <h2>Área do Cliente</h2>
      <form action="../controllers/login/loginController.php" method="POST">
        <label for="email">E-mail</label>
        <input id="email" placeholder="Digite seu e-mail" name="email" type="email" required>

        <label for="senha">Senha</label>
        <input id="senha" placeholder="Digite sua senha" name="password" type="password" required>

        <div class="remember">
          <label><input type="checkbox"> Lembrar-me</label>
          <a href="recuperarsenha.php">Esqueceu a senha?</a>
        </div>

        <button type="submit" class="btn-primary">Entrar</button>

        <p class="signup-text">Ainda não tem uma conta? <a href="register.php">Cadastre-se</a></p>
      </form>
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
              case 'empty_email':
                echo 'Preencha seu email!';
                break;
              case 'empty_password':
                echo 'Preencha sua senha!';
                break;
              case 'invalid_credentials':
                echo 'Falha ao logar! Email ou senha incorretos!';
                break;
              case 'server_error':
                echo 'Erro interno do servidor. Tente novamente mais tarde.';
                break;
              case 'empty_fields':
                echo 'Preencha todos os campos!';
                break;
              case 'invalid_method':
                echo 'Método de requisição inválido!';
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
        // Inicia a animação da barra de progresso
        const progressBar = notification.querySelector('.progress-bar');
        progressBar.style.animation = 'progress 5s linear forwards';
        
        // Remove a notificação após 5 segundos
        setTimeout(() => {
          closeNotification();
        }, 5000);
      }
    });

    function closeNotification() {
      const notification = document.getElementById('errorNotification');
      if (notification) {
        notification.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => {
          notification.remove();
        }, 300);
      }
    }
  </script>
</body>
</html>
