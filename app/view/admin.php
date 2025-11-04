<?php
  require("../controllers/admin/adminAuthentication.php");
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
    <nav class="admin-nav">
      <a href="#" class="active" data-section="dashboard"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a href="#" data-section="clientes"><i class="fa-solid fa-users"></i> Clientes</a>
      <a href="#" data-section="placas"><i class="fa-solid fa-solar-panel"></i> Placas</a>
      <a href="#" data-section="usuarios"><i class="fa-solid fa-id-badge"></i> Usuários</a>
      <a href="#" id="logout"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
  </header>

  <!-- CONTEÚDO -->
  <main class="content">

    <!-- DASHBOARD -->
    <section id="dashboard" class="section active">
      <h2>Visão Geral</h2>
      <p class="muted">Resumo operacional do sistema Helios.</p>

      <div class="card-grid">
        <div class="card">
          <i class="fa-solid fa-wallet card-icon"></i>
          <h3>Economia Mensal</h3>
          <p>R$ 28.430,00</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-solar-panel card-icon"></i>
          <h3>Placas Ativas</h3>
          <p>184 unidades</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-user-check card-icon"></i>
          <h3>Clientes Ativos</h3>
          <p>96</p>
        </div>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h3>Geração x Consumo (12 meses)</h3>
        </div>
        <div class="chart-wrap chart-wrap--small">
          <canvas id="dashChart"></canvas>
        </div>
      </div>
    </section>

    <!-- CLIENTES -->
    <section id="clientes" class="section">
      <h2>Clientes</h2>
      <p class="muted">Gerencie os clientes: ver cadastro, editar e definir permissões.</p>

      <div class="table-actions">
        <input type="text" id="searchCliente" placeholder="Buscar cliente..."/>
      </div>

      <table class="table" id="clientesTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr data-endereco="Rua das Flores, 123 - Centro, SP" data-telefone="+55 11 98765-4321" data-nascimento="1985-04-12">
            <td>1</td>
            <td>João da Silva</td>
            <td>joao@helios.com</td>
            <td><span class="badge success">Ativo</span></td>
            <td class="actions">
              <button class="btn-small info ver-cadastro"><i class="fa-solid fa-eye"></i></button>
              <button class="btn-small edit editar-cadastro"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-small warn tornar-admin"><i class="fa-solid fa-user-shield"></i></button>
            </td>
          </tr>
          <tr data-endereco="Av. Paulista, 1000 - Apto 45, SP" data-telefone="+55 11 91234-5678" data-nascimento="1992-09-03">
            <td>2</td>
            <td>Maria Souza</td>
            <td>maria@helios.com</td>
            <td><span class="badge success">Ativo</span></td>
            <td class="actions">
              <button class="btn-small info ver-cadastro"><i class="fa-solid fa-eye"></i></button>
              <button class="btn-small edit editar-cadastro"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-small warn tornar-admin"><i class="fa-solid fa-user-shield"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- PLACAS -->
    <section id="placas" class="section">
      <h2>Placas</h2>
      <p class="muted">Gerencie os modelos de placas solares.</p>

      <div class="placas-head">
        <button id="novaPlacaBtn" class="btn"><i class="fa-solid fa-plus"></i> Nova Placa</button>
      </div>

      <div id="placasListagem" class="tabela-placas">
        <h3>Placas Cadastradas</h3>
        <table id="tabelaPlacas" class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Modelo</th>
              <th>Potência (W)</th>
              <th>Eficiência (%)</th>
              <th>Preço (R$)</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Helios 550W</td>
              <td>550</td>
              <td>21.6</td>
              <td>1499.99</td>
              <td>
                <button class="btn-small info ver-placa"><i class="fa-solid fa-eye"></i></button>
                <button class="btn-small edit editar-placa"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-small del excluir-placa"><i class="fa-solid fa-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="carrosselPlaca" class="carrossel hidden">
        <div class="carrossel-head">
          <h3>Cadastro de Placa</h3>
          <button id="cancelarCarrossel" class="btn-outline"><i class="fa-solid fa-xmark"></i> Cancelar</button>
        </div>

        <div class="carrossel-steps">
          <div class="step-bullet active">1</div>
          <div class="step-line"></div>
          <div class="step-bullet">2</div>
          <div class="step-line"></div>
          <div class="step-bullet">3</div>
          <div class="step-line"></div>
          <div class="step-bullet">4</div>
          <div class="step-line"></div>
          <div class="step-bullet">5</div>
          <div class="step-line"></div>
          <div class="step-bullet">6</div>
        </div>

        <div id="itensCriados" class="carrossel-itens" style="margin:12px 0;padding:10px;border-radius:8px;background:#fbfbfb;border:1px solid #eee">
          <strong>Itens criados na sessão</strong>
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;margin-top:8px">
            <div>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <small>Células</small>
                <button id="clearCelulas" class="btn-small del" title="Limpar células">Limpar</button>
              </div>
              <ul id="listCelulas" style="margin:8px 0 0;padding-left:16px"></ul>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <small>Vidros</small>
                <button id="clearVidros" class="btn-small del" title="Limpar vidros">Limpar</button>
              </div>
              <ul id="listVidros" style="margin:8px 0 0;padding-left:16px"></ul>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <small>Estruturas</small>
                <button id="clearEstruturas" class="btn-small del" title="Limpar estruturas">Limpar</button>
              </div>
              <ul id="listEstruturas" style="margin:8px 0 0;padding-left:16px"></ul>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <small>Caixas</small>
                <button id="clearCaixas" class="btn-small del" title="Limpar caixas">Limpar</button>
              </div>
              <ul id="listCaixas" style="margin:8px 0 0;padding-left:16px"></ul>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <small>Tamanhos</small>
                <button id="clearTamanhos" class="btn-small del" title="Limpar tamanhos">Limpar</button>
              </div>
              <ul id="listTamanhos" style="margin:8px 0 0;padding-left:16px"></ul>
            </div>
          </div>
        </div>

        <form id="formPlaca" onsubmit="return false;">
          <!-- ETAPAS (1..6) - iguais ao exemplo anterior -->
          <div class="carrossel-page active" data-step="1">
            <h4>Etapa 1 – Célula</h4>
            <div class="grid">
              <div class="field" style="grid-column:1 / -1">
                <label>Descrição da célula (DSC_CEL)</label>
                <input type="text" id="celula_dsc" placeholder="Descrição da célula" />
              </div>
            </div>
          </div>

          <div class="carrossel-page" data-step="2">
            <h4>Etapa 2 – Vidro</h4>
            <div class="grid">
              <div class="field">
                <label>Tipo de vidro (TIPO_VIDRO)</label>
                <input type="text" id="vidro_tipo" placeholder="Ex: Temperado" />
              </div>
              <div class="field">
                <label>Espessura (ESPESSURA) mm</label>
                <input type="number" step="0.01" id="vidro_espessura" placeholder="Ex: 3.2" />
              </div>
            </div>
          </div>

          <div class="carrossel-page" data-step="3">
            <h4>Etapa 3 – Estrutura</h4>
            <div class="grid">
              <div class="field" style="grid-column:1 / -1">
                <label>Descrição da estrutura (DESCRICAO_ESTRUTURA)</label>
                <input type="text" id="estrutura_dsc" placeholder="Descrição da estrutura" />
              </div>
            </div>
          </div>

          <div class="carrossel-page" data-step="4">
            <h4>Etapa 4 – Caixa de Conexão</h4>
            <div class="grid">
              <div class="field"><label>IP</label><input type="text" id="caixa_ip" /></div>
              <div class="field"><label>DID</label><input type="text" id="caixa_did" /></div>
              <div class="field"><label>Espessura do cabo (mm)</label><input type="text" id="caixa_esp_cabo" /></div>
              <div class="field"><label>Comprimento (mm)</label><input type="number" id="caixa_comp" /></div>
              <div class="field" style="grid-column:1 / -1"><label>Tipo de conexão</label><input type="text" id="caixa_tipo_con" /></div>
            </div>
          </div>

          <div class="carrossel-page" data-step="5">
            <h4>Etapa 5 – Tamanho</h4>
            <div class="grid">
              <div class="field"><label>Altura (mm)</label><input type="number" step="0.1" id="tam_altura" /></div>
              <div class="field"><label>Largura (mm)</label><input type="number" step="0.1" id="tam_largura" /></div>
              <div class="field"><label>Espessura (mm)</label><input type="number" step="0.1" id="tam_espessura" /></div>
            </div>
          </div>

          <div class="carrossel-page" data-step="6">
            <h4>Etapa 6 – Placa (Dados técnicos e seleção)</h4>
            <div class="grid">
              <div class="field">
                <label>Preço (PRECO)</label>
                <input type="number" step="0.01" id="preco" placeholder="0.00" required>
              </div>
              <div class="field">
                <label>Potência Máxima (POTENCIA_MAX)</label>
                <input type="number" id="potencia" required>
              </div>
              <div class="field">
                <label>Tensão na potência (TENS_POTENCIA)</label>
                <input type="number" step="0.01" id="tens_potencia" />
              </div>
              <div class="field">
                <label>Corrente na potência (CORRENTE_POTENCIA)</label>
                <input type="number" step="0.01" id="corrente_potencia" />
              </div>
              <div class="field">
                <label>Eficiência (EFICIENCIA)</label>
                <input type="number" step="0.01" id="eficiencia" />
              </div>
              <div class="field">
                <label>Nº de células (NUM_CELULAS)</label>
                <input type="number" id="num_celulas" />
              </div>
              <div class="field">
                <label>Peso (PESO) kg</label>
                <input type="number" step="0.1" id="peso" />
              </div>
              <div class="field">
                <label>IMETRO (opcional)</label>
                <input type="text" id="imetro" maxlength="11" />
              </div>

              <div class="field" style="grid-column:1 / -1">
                <label>Célula</label>
                <select id="sel_celula"></select>
              </div>
              <div class="field">
                <label>Vidro</label>
                <select id="sel_vidro"></select>
              </div>
              <div class="field">
                <label>Estrutura</label>
                <select id="sel_estrutura"></select>
              </div>
              <div class="field">
                <label>Caixa de Conexão</label>
                <select id="sel_caixa"></select>
              </div>
              <div class="field">
                <label>Tamanho</label>
                <select id="sel_tamanho"></select>
              </div>
            </div>
          </div>

          <div class="carrossel-nav">
            <button type="button" class="btn-outline" id="voltarStep" disabled><i class="fa-solid fa-arrow-left"></i> Voltar</button>
            <div class="grow"></div>
            <button type="button" class="btn" id="proximoStep">Próximo <i class="fa-solid fa-arrow-right"></i></button>
            <button type="submit" class="btn hidden" id="finalizarCadastro"><i class="fa-solid fa-floppy-disk"></i> Cadastrar Placa</button>
          </div>
        </form>
      </div>
    </section>

    <!-- MODAL PLACA (visualizar) -->
    <div id="modalPlaca" class="modal" aria-hidden="true">
      <div class="modal-card">
        <div class="modal-head">
          <h3 id="modalPlacaTitulo">Dados da Placa</h3>
          <button id="fecharModalPlaca" class="btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px">
            <div><strong>Modelo:</strong> <div id="p_modelo"></div></div>
            <div><strong>Preço (R$):</strong> <div id="p_preco"></div></div>
            <div><strong>Potência (W):</strong> <div id="p_potencia"></div></div>
            <div><strong>Eficiência (%):</strong> <div id="p_eficiencia"></div></div>
            <div><strong>Tensão (V) na potência:</strong> <div id="p_tens_pot"></div></div>
            <div><strong>Corrente (A) na potência:</strong> <div id="p_corr_pot"></div></div>
            <div><strong>Tensão circuito aberto:</strong> <div id="p_tens_circ"></div></div>
            <div><strong>Corrente curto:</strong> <div id="p_corr_curto"></div></div>
            <div><strong>Nº células:</strong> <div id="p_num_cel"></div></div>
            <div><strong>Peso (kg):</strong> <div id="p_peso"></div></div>
            <div><strong>IMETRO:</strong> <div id="p_imetro"></div></div>
            <div><strong>Fusível:</strong> <div id="p_fusivel"></div></div>

            <div style="grid-column:1 / -1;margin-top:8px">
              <strong>Itens relacionados:</strong>
              <ul id="p_itens_rel" style="margin-top:6px;padding-left:16px"></ul>
            </div>
          </div>
        </div>
        <div class="modal-foot">
          <button id="fecharModalPlacaBtn" class="btn">Fechar</button>
        </div>
      </div>
    </div>

    <!-- MODAL CLIENTE (ver/editar) -->
    <div id="modalCliente" class="modal" aria-hidden="true">
      <div class="modal-card">
        <div class="modal-head">
          <h3 id="modalClienteTitulo">Dados do Cliente</h3>
          <button id="fecharModalCliente" class="btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
          <form id="formCliente">
            <div class="grid">
              <div class="field">
                <label>Nome</label>
                <input type="text" id="cliNome" />
              </div>
              <div class="field">
                <label>E-mail</label>
                <input type="email" id="cliEmail" />
              </div>
              <div class="field">
                <label>Status</label>
                <select id="cliStatus">
                  <option value="Ativo">Ativo</option>
                  <option value="Inativo">Inativo</option>
                </select>
              </div>

              <div class="field" style="grid-column: 1 / -1;">
                <label>Endereço</label>
                <input type="text" id="cliEndereco" placeholder="Rua, número, bairro, cidade" />
              </div>
              <div class="field">
                <label>Telefone</label>
                <input type="tel" id="cliTelefone" placeholder="+55 11 9xxxx-xxxx" />
              </div>
              <div class="field">
                <label>Data de Nascimento</label>
                <input type="date" id="cliNascimento" />
              </div>
            </div>
          </form>
        </div>
        <div class="modal-foot">
          <button id="salvarCliente" class="btn"><i class="fa-solid fa-check"></i> Salvar</button>
        </div>
      </div>
    </div>

    <!-- USUÁRIOS -->
    <section id="usuarios" class="section">
      <h2>Usuários</h2>
      <p class="muted">Gerencie os administradores e funcionários do sistema Helios.</p>

      <div class="usuarios-head">
        <button id="novoUsuarioBtn" class="btn"><i class="fa-solid fa-plus"></i> Novo Usuário</button>
      </div>

      <table id="usuariosTable" class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Função</th>
            <th>Status</th>
            <th>Ações</th>
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
              <button class="btn-small edit editar-usuario"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-small del excluir-usuario"><i class="fa-solid fa-trash"></i></button>
              <button class="btn-small warn reset-senha"><i class="fa-solid fa-key"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <!-- MODAL USUÁRIO -->
  <div id="modalUsuario" class="modal" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-head">
        <h3 id="modalUsuarioTitulo">Novo Usuário</h3>
        <button id="fecharModalUsuario" class="btn-icon"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form id="formUsuario">
          <div class="grid">
            <div class="field">
              <label>Nome</label>
              <input type="text" id="userNome" required />
            </div>
            <div class="field">
              <label>E-mail</label>
              <input type="email" id="userEmail" required />
            </div>
            <div class="field">
              <label>Função</label>
              <select id="userFuncao">
                <option>Visualizador</option>
                <option>Técnico</option>
                <option>Admin</option>
              </select>
            </div>
            <div class="field">
              <label>Status</label>
              <select id="userStatus">
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
              </select>
            </div>
            <div class="field" id="senhaField">
              <label>Senha</label>
              <input type="password" id="userSenha" placeholder="••••••••" required />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-foot">
        <button id="salvarUsuario" class="btn"><i class="fa-solid fa-check"></i> Salvar</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div id="toast" class="toast"><i class="fa-solid fa-check"></i> Ação realizada com sucesso</div>
</body>
</html>