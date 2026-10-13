<laravel-boost-guidelines>
=== foundation rules ===

# Diretrizes do Projeto & Laravel Boost Guidelines

## Contexto Fundamental & Stack Tecnológica

Esta é uma aplicação moderna baseada na **TALL Stack (Laravel, Livewire, Alpine.js, Tailwind CSS)** com **Flux UI** e autenticação **Fortify**, executando em **PHP 8.5** (compatível com `^8.3`).

Sempre utilize as APIs que correspondem à versão principal instalada de cada pacote — nunca assuma versões legadas.

### Versões Ativas no Projeto
- **PHP**: `8.5.x` (requisito: `^8.3`)
- **Laravel Framework**: `13.x`
- **Livewire**: `4.x` / `3.x` (compatibilidade nativa com componentes de classe e SFC)
- **Flux UI**: `2.x` (edição padrão, componentes `<flux:*>`)
- **Tailwind CSS**: `4.x` (CSS-first, `@import "tailwindcss"`)
- **Laravel Fortify**: `^1.37` (autenticação headless, 2FA, Passkeys)
- **Spatie Laravel Permission**: `^8.3` (RBAC - Roles & Permissions)
- **Pest PHP**: `^5.3` (com `pest-plugin-laravel ^5.0`)
- **Laravel Pint**: Linter e formatador de código oficial
- **Larastan**: Análise estática nível 5 (`phpstan.neon`)

Antes de depender da API de qualquer pacote, confirme a versão instalada:
- Pacotes PHP: `composer show --direct` ou `composer show <vendor/package>`.
- Pacotes JS: verifique `package.json`.

## Ativação de Skills

Este projeto possui skills específicas configuradas em `skills/` e plugins da IDE. Sempre ative a skill correspondente ao domínio da tarefa:
- `tall-stack-framework`: Padrão obrigatório para arquitetura TALL, Flux UI, Eloquent e Livewire.
- `livewire-development`: Criação, reatividade, ciclos de vida e otimização de Livewire.
- `fluxui-development`: Uso de componentes `<flux:*>`, ícones e layout.
- `tailwindcss-development`: Estilização utilitária com Tailwind v4.
- `laravel-best-practices`: Boas práticas de backend PHP, Eloquent, Policies e Jobs.
- `fortify-development`: Fluxos de autenticação, perfil, 2FA e credenciais.
- `pest-testing`: Escrita e manutenção de testes automatizados com Pest.

## Convenções de Arquitetura & Estrutura de Pastas

- **Livewire Components**:
  - Padrão do projeto: Componentes baseados em classe em `app/Livewire/{Dominio}/{Componente}.php` e templates Blade em `resources/views/livewire/{dominio}/{componente}.blade.php`.
  - Componentes de página inteira utilizam `#[Layout('layouts.app')]`.
  - Use `#[Title('...')]` para definição de título dinâmico da página.
- **Flux UI**:
  - Priorize os componentes nativos do Flux (`<flux:button>`, `<flux:input>`, `<flux:sidebar>`, `<flux:table>`, `<flux:toast>`, `<flux:modal>`, etc.) antes de criar HTML customizado.
  - Ícones: Utilize os ícones integrados do Heroicons ou Lucide (`icon="nome-do-icone"`).
  - **Modais de Confirmação Obrigatórios**: Toda ação destrutiva ou de confirmação (exclusão, cancelamento, revogação) deve obrigatoriamente usar `<flux:modal>`. É proibido usar o alerta/diálogo nativo do navegador (`wire:confirm`).
- **Layouts**:
  - Layout principal da aplicação: `resources/views/layouts/app.blade.php`, que invoca `<x-layouts::app.sidebar>` e envolve o conteúdo em `<flux:main>`.
- **RBAC (Controle de Acesso)**:
  - Gerenciado via `spatie/laravel-permission`.
  - Model `User` deve obrigatoriamente incluir a trait `HasRoles`.
  - Middlewares registrados em `bootstrap/app.php`: `role`, `permission`, `role_or_permission`.
- **Policies de Autorização**:
  - Armazenadas em `app/Policies/` e mapeadas aos Models.
  - Toda mutação ou consulta restrita deve invocar autorização explícita (`$this->authorize(...)`).

## Regras de Ouro (Segurança & Integridade)

1. **Proteção contra Auto-Exclusão**:
   - Um usuário autenticado **NUNCA** pode excluir a si mesmo. Essa regra deve ser garantida tanto na Policy (`UserPolicy@delete`) quanto na ação do componente Livewire.
2. **Proteção contra Auto-Revogação de Privilégios**:
   - Um usuário com privilégios administrativos **NUNCA** pode remover suas próprias roles essenciais ao editar seu próprio perfil no sistema.
3. **Prevenção de N+1 Queries**:
   - Toda listagem de dados relacionais deve usar eager-loading explícito (`User::with('roles')`).
4. **Validação de Entrada em Duas Camadas**:
   - Validação sempre no backend no componente Livewire ou Form Request.
   - Em formulários com regras dinâmicas (ex: `Rule::unique` ignorando o ID atual ou senha condicional), utilize o método `rules()` no Livewire.
