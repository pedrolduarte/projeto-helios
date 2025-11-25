<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulação Helios — Economia</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="../../public/assets/img/Sun.png">

  <!-- Estilos -->
  <link rel="stylesheet" href="../../public/assets/css/simulacao.css">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div class="sim-wrap">
    <div class="sim-card">
      <header class="sim-header">
        <h1>Resultado</h1>
        <button id="simClose" title="Fechar" onclick="window.close();"><i class="fa-solid fa-xmark"></i></button>
      </header>

      <section class="sim-controls">
        <form id="simForm">
          <div class="row">
            <label>Consumo médio (kWh/mês)
              <input id="sim_consumo" type="number" min="0" step="0.1" value="300" />
            </label>

            <label>Tarifa média (R$/kWh)
              <input id="sim_tarifa" type="number" min="0" step="0.01" value="0.90" />
            </label>

            <label>Cobertura desejada (%)
              <input id="sim_cobertura" type="number" min="10" max="100" value="80" />
            </label>
          </div>

          <div class="actions">
            <button type="button" id="calcSimBtn" class="btn">Calcular</button>
            <button type="button" id="resetSimBtn" class="btn-outline">Limpar</button>
            <a id="solicitarBtn" class="btn-primary" href="register.php" style="display:none;" target="_self">Solicitar orçamento</a>
          </div>
        </form>
      </section>

      <section id="simResults" class="sim-results" aria-hidden="true">
        <div class="grid">
          <div class="item">
            <i class="fa-solid fa-solar-panel icon"></i>
            <small>Potência instalada*</small>
            <div id="r_kwp" class="value">— kWp</div>
          </div>
          <div class="item">
            <i class="fa-solid fa-vector-square icon"></i>
            <small>Área mínima necessária*</small>
            <div id="r_area" class="value">— m²</div>
          </div>
          <div class="item">
            <i class="fa-solid fa-coins icon"></i>
            <small>Valor aproximado do sistema*</small>
            <div id="r_valor" class="value">R$ —</div>
          </div>

          <div class="item">
            <i class="fa-solid fa-chart-line icon"></i>
            <small>Produção mensal*</small>
            <div id="r_producao" class="value">— kWh / mês</div>
          </div>
          <div class="item">
            <i class="fa-solid fa-calendar-check icon"></i>
            <small>Economia anual aproximada*</small>
            <div id="r_economia_ano" class="value">R$ —</div>
          </div>
          <div class="item">
            <i class="fa-solid fa-hourglass-half icon"></i>
            <small>Tempo aproximado de retorno*</small>
            <div id="r_payback" class="value">— anos</div>
          </div>
        </div>

        <p class="note">
          *Estimativa baseada em parâmetros médios. Consulte um especialista para orçamento detalhado.
        </p>
      </section>
    </div>
  </div>

  <script src="../../public/assets/js/simulacao.js" defer></script>
</body>
</html>