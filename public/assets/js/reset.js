const step1 = document.querySelector('.step-1');
const step2 = document.querySelector('.step-2');
const step3 = document.querySelector('.step-3');
const successBox = document.getElementById('successBox');
const backButtons = document.querySelectorAll('.btn-back');

// Variável global para armazenar o token coletado
let recoveryToken = '';

// ===== Lógica para os 6 campos de código =====
const codeInputs = document.querySelectorAll(".code-box");

codeInputs.forEach((input, index) => {
  input.addEventListener("input", e => {
    // Aceitar apenas números
    e.target.value = e.target.value.replace(/[^0-9a-fA-F]/g, '');
    
    input.classList.add("filled");
    if (e.target.value && index < codeInputs.length - 1) {
      codeInputs[index + 1].focus();
    }
  });

  input.addEventListener("keydown", e => {
    if (e.key === "Backspace" && !input.value && index > 0) {
      codeInputs[index - 1].focus();
    }
  });
});

// Função para mostrar notificações
function showNotification(message, type = 'error') {
  // Remove notificação existente
  const existingNotification = document.getElementById('notification');
  if (existingNotification) {
    existingNotification.remove();
  }

  // Define ícone e classe baseado no tipo
  let icon, className;
  switch(type) {
    case 'success':
      icon = 'fas fa-check-circle';
      className = 'notification success-notification';
      break;
    case 'loading':
      icon = 'fas fa-spinner fa-spin';
      className = 'notification loading-notification';
      break;
    case 'error':
    default:
      icon = 'fas fa-exclamation-circle';
      className = 'notification error-notification';
      break;
  }

  // Cria nova notificação
  const notification = document.createElement('div');
  notification.id = 'notification';
  notification.className = className;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="${icon}"></i>
      <span class="notification-message">${message}</span>
      ${type !== 'loading' ? '<button class="close-btn" onclick="this.parentElement.parentElement.remove()">&times;</button>' : ''}
    </div>
  `;

  document.body.appendChild(notification);

  // Auto-remover após tempo específico (exceto loading)
  if (type !== 'loading') {
    const timeout = type === 'success' ? 2000 : 4000;
    setTimeout(() => {
      if (notification.parentElement) {
        notification.remove();
      }
    }, timeout);
  }
}

// ETAPA 1: Enviar email para requestTokenController.php
document.getElementById('emailForm').addEventListener('submit', e => {
  e.preventDefault();
  
  const email = document.getElementById('email').value.trim();
  
  if (!email) {
    showNotification('Por favor, digite seu email.', 'error');
    return;
  }
  
  // Validação básica de email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification('Por favor, digite um email válido.', 'error');
    return;
  }
  
  // Mostrar loading
  showNotification('Enviando código para seu email...', 'loading');
  
  // Desabilitar botão
  const submitBtn = document.querySelector('#emailForm button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
  
  // Preparar dados
  const formData = new FormData();
  formData.append('email', email);
  
  // Enviar requisição
  fetch('../controllers/requestTokenController.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    console.log('Response status:', response.status);
    console.log('Response redirected:', response.redirected);
    console.log('Response URL:', response.url);
    
    if (response.redirected) {
      if (response.url.includes('success=token_sent')) {
        // Sucesso - ir para etapa 2
        showNotification('Código enviado com sucesso! Verifique seu email.', 'success');
        setTimeout(() => {
          step1.classList.remove('active');
          step2.classList.add('active');
        }, 1500);
      } else if (response.url.includes('error=')) {
        // Extrair erro da URL
        const urlParams = new URLSearchParams(response.url.split('?')[1]);
        const errorType = urlParams.get('error');
        
        const errorMessages = {
          'empty_email': 'Por favor, digite seu email.',
          'email_not_found': 'Email não encontrado em nosso sistema.',
          'token_active': 'Código reenviado! Verifique seu email.',
          'server_error': 'Erro interno do servidor. Tente novamente.',
          'email_failed': 'Falha ao enviar email. Tente novamente.'
        };
        
        const errorMessage = errorMessages[errorType] || 'Erro desconhecido.';
        
        // Se já tem token ativo, ir para etapa 2 com mensagem de sucesso
        if (errorType === 'token_active') {
          showNotification(errorMessage, 'success');
          setTimeout(() => {
            step1.classList.remove('active');
            step2.classList.add('active');
          }, 1500);
        } else {
          showNotification(errorMessage, 'error');
        }
      }
    } else {
      return response.text();
    }
  })
  .then(data => {
    if (data) {
      console.error('Resposta inesperada:', data);
      showNotification('Erro no servidor. Tente novamente.', 'error');
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    showNotification('Erro de conexão. Verifique sua internet.', 'error');
  })
  .finally(() => {
    // Reabilitar botão
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Enviar Código';
  });
});

// ETAPA 2: Validar código coletado
document.getElementById('codeForm').addEventListener('submit', e => {
  e.preventDefault();
  
  // Coletar código dos 6 campos
  const codeValues = Array.from(codeInputs).map(input => input.value.trim());
  const fullCode = codeValues.join('');
  
  console.log('Código coletado:', fullCode);
  
  if (fullCode.length !== 6) {
    showNotification('Digite o código completo de 6 dígitos.', 'error');
    return;
  }
  
  // Validar se todos os caracteres são hexadecimais (0-9, a-f, A-F)
  const hexRegex = /^[0-9a-fA-F]{6}$/;
  if (!hexRegex.test(fullCode)) {
    showNotification('Código inválido. Use apenas números e letras (A-F).', 'error');
    return;
  }
  
  // Salvar código para usar na etapa 3
  recoveryToken = fullCode;
  
  // Ir para etapa 3
  step2.classList.remove('active');
  step3.classList.add('active');
  
  showNotification('Código validado! Agora defina sua nova senha.', 'success');
});

// ETAPA 3: Enviar token + nova senha para useTokenController.php
document.getElementById('passwordForm').addEventListener('submit', e => {
  e.preventDefault();
  
  const newPassword = document.getElementById('password').value.trim();
  
  if (!newPassword) {
    showNotification('Por favor, digite sua nova senha.', 'error');
    return;
  }
  
  if (newPassword.length < 6) {
    showNotification('A senha deve ter pelo menos 6 caracteres.', 'error');
    return;
  }
  
  if (!recoveryToken) {
    showNotification('Erro: Token não encontrado. Reinicie o processo.', 'error');
    return;
  }
  
  // Mostrar loading
  showNotification('Atualizando sua senha...', 'loading');
  
  // Desabilitar botão
  const submitBtn = document.querySelector('#passwordForm button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Alterando...';
  
  // Preparar dados
  const formData = new FormData();
  formData.append('token', recoveryToken);
  formData.append('new_password', newPassword);
  
  console.log('Enviando token:', recoveryToken);
  console.log('Enviando nova senha:', '***hidden***');
  
  // Enviar requisição
  fetch('../controllers/useTokenController.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    console.log('Response status:', response.status);
    console.log('Response redirected:', response.redirected);
    console.log('Response URL:', response.url);
    
    if (response.redirected) {
      if (response.url.includes('success=password_updated')) {
        // Sucesso - mostrar mensagem e redirecionar
        step3.classList.remove('active');
        successBox.classList.add('show');
        
        setTimeout(() => {
          window.location.href = 'login.php';
        }, 2500);
      } else if (response.url.includes('error=')) {
        // Extrair erro da URL
        const urlParams = new URLSearchParams(response.url.split('?')[1]);
        const errorType = urlParams.get('error');
        
        const errorMessages = {
          'empty_fields': 'Preencha todos os campos.',
          'invalid_token': 'Código inválido ou expirado. Solicite um novo código.',
          'server_error': 'Erro interno do servidor. Tente novamente.'
        };
        
        const errorMessage = errorMessages[errorType] || 'Erro desconhecido.';
        showNotification(errorMessage, 'error');
      }
    } else {
      return response.text();
    }
  })
  .then(data => {
    if (data) {
      console.error('Resposta inesperada:', data);
      showNotification('Erro no servidor. Tente novamente.', 'error');
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    showNotification('Erro de conexão. Verifique sua internet.', 'error');
  })
  .finally(() => {
    // Reabilitar botão
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Alterar Senha';
  });
});

// Função de voltar
backButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    if (step3.classList.contains('active')) {
      step3.classList.remove('active');
      step2.classList.add('active');
    } else if (step2.classList.contains('active')) {
      step2.classList.remove('active');
      step1.classList.add('active');
      // Limpar código ao voltar
      recoveryToken = '';
      codeInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled');
      });
    }
  });
});

// Verificar se há parâmetros de erro/sucesso na URL ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get('error');
  const success = urlParams.get('success');
  
  if (error) {
    const errorMessages = {
      'token_active': 'Você já recebeu um código! Verifique seu email.',
      'email_not_found': 'Email não encontrado em nosso sistema.',
      'invalid_token': 'Código inválido ou expirado.',
      'server_error': 'Erro interno do servidor.'
    };
    
    const errorMessage = errorMessages[error] || 'Erro desconhecido.';
    
    // Se já tem token ativo, ir direto para etapa 2 com mensagem de sucesso
    if (error === 'token_active') {
      step1.classList.remove('active');
      step2.classList.add('active');
      showNotification(errorMessage, 'success');
    } else {
      showNotification(errorMessage, 'error');
    }
  }
  
  if (success) {
    if (success === 'token_sent') {
      step1.classList.remove('active');
      step2.classList.add('active');
      showNotification('Código enviado com sucesso! Verifique seu email.', 'success');
    }
  }
});
