<?php
    require("../controllers/protect.php");
    require("../config/connection.php");
    
    // Inicia a sessão se não estiver iniciada
    if (!isset($_SESSION)) {
        session_start();
    }

    // Salva o nome completo do usuário na sessão
    if (!isset($_SESSION['completeName'])) {
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

    // Calcula CO2 evitado e economia estimada com base em GERACAO_KWH para cliente, mês/ano atuais
    $co2Kg = 0;
    $economiaEstimada = 0;
    $emissionFactor = 0.082; // kg CO2 por kWh (ajustar conforme fonte)
    try {
        $clientID = $_SESSION['clientID'];
        $mesAtual = (int)date('n');
        $anoAtual = (int)date('Y');

        $q = $mysqli->prepare("SELECT COALESCE(GERACAO_KWH, 0) as geracao FROM CONSUMO WHERE ID_CLIENTE = ? LIMIT 1");
        if ($q) {
            $q->bind_param("i", $clientID);
            $q->execute();
            $res = $q->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                $geracao = (float)$row['geracao'];
                $co2Kg = round($geracao * $emissionFactor, 1);
                $economiaEstimada = round($geracao * 0.71, 1); // Exemplo: 71% do valor de geração
            }
            $q->close();
        } else {
            error_log("Erro prepare GERACAO query: " . $mysqli->error);
        }
    } catch (Exception $e) {
        error_log("Erro ao calcular CO2: " . $e->getMessage());
    }
    
  // Buscar dados do perfil do usuário para preencher o formulário
  $profileName = "";
  $profileEmail = "";
  $profilePhone = "";
  $profileCep = "";
  $profileNumero = "";
  $profileLogradouro = "";
  $profileCidade = "";
  try {
    $clientID = $_SESSION['clientID'];
    $stmt = $mysqli->prepare("SELECT NOME_CLIENTE FROM CLIENTE WHERE ID_CLIENTE = ?");
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
      $row = $result->fetch_assoc();
      $profileName = $row['NOME_CLIENTE'];
    }

    $stmt = $mysqli->prepare("SELECT EMAIL, TELEFONE FROM CONTA WHERE ID_CLIENTE = ?");
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
      $row = $result->fetch_assoc();
      $profileEmail = $row['EMAIL'];
      $profilePhone = $row['TELEFONE'];
    }

    $stmt = $mysqli->prepare("SELECT CEP, NUMERO FROM CLIENTE_ENDERECO WHERE ID_CLIENTE = ?");
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows >= 1) {
      $row = $result->fetch_assoc();
      $profileCep = $row['CEP'];
      $profileNumero = $row['NUMERO'];
    }

    if ($profileCep !== "") {
      // Buscar logradouro via API viacep
      $cepClean = preg_replace('/\D/', '', $profileCep);
      $url = "https://viacep.com.br/ws/{$cepClean}/json/";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);
      $data = json_decode($response, true);
      if (isset($data['logradouro'])) {
        $profileLogradouro = $data['logradouro'];
        $profileCidade = $data['localidade'];
      }
    }

    $profileAddress = "{$profileLogradouro}, {$profileNumero}, {$profileCidade}";
  } catch (Exception $e) {
    error_log("Erro ao obter ID do cliente: " . $e->getMessage());
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
        <a href="../controllers/finishSessionController.php" id="logout">Sair</a>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar" aria-hidden="true">
    <nav class="menu">
      <a href="#" class="active" data-section="overview">Visão Geral</a>
      <a href="#" data-section="consumo">Consumo</a>
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
            <div class="kpi-value"><?= $economiaEstimada . ' R$' ?></div>
            <div class="kpi-sub">Tarifa média R$ 0,71</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon">🌿</div>
          <div class="kpi-meta">
            <div class="kpi-label">CO₂ evitado</div>
            <div class="kpi-value"><?= $co2Kg . ' kg' ?></div>
            <div class="kpi-sub">Tempo Total</div>
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
                <div class="value" id="viewName"><?= htmlspecialchars($profileName) ?></div>
              </div>
              <div class="field">
                <label>E-mail</label>
                <div class="value" id="viewEmail"><?= htmlspecialchars($profileEmail) ?></div>
              </div>
              <div class="field">
                <label>Telefone</label>
                <?php
                  $phoneRaw = $profilePhone ?? '';
                  $digits = preg_replace('/\D+/', '', $phoneRaw);
                  $formattedPhone = htmlspecialchars($phoneRaw);

                  if ($digits !== '') {
                    if (strlen($digits) === 11) {
                      // (AA) 9XXXX-XXXX
                      $formattedPhone = sprintf('(%s) %s-%s',
                        substr($digits, 0, 2),
                        substr($digits, 2, 5),
                        substr($digits, 7, 4)
                      );
                    } elseif (strlen($digits) === 10) { 
                      // (AA) XXXX-XXXX
                      $formattedPhone = sprintf('(%s) %s-%s',
                        substr($digits, 0, 2),
                        substr($digits, 2, 4),
                        substr($digits, 6, 4)
                      );
                    } else {
                      // fallback: mostra apenas os dígitos
                      $formattedPhone = $digits;
                    }
                    $formattedPhone = htmlspecialchars($formattedPhone);
                  }
                ?>
                <div class="value" id="viewPhone"><?= $formattedPhone ?></div>
              </div>
              <div class="field">
                <label>Endereço</label>
                <div class="value" id="viewAddress"><?= htmlspecialchars($profileAddress) ?></div>
              </div>
            </div>
          </div>
        </div>

        <div class="profile-edit">
          <form id="profileForm" method="POST" action="../controllers/costumer/profileEditController.php">
            <div class="field">
              <label for="inputName">Nome</label>
              <input id="inputName" name="nome_completo" type="text" placeholder="Digite seu nome" value="<?= htmlspecialchars($profileName) ?>" required>
            </div>
            <div class="field">
              <label for="inputEmail">E-mail</label>
              <input id="inputEmail" name="email" type="email" placeholder="Seu e-mail" value="<?= htmlspecialchars($profileEmail) ?>" required>
            </div>
            <div class="field">
              <label for="inputPhone">Telefone</label>
              <input id="inputPhone" name="telefone" type="text" placeholder="(99) 99999-9999" value="<?= htmlspecialchars($profilePhone) ?>" required>
            </div>
            <div class="field">
              <label for="inputCep">CEP</label>
              <input id="inputCep" name="cep" type="text" placeholder="00000-000" value="<?= htmlspecialchars($profileCep) ?>" required>
            </div>
            <div class="field">
              <label for="inputNumero">Número</label>
              <input id="inputNumero" name="numero" type="text" placeholder="Número" value="<?= htmlspecialchars($profileNumero) ?>" required>
            </div>
            <div class="field">
              <label for="inputLogradouro">Endereço</label>
              <input id="inputLogradouro" type="text" placeholder="Logradouro (preenchido automaticamente)" readonly value="<?= htmlspecialchars($profileLogradouro) ?>">
            </div>
            <div style="display:flex;gap:10px;align-items:center;margin-top:6px;">
              <button id="saveProfile" type="submit" class="btn">Salvar alterações</button>
              <span class="muted" style="font-size:0.9rem;">As alterações serão aplicadas ao seu cadastro.</span>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- Toast -->
    <div id="toast" class="toast">✔ Alterações salvas!</div>

    <!-- Notification Toast para consumo -->
    <div id="consumoToast" class="toast">✔ Consumo salvo com sucesso!</div>

  </main>

  <script src="../../public/assets/js/dashboard.js"></script>
</body>
</html>
