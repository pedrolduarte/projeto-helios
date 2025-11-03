<?php
    require("../controllers/protect.php");
    require("../config/connection.php");

    // Salva o nome completo do usuário na sessão
    if (!$_SESSION['completeName']) {
        $clientID = $_SESSION['clientID'];
        $stmt = $mysqli->prepare("SELECT NOME_CLIENTE FROM CLIENTE WHERE ID_CLIENTE = ?");
        $stmt->bind_param("i", $clientID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['completeName'] = $row['NOME_CLIENTE'];
        } else {
            $_SESSION['completeName'] = "Usuário";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Helios – Área do Cliente</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="../../public/assets/img/Sun.png">

  <link rel="stylesheet" href="../../public/assets/css/dashboard.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
  <!-- Topbar -->
  <header class="topbar">
    <button id="openMenu" class="logo-btn" aria-label="Abrir menu" title="Menu">
      <img src="../../public/assets/img/Sun.png" alt="Helios"/>
    </button>

    <h1>Área do Cliente</h1>

    <div class="user-box">
      <button id="userBtn" class="user-btn">Conta</button>
      <div id="userDropdown" class="user-dropdown">
        <a href="#" data-section="profile">Meu perfil</a>
        <a href="#" id="openSettings">Configurações</a>
        <a href="#" id="logout">Sair</a>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar" aria-hidden="true">
    <nav class="menu">
      <a href="#" class="active" data-section="overview">Visão Geral</a>
      <a href="#" data-section="consumo">Consumo</a>
      <a href="#" data-section="simulacao">Simulação</a>
      <a href="#" data-section="profile">Perfil</a>
    </nav>
  </aside>
  <div id="overlay" class="overlay" aria-hidden="true"></div>

  <!-- Conteúdo -->
  <main class="content">

    <!-- VISÃO GERAL -->
    <section id="overview" class="section active">
      <div class="welcome welcome--row">
        <div class="welcome">
          <h2>Bem-vindo, <span id="clientName"><?php echo htmlspecialchars($_SESSION['completeName']); ?></span>!</h2>
          <p class="muted">Seu sistema está ativo e gerando energia normalmente.</p>
        </div>
        <div class="welcome-actions">
          <button id="goToConsumo" class="btn-outline">Ver consumo</button>
          <button id="goToSimulacao" class="btn">Simular economia</button>
        </div>
      </div>

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-icon">⚡</div>
          <div class="kpi-meta">
            <div class="kpi-label">Energia Gerada (mês)</div>
            <div class="kpi-value">325 kWh</div>
            <div class="kpi-sub">Últimos 30 dias</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon">💰</div>
          <div class="kpi-meta">
            <div class="kpi-label">Economia Estimada</div>
            <div class="kpi-value">R$ 240,00</div>
            <div class="kpi-sub">Tarifa média R$ 0,75</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon">🌿</div>
          <div class="kpi-meta">
            <div class="kpi-label">CO₂ evitado</div>
            <div class="kpi-value">72 kg</div>
            <div class="kpi-sub">Mês corrente</div>
          </div>
        </div>

        <!-- removido o 4º bloco conforme solicitado -->
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-head">
            <h3>Geração vs Consumo</h3>
            <button class="chip chip--ghost" id="goToConsumo2">Detalhar</button>
          </div>
          <div class="chart-wrap chart-wrap--fill">
            <canvas id="kpiBarChart"></canvas>
          </div>
        </div>

        <div class="grid-1-1">
          <div class="donut-card">
            <h4>Meta de economia</h4>
            <div class="chart-donut"><canvas id="donutEconomia"></canvas></div>
            <p class="muted">67% já atingido</p>
          </div>
          <div class="donut-card">
            <h4>Eficiência do sistema</h4>
            <div class="chart-donut"><canvas id="donutEficiencia"></canvas></div>
            <p class="muted">Operando dentro do esperado</p>
          </div>
        </div>
      </div>
    </section>

    <section id="consumo" class="section">
  <h2>Histórico de Consumo</h2>

  <!-- ===== Cadastro de Consumo Mensal (agora acima do gráfico) ===== -->
  <div class="cadastro-consumo">
    <h3>Cadastrar Consumo Mensal</h3>
    <form id="formConsumo" method="POST" action="../controllers/costumer/consumoRegisterController.php">
      <div class="cc-grid">
        <div class="field">
          <label for="consumoAno">Ano</label>
          <select id="consumoAno" name="ano_consumo" required>
              <?php
                  $currentYear = (int)date('Y');
                  for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                          $sel = ($y === $currentYear) ? ' selected' : '';
                          echo "<option value=\"{$y}\"{$sel}>{$y}</option>\n";
                  }
              ?>
          </select>
        </div>
        <div class="field">
          <label for="consumoMes">Mês</label>
          <select id="consumoMes" name="mes_consumo" required>
            <option value="1">Janeiro</option>
            <option value="2">Fevereiro</option>
            <option value="3">Março</option>
            <option value="4">Abril</option>
            <option value="5">Maio</option>
            <option value="6">Junho</option>
            <option value="7">Julho</option>
            <option value="8">Agosto</option>
            <option value="9">Setembro</option>
            <option value="10">Outubro</option>
            <option value="11">Novembro</option>
            <option value="12">Dezembro</option>
          </select>
        </div>
        <div class="field">
          <label for="consumoValor">Valor (kWh)</label>
          <input id="consumoValor" name="consumo_kwh" type="number" step="0.01" placeholder="Ex: 450" required>
        </div>
        <button type="submit" class="btn" id="btnAddConsumo">+ Adicionar</button>
      </div>
    </form>
  </div>

  <!-- ===== Gráfico de Consumo ===== -->
  <div class="panel chart-wrap chart-wrap--fill">
    <canvas id="consumoChart"></canvas>
  </div>

  <!-- ===== Mini Cards ===== -->
  <div class="kpi-line">
    <div class="mini-card">
      <i class="fa-solid fa-chart-line mini-icon"></i>
      <div>
        <div class="mini-label">Menor consumo</div>
        <div class="mini-value" id="minConsumo">480 kWh</div>
      </div>
    </div>
    <div class="mini-card">
      <i class="fa-solid fa-chart-area mini-icon"></i>
      <div>
        <div class="mini-label">Maior consumo</div>
        <div class="mini-value" id="maxConsumo">650 kWh</div>
      </div>
    </div>
  </div>

  <!-- ===== Tabela Consumo x Economia ===== -->
  <div class="panel">
    <div class="panel-head">
      <h3>Consumo x Economia</h3>
      <div class="consumo-filter">
        <label for="anoSelect">Ano:</label>
        <select id="anoSelect">
            <?php
                $currentYear = (int)date('Y');
                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                    $sel = ($y === $currentYear) ? ' selected' : '';
                    echo "<option value=\"{$y}\"{$sel}>{$y}</option>\n";
                }
            ?>
        </select>
      </div>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>Mês</th>
          <th>Consumo (kWh)</th>
          <th>Economia (R$)</th>
        </tr>
      </thead>
      <tbody id="historyTable"></tbody>
    </table>
  </div>
