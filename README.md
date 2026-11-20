# NOXVAULT — Sistema Web de Loja de Jogos

## Integrantes

- Júlio César Rabelo Silva

## Tema e descrição

O NOXVAULT é uma loja virtual de jogos com catálogo público, cadastro de clientes, carrinho, pedidos e gerenciamento administrativo. Os produtos e informações comerciais são carregados do banco de dados. A aplicação controla disponibilidade e estoque, preserva os preços históricos dos pedidos e separa as permissões de clientes e administradores.

As compras são demonstrativas: o sistema registra pedidos, sem processar pagamentos ou entregar licenças digitais.

A descrição completa e as regras implementadas estão em [Descrição e regras de negócio](Documentacao/Descricao_e_Regras_de_Negocio.md).

## Tecnologias utilizadas

| Tecnologia | Aplicação |
| --- | --- |
| PHP 8 | Requisições, autenticação, validações e regras de negócio. |
| MySQL/MariaDB | Persistência relacional, integridade, índices e transações. |
| PDO | Conexão e consultas parametrizadas. |
| POO | Classes, herança e organização das responsabilidades. |
| MVC | Models para dados e domínio, Controllers para requisições e Views para apresentação. |
| HTML | Estrutura das páginas e formulários. |
| CSS | Identidade visual e responsividade. |
| JavaScript | Validação, menu, confirmações, quantidade e prévia de imagens. |

O ambiente verificado utiliza **PHP 8.2.12 e MariaDB 10.4.32 do XAMPP**. O código requer PHP 8.1 ou superior. O funcionamento da aplicação não depende de Node.js, frameworks JavaScript ou serviços externos.

## Diagrama de classes

![Diagrama de classes UML do NOXVAULT](Documentacao/Diagrama_de_Classes.svg)

[Abrir o diagrama em tamanho completo](Documentacao/Diagrama_de_Classes.svg). O modelo apresenta as classes reais, suas propriedades, métodos, herança e relacionamentos. As entidades persistentes manipulam arrays; as colunas do banco são detalhadas no modelo relacional.

## Modelo do banco de dados

![Modelo relacional do banco NOXVAULT](Documentacao/Modelo_do_Banco_de_Dados.svg)

[Abrir o modelo em tamanho completo](Documentacao/Modelo_do_Banco_de_Dados.svg). O script SQL permanece em [`banco/noxvault.sql`](banco/noxvault.sql).

## Instalação e execução no XAMPP

