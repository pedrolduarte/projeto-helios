<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Cliente - Helios</title>
  <link rel="stylesheet" href="Css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h1><img src="img/Sun.png" alt="logo helios" class="logologin"> HELIOS</h1>
      <h2>Área do Cliente</h2>
      <form action = 'loginMethod.php' method = 'POST'>
        <div class="input-group">
          <i class="fa-solid fa-user"></i>
          <input type="text" placeholder="Email" name="email" type="email" required>
        </div>
        <div class="input-group">
          <i class="fa-solid fa-lock"></i>
          <input placeholder="Senha" name="password" type="password" required>
        </div>
        <button type="submit" class="btn-login">Entrar</button>
      </form>
      <p class="signup">Não tem conta? <a href="#">Cadastre-se</a></p>
    </div>
  </div>

  <!-- Área para exibir mensagens -->
    <?php if(isset($_GET['error'])): ?>
        <p style="color: red;">
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
                    default:
                        echo 'Erro desconhecido.';
                }
            ?>
        </p>
    <?php endif; ?>
</body>
</html>
