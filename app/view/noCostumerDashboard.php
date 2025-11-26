<?php
    require("../controllers/protect.php");
    require("../config/connection.php");

    $orcamento = null;
    
    if (isset($_SESSION['clientID'])) {
        $stmt = $mysqli->prepare("SELECT ID_ORCAMENTO, STATUS, DATA_CRIACAO FROM ORCAMENTOS WHERE ID_CLIENTE = ? AND STATUS = 'PENDENTE' ORDER BY DATA_CRIACAO DESC LIMIT 1");
        if ($stmt) {
            $clientId = $_SESSION['clientID'];
            $stmt->bind_param("i", $clientId);
            $stmt->execute();   
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $orcamento = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Helios — Painel Simples</title>
  <link rel="stylesheet" href="../../public/assets/css/noCostumerDashboard.css" />
</head>
<body>
  <header class="sd-header">
    <div class="brand">
      <img src="../../public/assets/img/Sun.png" alt="Helios" class="logo">
      <h1>Helios</h1>
    </div>
    <div class="user-area" id="userArea"></div>
  </header>

  <main class="sd-main">


    <section id="overview" class="sd-section active" role="tabpanel">
      <h2 id="titleOverview">Visão Geral</h2>

      <div id="simBox" class="sim-box">
        <?php if ($orcamento): ?>
          <div class="sim-row" style="align-items:center;justify-content:center;flex-direction:column">
            <div class="sim-item" style="max-width:640px;text-align:center;padding:18px;border-radius:10px">
              <div style="font-weight:700;font-size:1.05rem;margin-bottom:6px">Sua solicitação já foi realizada</div>
              <div style="color:#6b6b6b">Em breve nossa equipe entrará em contato com você.</div>
            </div>
          </div>
        <?php else: ?>
          <div class="sim-row" style="align-items:center;justify-content:center;flex-direction:column">
            <div class="sim-item" style="max-width:640px;text-align:center;padding:18px;border-radius:10px">
              <div style="font-weight:700;font-size:1.05rem;margin-bottom:6px">Nenhum orçamento encontrado</div>
              <div style="color:#6b6b6b">Faça uma simulação para receber um orçamento personalizado.</div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="actions">
        <a id="btnSimular" class="btn btn-secondary" href="simulacao.php">Simule agora</a>
        <button id="btnSolicitar" class="btn btn-primary">Solicitar novo orçamento</button>
      </div>

      <div id="statusMsg" class="status-msg" aria-live="polite"></div>
    </section>


      </div>
    </section>
  </main>

  <script src="../../public/assets/js/noCostumerDashboard.js"></script>
  <script>
    // Informação do servidor sobre orçamento pendente (se houver)
    window.serverOrcamento = <?php echo json_encode($orcamento); ?>;
  </script>
</body>
</html>