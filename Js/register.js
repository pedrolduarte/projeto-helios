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
                showNotification("Por favor, preencha todos os campos antes de prosseguir.");
                return false;
            }

            // Validação básica de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification("Por favor, insira um email válido.");
                return false;
            }

            // Validação básica de CPF (formato brasileiro)
            const cpfRegex = /^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/;
            if (!cpfRegex.test(cpf)) {
                showNotification("Por favor, insira um CPF válido (formato: 123.456.789-00).");
                return false;
            }

            // Validação básica de senha
            if (senha.length < 6) {
                showNotification("A senha deve ter pelo menos 6 caracteres.");
                return false;
            }

            return true;
        }

        // Função para mostrar notificação
        function showNotification(message) {
            // Remove notificação existente se houver
            const existingNotification = document.getElementById('validationNotification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Cria nova notificação
            const notification = document.createElement('div');
            notification.id = 'validationNotification';
            notification.className = 'notification error-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="notification-message">${message}</span>
                    <button class="close-btn" onclick="this.parentElement.parentElement.remove()">&times;</button>
                </div>
                <div class="progress-bar"></div>
            `;

            document.body.appendChild(notification);

            // Auto-remover após 3 segundos
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.animation = 'slideOut 0.3s ease-in forwards';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 3000);
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

        // Event listeners para registro
        nextBtn.addEventListener("click", (e) => {
            e.preventDefault();
            if (validateStep1()) {
                goToStep2();
            }
        });

        prevBtn.addEventListener("click", (e) => {
            e.preventDefault();
            goToStep1();
        });

        // Submissão do formulário final
        step2.addEventListener("submit", (e) => {
            e.preventDefault();
            
            // Validar campos da etapa 2
            const nascimento = document.getElementById("nascimento").value;
            const cep = document.getElementById("cep").value.trim();
            const numero = document.getElementById("numero").value.trim();
            const telefone = document.getElementById("telefone").value.trim();

            if (!nascimento || !cep || !numero || !telefone) {
                showNotification("Por favor, preencha todos os campos para finalizar o cadastro.");
                return;
            }

            // Validação básica de CEP (formato brasileiro)
            const cepRegex = /^\d{5}-?\d{3}$/;
            if (!cepRegex.test(cep)) {
                showNotification("Por favor, insira um CEP válido (formato: 12345-678).");
                return;
            }

            // Validação básica de telefone
            const telefoneRegex = /^\(\d{2}\)\s?\d{4,5}-?\d{4}$/;
            if (!telefoneRegex.test(telefone)) {
                showNotification("Por favor, insira um telefone válido (formato: (11) 99999-9999).");
                return;
            }

            // Aqui você pode enviar os dados para o servidor
            showNotification("Cadastro realizado com sucesso! Redirecionando...");
            
            // Simular redirecionamento após 2 segundos
            setTimeout(() => {
                window.location.href = "login.php";
            }, 2000);
        });
    }

    // Código original do register.js (funcionalidade simples)
    // Mantido para compatibilidade caso seja usado em outros lugares
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
  