1. Instale o XAMPP com PHP 8.1 ou superior. No painel de controle, inicie **Apache** e **MySQL**. O serviço chamado MySQL no XAMPP pode utilizar MariaDB.
2. Copie a pasta do projeto para `C:\xampp\htdocs\crud2022`. Caso utilize outro diretório de instalação do XAMPP, ajuste esse caminho.
3. Acesse [phpMyAdmin](http://localhost/phpmyadmin/). Na opção **Importar**, selecione o arquivo existente `banco/noxvault.sql` e execute a importação. O script cria e seleciona o banco `noxvault`, define as tabelas e inclui os dados iniciais de catálogo.
4. Confira a conexão em [`admin/config/configuracao.php`](admin/config/configuracao.php). Os valores padrão locais são host `127.0.0.1`, porta `3306`, banco `noxvault`, usuário `root` e senha vazia. Ajuste os valores se o seu MySQL utilizar outra configuração. As mesmas opções podem ser fornecidas pelas variáveis `NOX_DB_HOST`, `NOX_DB_PORT`, `NOX_DB_NAME`, `NOX_DB_USER` e `NOX_DB_PASSWORD`. O nome normal do banco deve permanecer `noxvault`.
5. Abra o PowerShell na raiz do projeto e execute o inicializador abaixo. Ele cria as duas contas de demonstração em uma instalação sem usuários e mostra as respectivas senhas no terminal.

```powershell
Set-Location 'C:\xampp\htdocs\crud2022'
& 'C:\xampp\php\php.exe' '.\banco\inicializar.php'
```

6. **Anote as duas senhas exibidas.** Importar somente o SQL não cria as contas: essa etapa é realizada pelo inicializador PHP. Se já houver usuários no banco, o inicializador preserva as contas e suas senhas; não as reexibe nem redefine.
7. Mantenha as pastas `admin/public/imagens/envios`, `admin/public/imagens/perfis` e `armazenamento` com permissão de gravação para o PHP. No XAMPP padrão, PHP deve ter as extensões PDO MySQL, Fileinfo e Mbstring habilitadas.
8. Acesse a [página inicial](http://localhost/crud2022/inicio). O Apache deve permitir a leitura do `.htaccess` e ter o módulo de reescrita ativo para esse endereço amigável. O catálogo também pode ser acessado diretamente pelo link abaixo.

| Tela | Endereço local |
| --- | --- |
| Início | <http://localhost/crud2022/inicio> |
| Catálogo público | <http://localhost/crud2022/index.php?r=catalogo> |
| Login | <http://localhost/crud2022/login.php> |
| Cadastro de cliente | <http://localhost/crud2022/criar_conta.php> |
| Painel administrativo | <http://localhost/crud2022/index.php?r=admin> |

O login é compartilhado pelos dois perfis. O cliente entra no catálogo; o administrador entra no painel. Os caminhos internos são calculados a partir da pasta do projeto; se renomear `crud2022`, ajuste os endereços apresentados nesta documentação. Não é necessário alterar os arquivos do banco antigo do GameStock.

## Usuários e senhas de teste

As contas abaixo são criadas pelo inicializador em uma instalação nova:

| Nome | E-mail | Perfil | Senha de demonstração |
| --- | --- | --- | --- |
| admin | `admin@gmail.com` | Administrador | Gerada e exibida no terminal por `banco/inicializar.php`. |
| Julio | `julio@gmail.com` | Cliente | Gerada e exibida no terminal por `banco/inicializar.php`. |

As senhas geradas são destinadas à demonstração local, independentes de serviços externos. Não há senha única gravada neste README: utilize os valores emitidos na sua instalação. No banco, as senhas são armazenadas com `password_hash()` e verificadas com `password_verify()`. Instalações existentes continuam utilizando as senhas que seus responsáveis definiram.

## Funcionalidades implementadas

- Página inicial pública com jogos carregados do banco e catálogo dinâmico.
- Cadastro de clientes, login, logout, controle de sessão e permissões por perfil.
- CRUDs de **Usuario, Game, Categoria, Fornecedor e Pedido**, com exclusão lógica e regras de integridade.
- Consulta individual de jogos e gerenciamento de dados pessoais, senha e foto de perfil.
- Pesquisa por título, filtro por categoria e preço, ordenação e paginação.
- Carrinho exclusivo de clientes, com inclusão, remoção, quantidades, subtotais e total.
- Pedidos persistidos com status, itens e preços históricos; consulta dos próprios pedidos pelo cliente.
- Controle de estoque transacional e verificação de concorrência; cancelamento com reposição controlada.
- Upload validado de capas e fotos de perfil, com prévia no navegador.
- Dashboard com indicadores reais, vendas de pedidos concluídos, baixo estoque e pedidos recentes.
- Validações JavaScript, menu móvel, confirmação de operações e mensagens de resultado.

As oito funcionalidades extras são pesquisa, filtro por categoria, upload, carrinho, estoque, dashboard, detalhes dos jogos e validações JavaScript.

## Prints das principais telas

As capturas abaixo são obtidas da aplicação em execução local, com contas de demonstração e banco de teste separado. Os produtos e suas imagens correspondem aos dados locais do projeto. As operações demonstradas não alteram os pedidos ou o estoque do banco de desenvolvimento.

### Página inicial

![Página inicial pública do NOXVAULT](Documentacao/Capturas/Inicio.png)

### Catálogo

![Catálogo com pesquisa e filtros](Documentacao/Capturas/Catalogo.png)

### Detalhes de um jogo

![Página de detalhes de um jogo](Documentacao/Capturas/Detalhes_do_Jogo.png)

### Login

![Formulário de login](Documentacao/Capturas/Login.png)

### Carrinho

![Carrinho de um cliente autenticado](Documentacao/Capturas/Carrinho.png)

### Dashboard administrativo

![Dashboard administrativo com indicadores do banco](Documentacao/Capturas/Painel_Administrativo.png)

### Gerenciamento de jogos

![Tela representativa do CRUD de jogos](Documentacao/Capturas/Gerenciamento_de_Jogos.png)

## Repositório GitHub

(https://github.com/juliocesarrsc/NOXVAULT/tree/main)