5. **Propriedades Públicas são Entradas Não Confiáveis**:
   - Utilize `#[Locked]` em propriedades que não podem ser manipuladas pelo cliente (ex: `$userId`).

## Frontend Bundling & Assets

- Se uma alteração no frontend não refletir no navegador ou ocorrer o erro "Unable to locate file in Vite manifest", execute `npm run build` ou inicie o servidor com `composer run dev`.

=== boost rules ===

# Laravel Boost & Ferramentas MCP

## Ferramentas Disponíveis
- Prefira as ferramentas do Laravel Boost quando disponíveis.
- Use `database-query` para consultas somente-leitura.
- Use `database-schema` para inspecionar tabelas antes de escrever migrations ou models.
- Use `get-absolute-url` para resolver a URL correta do projeto.
- Use `browser-logs` para ler exceções e logs recentes do navegador.

## Busca em Documentação (`search-docs`)
- Use `search-docs` antes de alterações que dependam de comportamento ou sintaxe específica de pacotes do ecossistema Laravel.
- Use múltiplas palavras-chave amplas por tópico.

## Artisan & Tinker
- Comandos Artisan devem ser executados com `--no-interaction`.
- Listar rotas: `php artisan route:list`.
- Depuração em Tinker sempre com aspas simples para evitar expansão no shell:
  `php artisan tinker --execute 'User::count();'`

=== php rules ===

# Padrões de Código PHP

- **Tipagem Estrita**: Declare tipos de retorno e type-hints em todos os parâmetros de métodos e funções.
- **Estruturas de Controle**: Sempre use chaves `{}` em blocos `if`, `else`, `foreach`, mesmo que tenham apenas uma linha.
- **PHP 8 Constructor Promotion**: Use `public function __construct(public Service $service) { }`. Não deixe construtores vazios sem parâmetros a menos que sejam privados.
- **PHP Attributes**: Prefira atributos nativos em Models (`#[Fillable]`, `#[Hidden]`), Livewire (`#[Layout]`, `#[Title]`, `#[Locked]`, `#[Validate]`, `#[Computed]`, `#[Url]`).
- **Enums**: Utilize TitleCase para chaves de Enum (`Admin`, `Pending`, `Monthly`).
- **PHPDoc**: Utilize blocos PHPDoc quando definir tipos complexos (ex: array shapes `@var array<string>`). Evite comentários óbvios em linhas individuais.

=== livewire/core rules ===

# Padrões do Livewire

- Mantenha o estado no servidor. Autorize e valide em todas as ações públicas.
- **Navegação SPA**: Utilize `wire:navigate` em links internos (`<a>` e `<flux:sidebar.item>`) para carregamento instantâneo sem refresh de página.
- **Loops & Chaves**: Sempre inclua `wire:key` em elementos dentro de loops `@foreach` (ex: `wire:key="item-{{ $item->id }}"`).
- **Estados de Carregamento**: Adicione feedback visual em ações que realizam mutação (`wire:loading`, `wire:loading.attr="disabled"`).
- **Confirmação de Ações Destrutivas (Flux Modal Obrigatório)**:
  - **NUNCA** utilize `wire:confirm` ou diálogos nativos do navegador (`window.confirm`).
  - **SEMPRE** utilize o componente `<flux:modal>` controlado pelo estado do Livewire (ex: `$showDeleteModal`, métodos `confirmDelete($id)` e `deleteRecord()`).
  - Forneça feedback visual pós-ação utilizando `Flux::toast(variant: 'success', text: '...')`.
- **Reatividade com Debounce**: Em campos de busca, utilize debounce (`wire:model.live.debounce.300ms="search"`).

=== pint/core rules ===

# Formatação com Laravel Pint

- Após criar ou modificar arquivos PHP, você **DEVE** executar o formatador:
  ```bash
  vendor/bin/pint --format agent
  ```
- Não finalize alterações sem garantir que o código passou na validação do Pint.

=== pest/core rules ===

# Testes com Pest PHP

- Este projeto utiliza **Pest**. Crie novos testes com:
  ```bash
  php artisan make:test --pest {NomeDoTeste}
  ```
- Não inclua o diretório no `{nome}`. Use `UserManagementTest`, e não `Feature/UserManagementTest`.
- Execute a suíte de testes relevante com:
  ```bash
  php artisan test --compact
  # ou diretamente com o runner do Pest:
  vendor/bin/pest
  ```
- Toda correção de bug e nova funcionalidade de negócio deve incluir cobertura de teste cobrindo casos de sucesso e bloqueios de autorização.

=== workflow rules ===

# Comandos Úteis do Projeto

- Servidor de desenvolvimento: `composer run dev`
- Executar migrations e seeders: `php artisan migrate:fresh --seed`
- Executar testes automatizados: `php artisan test --compact`
- Formatar código com Pint: `vendor/bin/pint --format agent`
- Análise estática com Larastan: `vendor/bin/phpstan analyse`

</laravel-boost-guidelines>
