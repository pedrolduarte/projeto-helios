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
  document.getElementById("logout").addEventListener("click", (e) => {
    e.preventDefault();
    alert("Sessão encerrada com sucesso!");
  });

  /* ===== Dashboard Chart ===== */
  const dashCtx = document.getElementById("dashChart");
  if (dashCtx) {
    const meses = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
    const geracao = [210,230,240,270,310,320,340,330,300,280,250,240];
    const consumo = [190,200,205,220,250,260,270,265,240,230,220,215];
    new Chart(dashCtx, {
      type: "bar",
      data: {
        labels: meses,
        datasets: [
          { label:"Geração (kWh)", data:geracao, backgroundColor:"rgba(255,152,0,.9)", borderRadius:8, maxBarThickness:28 },
          { label:"Consumo (kWh)", data:consumo, backgroundColor:"rgba(0,0,0,.08)", borderRadius:8, maxBarThickness:28 }
        ]
      },
      options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:true } },
        scales:{ y:{ beginAtZero:true }, x:{} }
      }
    });
  }

  /* ===== Clientes: busca simples ===== */
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

  /* ===== Clientes modal ===== */
  const toast = document.getElementById("toast");
  const modalCliente = document.getElementById("modalCliente");
  const fecharModalCliente = document.getElementById("fecharModalCliente");
  const salvarCliente = document.getElementById("salvarCliente");
  const formCliente = document.getElementById("formCliente");
  const cliNome = document.getElementById("cliNome");
  const cliEmail = document.getElementById("cliEmail");
  const cliStatus = document.getElementById("cliStatus");
  const modalClienteTitulo = document.getElementById("modalClienteTitulo");
  const cliEndereco = document.getElementById("cliEndereco");
  const cliTelefone = document.getElementById("cliTelefone");
  const cliNascimento = document.getElementById("cliNascimento");

  let clienteModo = "ver"; // "ver" ou "editar"

  const openModalCliente = (row, modo="ver") => {
    clienteModo = modo;
    const tds = row.querySelectorAll("td");
    cliNome.value = tds[1].innerText.trim();
    cliEmail.value = tds[2].innerText.trim();
    cliStatus.value = tds[3].innerText.includes("Ativo") ? "Ativo" : "Inativo";
    cliEndereco.value = row.dataset.endereco || "";
    cliTelefone.value = row.dataset.telefone || "";
    cliNascimento.value = row.dataset.nascimento || "";
    modalClienteTitulo.textContent = (modo === "editar") ? "Editar Cliente" : "Dados do Cliente";

    const readOnly = modo === "ver";
    cliNome.disabled = readOnly;
    cliEmail.disabled = readOnly;
    cliStatus.disabled = readOnly;
    cliEndereco.disabled = readOnly;
    cliTelefone.disabled = readOnly;
    cliNascimento.disabled = readOnly;

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
  fecharModalCliente.addEventListener("click", closeModalCliente);
  modalCliente.addEventListener("click", (e) => { if (e.target === modalCliente) closeModalCliente(); });

  if (clientesTable) {
    clientesTable.addEventListener("click", (e) => {
      const btn = e.target.closest("button");
      if (!btn) return;
      const row = e.target.closest("tr");

      if (btn.classList.contains("ver-cadastro")) {
        openModalCliente(row, "ver");
      }
      if (btn.classList.contains("editar-cadastro")) {
        openModalCliente(row, "editar");
      }
      if (btn.classList.contains("tornar-admin")) {
        toast.textContent = "Permissão alterada: usuário agora é Admin";
        toast.classList.add("show");
        setTimeout(()=> toast.classList.remove("show"), 2200);
      }
    });
  }

  salvarCliente.addEventListener("click", () => {
    if (clienteModo !== "editar") {
      closeModalCliente();
      return;
    }

    const idx = parseInt(salvarCliente.dataset.rowIndex, 10);
    if (clientesTable && idx > 0) {
      const row = clientesTable.rows[idx];
      row.cells[1].innerText = cliNome.value.trim() || row.cells[1].innerText;
      row.cells[2].innerText = cliEmail.value.trim() || row.cells[2].innerText;
      row.cells[3].innerHTML = `<span class="badge ${cliStatus.value === "Ativo" ? "success" : ""}">${cliStatus.value}</span>`;
      if (cliEndereco) row.dataset.endereco = cliEndereco.value.trim();
      if (cliTelefone) row.dataset.telefone = cliTelefone.value.trim();
      if (cliNascimento) row.dataset.nascimento = cliNascimento.value || "";
    }
    closeModalCliente();
    toast.textContent = "Cadastro do cliente salvo com sucesso";
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 2200);
  });

  /* ===== Placas: carrossel de cadastro ===== */
  const novaPlacaBtn = document.getElementById("novaPlacaBtn");
  const placasListagem = document.getElementById("placasListagem");
  const carrosselPlaca = document.getElementById("carrosselPlaca");
  const cancelarCarrossel = document.getElementById("cancelarCarrossel");

  const bullets = document.querySelectorAll(".carrossel-steps .step-bullet");
  const pages = document.querySelectorAll(".carrossel-page");
  const voltarStep = document.getElementById("voltarStep");
  const proximoStep = document.getElementById("proximoStep");
  const finalizarCadastro = document.getElementById("finalizarCadastro");
  const formPlaca = document.getElementById("formPlaca");
  const tabelaPlacasBody = document.querySelector("#tabelaPlacas tbody");
  const itensCriadosEl = document.getElementById("itensCriados");
  const listCelulas = document.getElementById("listCelulas");
  const listVidros = document.getElementById("listVidros");
  const listEstruturas = document.getElementById("listEstruturas");
  const listCaixas = document.getElementById("listCaixas");
  const listTamanhos = document.getElementById("listTamanhos");

  const sel_celula = document.getElementById("sel_celula");
  const sel_vidro = document.getElementById("sel_vidro");
  const sel_estrutura = document.getElementById("sel_estrutura");
  const sel_caixa = document.getElementById("sel_caixa");
  const sel_tamanho = document.getElementById("sel_tamanho");

  // temporários
  const celulas = [];
  const vidros = [];
  const estruturas = [];
  const caixas = [];
  const tamanhos = [];

  // modal placa elements
  const modalPlaca = document.getElementById("modalPlaca");
  const fecharModalPlaca = document.getElementById("fecharModalPlaca");
  const fecharModalPlacaBtn = document.getElementById("fecharModalPlacaBtn");
  const p_modelo = document.getElementById("p_modelo");
  const p_preco = document.getElementById("p_preco");
  const p_potencia = document.getElementById("p_potencia");
  const p_eficiencia = document.getElementById("p_eficiencia");
  const p_tens_pot = document.getElementById("p_tens_pot");
  const p_corr_pot = document.getElementById("p_corr_pot");
  const p_tens_circ = document.getElementById("p_tens_circ");
  const p_corr_curto = document.getElementById("p_corr_curto");
  const p_num_cel = document.getElementById("p_num_cel");
  const p_peso = document.getElementById("p_peso");
  const p_imetro = document.getElementById("p_imetro");
  const p_fusivel = document.getElementById("p_fusivel");
  const p_itens_rel = document.getElementById("p_itens_rel");

  let step = 1;
  const totalSteps = 6;
  let placaEditRow = null; // se estiver editando

  const renderItensList = () => {
    const render = (el, arr, type) => {
      el.innerHTML = "";
      arr.forEach((it, idx) => {
        const li = document.createElement("li");
        li.style.display = "flex";
        li.style.justifyContent = "space-between";
        li.style.alignItems = "center";
        li.innerHTML = `<span>${it.label || it}</span> <div><button data-idx="${idx}" data-type="${type}" class="btn-small del remove-item">Remover</button></div>`;
        el.appendChild(li);
      });
      if (arr.length === 0) el.innerHTML = "<li style='opacity:.6'>— vazio —</li>";
    };
    if (listCelulas) render(listCelulas, celulas, "celula");
    if (listVidros) render(listVidros, vidros, "vidro");
    if (listEstruturas) render(listEstruturas, estruturas, "estrutura");
    if (listCaixas) render(listCaixas, caixas, "caixa");
    if (listTamanhos) render(listTamanhos, tamanhos, "tamanho");
  };

  document.getElementById("clearCelulas")?.addEventListener("click", () => { celulas.length = 0; renderItensList(); });
  document.getElementById("clearVidros")?.addEventListener("click", () => { vidros.length = 0; renderItensList(); });
  document.getElementById("clearEstruturas")?.addEventListener("click", () => { estruturas.length = 0; renderItensList(); });
  document.getElementById("clearCaixas")?.addEventListener("click", () => { caixas.length = 0; renderItensList(); });
  document.getElementById("clearTamanhos")?.addEventListener("click", () => { tamanhos.length = 0; renderItensList(); });

  const setStep = (n) => {
    step = n;
    pages.forEach(p => p.classList.toggle("active", parseInt(p.dataset.step) === step));
    bullets.forEach((b, i) => b.classList.toggle("active", i < step));
    voltarStep.disabled = (step === 1);
    proximoStep.classList.toggle("hidden", step === totalSteps);
    finalizarCadastro.classList.toggle("hidden", step !== totalSteps);

    if (step === totalSteps) {
      const sel = (el, items) => {
        if (!el) return;
        el.innerHTML = "";
        el.appendChild(new Option(items.length ? "— selecione —" : "— vazio —", ""));
        items.forEach((it, i) => el.appendChild(new Option(it.label || it, i)));
      };
      sel(sel_celula, celulas);
      sel(sel_vidro, vidros);
      sel(sel_estrutura, estruturas);
      sel(sel_caixa, caixas);
      sel(sel_tamanho, tamanhos);
    }
  };

  const openCarrossel = (editRow = null) => {
    placasListagem.classList.add("hidden");
    carrosselPlaca.classList.remove("hidden");
    formPlaca.reset();
    placaEditRow = null;
    renderItensList();
    setStep(1);

    if (editRow) {
      // avançar até etapa final e popular selects
      renderItensList();
      setStep(totalSteps);
      // preencher campos a partir dos data-attributes da linha
      const r = editRow;
      document.getElementById("preco").value = r.dataset.preco || "";
      document.getElementById("potencia").value = r.dataset.potencia || "";
      document.getElementById("eficiencia").value = r.dataset.eficiencia || "";
      document.getElementById("tens_potencia").value = r.dataset.tens_pot || "";
      document.getElementById("corrente_potencia").value = r.dataset.corr_pot || "";
      document.getElementById("tens_circ").value = r.dataset.tens_circ || "";
      document.getElementById("corrente_curto").value = r.dataset.corr_curto || "";
      document.getElementById("tolerancia").value = r.dataset.toler || "";
      document.getElementById("temp_min").value = r.dataset.temp_min || "";
      document.getElementById("temp_max").value = r.dataset.temp_max || "";
      document.getElementById("temp_max_suporte").value = r.dataset.temp_sup || "";
      document.getElementById("fusivel").value = r.dataset.fusivel || "";
      document.getElementById("num_celulas").value = r.dataset.num_cel || "";
      document.getElementById("peso").value = r.dataset.peso || "";
      document.getElementById("imetro").value = r.dataset.imetro || "";

      // selecionar itens (indices)
      sel_celula.value = r.dataset.celula || "";
      sel_vidro.value = r.dataset.vidro || "";
      sel_estrutura.value = r.dataset.estrutura || "";
      sel_caixa.value = r.dataset.caixa || "";
      sel_tamanho.value = r.dataset.tamanho || "";

      placaEditRow = r;
    }
  };
  const closeCarrossel = () => {
    carrosselPlaca.classList.add("hidden");
    placasListagem.classList.remove("hidden");
    placaEditRow = null;
  };

  novaPlacaBtn.addEventListener("click", () => openCarrossel(null));
  cancelarCarrossel.addEventListener("click", closeCarrossel);
  voltarStep.addEventListener("click", () => { if (step > 1) setStep(step - 1); });
  proximoStep.addEventListener("click", () => {
     if (step === 1) {
       const d = document.getElementById("celula_dsc").value.trim();
       if (!d) return alert("Preencha a descrição da célula.");
       celulas.push({ label: d, dsc: d });
     } else if (step === 2) {
       const tipo = document.getElementById("vidro_tipo").value.trim();
       const esp = document.getElementById("vidro_espessura").value.trim();
       if (!tipo) return alert("Preencha o tipo de vidro.");
       vidros.push({ label: `${tipo} ${esp ? `(${esp}mm)` : ""}`, tipo, esp });
     } else if (step === 3) {
       const d = document.getElementById("estrutura_dsc").value.trim();
       if (!d) return alert("Preencha a descrição da estrutura.");
       estruturas.push({ label: d, dsc: d });
     } else if (step === 4) {
       const ip = document.getElementById("caixa_ip").value.trim();
       const did = document.getElementById("caixa_did").value.trim();
       const esp_cabo = document.getElementById("caixa_esp_cabo").value.trim();
       const comp = document.getElementById("caixa_comp").value.trim();
       const tipo_con = document.getElementById("caixa_tipo_con").value.trim();
       if (!ip && !did) return alert("Preencha pelo menos IP ou DID da caixa.");
       caixas.push({ label: `${ip || did}`, ip, did, esp_cabo, comp, tipo_con });
     } else if (step === 5) {
       const alt = document.getElementById("tam_altura").value.trim();
       const lar = document.getElementById("tam_largura").value.trim();
       const esp = document.getElementById("tam_espessura").value.trim();
       if (!alt || !lar) return alert("Preencha altura e largura do tamanho.");
       tamanhos.push({ label: `${alt}x${lar}x${esp}`, alt, lar, esp });
     }

     renderItensList();
     if (step < totalSteps) setStep(step + 1);
  });

  itensCriadosEl?.addEventListener("click", (e) => {
    const btn = e.target.closest(".remove-item");
    if (!btn) return;
    const idx = parseInt(btn.dataset.idx, 10);
    const type = btn.dataset.type;
    if (type === "celula") celulas.splice(idx,1);
    if (type === "vidro") vidros.splice(idx,1);
    if (type === "estrutura") estruturas.splice(idx,1);
    if (type === "caixa") caixas.splice(idx,1);
    if (type === "tamanho") tamanhos.splice(idx,1);
    renderItensList();
  });

  formPlaca.addEventListener("submit", (e) => {
    e.preventDefault();

    const preco = parseFloat(document.getElementById("preco").value || 0).toFixed(2);
    const potencia = parseInt(document.getElementById("potencia").value || 0, 10);
    const eficiencia = parseFloat(document.getElementById("eficiencia").value || 0).toFixed(2);
    const tens_pot = parseFloat(document.getElementById("tens_potencia").value || 0).toFixed(2);
    const corr_pot = parseFloat(document.getElementById("corrente_potencia").value || 0).toFixed(2);
    const tens_circ = parseFloat(document.getElementById("tens_circ").value || 0).toFixed(2);
    const corr_curto = parseFloat(document.getElementById("corrente_curto").value || 0).toFixed(2);
    const toler = parseInt(document.getElementById("tolerancia").value || 0, 10);
    const temp_min = parseInt(document.getElementById("temp_min").value || 0, 10);
    const temp_max = parseInt(document.getElementById("temp_max").value || 0, 10);
    const temp_sup = parseInt(document.getElementById("temp_max_suporte").value || 0, 10);
    const fusivel = parseInt(document.getElementById("fusivel").value || 0, 10);
    const num_cel = parseInt(document.getElementById("num_celulas").value || 0, 10);
    const peso = parseFloat(document.getElementById("peso").value || 0).toFixed(1);
    const imetro = document.getElementById("imetro").value || "";

    const selCel = sel_celula.value;
    const selVid = sel_vidro.value;
    const selEst = sel_estrutura.value;
    const selCaixa = sel_caixa.value;
    const selTam = sel_tamanho.value;

    if (tabelaPlacasBody) {
      if (placaEditRow) {
        // atualizar linha existente
        const row = placaEditRow;
        row.dataset.preco = preco;
        row.dataset.potencia = potencia;
        row.dataset.eficiencia = eficiencia;
        row.dataset.tens_pot = tens_pot;
        row.dataset.corr_pot = corr_pot;
        row.dataset.tens_circ = tens_circ;
        row.dataset.corr_curto = corr_curto;
        row.dataset.toler = toler;
        row.dataset.temp_min = temp_min;
        row.dataset.temp_max = temp_max;
        row.dataset.temp_sup = temp_sup;
        row.dataset.fusivel = fusivel;
        row.dataset.num_cel = num_cel;
        row.dataset.peso = peso;
        row.dataset.imetro = imetro;
        row.dataset.celula = selCel;
        row.dataset.vidro = selVid;
        row.dataset.estrutura = selEst;
        row.dataset.caixa = selCaixa;
        row.dataset.tamanho = selTam;

        row.cells[1].innerText = `Helios ${potencia}W`;
        row.cells[2].innerText = potencia;
        row.cells[3].innerText = eficiencia;
        row.cells[4].innerText = preco;
      } else {
        const id = tabelaPlacasBody.rows.length + 1;
        const tr = document.createElement("tr");
        tr.dataset.preco = preco;
        tr.dataset.potencia = potencia;
        tr.dataset.eficiencia = eficiencia;
        tr.dataset.tens_pot = tens_pot;
        tr.dataset.corr_pot = corr_pot;
        tr.dataset.tens_circ = tens_circ;
        tr.dataset.corr_curto = corr_curto;
        tr.dataset.toler = toler;
        tr.dataset.temp_min = temp_min;
        tr.dataset.temp_max = temp_max;
        tr.dataset.temp_sup = temp_sup;
        tr.dataset.fusivel = fusivel;
        tr.dataset.num_cel = num_cel;
        tr.dataset.peso = peso;
        tr.dataset.imetro = imetro;
        tr.dataset.celula = selCel;
        tr.dataset.vidro = selVid;
        tr.dataset.estrutura = selEst;
        tr.dataset.caixa = selCaixa;
        tr.dataset.tamanho = selTam;

        tr.innerHTML = `
          <td>${id}</td>
          <td>Helios ${potencia}W</td>
          <td>${potencia}</td>
          <td>${eficiencia}</td>
          <td>${preco}</td>
          <td>
            <button class="btn-small info ver-placa"><i class="fa-solid fa-eye"></i></button>
            <button class="btn-small edit editar-placa"><i class="fa-solid fa-pen"></i></button>
            <button class="btn-small del excluir-placa"><i class="fa-solid fa-trash"></i></button>
          </td>
        `;
        tabelaPlacasBody.appendChild(tr);
      }
    }

    closeCarrossel();
    toast.textContent = placaEditRow ? "Placa atualizada com sucesso!" : "Placa cadastrada com sucesso!";
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 2200);
  });

  // ações na tabela de placas (visualizar, editar, excluir)
  tabelaPlacasBody?.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const row = btn.closest("tr");
    if (!row) return;

    // visualizar
    if (btn.classList.contains("ver-placa")) {
      p_modelo.textContent = row.cells[1].innerText;
      p_preco.textContent = row.dataset.preco || "";
      p_potencia.textContent = row.dataset.potencia || "";
      p_eficiencia.textContent = row.dataset.eficiencia || "";
      p_tens_pot.textContent = row.dataset.tens_pot || "";
      p_corr_pot.textContent = row.dataset.corr_pot || "";
      p_tens_circ.textContent = row.dataset.tens_circ || "";
      p_corr_curto.textContent = row.dataset.corr_curto || "";
      p_num_cel.textContent = row.dataset.num_cel || "";
      p_peso.textContent = row.dataset.peso || "";
      p_imetro.textContent = row.dataset.imetro || "";
      p_fusivel.textContent = row.dataset.fusivel || "";

      p_itens_rel.innerHTML = "";
      const pushItem = (label, arr, idx) => {
        if (idx === undefined || idx === null || idx === "") return;
        const i = parseInt(idx,10);
        if (Number.isFinite(i) && arr[i]) {
          const li = document.createElement("li");
          li.textContent = `${label}: ${arr[i].label || JSON.stringify(arr[i])}`;
          p_itens_rel.appendChild(li);
        }
      };
      pushItem("Célula", celulas, row.dataset.celula);
      pushItem("Vidro", vidros, row.dataset.vidro);
      pushItem("Estrutura", estruturas, row.dataset.estrutura);
      pushItem("Caixa", caixas, row.dataset.caixa);
      pushItem("Tamanho", tamanhos, row.dataset.tamanho);

      modalPlaca.classList.add("show");
      modalPlaca.setAttribute("aria-hidden","false");
    }

    // editar
    if (btn.classList.contains("editar-placa")) {
      openCarrossel(row);
    }

    // excluir
    if (btn.classList.contains("excluir-placa")) {
      if (confirm("Remover esta placa?")) {
        row.remove();
        toast.textContent = "Placa removida";
        toast.classList.add("show");
        setTimeout(()=> toast.classList.remove("show"), 2000);
      }
    }
  });

  fecharModalPlaca?.addEventListener("click", () => {
    modalPlaca.classList.remove("show");
    modalPlaca.setAttribute("aria-hidden","true");
  });
  fecharModalPlacaBtn?.addEventListener("click", () => {
    modalPlaca.classList.remove("show");
    modalPlaca.setAttribute("aria-hidden","true");
  });
});

