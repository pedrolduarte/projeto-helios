document.addEventListener("DOMContentLoaded", () => {
  const el = id => document.getElementById(id);
  const calcBtn = el("calcSimBtn");
  const resetBtn = el("resetSimBtn");
  const resultsSection = el("simResults");

  function calcular() {
    const consumo = parseFloat(el("sim_consumo").value) || 0;
    const tarifa = parseFloat(el("sim_tarifa").value) || 0;
    const cobertura = Math.max(0, Math.min(100, parseFloat(el("sim_cobertura").value) || 0));
    const insolacao = parseFloat(el("sim_insolacao")?.value) || 4.5;
    const precoKwp = parseFloat(el("sim_preco_kwp")?.value) || 3500;

    if (consumo <= 0 || tarifa <= 0 || cobertura <= 0) {
      alert("Preencha consumo, tarifa e cobertura desejada.");
      return;
    }
    // Primeiro, tentar chamar a API de simulação no servidor
    const params = new URLSearchParams({
      consumoMedio: consumo,
      tarifaMedia: tarifa,
      coberturaDesejada: cobertura
    });

    const apiUrl = `../controllers/costumer/simulacaoController.php?${params.toString()}`;

    // Mostrar resultados somente após receber resposta
    fetch(apiUrl, { method: 'GET', credentials: 'same-origin' })
      .then(async (response) => {
        if (!response.ok) throw new Error('Resposta do servidor: ' + response.status);
        const text = await response.text();
        // Remover BOM se houver e trim
        const cleanText = text.replace(/^\uFEFF/, '').trim();
        let json;
        try {
          json = JSON.parse(cleanText);
        } catch (parseErr) {
          console.error('Resposta da API (raw):', text);
          if (cleanText.startsWith('<')) {
            throw new Error('Resposta não é JSON (HTML). Possível redirect/login ou erro que gerou HTML. Verifique a resposta no Network tab.');
          }
          throw new Error('Resposta não é JSON: ' + parseErr.message);
        }
        if (json.error) throw new Error(json.message || 'Erro na simulação');

        const d = json.data || {};

        // Mapear campos da API para os elementos da UI (usar bracket notation para chaves com caracteres especiais)
        el("r_kwp").textContent = (d['potencia_instalada_kwp'] !== undefined) ? `${Number(d['potencia_instalada_kwp']).toFixed(2)} kWp` : '— kWp';
        el("r_area").textContent = (d['area_minima_m2'] !== undefined) ? `${Number(d['area_minima_m2']).toLocaleString('pt-BR')} m²` : '— m²';
        el("r_producao").textContent = (d['producao_mensal_kwh'] !== undefined) ? `${Number(d['producao_mensal_kwh']).toLocaleString('pt-BR')} kWh / mês` : '—';
        el("r_economia_ano").textContent = (d['economia_anual_r$'] !== undefined) ? `R$ ${Number(d['economia_anual_r$']).toLocaleString('pt-BR',{minimumFractionDigits:2})}` : 'R$ —';
        el("r_valor").textContent = (d['custo_total_r$'] !== undefined) ? `R$ ${Number(d['custo_total_r$']).toLocaleString('pt-BR',{minimumFractionDigits:2})}` : 'R$ —';
        el("r_payback").textContent = (d['payback_anos'] !== undefined) ? `${Number(d['payback_anos']).toLocaleString('pt-BR')} anos` : '—';

        resultsSection.setAttribute("aria-hidden","false");

        // Mostrar botão de solicitar orçamento
        const solicitarBtn = el("solicitarBtn");
        if (solicitarBtn) {
          solicitarBtn.style.display = 'inline-block';
          solicitarBtn.setAttribute('href', 'register.php');
          solicitarBtn.textContent = 'Solicitar orçamento';
          solicitarBtn.setAttribute('target', '_self');
        }
      })
      .catch((err) => {
        console.error('Simulação via API falhou:', err);
        // Se a API falhar por qualquer motivo, realizar cálculo local como fallback
        // (mantive a mesma lógica local que existia antes)
        const perfRatio = 0.75;
        const diasMes = 30;
        const prodKwpMes = insolacao * diasMes * perfRatio;

        const energiaObjetivo = consumo * (cobertura/100);
        const kwp = energiaObjetivo / prodKwpMes;
        const kwpRounded = Math.max(0.1, Math.round(kwp * 100) / 100);
        const areaPorKwp = 6.5;
        const area = Math.round(kwpRounded * areaPorKwp * 10) / 10;
        const producaoMensal = Math.round(kwpRounded * prodKwpMes * 10) / 10;
        const economiaMensal = producaoMensal * tarifa;
        const economiaAnual = Math.round(economiaMensal * 12 * 100) / 100;
        const valorSistema = Math.round(kwpRounded * precoKwp * 100) / 100;
        const payback = economiaAnual > 0 ? Math.round((valorSistema / economiaAnual) * 10) / 10 : null;

        el("r_kwp").textContent = `${kwpRounded.toFixed(2)} kWp`;
        el("r_area").textContent = `${area} m²`;
        el("r_producao").textContent = `${producaoMensal.toLocaleString('pt-BR')} kWh / mês`;
        el("r_economia_ano").textContent = `R$ ${economiaAnual.toLocaleString('pt-BR',{minimumFractionDigits:2})}`;
        el("r_valor").textContent = `R$ ${valorSistema.toLocaleString('pt-BR',{minimumFractionDigits:2})}`;
        el("r_payback").textContent = payback ? `${payback} anos` : "—";

        resultsSection.setAttribute("aria-hidden","false");

        const solicitarBtn = el("solicitarBtn");
        if (solicitarBtn) solicitarBtn.style.display = 'inline-block';
      });
  }

  calcBtn?.addEventListener("click", calcular);
  resetBtn?.addEventListener("click", () => {
    const form = document.getElementById("simForm");
    if (form) form.reset();
    resultsSection.setAttribute("aria-hidden","true");
    ["r_kwp","r_area","r_producao","r_economia_ano","r_valor","r_payback"].forEach(id => {
      const elId = document.getElementById(id);
      if (!elId) return;
      elId.textContent = id === "r_kwp" ? "— kWp" : (id === "r_area" ? "— m²" : "—");
    });
  });

  const form = document.getElementById("simForm");
  if (form) {
    form.addEventListener("submit", (e) => { e.preventDefault(); calcular(); });
  }
});