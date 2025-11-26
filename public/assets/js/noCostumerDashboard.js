// Dashboard simples (ajustado): a Visão Geral mostra apenas a informação
// "Sua solicitação já foi realizada" e, abaixo, o botão laranja "Solicitar orçamento".
(function () {
    function $id(id){ return document.getElementById(id); }
    function loadJSON(key){ try{ return JSON.parse(localStorage.getItem(key) || sessionStorage.getItem(key) || 'null'); }catch(e){return null;} }
    function loadLocal(key){ try{ return JSON.parse(localStorage.getItem(key) || '[]'); }catch(e){return []; } }
    function saveLocal(key, val){ localStorage.setItem(key, JSON.stringify(val)); }
  
    var user = loadJSON('helios_logged_user'); // session
    var pending = loadJSON('helios_pending_sim'); // session pending sim (if any)
    var sims = loadLocal('helios_simulacoes');
  
    var userArea = $id('userArea');
    var simBox = $id('simBox');
    var btnSolicitar = $id('btnSolicitar');
    var btnSimular = $id('btnSimular');
    var statusMsg = $id('statusMsg');
    var requestsList = $id('requestsList');
  
    // mostra area do usuário
    userArea.textContent = user ? ('Olá, ' + user.nome) : ('Visitante');
  
    // Renderização simplificada conforme solicitado: Visão Geral mostra só a informação
    function renderOverview(){
      console.log('=== DEBUG renderOverview ===');
      console.log('window.serverOrcamento:', window.serverOrcamento);
      console.log('window.serverOrcamento && window.serverOrcamento.ID_ORCAMENTO:', !!(window.serverOrcamento && window.serverOrcamento.ID_ORCAMENTO));
      
      // Se há um orçamento pendente informado pelo servidor, mostra informação de solicitação realizada
      if (window.serverOrcamento && window.serverOrcamento.ID_ORCAMENTO) {
        console.log('ENTRANDO NO IF - tem orçamento');
        simBox.innerHTML = '<div class="sim-row" style="align-items:center;justify-content:center;flex-direction:column">' +
          '<div class="sim-item" style="max-width:640px;text-align:center;padding:18px;border-radius:10px">' +
          '<div style="font-weight:700;font-size:1.05rem;margin-bottom:6px">Sua solicitação já foi realizada</div>' +
          '<div style="color:#6b6b6b">Em breve nossa equipe entrará em contato com você.</div>' +
          '</div></div>';

        // Botão laranja "Solicitar orçamento" abaixo da mensagem (permite nova solicitação)
        if(btnSolicitar){
          btnSolicitar.disabled = false;
          btnSolicitar.className = 'btn btn-primary';
          btnSolicitar.textContent = 'Solicitar orçamento';
          btnSolicitar.onclick = function(){ 
            // Criar e enviar form para controller
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../controllers/costumer/solicitarOrcamentoController.php';
            document.body.appendChild(form);
            
            // Manter o estado atual (já tem orçamento)
            // Re-renderizar interface para garantir consistência
            renderOverview();
            
            form.submit();
          };
        }
      } else {
        // Sem orçamento pendente: não mostrar o quadro de "solicitação já realizada".
        // Mostrar convite para simular agora.
        simBox.innerHTML = '<div class="sim-row" style="align-items:center;justify-content:center;flex-direction:column">' +
          '<div class="sim-item" style="max-width:640px;text-align:center;padding:18px;border-radius:10px">' +
          '<div style="font-weight:700;font-size:1.05rem;margin-bottom:6px">Nenhum orçamento encontrado</div>' +
          '<div style="color:#6b6b6b">Faça uma simulação para receber um orçamento personalizado.</div>' +
          
          '</div></div>';

        // Habilitar botão para criar primeiro orçamento
        if (btnSolicitar) {
          btnSolicitar.disabled = false;
          btnSolicitar.className = 'btn btn-primary';
          btnSolicitar.textContent = 'Solicitar orçamento';
          btnSolicitar.onclick = function(){
            // Criar e enviar form para controller
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../controllers/costumer/solicitarOrcamentoController.php';
            document.body.appendChild(form);
            
            // Atualizar estado local para refletir que orçamento será criado
            window.serverOrcamento = { id_orcamento: 'pending', status: 'PENDENTE' };
            
            // Re-renderizar interface imediatamente
            renderOverview();
            
            form.submit();
          };
        }
      }

      // Não limpar statusMsg automaticamente para preservar notificações
    }
  
    // Lista de solicitações (mantida igual ao anterior)
    function renderRequests(){
      requestsList.innerHTML = '';
      var list = [];
      if(user){
        list = loadLocal('helios_simulacoes').filter(function(s){ return s.userId === user.id; }).sort(function(a,b){ return new Date(b.createdAt) - new Date(a.createdAt); });
      } else {
        var pendingName = pending && pending.nome ? pending.nome.trim().toLowerCase() : '';
        if(pendingName){
          list = loadLocal('helios_simulacoes').filter(function(s){ return (s.nome && s.nome.trim().toLowerCase() === pendingName); });
        }
      }
  
      if(!list.length){
        // Se o servidor informou um orçamento pendente, exibir esse item em vez de mostrar "Nenhuma solicitação encontrada"
        if (window.serverOrcamento) {
          var r = window.serverOrcamento;
          var div = document.createElement('div');
          div.className = 'request-row';
          var meta = document.createElement('div');
          meta.className = 'request-meta';
          meta.innerHTML = '<div class="request-id">' + (r.id_orcamento || r.id || '—') + '</div>' +
                           '<div class="request-status">' + (r.status || 'PENDENTE') + ' · ' + (r.DATA_CRIACAO ? new Date(r.DATA_CRIACAO).toLocaleString() : '') + '</div>';
          var right = document.createElement('div');
          right.className = 'request-actions';
          var details = document.createElement('div');
          details.className = 'request-details';
          details.innerHTML = '<small>Protocolo:</small> ' + (r.id_orcamento || r.id || '—');
          var btnView = document.createElement('button');
          btnView.type = 'button';
          btnView.textContent = 'Ver';
          btnView.addEventListener('click', function(){
            alert('Protocolo: ' + (r.id_orcamento || r.id || '—') + '\nStatus: ' + (r.status || 'PENDENTE'));
          }, false);
          right.appendChild(details);
          right.appendChild(btnView);
          div.appendChild(meta);
          div.appendChild(right);
          requestsList.appendChild(div);
          return;
        }

        requestsList.innerHTML = '<div class="empty">Nenhuma solicitação encontrada.</div>';
        return;
      }
  
      list.forEach(function(r){
        var div = document.createElement('div');
        div.className = 'request-row';
        var meta = document.createElement('div');
        meta.className = 'request-meta';
        meta.innerHTML = '<div class="request-id">' + (r.id || '—') + '</div>' +
                         '<div class="request-status">' + (r.status || '—') + ' · ' + (r.createdAt ? new Date(r.createdAt).toLocaleString() : '—') + '</div>';
        var right = document.createElement('div');
        right.className = 'request-actions';
        var details = document.createElement('div');
        details.innerHTML = '<small>Consumo:</small> ' + (r.consumo || '—') + ' kWh · <small>Valor:</small> ' + (r.preco_kwp ? ('R$ ' + Number(r.preco_kwp).toLocaleString('pt-BR',{minimumFractionDigits:2})) : '—');
        var btnView = document.createElement('button');
        btnView.type = 'button';
        btnView.textContent = 'Ver';
        btnView.addEventListener('click', function(){
          alert('Protocolo: ' + r.id + '\nStatus: ' + r.status + '\nCriado: ' + (r.createdAt ? new Date(r.createdAt).toLocaleString() : '—'));
        }, false);
  
        right.appendChild(details);
        right.appendChild(btnView);
  
        div.appendChild(meta);
        div.appendChild(right);
  
        requestsList.appendChild(div);
      });
    }
  
    // Tabs
    var tabs = document.querySelectorAll('.tab');
    tabs.forEach(function(t){
      t.addEventListener('click', function(){
        tabs.forEach(function(x){ x.classList.remove('active'); });
        t.classList.add('active');
        var tab = t.getAttribute('data-tab');
        document.querySelectorAll('.sd-section').forEach(function(s){ s.classList.remove('active'); s.setAttribute('aria-hidden','true'); });
        var sel = $id(tab);
        if(sel){ sel.classList.add('active'); sel.setAttribute('aria-hidden','false'); }
        if(tab === 'requests') renderRequests();
      }, false);
    });
  
    // Inicializa: sempre mostra a mensagem fixa na visão geral e prepara as solicitações
    // Verifica query string para mensagens de controller e exibe notificação
    function showStatusMessage(text, type) {
      if (!statusMsg) return;
      statusMsg.textContent = text;
      statusMsg.classList.remove('status-success','status-error');
      if (type === 'success') statusMsg.classList.add('status-success');
      else if (type === 'error') statusMsg.classList.add('status-error');
      // limpar após 6 segundos
      setTimeout(() => { statusMsg.textContent = ''; statusMsg.classList.remove('status-success','status-error'); }, 6000);
    }

    (function handleQueryMessages(){
      try {
        var params = new URLSearchParams(window.location.search);
        if (params.has('success')) {
          var s = params.get('success');
          if (s === 'orcamento_created') showStatusMessage('Orçamento solicitado com sucesso. Em breve entraremos em contato.', 'success');
          // remover param da url sem recarregar
          params.delete('success');
        }
        if (params.has('error')) {
          var e = params.get('error');
          if (e === 'existing_orcamento') showStatusMessage('Você já possui um orçamento pendente.', 'error');
          else if (e === 'invalid_method') showStatusMessage('Método de requisição inválido.', 'error');
          else if (e === 'server_error') showStatusMessage('Erro interno no servidor. Tente novamente mais tarde.', 'error');
          else showStatusMessage('Erro: ' + e, 'error');
          params.delete('error');
        }
        // Se removemos parâmetros, atualizar a URL para evitar repetição da notificação
        if (window.history && (window.location.search.length > 0)) {
          var newQuery = params.toString();
          var newUrl = window.location.origin + window.location.pathname + (newQuery ? ('?' + newQuery) : '');
          window.history.replaceState({}, document.title, newUrl);
        }
      } catch (ex) {
        console.error('Erro ao processar query params:', ex);
      }
    })();

    // Configurar botão de solicitar orçamento
    function setupSolicitarButton(){
      if (btnSolicitar) {
        btnSolicitar.disabled = false;
        btnSolicitar.className = 'btn btn-primary';
        btnSolicitar.textContent = 'Solicitar orçamento';
        btnSolicitar.onclick = function(){
          console.log('Clicou no botão Solicitar orçamento');
          // Criar e enviar form para controller
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '../controllers/costumer/solicitarOrcamentoController.php';
          document.body.appendChild(form);
          form.submit();
        };
      }
    }

    setupSolicitarButton();
  })();