/* ===== Usuários: CRUD Simulado ===== */
document.addEventListener("DOMContentLoaded", () => {
  const usuariosTable = document.getElementById("usuariosTable")?.querySelector("tbody");
  const novoUsuarioBtn = document.getElementById("novoUsuarioBtn");
  const modalUsuario = document.getElementById("modalUsuario");
  const fecharModalUsuario = document.getElementById("fecharModalUsuario");
  const salvarUsuario = document.getElementById("salvarUsuario");
  const modalUsuarioTitulo = document.getElementById("modalUsuarioTitulo");
  const formUsuario = document.getElementById("formUsuario");
  const senhaField = document.getElementById("senhaField");
  const toast = document.getElementById("toast");

  let editMode = false;
  let editRow = null;

  const openModalUsuario = (modo = "novo", row = null) => {
    editMode = modo === "editar";
    editRow = row;
    formUsuario.reset();

    modalUsuarioTitulo.textContent = editMode ? "Editar Usuário" : "Novo Usuário";
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

  fecharModalUsuario.addEventListener("click", closeModalUsuario);
  modalUsuario.addEventListener("click", (e) => { if (e.target === modalUsuario) closeModalUsuario(); });

  // Novo usuário
  novoUsuarioBtn?.addEventListener("click", () => openModalUsuario("novo"));

  salvarUsuario.addEventListener("click", () => {
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

  // Edição / Exclusão / Redefinição
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