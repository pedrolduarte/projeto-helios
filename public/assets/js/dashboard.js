document.addEventListener("DOMContentLoaded", () => {
  /* ===== MENU ===== */
  const openMenuBtn = document.getElementById("openMenu");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("overlay");

  const openMenu = () => {
    sidebar.classList.add("open");
    overlay.classList.add("show");
  };
  const closeMenu = () => {
    sidebar.classList.remove("open");
    overlay.classList.remove("show");
  };

  openMenuBtn.addEventListener("click", openMenu);
  overlay.addEventListener("click", closeMenu);
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
  });

  /* ===== NAV SECTIONS ===== */
  const menuLinks = document.querySelectorAll(".menu a");
  const sections = document.querySelectorAll(".section");

  const showSection = (id) => {
    sections.forEach((s) => s.classList.remove("active"));
    document.getElementById(id).classList.add("active");
    menuLinks.forEach((l) =>
      l.classList.toggle("active", l.dataset.section === id)
    );
  };

  menuLinks.forEach((link) =>
    link.addEventListener("click", (e) => {
      e.preventDefault();
      showSection(link.dataset.section);
      closeMenu();
    })
  );

  document.getElementById("goToConsumo")?.addEventListener("click", () => showSection("consumo"));
  document.getElementById("goToConsumo2")?.addEventListener("click", () => showSection("consumo"));
  document.getElementById("goToSimulacao")?.addEventListener("click", () => showSection("simulacao"));

  /* ===== USER DROPDOWN ===== */
  const userBtn = document.getElementById("userBtn");
  const userDropdown = document.getElementById("userDropdown");

  userBtn.addEventListener("click", () => userDropdown.classList.toggle("show"));
  document.addEventListener("click", (e) => {
    if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
      userDropdown.classList.remove("show");
    }
  });

  userDropdown.querySelector('[data-section="profile"]')?.addEventListener("click", (e) => {
    e.preventDefault();
    showSection("profile");
    userDropdown.classList.remove("show");
  });

  // Logout: garantir que o clique redirecione ao controller de logout.
  const logoutLink = document.getElementById("logout");
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      // Não prevenir o comportamento padrão; forçar redirect para garantir compatibilidade
      const href = logoutLink.getAttribute('href');
      if (href) {
        window.location.href = href;
      }
    });
  }

  document.getElementById("openSettings").addEventListener("click", (e) => {
    e.preventDefault();
    alert("Configurações em breve 😉");
  });

  /* ===== CLIENT NAME (readonly - dados vêm do PHP) ===== */
  const clientName = document.getElementById("clientName");
  // Nome vem do banco via PHP, não precisa de localStorage
  console.log("Nome do cliente carregado:", clientName.textContent);
  
  // Limpar localStorage antigo (uma vez)
  if (localStorage.getItem("heliosClientName")) {
    localStorage.removeItem("heliosClientName");
    console.log("localStorage limpo");
  }

  /* ===== CHARTS - OVERVIEW ===== */
  const kpiBarCtx = document.getElementById("kpiBarChart");
  // A criação do gráfico ocorre mais abaixo, depois de carregar os dados de consumo
  // para que possamos popular o dataset de consumo com os valores do servidor.

  const donutEconomia = document.getElementById("donutEconomia");
  if (donutEconomia)
    new Chart(donutEconomia, {
      type: "doughnut",
      data: {
        labels: ["Atingido", "Restante"],
        datasets: [
          {
            data: [67, 33],
            backgroundColor: ["#ff9800", "#ffe0b2"],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "66%",
        plugins: { legend: { display: false } },
      },
    });

  const donutEficiencia = document.getElementById("donutEficiencia");
  if (donutEficiencia)
    new Chart(donutEficiencia, {
      type: "doughnut",
      data: {
        labels: ["Eficiência", "Perdas"],
        datasets: [
          {
            data: [92, 8],
            backgroundColor: ["#ff9800", "#ffe0b2"],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "66%",
        plugins: { legend: { display: false } },
      },
    });

  /* ===== CONSUMO (gráfico + tabela + cadastro) ===== */
  const consumoCtx = document.getElementById("consumoChart");
  const historyTable = document.getElementById("historyTable");
  const anoSelect = document.getElementById("anoSelect");
  
  let consumoChart = null;
  let dadosConsumoCache = {}; // Cache para evitar requisições desnecessárias

  // Função para buscar dados do controller
  async function carregarDadosConsumo(ano) {
    // Verificar se já temos os dados no cache
    if (dadosConsumoCache[ano]) {
      console.log(`Dados do ano ${ano} carregados do cache`);
      return dadosConsumoCache[ano];
    }

    try {
      console.log(`Buscando dados do ano ${ano} no servidor...`);
      const response = await fetch(`../controllers/costumer/consumoListController.php?ano=${ano}`);
      
      if (!response.ok) {
        throw new Error(`Erro HTTP: ${response.status}`);
      }
      
      // Debug: Verificar o que realmente está sendo retornado
      const responseText = await response.text();
      console.log('Resposta do servidor (raw):', responseText);
      
      // Tentar fazer parse do JSON
      let dados;
      try {
        dados = JSON.parse(responseText);
      } catch (parseError) {
        console.error('Erro ao fazer parse do JSON:', parseError);
        console.error('Conteúdo retornado:', responseText);
        throw new Error('Resposta do servidor não é um JSON válido');
      }
      
      if (dados.error) {
        throw new Error(dados.message);
      }
      
      // Salvar no cache
      dadosConsumoCache[ano] = dados;
      console.log(`Dados do ano ${ano} carregados:`, dados);
      
      return dados;
    } catch (error) {
      console.error('Erro ao carregar dados de consumo:', error);
      alert(`Erro: ${error.message}`);
      return [];
    }
  }

    // ===== Inicializa gráfico de KPI usando dados de consumo do ano atual =====
    (async () => {
      if (!kpiBarCtx) return;

      const meses = [
        "Jan", "Fev", "Mar", "Abr", "Mai", "Jun",
        "Jul", "Ago", "Set", "Out", "Nov", "Dez"
      ];

      // Valores de geração mantidos (pode ser adaptado futuramente)
      const geracao = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

      // Pega dados do ano atual (retorna array com {mes, consumo, economia} ou [])
      const currentYear = new Date().getFullYear();
      let dadosAno = [];
      try {
        dadosAno = await carregarDadosConsumo(currentYear);
      } catch (err) {
        console.error('Falha ao carregar dados de consumo para KPI:', err);
        dadosAno = [];
      }

      // Preencher array de consumo com zeros por padrão
      const consumoArr = new Array(12).fill(0);
      const geracaoArr = new Array(12).fill(0);

      // Mapeia os dados retornados para índices (meses) — aceita mes numérico ou string
      const monthNameMap = { jan:0, fev:1, mar:2, abr:3, mai:4, jun:5, jul:6, ago:7, set:8, out:9, nov:10, dez:11 };
      dadosAno.forEach(item => {
        let idx = null;
        if (typeof item.mes === 'number') idx = item.mes - 1;
        else if (!isNaN(parseInt(item.mes))) idx = parseInt(item.mes) - 1;
        else {
          const key = String(item.mes).toLowerCase().slice(0,3);
          if (monthNameMap[key] !== undefined) idx = monthNameMap[key];
        }

        if (idx !== null && idx >= 0 && idx < 12) {
          // garante número
          consumoArr[idx] = Number(item.consumo) || 0;
          geracaoArr[idx] = Number(item.geracao) || 0;
        }
      });

      // Cria instância do Chart e expõe para debug/atualizações futuras
      const kpiBarChart = new Chart(kpiBarCtx, {
        type: 'bar',
        data: {
          labels: meses,
          datasets: [
            {
              label: 'Geração (kWh)',
              data: geracaoArr,
              backgroundColor: 'rgba(255,152,0,.9)',
              borderRadius: 8,
              maxBarThickness: 28,
            },
            {
              label: 'Consumo (kWh)',
              data: consumoArr,
              backgroundColor: 'rgba(96, 96, 96, 0.5)',
              borderRadius: 8,
              maxBarThickness: 28,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: true } },
          scales: {
            y: { beginAtZero: true, ticks: { color: '#555' } },
            x: { ticks: { color: '#555' } },
          },
        }
      });

      // Disponibiliza no escopo global para inspeção/atualização manual
      window.kpiBarChart = kpiBarChart;
    })();

  // Função para calcular estatísticas (min/max)
  function calcularEstatisticas(dados) {
    if (!dados || dados.length === 0) {
      document.getElementById("minConsumo").textContent = "0 kWh";
      document.getElementById("maxConsumo").textContent = "0 kWh";
      return;
    }

    const consumos = dados.map(item => item.consumo);
    const minConsumo = Math.min(...consumos);
    const maxConsumo = Math.max(...consumos);

    document.getElementById("minConsumo").textContent = `${minConsumo} kWh`;
    document.getElementById("maxConsumo").textContent = `${maxConsumo} kWh`;
    
    console.log(`Estatísticas: Min=${minConsumo} kWh, Max=${maxConsumo} kWh`);
  }

  // Função para renderizar tabela e gráfico
  async function renderTabelaEAno(ano) {
    if (!historyTable || !consumoCtx) return;

    // Mostrar loading
    historyTable.innerHTML = "<tr><td colspan='3' style='text-align: center;'>Carregando...</td></tr>";
    
    // Buscar dados do controller
    const dados = await carregarDadosConsumo(ano);
    
    // Limpar tabela
    historyTable.innerHTML = "";
    
    // Referência ao container do gráfico
    const chartContainer = consumoCtx.closest('.panel');
    
    if (!dados || dados.length === 0) {
      historyTable.innerHTML = "<tr><td colspan='3' style='text-align: center; color: #999;'>Nenhum dado encontrado para este ano</td></tr>";
      
      // Ocultar gráfico quando não há dados
      if (chartContainer) {
        chartContainer.style.display = 'none';
      }
      
      // Destruir gráfico se existir
      if (consumoChart) {
        consumoChart.destroy();
        consumoChart = null;
      }
      
      // Zerar estatísticas
      calcularEstatisticas([]);
      return;
    }

    // Mostrar gráfico quando há dados
    if (chartContainer) {
      chartContainer.style.display = 'block';
    }

    // Renderizar tabela
    dados.forEach(item => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${item.mes}</td>
        <td>${item.consumo} kWh</td>
        <td>R$ ${item.economia.toFixed(2)}</td>
      `;
      historyTable.appendChild(tr);
    });

    // Calcular estatísticas
    calcularEstatisticas(dados);

    // Preparar dados para o gráfico
    const meses = dados.map(item => item.mes);
    const consumos = dados.map(item => item.consumo);

    // Destruir gráfico anterior se existir
    if (consumoChart) consumoChart.destroy();
    
    // Criar novo gráfico
    consumoChart = new Chart(consumoCtx, {
      type: "line",
      data: {
        labels: meses,
        datasets: [
          {
            label: `Consumo ${ano} (kWh)`,
            data: consumos,
            borderColor: "#ff9800",
            backgroundColor: "rgba(255,152,0,.15)",
            fill: true,
            tension: 0.35,
            pointRadius: 3,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { color: "#555" } },
          x: { ticks: { color: "#555" } },
        },
      },
    });
  }

  // Event listener para mudança de ano
  anoSelect?.addEventListener("change", (e) => renderTabelaEAno(e.target.value));
  
  // Carregar dados iniciais
  renderTabelaEAno(anoSelect?.value || "2025");

  /* ===== CADASTRO DE CONSUMO MENSAL COM AJAX ===== */
  const formConsumo = document.getElementById("formConsumo");
  const btnAddConsumo = document.getElementById("btnAddConsumo");
  const consumoToast = document.getElementById("consumoToast");

  // Função para mostrar notificação
  const showConsumoToast = (message, isSuccess = true) => {
    if (consumoToast) {
      consumoToast.textContent = isSuccess ? `✔ ${message}` : `✖ ${message}`;
      consumoToast.style.backgroundColor = isSuccess ? '#4caf50' : '#f44336';
      consumoToast.classList.add("show");
      setTimeout(() => consumoToast.classList.remove("show"), 3000);
    } else {
      alert(message);
    }
  };

  // Event listener para o formulário
  formConsumo?.addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevenir envio padrão do formulário

    const formData = new FormData(formConsumo);
    const ano = formData.get('ano_consumo');
    const mes = formData.get('mes_consumo');
    const consumoKwh = formData.get('consumo_kwh');

    // Validação básica
    if (!ano || !mes || !consumoKwh || parseFloat(consumoKwh) <= 0) {
      showConsumoToast("Por favor, preencha todos os campos com valores válidos.", false);
      return;
    }

    // Desabilitar botão durante requisição
    btnAddConsumo.disabled = true;
    btnAddConsumo.textContent = "Salvando...";

    try {
      console.log("Enviando dados:", { ano, mes, consumoKwh });

      const response = await fetch("../controllers/costumer/consumoRegisterController.php", {
        method: "POST",
        body: formData,
        redirect: 'manual' // Não seguir redirects automaticamente
      });

      // Capturar redirect manual
      if (response.type === 'opaqueredirect' || response.status === 302 || response.status === 301) {
        // Fazer nova requisição para pegar a URL final
        const finalResponse = await fetch("../controllers/costumer/consumoRegisterController.php", {
          method: "POST",
          body: formData
        });
        
        const finalUrl = new URL(finalResponse.url);
        const urlParams = new URLSearchParams(finalUrl.search);
        
        if (urlParams.has('success')) {
          const successType = urlParams.get('success');
          const messages = {
            'consumo_added': 'Consumo cadastrado com sucesso!',
            'consumo_updated': 'Consumo atualizado com sucesso!'
          };
          
          showConsumoToast(messages[successType] || 'Operação realizada com sucesso!');
          
          // Limpar formulário
          document.getElementById("consumoValor").value = "";
          
          // Limpar cache para forçar recarregamento
          delete dadosConsumoCache[ano];
          
          // Recarregar dados se estamos visualizando o mesmo ano
          if (anoSelect.value === ano) {
            await renderTabelaEAno(ano);
          }
          
        } else if (urlParams.has('error')) {
          const errorType = urlParams.get('error');
          const errorMessages = {
            'empty_fields': 'Por favor, preencha todos os campos.',
            'invalid_input': 'Por favor, insira valores numéricos válidos.',
            'server_error': 'Erro interno do servidor. Tente novamente.',
            'invalid_method': 'Método de requisição inválido.'
          };
          
          showConsumoToast(errorMessages[errorType] || 'Erro desconhecido. Tente novamente.', false);
        } else {
          // Se chegou aqui mas não tem parâmetros, é sucesso sem parâmetros
          showConsumoToast('Operação realizada com sucesso!');
          document.getElementById("consumoValor").value = "";
          delete dadosConsumoCache[ano];
          if (anoSelect.value === ano) {
            await renderTabelaEAno(ano);
          }
        }
      } 
      // Verificar se a resposta final tem redirected = true
      else if (response.redirected) {
        const url = new URL(response.url);
        const urlParams = new URLSearchParams(url.search);
        
        if (urlParams.has('success')) {
          const successType = urlParams.get('success');
          const messages = {
            'consumo_added': 'Consumo cadastrado com sucesso!',
            'consumo_updated': 'Consumo atualizado com sucesso!'
          };
          
          showConsumoToast(messages[successType] || 'Operação realizada com sucesso!');
          document.getElementById("consumoValor").value = "";
          delete dadosConsumoCache[ano];
          if (anoSelect.value === ano) {
            await renderTabelaEAno(ano);
          }
          
        } else if (urlParams.has('error')) {
          const errorType = urlParams.get('error');
          const errorMessages = {
            'empty_fields': 'Por favor, preencha todos os campos.',
            'invalid_input': 'Por favor, insira valores numéricos válidos.',
            'server_error': 'Erro interno do servidor. Tente novamente.',
            'invalid_method': 'Método de requisição inválido.'
          };
          
          showConsumoToast(errorMessages[errorType] || 'Erro desconhecido. Tente novamente.', false);
        }
      }
      // Se não há redirect, verificar se teve sucesso pela ausência de erro
      else if (response.ok) {
        showConsumoToast('Consumo salvo com sucesso!');
        document.getElementById("consumoValor").value = "";
        delete dadosConsumoCache[ano];
        if (anoSelect.value === ano) {
          await renderTabelaEAno(ano);
        }
      } else {
        throw new Error(`Erro do servidor: ${response.status}`);
      }

    } catch (error) {
      console.error('Erro ao cadastrar consumo:', error);
      showConsumoToast('Erro ao conectar com o servidor. Verifique sua conexão.', false);
    } finally {
      // Reabilitar botão
      btnAddConsumo.disabled = false;
      btnAddConsumo.textContent = "+ Adicionar";
    }
  });

  // Remover o antigo event listener do botão (se existir)
  const oldBtnEvent = btnAddConsumo?.cloneNode(true);
  if (oldBtnEvent) {
    btnAddConsumo?.parentNode.replaceChild(oldBtnEvent, btnAddConsumo);
  }

  /* ===== PERFIL ===== */
  const view = {
    name: document.getElementById("viewName"),
    email: document.getElementById("viewEmail"),
    phone: document.getElementById("viewPhone"),
    address: document.getElementById("viewAddress"),
  };

  const inputs = {
    name: document.getElementById("inputName"),
    email: document.getElementById("inputEmail"),
    phone: document.getElementById("inputPhone"),
    address: document.getElementById("inputAddress"),
  };

  const toast = document.getElementById("toast");
  document.getElementById("saveProfile").addEventListener("click", () => {
    if (inputs.name.value.trim()) {
      view.name.textContent = inputs.name.value.trim();
      document.getElementById("clientName").textContent = inputs.name.value.trim();
      // TODO: Implementar salvamento no banco via AJAX
    }
    if (inputs.email.value.trim()) view.email.textContent = inputs.email.value.trim();
    if (inputs.phone.value.trim()) view.phone.textContent = inputs.phone.value.trim();
    if (inputs.address.value.trim()) view.address.textContent = inputs.address.value.trim();

    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2200);
  });

  /* ===== SIMULAÇÃO (quantidade de placas) ===== */
  const selectPlaca = document.getElementById("selectPlaca");
  const potenciaPlaca = document.getElementById("potenciaPlaca");
  const inputConta = document.getElementById("inputConta");
  const quantPlacas = document.getElementById("quantPlacas");
  const pvConta = document.getElementById("pvConta");
  const pvPlaca = document.getElementById("pvPlaca");
  const pvQtd = document.getElementById("pvQtd");
  const pvGeracao = document.getElementById("pvGeracao");
  const pvEconomia = document.getElementById("pvEconomia");

  const updatePreview = () => {
    const conta = parseFloat(inputConta.value) || 0;
    const pot = parseFloat(selectPlaca.value) || 0;
    const qtd = parseInt(quantPlacas.value) || 1;

    pvConta.textContent = `R$ ${conta.toFixed(2)}`;
    pvPlaca.textContent = pot ? `${pot} W` : "—";
    pvQtd.textContent = qtd;

    const geracao = pot ? (pot * qtd * 4 * 30) / 1000 : 0;
    const economia = Math.min(conta, geracao * TARIFA);

    pvGeracao.textContent = `${geracao.toFixed(1)} kWh/mês`;
    pvEconomia.textContent = `R$ ${economia.toFixed(2)} / mês`;
  };

  selectPlaca?.addEventListener("change", () => {
    potenciaPlaca.textContent = selectPlaca.value ? `${selectPlaca.value} W` : "—";
    updatePreview();
  });
  inputConta?.addEventListener("input", updatePreview);
  quantPlacas?.addEventListener("input", updatePreview);

  const modal = document.getElementById("resultModal");
  const modalClose = document.getElementById("closeModal");
  const resultEl = document.getElementById("resultContent");
  const calcBtn = document.getElementById("calcSimulacao");

  const openModal = () => {
    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
  };

  const closeModal = () => {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
  };

  modalClose.addEventListener("click", closeModal);
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  calcBtn.addEventListener("click", () => {
    const conta = parseFloat(inputConta.value);
    const pot = parseFloat(selectPlaca.value);
    const qtd = parseInt(quantPlacas.value);

    if (isNaN(conta) || conta <= 0) return alert("Informe um valor válido para a conta.");
    if (isNaN(pot)) return alert("Selecione uma placa.");
    if (isNaN(qtd) || qtd < 1) return alert("Informe a quantidade de placas.");

    const geracao = (pot * qtd * 4 * 30) / 1000;
    const economia = Math.min(conta, geracao * TARIFA);

    resultEl.innerHTML = `
      <p><strong>Conta atual:</strong> R$ ${conta.toFixed(2)}</p>
      <p><strong>Placa escolhida:</strong> ${pot} W</p>
      <p><strong>Quantidade:</strong> ${qtd}</p>
      <p><strong>Geração estimada:</strong> ${geracao.toFixed(1)} kWh/mês</p>
      <p><strong>Economia estimada:</strong> R$ ${economia.toFixed(2)} / mês</p>
    `;
    openModal();
  });

  /* ===== CEP Lookup para formulário de perfil (ViaCEP) ===== */
  const inputCep = document.getElementById('inputCep');
  const inputLogradouro = document.getElementById('inputLogradouro');

  async function buscarCep(cep) {
    try {
      const c = cep.replace(/[^0-9]/g, '');
      if (c.length !== 8) return null;
      const res = await fetch(`https://viacep.com.br/ws/${c}/json/`);
      if (!res.ok) return null;
      const data = await res.json();
      if (data.erro) return null;
      return data; // {logradouro, bairro, localidade, uf, ...}
    } catch (e) {
      console.error('CEP lookup falhou', e);
      return null;
    }
  }

  if (inputCep) {
    inputCep.addEventListener('blur', async () => {
      const data = await buscarCep(inputCep.value);
      if (data && inputLogradouro) {
        inputLogradouro.value = `${data.logradouro || ''} ${data.bairro || ''} ${data.localidade || ''} ${data.uf || ''}`.trim();
      }
    });
  }
});
