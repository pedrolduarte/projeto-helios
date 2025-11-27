<?php
  require("../config/connection.php");
  require("../controllers/admin/adminAuthentication.php");

  $orcamentosCount = 0;
  $simulacoesCount = 0;
  $clientesCount = 0;

  try {

    // Orçamentos
    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM ORCAMENTOS");
    if ($stmt && $stmt->execute()) {
      $result = $stmt->get_result();
      if ($result) {
        $data = $result->fetch_assoc();
        $orcamentosCount = $data['total'] ?? 0;
      }

      $stmt->close();
    }

    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM SIMULACOES");
    if ($stmt && $stmt->execute()) {
      $result = $stmt->get_result();
      if ($result) {
        $data = $result->fetch_assoc();
        $simulacoesCount = $data['total'] ?? 0;
      }

      $stmt->close();
    }

    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM CLIENTES WHERE CLIENTE_INSTALADO = 1");
    if ($stmt && $stmt->execute()) {
      $result = $stmt->get_result();
      if ($result) {
        $data = $result->fetch_assoc();
        $clientesCount = $data['total'] ?? 0;
      }

      $stmt->close();
    }

  } catch (Exception $e) {
    error_log("ERRO: Falha ao buscar dados para o painel administrativo em admin.php - " . $e->getMessage());
  }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Helios - Painel Administrativo</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="../../public/assets/img/Sun.png">

  <!-- CSS -->
  <link rel="stylesheet" href="../../public/assets/css/admin.css"/>

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

  <!-- Small utility for screen-reader-only text -->
  <style>
    .sr-only {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      padding: 0 !important;
      margin: -1px !important;
      overflow: hidden !important;
      clip: rect(0, 0, 0, 0) !important;
      white-space: nowrap !important;
      border: 0 !important;
    }
  </style>

  <!-- Chart (Dashboard) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- JS -->
  <script defer src="../../public/assets/js/admin.js"></script>
