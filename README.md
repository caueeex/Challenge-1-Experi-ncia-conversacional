# Furiaverse - Plataforma FURIA Esports

![FURIA Logo]

## 📋 Sobre o Projeto

Furiaverse é uma plataforma web dedicada à FURIA Esports, oferecendo uma experiência completa para fãs acompanharem seus times favoritos. A plataforma integra informações em tempo real, chatbot interativo e sistema de gamificação para engajar a comunidade.

## ✨ Funcionalidades

- **Sistema de Autenticação**
  - Login de usuários
  - Sistema de pontos (gamification)
  - Perfil personalizado

- **Chatbot Interativo**
  - Informações em tempo real sobre partidas
  - Menu interativo
  - Integração com API PandaScore

- **Informações dos Times**
  - CS:GO
  - VALORANT
  - League of Legends
  - Rainbow Six
  - Rosters completos
  - Conquistas recentes
  - Redes sociais

- **Interface Moderna**
  - Design responsivo
  - Interface intuitiva
  - Experiência mobile-first

## 🛠️ Tecnologias Utilizadas

- **Frontend**
  - HTML5
  - CSS3 (Tailwind CSS)
  - JavaScript (Alpine.js)
  - WebSocket

- **Backend**
  - PHP 8.0+
  - MySQL 8.0+
  - Node.js 18.0+

- **APIs e Integrações**
  - PandaScore API
  - WebSocket para comunicação em tempo real

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP 8.0 ou superior
- Node.js 18.0 ou superior
- MySQL 8.0 ou superior
- Composer (gerenciador de dependências PHP)
- npm (gerenciador de pacotes Node.js)

### Passo 1: Clonar o Repositório

```bash
git clone https://github.com/caueeex/Challenge-1-Experi-ncia-conversacional.git
cd furiaverse
```

### Passo 2: Configurar o Ambiente PHP

1. Instale as dependências PHP:
```bash
composer install
```

2. Configure o servidor PHP:
```bash
# Para Windows (XAMPP):
# Certifique-se que o XAMPP está instalado e o serviço Apache está rodando

# Para Linux/Mac:
sudo apt-get install php php-mysql php-curl php-json
```

3. Inicie o servidor PHP:
```bash
# Para desenvolvimento local:
php -S localhost:8000 -t public/

# Para produção (Apache):
# Configure o VirtualHost no Apache
```

### Passo 3: Configurar o Node.js

1. Instale as dependências Node.js:
```bash
npm install
```

2. Inicie o servidor Node.js para o chatbot:
```bash
# Desenvolvimento
npm run dev

# Produção
npm start
```

3. Configure o arquivo `.env`:
```env
NODE_ENV=development
PORT=3000
PANDASCORE_API_KEY=sua_chave_aqui
```

### Passo 4: Configurar o Banco de Dados

1. Crie o banco de dados:
```sql
CREATE DATABASE furia_db;
```

2. Importe o schema:
```bash
mysql -u root -p furia_db < database/schema.sql
```

3. Configure as credenciais no arquivo `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'furia_db');
```

### Passo 5: Iniciar a Aplicação

1. Inicie o servidor PHP:
```bash
php -S localhost:8000
```

2. Em outro terminal, inicie o servidor Node.js:
```bash
npm start
```

3. Acesse a aplicação:
```
http://localhost:8000
```

## 🔧 Configuração de Ambiente

### Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
# Configurações do Banco de Dados
DB_HOST=localhost
DB_USER=seu_usuario
DB_PASS=sua_senha
DB_NAME=furia_db

# Configurações da API
PANDASCORE_API_KEY=sua_chave_aqui

# Configurações do Node.js
NODE_ENV=development
PORT=3000
```

### Configuração do Apache (Produção)

```apache
<VirtualHost *:80>
    ServerName furiaverse.local
    DocumentRoot /caminho/para/furiaverse/public
    
    <Directory /caminho/para/furiaverse/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 🤝 Contribuindo

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

Para suporte, envie um email para suporte@furia.gg ou abra uma issue no GitHub.

## 🙏 Agradecimentos

- Equipe FURIA Esports
- Comunidade de fãs
- Desenvolvedores e contribuidores

---

Desenvolvido com ❤️ pela equipe FURIA Esports
