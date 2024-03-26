
# Francês Streaming

O objetivo do projeto é criar uma plataforma streaming para cursos online, com o intuito de melhorar a experiência do aluno. A estrutura contará com seções e categorias em todos os conteúdos, inspirada no produto Netflix.

<br/>

[Escopo do projeto](https://docs.google.com/document/d/1jM0_uHC7XWwhPbGz7ISUhhyM-ZTb75Vv/edit)

[Protótipo Figma](https://www.figma.com/file/4dWoyDrzOW1VabW1Xhj8ah/Streaming-FCL-(Oficial)?node-id=276%3A11298&t=cSe0yoRW9qGRUqat-0)

## Instalação

### Clonando e instalando dependências

Clone o repositório

    // SSH
    git clone git@bitbucket.org:agilytecnologia/frances_streaming.git

    // HTTPS (Substitua {USER} pelo usuário autorizado)
    git clone https://{USER}@bitbucket.org/agilytecnologia/frances_streaming.git

Navegue até a pasta do projeto:

    cd .\frances_streaming

Faça a instalação das dependencias PHP com o comando:

    composer install

Para as dependencias JavaScript, utilize:

    npm install

### Comandos para execução e construção

Copie o arquivo env de exemplo e faça as alterações de configuração necessárias no arquivo .env

    cp .env.example .env

Gerar uma nova chave de aplicativo

    php artisan key:generate

Para criar o link simbólico (para arquivos que serão acessíveis publicamente), use o comando

    php artisan storage:link

Para criação do banco de dados

    php artisan migrate

Inicie o servidor de desenvolvimento local

    php artisan serve

Construa e execute o projeto com o comando

    npm run build 
    // or
    npm run dev

Agora você pode acessar o servidor em localhost

## Visão geral do código

### Dependências

- [Laravel](https://laravel.com/)
- [VueJs](https://vuejs.org/)
- [Quasar](https://quasar.dev/)
- [InertiaJs](https://inertiajs.com/)

### Pastas

- `app/Http/Exports` - Classe para exportação (Excel)
- `app/Helpers` - Classes de utilitários
- `app/Http/Controllers/Admin` - Controladores do o ambiente administrativo
- `app/Http/Controllers/Requests` - Classes para validação e autorização de requisições
- `app/Http/Controllers/Resources` - Classes para estruturação e formatação de dados para serem retornados em uma requisição
- `app/Http/Models` - Contém modelos Eloquent
- `app/Services` - Classes de serviços para CRUD (Create, Read, Update, Delete), exclusão em massa, integrações, envio de notificações
- `config` - Arquivos de configuração do projeto
- `resources/view` - Views (.blade) para implementação de HTML e dependecias JS e arquivos para envio de e-mail ou download
- `resources/sass` - Arquivos de estilização
- `resources/js/Components` - Componentes em VueJs
- `resources/js/Pages` - Páginas construídas com VueJs
- `resources/js/Layouts` - Layouts construídos com VueJs
- `routes/admin` - Rotas para o ambiente administrativo
- `routes/customer` - Rotas para o ambiente do cliente/aluno

### Variáveis de ambiente

- `.env` - As variáveis ​​de ambiente podem ser definidas neste arquivo.
