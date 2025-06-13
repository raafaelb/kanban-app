# Kanban Board - Vox Tecnologia

Sistema completo de gerenciamento de tarefas estilo Kanban (semelhante ao Trello), desenvolvido com Laravel 11, Bootstrap 5 e jQuery.

---

## Índice

- [✨ Funcionalidades Principais](#-funcionalidades-principais)
- [🛠️ Tecnologias Utilizadas](#️-tecnologias-utilizadas)
- [⚙️ Configuração do Ambiente](#️-configuração-do-ambiente)
  - [Pré-requisitos](#pré-requisitos)
  - [Instalação](#instalação)
  - [Configuração](#configuração)
- [🔐 Credenciais Padrão](#-credenciais-padrão)
- [🚀 Como Usar](#-como-usar)

---

## ✨ Funcionalidades Principais

- **Autenticação de Usuários**
  - Sistema completo de login e registro
- **Gerenciamento de Quadros (Boards)**
  - Criação de múltiplos quadros Kanban
  - Templates pré-definidos (Básico, Desenvolvimento, Suporte)
- **Colunas Personalizáveis**
  - Adição/edição de colunas
  - Cores personalizadas
  - Reordenação via drag-and-drop
- **Gestão de Tarefas**
  - CRUD completo de tarefas
  - Prioridades (Alta, Média, Baixa)
  - Prazos definidos
  - Movimento entre colunas via drag-and-drop
- **Interface Responsiva**
  - Layout adaptável para desktop e mobile
  - Sidebar recolhível em telas menores
- **Estatísticas e Filtros**
  - Contagem total de tarefas
  - Filtragem por prioridade
- **Gestão de Equipe**
  - Visualização de membros da equipe em cada quadro

---

## 🛠️ Tecnologias Utilizadas

- **Backend**
  - Laravel 11
  - PHP 8.2+
  - PostgreSQL
  - Eloquent ORM
- **Frontend**
  - Bootstrap 5
  - jQuery
  - SortableJS (para drag-and-drop)
- **Outras Dependências**
  - Font Awesome (ícones)
  - Carbon (manipulação de datas)
  - AJAX: atualização em tempo real sem recarregar a página

---

## ⚙️ Configuração do Ambiente

### Pré-requisitos

- PHP 8.2+
- Composer
- PostgreSQL
- Node.js 18+ e NPM

### Instalação

1. **Clone o repositório**  
   ```bash
   git clone https://github.com/raafaelb/kanban-app.git
   cd kanban-app

2. **Instale dependências PHP**
    ```bash
    composer install

3. **Instale dependências JavaScript**
    ```bash
    npm install

### Configuração

1. **Copie e configure o .env**
    ```bash
    cp .env.example .env
    php artisan key:generate

2. **Ajuste variáveis de banco de dados no .env**
    ```bash
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=kanban
    DB_USERNAME=postgres
    DB_PASSWORD=password

3. **Execute migrações e seeders**
    ```bash
    php artisan migrate --seed

4. **Compile os assets**
    ```bash
    npm run build

5. **Inicie o servidor de desenvolvimento**
    ```bash
    php artisan serve



## 🔐 Credenciais Padrão

Um usuário administrador é criado automaticamente durante o seeder inicial:  
**Email:** `admin@voxtecnologia.com.br`  
**Senha:** `password`


## 🚀 Como Usar

1. Acesse a aplicação em [http://localhost:8000](http://localhost:8000) (ou a porta exibida pelo `artisan serve`).
2. Faça login com as credenciais padrão ou registre um novo usuário.
3. Adicione/edite colunas com cores personalizadas.
4. Crie tarefas, defina prioridade e prazos.
5. Arraste tarefas entre colunas para atualizar o status.
6. Utilize filtros de prioridade na interface para visualizar um subconjunto de tarefas.
7. Em telas menores, use a sidebar recolhível para navegação.
