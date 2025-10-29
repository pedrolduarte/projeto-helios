document.addEventListener("DOMContentLoaded", () => {
    // Elementos do carrossel (register)
    const nextBtn = document.getElementById("nextStep");
    const prevBtn = document.getElementById("prevStep");
    const step1 = document.getElementById("step1");
    const step2 = document.getElementById("step2");
    const dot1 = document.getElementById("dot1");
    const dot2 = document.getElementById("dot2");

    // Verificar se estamos na página de registro
    if (nextBtn && prevBtn && step1 && step2) {
        
        // Função para validar campos da etapa 1
        function validateStep1() {
            const nome = document.getElementById("nome").value.trim();
            const email = document.getElementById("email").value.trim();
            const cpf = document.getElementById("cpf").value.trim();
            const senha = document.getElementById("senha").value.trim();

            if (!nome || !email || !cpf || !senha) {
                showNotification("Por favor, preencha todos os campos antes de prosseguir.", 'error');
                return false;
            }

            // Validação básica de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification("Por favor, insira um email válido.", 'error');
                return false;
            }

            // Validação básica de CPF (formato brasileiro)
            const cpfRegex = /^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/;
            if (!cpfRegex.test(cpf)) {
                showNotification("Por favor, insira um CPF válido (formato: 123.456.789-00).", 'error');
                return false;
            }

            // Validação básica de senha
            if (senha.length < 6) {
                showNotification("A senha deve ter pelo menos 6 caracteres.", 'error');
                return false;
            }

            return true;
        }

        // Função para validar campos da etapa 2
        function validateStep2() {
            const nascimento = document.getElementById("nascimento").value;
            const cep = document.getElementById("cep").value.trim();
            const numero = document.getElementById("numero").value.trim();
            const telefone = document.getElementById("telefone").value.trim();

            if (!nascimento || !cep || !numero || !telefone) {
                showNotification("Por favor, preencha todos os campos para finalizar o cadastro.", 'error');
                return false;
            }

            // Validação básica de CEP (formato brasileiro)
            const cepRegex = /^\d{5}-?\d{3}$/;
            if (!cepRegex.test(cep)) {
                showNotification("Por favor, insira um CEP válido (formato: 12345-678).", 'error');
                return false;
            }

            // Validação de data de nascimento (idade mínima 18 anos)
            const birthDate = new Date(nascimento);
            const today = new Date();
            const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
            
            if (age < 18) {
                showNotification("Você deve ter pelo menos 18 anos para se cadastrar.", 'error');
                return false;
            }

            // Validação básica de telefone
            const telefoneRegex = /^\(\d{2}\)\s?\d{4,5}-?\d{4}$|^\d{10,11}$/;
            if (!telefoneRegex.test(telefone)) {
                showNotification("Por favor, insira um telefone válido (formato: (11) 99999-9999 ou 11999999999).", 'error');
                return false;
            }

            return true;
        }

        // Função para mostrar notificação melhorada
        function showNotification(message, type = 'error') {
            // Remove notificação existente se houver
            const existingNotification = document.getElementById('validationNotification');
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
            notification.id = 'validationNotification';
            notification.className = className;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="${icon}"></i>
                    <span class="notification-message">${message}</span>
                    ${type !== 'loading' ? '<button class="close-btn" onclick="this.parentElement.parentElement.remove()">&times;</button>' : ''}
                </div>
                ${type !== 'loading' ? '<div class="progress-bar"></div>' : ''}
            `;

            document.body.appendChild(notification);

            // Auto-remover após tempo específico (exceto loading)
            if (type !== 'loading') {
                const timeout = type === 'success' ? 2000 : 4000;
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.style.animation = 'slideOut 0.3s ease-in forwards';
                        setTimeout(() => notification.remove(), 300);
                    }
                }, timeout);
            }
        }

        // Função para transição para próxima etapa
        function goToStep2() {
            // Animar saída da etapa 1
            step1.style.animation = 'slideOutToLeft 0.5s ease-in-out forwards';
            
            setTimeout(() => {
                step1.classList.add('hidden');
                step1.classList.remove('active');
                
                // Preparar etapa 2 para entrada
                step2.classList.remove('hidden');
                step2.style.animation = 'slideInFromRight 0.5s ease-in-out forwards';
                step2.classList.add('active');
                
                // Atualizar indicadores de progresso
                dot1.classList.remove('active');
                dot2.classList.add('active');
            }, 250);
        }

        // Função para voltar para etapa anterior
        function goToStep1() {
            // Animar saída da etapa 2
            step2.style.animation = 'slideOutToLeft 0.5s ease-in-out reverse';
            
            setTimeout(() => {
                step2.classList.add('hidden');
                step2.classList.remove('active');
                
                // Preparar etapa 1 para entrada
                step1.classList.remove('hidden');
                step1.style.animation = 'slideInFromRight 0.5s ease-in-out reverse';
                step1.classList.add('active');
                
                // Atualizar indicadores de progresso
                dot2.classList.remove('active');
                dot1.classList.add('active');
            }, 250);
        }

        // Função para coletar todos os dados dos formulários
        function collectFormData() {
            const formData = new FormData();
            
            // Debug: log dos valores coletados
            console.log("=== COLETANDO DADOS DO FORMULÁRIO ===");
            
            // Dados do Step 1 (com os nomes corretos esperados pelo controller)
            const nome = document.getElementById("nome").value.trim();
            const email = document.getElementById("email").value.trim();
            const cpf = document.getElementById("cpf").value.replace(/[^0-9]/g, ''); // Remove formatação
            const senha = document.getElementById("senha").value.trim();
            
            formData.append('completeName', nome);
            formData.append('email', email);
            formData.append('cpf', cpf);
            formData.append('password', senha);
            
            console.log("Step 1 - Nome:", nome);
            console.log("Step 1 - Email:", email);
            console.log("Step 1 - CPF:", cpf);
            console.log("Step 1 - Senha:", senha ? "***fornecida***" : "vazia");
            
            // Dados do Step 2 (com os nomes corretos esperados pelo controller)
            const nascimento = document.getElementById("nascimento").value;
            const cep = document.getElementById("cep").value.replace(/[^0-9]/g, ''); // Remove formatação
            const numero = document.getElementById("numero").value.trim();
            const telefone = document.getElementById("telefone").value.replace(/[^0-9]/g, ''); // Remove formatação
            
            formData.append('birthDate', nascimento);
            formData.append('cep', cep);
            formData.append('adressNumber', numero); // Corrigido para coincidir com HTML
            formData.append('phone', telefone);
            
            console.log("Step 2 - Nascimento:", nascimento);
            console.log("Step 2 - CEP:", cep);
            console.log("Step 2 - Número:", numero);
            console.log("Step 2 - Telefone:", telefone);
            
            console.log("=== DADOS PRONTOS PARA ENVIO ===");

            return formData;
        }

        // Função para enviar dados para o servidor
        function submitRegistration() {
            const formData = collectFormData();
            
            // Mostrar loading
            showNotification("Processando cadastro...", 'loading');
            
            // Desabilitar botão de submit
            const submitBtn = step2.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';
            }

            console.log("=== ENVIANDO REQUISIÇÃO ===");
            console.log("URL:", '../controllers/login/registerController.php');

            // Enviar dados via fetch
            fetch('../controllers/login/registerController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log("=== RESPOSTA RECEBIDA ===");
                console.log("Status:", response.status);
                console.log("StatusText:", response.statusText);
                console.log("Headers:", response.headers);
                console.log("Redirected:", response.redirected);
                console.log("URL:", response.url);
                
                // Verificar se houve redirecionamento (indicando sucesso ou erro específico)
                if (response.redirected) {
                    console.log("=== REDIRECIONAMENTO DETECTADO ===");
                    console.log("Nova URL:", response.url);
                    
                    // Verificar se é redirecionamento de erro ou sucesso
                    if (response.url.includes('error=')) {
                        // É um erro, não redirecionar, mas extrair o erro da URL
                        const urlParams = new URLSearchParams(response.url.split('?')[1]);
                        const errorType = urlParams.get('error');
                        
                        // Mapear erros para mensagens amigáveis
                        const errorMessages = {
                            'empty_fields': 'Preencha todos os campos obrigatórios.',
                            'invalid_name': 'Nome deve ter mais de 5 caracteres.',
                            'invalid_email': 'Email inválido.',
                            'invalid_cpf': 'CPF inválido.',
                            'weak_password': 'Senha deve ter pelo menos 6 caracteres.',
                            'invalid_birthdate': 'Data de nascimento inválida.',
                            'underage': 'Você deve ter pelo menos 18 anos.',
                            'invalid_cep': 'CEP inválido.',
                            'invalid_adress_number': 'Número da residência deve conter apenas números.',
                            'invalid_phone': 'Telefone inválido.',
                            'email_taken': 'Este email já está cadastrado.',
                            'cpf_taken': 'Este CPF já está cadastrado.',
                            'server_error': 'Erro interno do servidor. Tente novamente.'
                        };
                        
                        const errorMessage = errorMessages[errorType] || 'Erro desconhecido: ' + errorType;
                        showNotification(errorMessage, 'error');
                        console.log("ERRO MAPEADO:", errorType, "->", errorMessage);
                        return null;
                    } else {
                        // É sucesso, redirecionar
                        showNotification("Cadastro realizado com sucesso! Redirecionando...", 'success');
                        setTimeout(() => {
                            window.location.href = response.url;
                        }, 1500);
                        return null;
                    }
                }
                
                // Se não houve redirecionamento, ler o conteúdo da resposta
                return response.text();
            })
            .then(data => {
                if (data !== null) { // Só processa se não houve redirecionamento
                    console.log("=== CONTEÚDO DA RESPOSTA ===");
                    console.log("Data:", data);
                    
                    if (data && data.trim() !== '') {
                        // Se retornou dados, algo deu errado
                        console.error('Resposta inesperada do servidor:', data);
                        
                        // Analisar tipo de erro para dar feedback específico
                        if (data.includes('Fatal error') || data.includes('Notice') || data.includes('Warning')) {
                            showNotification("Erro interno no servidor. Verifique se todas as tabelas do banco existem.", 'error');
                            console.error('ERRO PHP DETECTADO:', data);
                        } else if (data.includes('mysqli') || data.includes('database') || data.includes('connection')) {
                            showNotification("Erro de conexão com banco de dados. Verifique se o MySQL está rodando.", 'error');
                        } else if (data.includes('<!DOCTYPE') || data.includes('<html')) {
                            showNotification("Erro no servidor. Verifique o console para detalhes.", 'error');
                        } else if (data.includes('Falha ao conectar')) {
                            showNotification("Erro de conexão com banco de dados.", 'error');
                        } else {
                            // Tentar extrair mensagem de erro se for texto simples
                            const errorMessage = data.length > 100 ? data.substring(0, 100) + '...' : data;
                            showNotification("Erro: " + errorMessage, 'error');
                        }
                    } else {
                        // Sucesso sem redirecionamento (improvável)
                        showNotification("Cadastro processado! Aguarde...", 'success');
                    }
                }
            })
            .catch(error => {
                console.error('=== ERRO NA REQUISIÇÃO ===');
                console.error('Tipo do erro:', error.name);
                console.error('Mensagem:', error.message);
                console.error('Stack:', error.stack);
                showNotification("Erro de conexão: " + error.message, 'error');
            })
            .finally(() => {
                // Reabilitar botão
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Finalizar Cadastro';
                }
                
                // Remover notificação de loading se ainda estiver visível
                const loadingNotification = document.getElementById('validationNotification');
                if (loadingNotification && loadingNotification.classList.contains('loading-notification')) {
                    loadingNotification.remove();
                }
            });
        }

        // Função para testar conexão com o servidor
        function testServerConnection() {
            console.log("=== TESTANDO CONEXÃO COM SERVIDOR ===");
            fetch('../controllers/login/registerController.php', {
                method: 'GET'
            })
            .then(response => {
                console.log("Teste GET - Status:", response.status);
                return response.text();
            })
            .then(data => {
                console.log("Teste GET - Resposta:", data);
            })
            .catch(error => {
                console.error("Teste GET - Erro:", error);
            });
        }

        // Event listeners
        
        // Botão "Próximo" - valida step1 e vai para step2
        nextBtn.addEventListener("click", (e) => {
            e.preventDefault();
            if (validateStep1()) {
                goToStep2();
            }
        });

        // Botão "Voltar" - volta para step1
        prevBtn.addEventListener("click", (e) => {
            e.preventDefault();
            goToStep1();
        });

        // Submit do formulário final - valida step2 e envia dados
        step2.addEventListener("submit", (e) => {
            e.preventDefault();
            
            if (validateStep2()) {
                submitRegistration();
            }
        });

        // Opcional: Enter no step1 vai para próximo
        step1.addEventListener("keypress", (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (validateStep1()) {
                    goToStep2();
                }
            }
        });

        // Testar conexão na inicialização (opcional)
        // testServerConnection();
    }

    // Código de compatibilidade (mantido para casos simples)
    const nextBtnSimple = document.getElementById("nextStep");
    const step1Simple = document.getElementById("step1");
    const step2Simple = document.getElementById("step2");

    if (nextBtnSimple && step1Simple && step2Simple && !nextBtn) {
        nextBtnSimple.addEventListener("click", () => {
            const nome = document.getElementById("nome").value.trim();
            const email = document.getElementById("email").value.trim();
            const senha = document.getElementById("senha").value.trim();

            if (nome && email && senha) {
                step1Simple.classList.add("hidden");
                step2Simple.classList.remove("hidden");
            } else {
                alert("Por favor, preencha todos os campos antes de prosseguir.");
            }
        });
    }
});