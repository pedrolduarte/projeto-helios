# ☀️ Helios Web - Plataforma de Energia Solar

<div align="center">

![Helios Logo](public/assets/img/logo.png)

**Uma plataforma web completa para simulação, orçamento e gestão de energia solar**

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://html.spec.whatwg.org)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://www.w3.org/Style/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://javascript.info)

</div>

## 📋 Sobre o Projeto

**Helios Web** é uma plataforma web completa para simulação e orçamento de energia solar fotovoltaica. O sistema oferece calculadoras precisas de dimensionamento, gestão inteligente de orçamentos e interface intuitiva para conectar clientes e fornecedores de soluções em energia solar.

### 🎯 Objetivos

- **Democratizar** o acesso a informações sobre energia solar
- **Simplificar** o processo de contato entre consumidores e especialistas
- **Transparência** em custos, retorno do investimento e benefícios
- **Contribuir** com o ODS 7: Energia Limpa e Acessível da ONU

## 👥 Equipe de Desenvolvimento

Este projeto foi desenvolvido como trabalho acadêmico por:

- **Pedro Duarte** - Desenvolvedor Back-end
- **Gabriel Souza** - Desenvolvedor Front-end
- **Eduardo Kauan** - Administrador de Banco de Dados (DBA)
- **Levi Felipe** - Testes e Pesquisas

## 🚀 Funcionalidades Principais

### 🧮 Sistema de Simulação
- ⚡ **Calculadora Solar** - Dimensionamento preciso baseado no consumo
- 📊 **Análise de Potência** - Cálculo de kWp necessário
- 🏠 **Configuração Residencial** - Tipo de telhado e área disponível
- 💰 **Estimativa de Economia** - Projeção financeira personalizada
- 🌞 **Irradiação Solar** - Dados regionais de incidência solar

### 💼 Gestão de Orçamentos
- 📝 **Solicitação Automática** - Sistema inteligente de criação de orçamentos
- 🔄 **Status em Tempo Real** - Acompanhamento do progresso (PENDENTE/APROVADO)
- 📋 **Dashboard Personalizado** - Interface específica por tipo de cliente
- 🚨 **Sistema de Notificações** - Alertas de status via query parameters
- 📊 **Histórico de Solicitações** - Gestão completa de propostas anteriores

### 🔐 Autenticação e Segurança
- 👤 **Login Seguro** - Autenticação com prepared statements
- 🛡️ **Proteção de Sessão** - Middleware de proteção para áreas restritas
- 📱 **Interface Responsiva** - Design adaptativo para todos os dispositivos
- 🔒 **Validação Completa** - Sanitização de entrada e proteção CSRF

### 🏢 Gestão Empresarial
- 👥 **Clientes Cadastrados** - Base de dados estruturada de usuários
- 📈 **Análise de Demanda** - Relatórios de simulações e orçamentos
- 🎯 **Leads Qualificados** - Sistema de captação otimizado
- 📊 **Métricas de Conversão** - Acompanhamento de resultados

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 8+** - Linguagem principal do servidor
- **MySQL** - Banco de dados relacional
- **PDO/MySQLi** - Conexão e operações de banco

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilização moderna com animações
- **JavaScript ES6+** - Interatividade e validações
- **Font Awesome** - Ícones profissionais

### APIs e Integrações
- **ViaCEP API** - Preenchimento automático de endereços
- **Simulação Backend** - API interna para cálculos solares
- **Fetch API** - Comunicação assíncrona JavaScript
- **Query Parameters** - Sistema de notificações URL-based

### Arquitetura
- **MVC Pattern** - Separação clara de responsabilidades
- **Prepared Statements** - Segurança avançada contra SQL Injection
- **Session Management** - Controle robusto de autenticação
- **Middleware Protection** - Camada de segurança para rotas protegidas

## 📁 Estrutura do Projeto

```
helios-web/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── login/
│   │   │   └── loginController.php     # Autenticação de usuários
│   │   ├── costumer/
│   │   │   ├── simulacaoController.php # API de simulação solar
│   │   │   └── solicitarOrcamentoController.php # Gestão de orçamentos
│   │   ├── protect.php       # Middleware de proteção
│   │   └── finishSessionController.php # Logout seguro
│   ├── view/                 # Interfaces de usuário
│   │   ├── login.php         # Tela de autenticação
│   │   ├── noCostumerDashboard.php # Dashboard cliente
│   │   └── simulacao.php     # Calculadora solar
│   └── config/               # Configurações do sistema
│       └── connection.php    # Conexão MySQL
├── public/assets/            # Recursos públicos
│   ├── css/
│   │   ├── login.css         # Estilos de autenticação
│   │   ├── simulacao.css     # Estilos da calculadora
│   │   └── noCostumerDashboard.css # Estilos do dashboard
│   ├── js/
│   │   ├── simulacao.js      # Lógica da simulação
│   │   └── noCostumerDashboard.js # Lógica do dashboard
│   └── img/                  # Imagens e logos
├── tables.sql               # Estrutura do banco de dados
├── connection.php           # Configuração de conexão
├── index.html              # Página inicial
└── loginMethod.php         # Método de login legado
```

## ⚙️ Instalação e Configuração

### Pré-requisitos
- XAMPP (PHP 8+ e MySQL)
- Navegador web moderno
- Editor de código (recomendado: VS Code)