</section>

    <!-- SIMULAÇÃO -->
    <section id="simulacao" class="section">
      <div class="sim-grid">
        <div class="simulador">
          <h3>Simulação de Economia</h3>

          <div class="field">
            <label for="inputConta">Informe sua conta de luz (R$)</label>
            <input id="inputConta" type="number" step="0.01" placeholder="Ex: 240,00"/>
          </div>

          <div class="field">
            <label for="selectPlaca">Informe a placa desejada</label>
            <select id="selectPlaca">
              <option value="">Selecione…</option>
              <option value="450">450 W</option>
              <option value="500">500 W</option>
              <option value="550">550 W</option>
            </select>
            <small class="muted">Potência da placa selecionada: <strong id="potenciaPlaca">—</strong></small>
          </div>

          <div class="field">
            <label for="quantPlacas">Quantidade de placas</label>
            <input id="quantPlacas" type="number" min="1" value="1"/>
          </div>

          <button id="calcSimulacao" class="btn" style="margin-top:8px">Calcular</button>
        </div>

        <div class="preview-card">
          <div class="preview-head">
            <div class="preview-icon">📊</div>
            <h3>Pré-visualização</h3>
          </div>
          <div class="preview-body">
            <div class="preview-item"><span class="label">Conta informada</span><span class="value" id="pvConta">R$ 0,00</span></div>
            <div class="preview-item"><span class="label">Placa</span><span class="value" id="pvPlaca">—</span></div>
            <div class="preview-item"><span class="label">Quantidade</span><span class="value" id="pvQtd">1</span></div>
            <div class="preview-item"><span class="label">Geração estimada</span><span class="value" id="pvGeracao">0 kWh/mês</span></div>
            <div class="preview-item"><span class="label">Economia estimada</span><span class="value" id="pvEconomia">R$ 0,00 / mês</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- PERFIL -->
    <section id="profile" class="section">
      <div class="section-head">
        <h3>Seu Perfil</h3>
        <p class="muted">Visualize e atualize suas informações pessoais.</p>
      </div>

      <div class="profile-grid">
        <div class="profile-view">
          <div class="info-card">
            <div class="info-icon">👤</div>
            <div>
              <div class="field">
                <label>Nome</label>
                <div class="value" id="viewName">João da Silva</div>
              </div>
              <div class="field">
                <label>E-mail</label>
                <div class="value" id="viewEmail">joao.silva@helios.com.br</div>
              </div>
              <div class="field">
                <label>Telefone</label>
                <div class="value" id="viewPhone">(19) 99999-9999</div>
              </div>
              <div class="field">
                <label>Endereço</label>
                <div class="value" id="viewAddress">Rua das Flores, 123 — Araras/SP</div>
              </div>
            </div>
          </div>
        </div>

        <div class="profile-edit">
          <div class="field">
            <label for="inputName">Alterar Nome</label>
            <input id="inputName" type="text" placeholder="Digite seu nome">
          </div>
          <div class="field">
            <label for="inputEmail">Alterar E-mail</label>
            <input id="inputEmail" type="email" placeholder="Seu e-mail">
          </div>
          <div class="field">
            <label for="inputPhone">Alterar Telefone</label>
            <input id="inputPhone" type="text" placeholder="(99) 99999-9999">
          </div>
          <div class="field">
            <label for="inputAddress">Alterar Endereço</label>
            <input id="inputAddress" type="text" placeholder="Seu endereço">
          </div>
          <button id="saveProfile" class="btn">Salvar alterações</button>
        </div>
      </div>
    </section>

    <!-- Modal Resultado Simulação -->
    <div id="resultModal" class="modal" aria-hidden="true">
      <div class="modal-card">
        <h3>Resultado da simulação</h3>
        <div id="resultContent"></div>
        <div style="text-align:right;margin-top:12px">
          <button id="closeModal" class="btn-outline">Fechar</button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast">✔ Alterações salvas!</div>

    <!-- Notification Toast para consumo -->
    <div id="consumoToast" class="toast">✔ Consumo salvo com sucesso!</div>

  </main>

  <script src="../../public/assets/js/dashboard.js"></script>
</body>
</html>
