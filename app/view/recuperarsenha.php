<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recuperar Senha - Helios</title>
  <link rel="stylesheet" href="../../public/assets/css/reset.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
  <div class="reset-container">
    <div class="reset-card">
      <img src="../../public/assets/img/Sun.png" alt="Logo Helios" class="logo" />
      <h2 class="title">Recuperar Senha</h2>

      <!-- Etapa 1: E-mail -->
      <div class="step step-1 active" method="post">
        <p>Digite seu e-mail e enviaremos um código de verificação.</p>
        <form id="emailForm">
          <label for="email">E-mail</label>
          <input type="email" id="email" placeholder="exemplo@helios.com.br" required />
          <div class="button-group">
            <button type="submit" class="btn">Enviar Código</button>
          </div>
        </form>
      </div>

      <!-- Etapa 2: Código -->
<!-- Etapa 2: Código -->
<div class="step step-2">
  <p>Insira o código de 6 dígitos que enviamos ao seu e-mail.</p>
  <form id="codeForm">
    <div class="code-inputs">
      <input type="text" maxlength="1" class="code-box" />
      <input type="text" maxlength="1" class="code-box" />
      <input type="text" maxlength="1" class="code-box" />
      <input type="text" maxlength="1" class="code-box" />
      <input type="text" maxlength="1" class="code-box" />
      <input type="text" maxlength="1" class="code-box" />
    </div>
    <div class="button-group">
      <button type="button" class="btn-back">Voltar</button>
      <button type="submit" class="btn">Verificar Código</button>
    </div>
  </form>
</div>


      <!-- Etapa 3: Nova Senha -->
      <div class="step step-3">
        <p>Digite sua nova senha para redefinir o acesso.</p>
        <form id="passwordForm">
          <label for="password">Nova Senha</label>
          <input type="password" id="password" minlength="6" placeholder="••••••••" required />
          <div class="button-group">
            <button type="button" class="btn-back">Voltar</button>
            <button type="submit" class="btn">Alterar Senha</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="successBox" class="success-box">
    <i class="fa-solid fa-check"></i>  Senha alterada com sucesso!
  </div>

  <script src="../../public/assets/js/reset.js"></script>
</body>
</html>
