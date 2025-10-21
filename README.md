# ☀️ Helios Web - Plataforma de Energia Solar

<div align="center">

![Helios Logo](public/assets/img/logo.png)

**Uma plataforma web inovadora para democratizar o acesso à energia solar**

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://html.spec.whatwg.org)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://www.w3.org/Style/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://javascript.info)

</div>

## 📋 Sobre o Projeto

**Helios Web** é uma plataforma web exclusiva desenvolvida para empresas fornecedoras de soluções em energia solar. O sistema centraliza a captação e gestão de clientes interessados na tecnologia fotovoltaica, eliminando barreiras que dificultam a adoção da energia solar.

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

## 🚀 Funcionalidades

### Para Clientes
- ✅ **Cadastro Personalizado** - Sistema de registro com validação completa
- 📊 **Relatórios de Consumo** - Visualização do consumo mensal de energia
- 💰 **Análise de Payback** - Cálculo de retorno sobre investimento
- 🔒 **Área Segura** - Dashboard personalizado com autenticação
- 📞 **Suporte Integrado** - Sistema de chamados e atendimento

### Para Empresa
- 👥 **Gestão de Clientes** - Controle completo da base de clientes
- 📦 **Controle de Estoque** - Gerenciamento de produtos e componentes
- 📈 **Relatórios Segmentados** - Análises por região e estado
- 🎯 **Leads Qualificados** - Captação direcionada sem concorrência interna

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

### Arquitetura
- **MVC Pattern** - Separação de responsabilidades
- **Prepared Statements** - Segurança contra SQL Injection
- **Password Hashing** - Criptografia bcrypt para senhas
- **Session Management** - Controle de autenticação

## 📁 Estrutura do Projeto

```
helios-web/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── loginMethod.php
│   │   └── registerController.php
│   ├── view/                 # Interfaces de usuário
│   │   ├── login.php
│   │   ├── register.php
│   │   └── dashboard.php
│   └── config/               # Configurações
│       └── connection.php
├── public/
│   └── assets/
│       ├── css/              # Estilos
│       ├── js/               # Scripts
│       └── img/              # Imagens
├── database/
│   └── tables.sql           # Estrutura do banco
└── README.md
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
   http://localhost/helios-web/app/view/login.php
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

## 📊 Funcionalidades Técnicas

### Sistema de Autenticação
- Login seguro com validação
- Registro com múltiplas etapas
- Validação de CPF em tempo real
- Verificação de idade (18+ anos)

### Interface Responsiva
- Design moderno e intuitivo
- Carousel para formulários longos
- Notificações em tempo real
- Animações suaves CSS3

### Gestão de Dados
- Transações MySQL para integridade
- Logs de erro para debugging
- Validações client-side e server-side
- Backup automatizado de sessões

## 📝 Licença

Este projeto é desenvolvido para fins acadêmicos como parte do curso de Tecnologia em Sistemas de informação.

## 📞 Contato

Para dúvidas ou sugestões sobre o projeto:

- **Pedro Duarte** - Desenvolvedor Backend
- **Gabriel Souza** - Desenvolvedor Frontend  
- **Eduardo Kauan** - DBA
- **Levi Felipe** - Testes e pesquisas 

---

<div align="center">

**Helios Web - Iluminando o futuro com energia solar** ☀️

*Desenvolvido com 💚 para um mundo mais sustentável*

</div>