# Sistema de Gerenciamento de Biblioteca - Facilita Teste

## Visão Geral do Projeto

Este projeto é um sistema de gerenciamento de biblioteca desenvolvido com Laravel, seguindo princípios de arquitetura limpa (Clean Architecture) e boas práticas de desenvolvimento de software.

## Arquitetura e Padrões Utilizados

### Arquitetura Limpa (Clean Architecture)
O projeto foi estruturado seguindo os princípios da Clean Architecture, com uma separação clara de responsabilidades:

- **Camadas de Arquitetura**:
    1. **Presentation Layer (Controllers)**: Responsável por receber requisições HTTP e retornar respostas.
    2. **Application Layer (Services)**: Contém a lógica de negócio da aplicação.
    3. **Domain Layer (Interfaces, Enums)**: Define contratos e regras de negócio.
    4. **Data Layer (Repositories)**: Gerencia a persistência e recuperação de dados.

### Princípios SOLID
- **S**ingle Responsibility Principle
- **O**pen/Closed Principle
- **L**iskov Substitution Principle
- **I**nterface Segregation Principle
- **D**ependency Inversion Principle

### Padrões de Projeto Utilizados
- Repository Pattern
- Dependency Injection
- Data Transfer Object (DTO)
- Strategy Pattern
- Enum para gerenciamento de estados

## Tecnologias Principais
- **Backend**: Laravel 12
- **Banco de Dados**: SQLite (configurável para outros bancos)
- **Documentação**: Swagger (OpenAPI)
- **Testes**: PHPUnit

### Detalhamento da Estrutura

#### Camada de Aplicação (`app/`)

- **Controllers/**: Responsáveis por gerenciar as requisições HTTP e respostas
    - Processam entrada de dados
    - Interagem com Services
    - Retornam respostas formatadas

- **Services/**: Contém a lógica de negócio da aplicação
    - Implementam regras de negócio
    - Coordenam operações entre repositories
    - Processam transformações de dados

- **Repositories/**: Gerenciam a persistência e recuperação de dados
    - Abstraem a lógica de acesso a dados
    - Implementam interfaces de repositório
    - Realizam operações CRUD

- **Interfaces/**: Definem contratos e abstrações
    - Estabelecem padrões para implementações
    - Facilitam inversão de dependência
    - Permitem troca de implementações

- **Models/**: Representam entidades do domínio
    - Modelos Eloquent do Laravel
    - Definem relacionamentos
    - Contêm validações e mutators

- **DTO/**: Objetos de Transferência de Dados
    - Transportam dados entre camadas
    - Imutáveis
    - Validam estrutura de dados

- **Enums/**: Enumeradores
    - Representam conjuntos de constantes
    - Restringem valores possíveis
    - Melhoram legibilidade do código

- **Exceptions/**: Exceções personalizadas
    - Tratam erros específicos da aplicação
    - Fornecem mensagens claras
    - Facilitam tratamento de erros

## Funcionalidades

### Módulo de Usuários
- CRUD completo de usuários
- Validação de email e número de registro únicos

### Módulo de Livros
- Gerenciamento de livros
- Classificação por gênero
- Controle de status (disponível/emprestado)

### Módulo de Empréstimos
- Empréstimo de livros
- Controle de data de devolução
- Marcação de empréstimos atrasados
- Devolução de livros

## Pré-requisitos
- PHP 8.2+
- Composer
- Laravel 12

## Instalação caso tenha o PHP e o Composer instalados

1. Clone o repositório
```bash
  git clone https://github.com/seu-usuario/facilita-teste.git
  cd facilita-teste
```

2. Instale as dependências
```bash
  composer install
```
3. Copie o arquivo de environment

```bash
  cp .env.example .env
```

4. Gere a chave da aplicação

```bash
  php artisan key:generate
```

5. Configure o banco de dados no .env
Execute as migrações

```bash
  php artisan migrate
```

6. Gere a documentação Swagger
```bash
  php artisan l5-swagger:generate
```

7. Inicie o servidor
```bash
  php artisan serve
```
Documentação da API
Swagger
Acesse a documentação interativa da API em:
http://localhost:8000/api/documentation
Insomnia
Arquivo de configuração do Insomnia incluído no repositório:

Localização: insomnia/biblioteca_api.json
Importe no Insomnia para ter todas as rotas pré-configuradas

## Desenvolvimento com Docker

### Pré-requisitos
- Docker
- Docker Compose

### Iniciar Ambiente de Desenvolvimento com o Docker
```bash
  # Clonar o repositório
  git clone https://github.com/seu-usuario/facilita-teste.git
  cd facilita-teste

  # Copiar .env de exemplo
  cp .env.example .env

  # Construir e iniciar containers
  docker compose up -d --build

  # Instalar dependências
  docker compose exec app composer install

  # Gerar chave da aplicação
  docker compose exec app php artisan key:generate

  # Rodar migrações
  docker compose exec app php artisan migrate
  
  # Gere documentacao swagger
  docker compose exec app php artisan l5-swagger:generate

  # Acessar aplicação
  http://localhost:8000

  # Documentação Swagger
  http://localhost:8000/api/documentation
```

Desenvolvido por Luis Henrique
