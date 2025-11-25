<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Helios - Usuário não encontrado</title>
  <link rel="stylesheet" href="../../public/assets/css/error.css" />
</head>
<body>
  <header class="topbar">
    <div class="topbar-inner">
      <img src="../../public/assets/img/Sun.png" alt="Helios" class="logo">
      <div class="brand">Helios</div>
    </div>
  </header>

  <main class="error-page">
    <div class="error-content">
      <h1 class="big">DESCULPE</h1>
      <p class="lead">Não encontramos um cadastro para este e‑mail.</p>
      <p class="help">
        Se você ainda não tem uma conta, crie uma agora para acessar o sistema.
      </p>

      <div class="actions">
        <a href="register.php" class="btn btn-primary">Criar minha conta</a>
        <a href="login.php" class="btn btn-secondary">Voltar ao login</a>
      </div>

      <p class="redirect">
        Redirecionando para cadastro em <span id="countdown">8</span> segundos.
        <a href="register.php" class="fast-link">Ir agora</a>
      </p>
    </div>

    <div class="error-illustration">
      <img src="../../public/assets/img/Sun.png" alt="Mascote Helios" />
    </div>
  </main>

  <footer class="page-footer">
    <p>© Helios</p>
  </footer>

  <script>
    (function() {
      var seconds = 8;
      var el = document.getElementById('countdown');
      var interval = setInterval(function() {
        seconds -= 1;
        if (seconds <= 0) {
          clearInterval(interval);
          window.location.href = 'register.php';
        } else {
          el.textContent = seconds;
        }
      }, 1000);
    })();
  </script>
  
</body>
</html>