</head>
<body>
  <!-- HEADER -->
  <header class="admin-header">
    <div class="logo">
      <img src="../../public/assets/img/Sun.png" alt="Helios Logo"/>
      <h1>Admin</h1>
    </div>

    <nav class="admin-nav" aria-label="Navegação do painel">
      <a href="#" class="active" data-section="dashboard" aria-current="page"><i class="fa-solid fa-house" aria-hidden="true"></i> <span>Dashboard</span></a>
      <a href="#" data-section="clientes"><i class="fa-solid fa-users" aria-hidden="true"></i> <span>Clientes</span></a>

      <!-- Removida aba "Placas" e adicionadas "Orçamento" e "Simulações" -->
      <a href="#" data-section="orcamento"><i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> <span>Orçamento</span></a>
      <a href="#" data-section="simulacoes"><i class="fa-solid fa-calculator" aria-hidden="true"></i> <span>Simulações</span></a>

      <a href="#" data-section="usuarios"><i class="fa-solid fa-id-badge" aria-hidden="true"></i> <span>Usuários</span></a>
      <button id="logout" type="button" aria-label="Sair"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> <span class="sr-only">Sair</span></button>
    </nav>
  </header>

  <!-- CONTEÚDO -->
  <main class="content" id="main" role="main">
    <!-- DASHBOARD -->
    <section id="dashboard" class="section active" aria-labelledby="dashboard-title">
      <h2 id="dashboard-title">Visão Geral</h2>
      <p class="muted">Resumo operacional do sistema Helios.</p>

      <div class="card-grid">
        <div class="card">
          <i class="fa-solid fa-wallet card-icon" aria-hidden="true"></i>
          <h3>Orçamentos</h3>
          <p><?= $orcamentosCount ?> Cadastrados</p>
        </div>

        <div class="card">
          <i class="fa-solid fa-solar-panel card-icon" aria-hidden="true"></i>
          <h3>Simulações</h3>
          <p><?= $simulacoesCount ?> Realizadas</p>
        </div>

        <div class="card">
          <i class="fa-solid fa-user-check card-icon" aria-hidden="true"></i>
          <h3>Clientes Ativos</h3>
          <p><?= $clientesCount ?> Atualmente</p>
        </div>
      </div>

      <div class="panel" aria-labelledby="grafico-title">
        <div class="panel-head">
          <h3 id="grafico-title">Cadastro de Clientes Mensais</h3>
        </div>
        <div class="chart-wrap chart-wrap--small">
          <canvas id="dashChart" role="img" aria-label="Gráfico de cadastros de clientes por mês"></canvas>
        </div>
      </div>
    </section>

    <!-- CLIENTES (mantido) -->
    <section id="clientes" class="section" aria-labelledby="clientes-title">
      <h2 id="clientes-title">Clientes</h2>
      <p class="muted">Gerencie os clientes: ver cadastro, editar e definir permissões.</p>

      <div class="table-actions">
        <label for="searchCliente" class="sr-only">Buscar cliente</label>
        <input type="search" id="searchCliente" name="searchCliente" placeholder="Buscar cliente..." />
      </div>

      <table class="table" id="clientesTable" aria-describedby="clientes-title">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">E-mail</th>
            <th scope="col">Status</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr data-endereco="Rua das Flores, 123 - Centro, SP" data-telefone="+55 11 98765-4321" data-nascimento="1985-04-12">
            <td>1</td>
            <td>João da Silva</td>
            <td>joao@helios.com</td>
            <td><span class="badge success">Ativo</span></td>
            <td class="actions">
              <button type="button" class="btn-small info ver-cadastro" aria-label="Ver cadastro de João da Silva"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
              <button type="button" class="btn-small edit editar-cadastro" aria-label="Editar cadastro de João da Silva"><i class="fa-solid fa-pen" aria-hidden="true"></i></button>
              <button type="button" class="btn-small warn tornar-admin" aria-label="Tornar administrador João da Silva"><i class="fa-solid fa-user-shield" aria-hidden="true"></i></button>
            </td>
          </tr>
          <tr data-endereco="Av. Paulista, 1000 - Apto 45, SP" data-telefone="+55 11 91234-5678" data-nascimento="1992-09-03">
            <td>2</td>
            <td>Maria Souza</td>
            <td>maria@helios.com</td>
            <td><span class="badge success">Ativo</span></td>
            <td class="actions">
              <button type="button" class="btn-small info ver-cadastro" aria-label="Ver cadastro de Maria Souza"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
              <button type="button" class="btn-small edit editar-cadastro" aria-label="Editar cadastro de Maria Souza"><i class="fa-solid fa-pen" aria-hidden="true"></i></button>
              <button type="button" class="btn-small warn tornar-admin" aria-label="Tornar administrador Maria Souza"><i class="fa-solid fa-user-shield" aria-hidden="true"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- ORÇAMENTO (nova aba) -->
    <section id="orcamento" class="section" aria-labelledby="orcamento-title">
      <h2 id="orcamento-title">Orçamentos</h2>
      <p class="muted">Lista de orçamentos solicitados. Finalize quando o atendimento estiver concluído.</p>

      <div class="table-actions">
        <button id="novoOrcBtn" class="btn" type="button"><i class="fa-solid fa-plus" aria-hidden="true"></i> Novo Orçamento</button>
      </div>

      <table class="table" id="orcamentosTable" aria-describedby="orcamento-title">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome do Cliente</th>
            <th scope="col">Endereço</th>
            <th scope="col">Finalizado</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          <!-- Entradas serão renderizadas via JS -->
        </tbody>
      </table>
    </section>

    <!-- SIMULAÇÕES (nova aba) -->
    <section id="simulacoes" class="section" aria-labelledby="simulacoes-title">
      <h2 id="simulacoes-title">Simulações</h2>
      <p class="muted">Registro das simulações realizadas pelo site.</p>

      <table class="table" id="simulacoesTable" aria-describedby="simulacoes-title">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Consumo médio (kWh/mês)</th>
            <th scope="col">Tarifa média (R$/kWh)</th>
            <th scope="col">Cobertura (%)</th>
            <th scope="col">Área mínima (m²)</th>
            <th scope="col">Valor aproximado (R$)</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          <!-- Entradas serão renderizadas via JS -->
        </tbody>
      </table>
    </section>

    <!-- USUÁRIOS (mantido) -->
    <section id="usuarios" class="section" aria-labelledby="usuarios-title">
      <h2 id="usuarios-title">Usuários</h2>
      <p class="muted">Gerencie os administradores e funcionários do sistema Helios.</p>

      <div class="usuarios-head">
        <button id="novoUsuarioBtn" class="btn" type="button"><i class="fa-solid fa-plus" aria-hidden="true"></i> Novo Usuário</button>
      </div>

      <table id="usuariosTable" class="table" aria-describedby="usuarios-title">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">E-mail</th>
            <th scope="col">Função</th>
            <th scope="col">Status</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Gabriel Souza</td>
            <td>gabriel@helios.com</td>
            <td>Admin</td>
            <td><span class="badge success">Ativo</span></td>
            <td class="actions">
              <button type="button" class="btn-small edit editar-usuario" aria-label="Editar usuário Gabriel Souza"><i class="fa-solid fa-pen" aria-hidden="true"></i></button>
              <button type="button" class="btn-small del excluir-usuario" aria-label="Excluir usuário Gabriel Souza"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
              <button type="button" class="btn-small warn reset-senha" aria-label="Resetar senha de Gabriel Souza"><i class="fa-solid fa-key" aria-hidden="true"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <!-- MODALS CLIENTE / USUÁRIO / PLACA (PLACA removido) - mantidos os modais utilizados no JS -->
  <!-- MODAL CLIENTE (ver/editar) -->
  <div id="modalCliente" class="modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="modalClienteTitulo">
    <div class="modal-card">
      <div class="modal-head">
        <h3 id="modalClienteTitulo">Dados do Cliente</h3>
        <button id="fecharModalCliente" class="btn-icon" type="button" aria-label="Fechar modal de cliente"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
      </div>

      <div class="modal-body">
        <form id="formCliente" novalidate>
          <div class="grid">
            <div class="field">
              <label for="cliNome">Nome</label>
              <input type="text" id="cliNome" name="cliNome" />
            </div>

            <div class="field">
              <label for="cliEmail">E-mail</label>
              <input type="email" id="cliEmail" name="cliEmail" />
            </div>

            <div class="field">
              <label for="cliStatus">Status</label>
              <select id="cliStatus" name="cliStatus">
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
              </select>
            </div>

            <div class="field campo-duplo" style="grid-column: 1 / -1;">
              <label for="cliCep">Endereço - CEP</label>
              <div class="duo">
                <input type="text" id="cliCep" name="cliCep" placeholder="CEP">
                <input type="text" id="cliNumero" name="cliNumero" placeholder="Número">
              </div>
            </div>

            <div class="field">
              <label for="cliTelefone">Telefone</label>
              <input type="tel" id="cliTelefone" name="cliTelefone" placeholder="+55 11 9xxxx-xxxx" />
            </div>

            <div class="field">
              <label for="cliNascimento">Data de Nascimento</label>
              <input type="date" id="cliNascimento" name="cliNascimento" />
            </div>
          </div>
        </form>
      </div>

      <div class="modal-foot">
        <button id="salvarCliente" class="btn" type="button"><i class="fa-solid fa-check" aria-hidden="true"></i> Salvar</button>
      </div>
    </div>
  </div>

  <!-- MODAL USUÁRIO -->
  <div id="modalUsuario" class="modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="modalUsuarioTitulo">
    <div class="modal-card">
      <div class="modal-head">
        <h3 id="modalUsuarioTitulo">Novo Usuário</h3>
        <button id="fecharModalUsuario" class="btn-icon" type="button" aria-label="Fechar modal de usuário"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
      </div>

      <div class="modal-body">
        <form id="formUsuario" novalidate>
          <div class="grid">
            <div class="field">
              <label for="userNome">Nome</label>
              <input type="text" id="userNome" name="userNome" required />
            </div>

            <div class="field">
              <label for="userEmail">E-mail</label>
              <input type="email" id="userEmail" name="userEmail" required />
            </div>

            <div class="field">
              <label for="userFuncao">Função</label>
              <select id="userFuncao" name="userFuncao">
                <option>Visualizador</option>
                <option>Técnico</option>
                <option>Admin</option>
              </select>
            </div>

            <div class="field">
              <label for="userStatus">Status</label>
              <select id="userStatus" name="userStatus">
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
              </select>
            </div>

            <div class="field" id="senhaField">
              <label for="userSenha">Senha</label>
              <input type="password" id="userSenha" name="userSenha" placeholder="••••••••" required />
            </div>
          </div>
        </form>
      </div>

      <div class="modal-foot">
        <button id="salvarUsuario" class="btn" type="button"><i class="fa-solid fa-check" aria-hidden="true"></i> Salvar</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div id="toast" class="toast" role="status" aria-live="polite"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Ação realizada com sucesso</span></div>
</body>
</html>