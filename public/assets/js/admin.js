document.addEventListener("DOMContentLoaded", () => {
  /* ===== Navegação ===== */
  const links = document.querySelectorAll(".admin-nav a");
  const sections = document.querySelectorAll(".section");
  links.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      links.forEach(l => l.classList.remove("active"));
      link.classList.add("active");
      const id = link.dataset.section;
      sections.forEach(s => s.classList.toggle("active", s.id === id));
    });
  });

  /* ===== Logout (simulado) ===== */
  document.getElementById("logout")?.addEventListener("click", (e) => {
    e.preventDefault();
    window.location.href = "../controllers/finishSessionController.php";
  });

  /* ===== Dashboard Chart ===== */
  const dashCtx = document.getElementById("dashChart");
  if (dashCtx) {
    // Função para carregar dados reais do controller
    const loadChartData = async () => {
      try {
        console.log('Carregando dados do gráfico...');
        
        // Fetch dados do controller
        const response = await fetch('../controllers/admin/registersMonthController.php', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        // Primeiro verificar o que está sendo retornado
        const responseText = await response.text();
        console.log('Resposta raw do servidor:', responseText);

        // Tentar fazer parse do JSON
        let result;
        try {
          result = JSON.parse(responseText);
        } catch (parseError) {
          console.error('Erro de parse JSON:', parseError);
          console.error('Conteúdo retornado:', responseText);
          throw new Error('Servidor retornou conteúdo inválido: ' + responseText.substring(0, 200));
        }

        console.log('Resposta do controller:', result);

        if (result.error) {
          throw new Error(result.message || 'Erro desconhecido do servidor');
        }

        // Processar dados recebidos
        const cadastrosMensais = result.data || [];
        
        // Criar arrays completos para todos os 12 meses
        const mesesCompletos = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
        const dadosCompletos = new Array(12).fill(0); // Inicializar com zeros
        
        // Mapear dados recebidos para os meses corretos
        cadastrosMensais.forEach(item => {
          const mesNome = item.mes;
          const total = item.total || 0;
          
          // Encontrar índice do mês no array completo
          const mesesPorExtenso = [
            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
          ];
          
          const mesIndex = mesesPorExtenso.indexOf(mesNome);
          if (mesIndex !== -1) {
            dadosCompletos[mesIndex] = total;
          }
        });

        console.log('Dados processados para o gráfico:', dadosCompletos);

        // Criar gráfico com dados reais
        new Chart(dashCtx, {
          type: "bar",
          data: {
            labels: mesesCompletos,
            datasets: [
              { 
                label: "Cadastros de Clientes", 
                data: dadosCompletos, 
                backgroundColor: "rgba(255,152,0,.9)", 
                borderColor: "rgba(255,152,0,1)",
                borderWidth: 1,
                borderRadius: 8, 
                maxBarThickness: 28 
              },
            ]
          },
          options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
              legend: { display: true },
              title: {
                display: true,
                text: `Cadastros de Clientes - ${new Date().getFullYear()}`
              }
            },
            scales: { 
              y: { 
                beginAtZero: true,
                ticks: {
                  stepSize: 1 // Mostrar apenas números inteiros
                }
              }, 
              x: {} 
            }
          }
        });

      } catch (error) {
        console.error('Erro ao carregar dados do gráfico:', error);
        
        // Fallback: usar dados de exemplo em caso de erro
        const mesesFallback = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
        const dadosFallback = [5,8,12,15,10,7,9,14,18,22,16,11]; // Dados de exemplo mais realistas
        
        new Chart(dashCtx, {
          type: "bar",
          data: {
            labels: mesesFallback,
            datasets: [
              { 
                label: "Cadastros (Dados de Exemplo)", 
                data: dadosFallback, 
                backgroundColor: "rgba(255,152,0,.5)", // Mais transparente para indicar que são dados de exemplo
                borderColor: "rgba(255,152,0,1)",
                borderWidth: 2,
                borderRadius: 8, 
                maxBarThickness: 28 
              },
            ]
          },
          options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
              legend: { display: true },
              title: {
                display: true,
                text: "⚠️ Dados não disponíveis - Exibindo exemplo"
              }
            },
            scales: { 
              y: { beginAtZero: true }, 
              x: {} 
            }
          }
        });
      }
    };

    // Carregar dados quando a página carrega
    loadChartData();
  }

  /* ===== Clientes: busca simples e modal ===== */
  const searchCliente = document.getElementById("searchCliente");
  const clientesTable = document.getElementById("clientesTable");
  if (searchCliente && clientesTable) {
    searchCliente.addEventListener("input", () => {
      const q = searchCliente.value.toLowerCase();
      [...clientesTable.tBodies[0].rows].forEach(r => {
        const txt = r.innerText.toLowerCase();
        r.style.display = txt.includes(q) ? "" : "none";
      });
    });
  }

  const toast = document.getElementById("toast");
  const modalCliente = document.getElementById("modalCliente");
  const fecharModalCliente = document.getElementById("fecharModalCliente");
  const salvarCliente = document.getElementById("salvarCliente");
  const formCliente = document.getElementById("formCliente");
  const cliNome = document.getElementById("cliNome");
  const cliEmail = document.getElementById("cliEmail");
  const cliStatus = document.getElementById("cliStatus");
  const modalClienteTitulo = document.getElementById("modalClienteTitulo");
  const cliCep = document.getElementById("cliCep");
  const cliNumero = document.getElementById("cliNumero");
  const cliTelefone = document.getElementById("cliTelefone");
  const cliNascimento = document.getElementById("cliNascimento");

  let clienteModo = "ver"; // "ver" ou "editar"

  const openModalCliente = (row, modo="ver") => {
    clienteModo = modo;
    const tds = row.querySelectorAll("td");
    cliNome.value = tds[1].innerText.trim();
    cliEmail.value = tds[2].innerText.trim();
    cliStatus.value = tds[3].innerText.includes("Ativo") ? "Ativo" : "Inativo";
    // lê dataset endereços (se existirem)
    cliCep.value = row.dataset.cep || "";
    cliNumero.value = row.dataset.numero || "";
    cliTelefone.value = row.dataset.telefone || "";
    cliNascimento.value = row.dataset.nascimento || "";
    modalClienteTitulo.textContent = (modo === "editar") ? "Editar Cliente" : "Dados do Cliente";

    const readOnly = modo === "ver";
    [cliNome, cliEmail, cliStatus, cliCep, cliNumero, cliTelefone, cliNascimento].forEach(el => {
      if (el) el.disabled = readOnly;
    });

    salvarCliente.classList.toggle("hidden", readOnly);
    modalCliente.classList.add("show");
    modalCliente.setAttribute("aria-hidden","false");
    salvarCliente.dataset.rowIndex = row.rowIndex;
  };
  const closeModalCliente = () => {
    modalCliente.classList.remove("show");
    modalCliente.setAttribute("aria-hidden","true");
    salvarCliente.classList.remove("hidden");
  };
  fecharModalCliente?.addEventListener("click", closeModalCliente);
  modalCliente?.addEventListener("click", (e) => { if (e.target === modalCliente) closeModalCliente(); });

  clientesTable?.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const row = e.target.closest("tr");
    if (!row) return;

    if (btn.classList.contains("ver-cadastro")) openModalCliente(row, "ver");
    if (btn.classList.contains("editar-cadastro")) openModalCliente(row, "editar");
    if (btn.classList.contains("tornar-admin")) {
      toast.textContent = "Permissão alterada: usuário agora é Admin";
      toast.classList.add("show");
      setTimeout(()=> toast.classList.remove("show"), 2200);
    }
  });

  salvarCliente?.addEventListener("click", () => {
    if (clienteModo !== "editar") { closeModalCliente(); return; }
    const idx = parseInt(salvarCliente.dataset.rowIndex, 10);
    if (clientesTable && idx > 0) {
      const row = clientesTable.rows[idx];
      row.cells[1].innerText = cliNome.value.trim() || row.cells[1].innerText;
      row.cells[2].innerText = cliEmail.value.trim() || row.cells[2].innerText;
      row.cells[3].innerHTML = `<span class="badge ${cliStatus.value === "Ativo" ? "success" : ""}">${cliStatus.value}</span>`;
      if (cliCep) row.dataset.cep = cliCep.value.trim();
      if (cliNumero) row.dataset.numero = cliNumero.value.trim();
      if (cliTelefone) row.dataset.telefone = cliTelefone.value.trim();
      if (cliNascimento) row.dataset.nascimento = cliNascimento.value || "";
    }
    closeModalCliente();
    toast.textContent = "Cadastro do cliente salvo com sucesso";
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 2200);
  });

  /* ===== ORÇAMENTOS ===== */
  const orcamentosTableBody = document.querySelector("#orcamentosTable tbody");
  const novoOrcBtn = document.getElementById("novoOrcBtn");

  // exemplo inicial (pode vir do servidor/localStorage)
  const orcamentos = [
    { id: 1, nome: "Carlos Pereira", endereco: "R. Verde, 22", finalizado: false },
    { id: 2, nome: "Ana Oliveira", endereco: "Av. Leste, 100", finalizado: true }
  ];

  const renderOrcamentos = () => {
    if (!orcamentosTableBody) return;
    orcamentosTableBody.innerHTML = "";
    orcamentos.forEach(o => {
      const tr = document.createElement("tr");
      tr.dataset.id = o.id;
      tr.innerHTML = `
        <td>${o.id}</td>
        <td>${o.nome}</td>
        <td>${o.endereco}</td>
        <td>${o.finalizado ? '<span class="badge success">Sim</span>' : '<span class="badge">Não</span>'}</td>
        <td class="actions">
          <button class="btn-small ${o.finalizado ? 'btn-small.edit' : 'btn'} finalizar-btn" data-id="${o.id}" type="button">${o.finalizado ? 'Reabrir' : 'Finalizar'}</button>
          <button class="btn-small del excluir-orc" data-id="${o.id}" type="button" title="Remover"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
        </td>`;
      orcamentosTableBody.appendChild(tr);
    });
  };

  novoOrcBtn?.addEventListener("click", () => {
    const id = orcamentos.length ? orcamentos[orcamentos.length-1].id + 1 : 1;
    orcamentos.push({ id, nome: `Cliente ${id}`, endereco: "Endereço exemplo", finalizado: false });
    renderOrcamentos();
    toast.textContent = "Orçamento criado (exemplo)";
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 1800);
  });

  orcamentosTableBody?.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const id = parseInt(btn.dataset.id, 10);
    const idx = orcamentos.findIndex(o => o.id === id);
    if (btn.classList.contains("finalizar-btn")) {
      if (idx === -1) return;
      orcamentos[idx].finalizado = !orcamentos[idx].finalizado;
      renderOrcamentos();
      toast.textContent = orcamentos[idx].finalizado ? "Orçamento finalizado" : "Orçamento reaberto";
      toast.classList.add("show");
      setTimeout(()=> toast.classList.remove("show"), 1800);
    }
    if (btn.classList.contains("excluir-orc")) {
      if (idx === -1) return;
      if (!confirm("Remover este orçamento?")) return;
      orcamentos.splice(idx,1);
      renderOrcamentos();
      toast.textContent = "Orçamento removido";
      toast.classList.add("show");
      setTimeout(()=> toast.classList.remove("show"), 1800);
    }
  });

  renderOrcamentos();

  /* ===== SIMULAÇÕES ===== */
  const simulacoesTableBody = document.querySelector("#simulacoesTable tbody");
  // carregar simulações de exemplo ou localStorage
  const simulacoes = JSON.parse(localStorage.getItem("simulacoes")) || [
    { id: 1, consumo: 300, tarifa: 0.90, cobertura: 80, area: 20.0, valor: 12682.8 },
    { id: 2, consumo: 450, tarifa: 0.95, cobertura: 70, area: 30.0, valor: 21000.0 }
  ];

  const renderSimulacoes = () => {
    if (!simulacoesTableBody) return;
    simulacoesTableBody.innerHTML = "";
    simulacoes.forEach(s => {
      const tr = document.createElement("tr");
      tr.dataset.id = s.id;
      tr.innerHTML = `
        <td>${s.id}</td>
        <td>${s.consumo}</td>
        <td>R$ ${s.tarifa.toFixed(2)}</td>
        <td>${s.cobertura}%</td>
        <td>${(s.area || '—')}</td>
        <td>R$ ${Number(s.valor).toLocaleString('pt-BR', {minimumFractionDigits:2})}</td>
        <td class="actions">
          <button class="btn-small del excluir-sim" data-id="${s.id}" type="button" title="Remover"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
        </td>`;
      simulacoesTableBody.appendChild(tr);
    });
  };

  simulacoesTableBody?.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    if (btn.classList.contains("excluir-sim")) {
      const id = parseInt(btn.dataset.id,10);
      const idx = simulacoes.findIndex(s => s.id === id);
      if (idx === -1) return;
      if (!confirm("Remover esta simulação?")) return;
      simulacoes.splice(idx,1);
      localStorage.setItem("simulacoes", JSON.stringify(simulacoes));
      renderSimulacoes();
      toast.textContent = "Simulação removida";
      toast.classList.add("show");
      setTimeout(()=> toast.classList.remove("show"), 1800);
    }
  });

  renderSimulacoes();

  /* ===== Usuários: CRUD Simulado (mantido) ===== */
  const usuariosTable = document.getElementById("usuariosTable")?.querySelector("tbody");
  const novoUsuarioBtn = document.getElementById("novoUsuarioBtn");
  const modalUsuario = document.getElementById("modalUsuario");
  const fecharModalUsuario = document.getElementById("fecharModalUsuario");
  const salvarUsuario = document.getElementById("salvarUsuario");
  const modalUsuarioTitulo = document.getElementById("modalUsuarioTitulo");
  const formUsuario = document.getElementById("formUsuario");
  const senhaField = document.getElementById("senhaField");

  let editMode = false;
  let editRow = null;

  const openModalUsuario = (modo = "novo", row = null) => {
    editMode = modo === "editar";
    editRow = row;
    formUsuario.reset();

    modalUsuarioTitleText = editMode ? "Editar Usuário" : "Novo Usuário";
    modalUsuarioTitleText = modalUsuarioTitleText; // no-op to keep linter quiet
    modalUsuarioTitleElement = modalUsuarioTitulo; // no-op
    senhaField.style.display = editMode ? "none" : "block";
    modalUsuario.classList.add("show");
    modalUsuario.setAttribute("aria-hidden", "false");

    if (editMode && row) {
      const cells = row.querySelectorAll("td");
      document.getElementById("userNome").value = cells[1].innerText;
      document.getElementById("userEmail").value = cells[2].innerText;
      document.getElementById("userFuncao").value = cells[3].innerText;
      document.getElementById("userStatus").value = cells[4].innerText.includes("Ativo") ? "Ativo" : "Inativo";
    }
  };

  const closeModalUsuario = () => {
    modalUsuario.classList.remove("show");
    modalUsuario.setAttribute("aria-hidden", "true");
  };

  fecharModalUsuario?.addEventListener("click", closeModalUsuario);
  modalUsuario?.addEventListener("click", (e) => { if (e.target === modalUsuario) closeModalUsuario(); });

  novoUsuarioBtn?.addEventListener("click", () => openModalUsuario("novo"));

  salvarUsuario?.addEventListener("click", () => {
    const nome = document.getElementById("userNome").value;
    const email = document.getElementById("userEmail").value;
    const funcao = document.getElementById("userFuncao").value;
    const status = document.getElementById("userStatus").value;

    if (!nome || !email) return alert("Preencha todos os campos obrigatórios!");

    if (editMode && editRow) {
      const c = editRow.querySelectorAll("td");
      c[1].innerText = nome;
      c[2].innerText = email;
      c[3].innerText = funcao;
      c[4].innerHTML = `<span class="badge ${status === "Ativo" ? "success" : ""}">${status}</span>`;
    } else if (usuariosTable) {
      const id = usuariosTable.rows.length + 1;
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${id}</td>
        <td>${nome}</td>
        <td>${email}</td>
        <td>${funcao}</td>
        <td><span class="badge ${status === "Ativo" ? "success" : ""}">${status}</span></td>
        <td class="actions">
          <button class="btn-small edit editar-usuario"><i class="fa-solid fa-pen"></i></button>
          <button class="btn-small del excluir-usuario"><i class="fa-solid fa-trash"></i></button>
          <button class="btn-small warn reset-senha"><i class="fa-solid fa-key"></i></button>
        </td>`;
      usuariosTable.appendChild(tr);
    }

    closeModalUsuario();
    toast.textContent = editMode ? "Usuário atualizado com sucesso!" : "Usuário cadastrado com sucesso!";
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2000);
  });

  usuariosTable?.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const row = e.target.closest("tr");

    if (btn.classList.contains("editar-usuario")) openModalUsuario("editar", row);

    if (btn.classList.contains("excluir-usuario")) {
      row.remove();
      toast.textContent = "Usuário removido!";
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 2000);
    }

    if (btn.classList.contains("reset-senha")) {
      toast.textContent = "Senha redefinida e enviada ao e-mail do usuário.";
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 2500);
    }
  });
});