### Passo a Passo

1. **Clone o repositório**
   ```bash
   git clone https://github.com/pedrolduarte/projeto-helios.git
   cd projeto-helios
   ```

2. **Configure o XAMPP**
   - Inicie o Apache e MySQL
   - Coloque o projeto em `c:\xampp\htdocs\helios-web\`

3. **Configure o Banco de Dados**
   ```sql
   -- Acesse phpMyAdmin (http://localhost/phpmyadmin)
   -- Crie um banco chamado 'helios_db'
   -- Importe o arquivo tables.sql
   ```

4. **Configure a Conexão**
   ```php
   // Em app/config/connection.php
   $host = "localhost";
   $username = "root";
   $password = "";
   $database = "helios_db";
   ```

5. **Acesse a Aplicação**
   ```
   # Página inicial
   http://localhost/helios-web/index.html
   
   # Login de usuários
   http://localhost/helios-web/app/view/login.php
   
   # Simulação solar (público)
   http://localhost/helios-web/app/view/simulacao.php
   ```

## 🔐 Recursos de Segurança

- **Validação de Entrada** - Sanitização completa de dados
- **Prepared Statements** - Proteção contra SQL Injection
- **Password Hashing** - Criptografia bcrypt para senhas
- **Session Security** - Gerenciamento seguro de sessões
- **CSRF Protection** - Validação de métodos HTTP

## 🌱 Impacto Sustentável

O projeto contribui diretamente com o **ODS 7 - Energia Limpa e Acessível** através de:

- 🌍 **Democratização** do acesso à energia solar
- 📚 **Educação** sobre benefícios da energia renovável
- 💡 **Transparência** em investimentos sustentáveis
- 🤝 **Conexão** entre consumidores e soluções verdes

## 📊 Funcionalidades Técnicas Avançadas

### 🧮 Motor de Cálculo Solar
- **Algoritmo de Dimensionamento** - Cálculo preciso baseado em kWh/mês
- **Fator de Irradiação** - Dados regionais brasileiros (4.5-6.5 kWh/m²/dia)
- **Eficiência de Sistema** - Consideração de perdas (inversor, cabeamento, temperatura)
- **API Híbrida** - Backend PHP + fallback JavaScript para máxima confiabilidade

### 🔄 Sistema de Estados Inteligente
- **Gestão de Orçamentos** - Status PENDENTE/APROVADO com transições automáticas
- **Interface Dinâmica** - HTML gerado server-side baseado no estado do banco
- **Notificações Contextuais** - Sistema de mensagens via query parameters
- **Persistência de Sessão** - Manutenção de estado entre requisições

### 🛡️ Segurança Avançada
- **Prepared Statements** - Proteção total contra SQL Injection
- **Session Protection** - Middleware de validação em todas as rotas protegidas
- **Input Sanitization** - Validação rigorosa de entrada de dados
- **Error Handling** - Tratamento elegante de erros sem exposição de dados sensíveis

### 🎨 Interface Moderna
- **Design Responsivo** - Adaptação perfeita para mobile, tablet e desktop
- **CSS Grid/Flexbox** - Layout moderno e flexível
- **Animações CSS3** - Transições suaves e profissionais
- **UX Otimizada** - Fluxo de usuário intuitivo e conversão otimizada

### 🔧 Integração de APIs
- **ViaCEP Integration** - Preenchimento automático de endereços brasileiros
- **Fetch API** - Comunicação assíncrona moderna
- **Error Fallback** - Sistema de backup para máxima disponibilidade
- **Timeout Handling** - Gestão inteligente de timeouts de rede

## 📝 Licença

Este projeto é desenvolvido para fins acadêmicos como parte do curso de Tecnologia em Sistemas de informação.

## 🎯 Fluxo de Uso da Aplicação

### Para Novos Usuários
1. **Acesso Inicial** → `index.html` (página de apresentação)
2. **Simulação Pública** → `simulacao.php` (calculator sem login)
3. **Cadastro/Login** → `login.php` (autenticação necessária)
4. **Dashboard** → `noCostumerDashboard.php` (área do cliente)
5. **Solicitação** → Orçamento via `solicitarOrcamentoController.php`

### Para Usuários Autenticados
1. **Login Direto** → Acesso ao dashboard personalizado
2. **Status Check** → Verificação automática de orçamentos pendentes
3. **Nova Simulação** → Acesso às ferramentas de cálculo
4. **Gestão** → Histórico e status de solicitações

## 🚀 Próximas Funcionalidades

- [ ] **Painel Administrativo** - Dashboard para empresa fornecedora
- [ ] **Sistema de Chat** - Comunicação em tempo real
- [ ] **Geração de Propostas** - PDFs automáticos de orçamento
- [ ] **Integração Financeira** - Simulação de financiamento
- [ ] **App Mobile** - Versão React Native

## 📞 Contato

Para dúvidas ou sugestões sobre o projeto:

- **Pedro Duarte** - Desenvolvedor Backend & Arquitetura
- **Gabriel Souza** - Desenvolvedor Frontend & UX/UI
- **Eduardo Kauan** - DBA & Modelagem de Dados
- **Levi Felipe** - QA & Testes de Sistema 

---

<div align="center">

**Helios Web - Iluminando o futuro com energia solar** ☀️

*Desenvolvido com 💚 para um mundo mais sustentável*

</div>