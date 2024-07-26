# VoxApiTest


# Contexto
Este projeto trata-se de uma simples API Restful onde o cliente pode acessar quadros societários de algumas empresas registradas.
Caso o usuário tenha um papel de administrador do sistema ele poderá criar, atualizar e excluir empresas e sócios, e se for apenas
um usuário comum terá acesso apenas a listagem e detalhes de empresas e sócios.


## Desenvolvimento 

> Backend
```bash
Feito em PHP com o framework Symfony e o ORM Doctrine com Postgres. 

```
> Banco de dados
```bash
Feito com o banco de dados relacional Postgres.O banco contará com as tabelas de companies, partners, addresses, 
user. 
``` 

## Instalando Dependências

> Backend
```bash
Você precisará ter PHP 8.1 instalado e também o Symfony 6.4 (atual versão LTS). 
Após clonar este repositório em seu diretório local, acesse a pasta onde foi clonado, 
então rode o comando composer install para instalar todas as dependências. Crie um arquivo
.env contendo suas credencias para acesso ao banco de dados e servidor. Um Exemplo do que
precisará em seu arquivo de variáveis de ambiente:

`
APP_ENV=dev
APP_SECRET=51ebc5fd4d7575b306b88724b912810a

DATABASE_URL="pgsql://postgres:password@localhost:5432/api-vox"

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=49c7c2b4f867b95401ce4d4e8d2cc4d7af00b6de8ffdd28c4cc7f5060b7f2ceb

CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
`

Também precisará das chaves para configurar o serviço de autenticação com JWT. 
Rode o comando 'symfone console lexik:jwt:generate-keypair'.

``` 

> Banco de dados
```bash
Após instalar back-end você irá configurar seu banco de dados Postgres, Caso já
tenha o serviço rodando use a url de conexão para add ao arquivo .env, caso não
tenha instalado poderá rodar também via container Docker.
Ex: 'docker run --name my-postgres -e POSTGRES_PASSWORD=mysecretpassword -d postgres'.
``` 
## Executando aplicação

* Para rodar o servidor:

  ```
  Acesse a pasta raíz do projeto e rode `symfony server:start`.
  ```
* Para implementar as tabelas do banco de dados:

  ```
  Acesse a pasta raíz do projeto e rode `symfony console doctrine:migrations:migrate` 
  para rodar todas as migrations do banco e também para cadastrar dois usuários para 
  seus testes rode `symfony console doctrine:fixtures:load`.
  
  
 ### DOCUMENTAÇÃO API REST:

    `Obs: existe um arquivo JSON no projeto chamado 'requestsApi' use-o para configurar seu ambiente de testes com
    clients como Insomnia.`
    

### Acesso: 
 
 * Rota para login (POST - /login). Campos email e password obrigatórios. 
 Exemplo:
 
 <img src="./images/login.png">



 *Obs: existem dois usuários disponíveis para testes, com roles diferentes: 'user@meuemail.com, userpassword' e 'admin@meuemail.com, admin password'. Como dito acima, caso tente acessar alguma rota que não tem permissão receberá
 um erro. Também precisará passar o token recebido ao logar no Authorization em todas as requests, como na 
 imagem abaixo:
 
  <img src="./images/authtoken.png">
 
 
  ### Empresas: 

 * Rota de registro de empresa (POST - /company/create). Campos obrigatórios. 
 
 Exemplo:
 
 <img src="./images/companyCreate.png">


 
   * Rota de listagem de empresas com seus respectivos endereços e sócios:
    (GET - /companies).
    
    Exemplo:
 
  <img src="./images/companiesList.png">


 
 
  * Rota para retornar apenas uma empresa em detalhe (GET - /company/show/:id). 
  
  Exemplo:
  
  <img src="./images/showCompany.png">


 
  
 * Rota para atualizar empresa e por 'id' (PUT - /company/edit/:id). 
  Exemplo:
 
  <img src="./images/editCompany.png">


 
 
 * Rota deleta empresa por 'id' (DELETE - /company/delete/:id).
  Exemplo:
 
  <img src="./images/deleteCompany.png">


 
 
  ### Sócios: 

  * Rota adciona novo sócio (POST - /partner/create). Campos obrigatórios.
   Exemplo:
 
  <img src="./images/createPartner.png">


 
 
 * Rota atualiza sócio por id (PUT - /partner/edit/:id). 
    Exemplo:
 
  <img src="./images/updatePartner.png">


 
 
  * Rota lista sócios (GET - /partners).
   Exemplo:
 
  <img src="./images/partnersList.png">


 
 
  * Rota lista todos os sócios e suas respectivas empresas (GET - /partner/show/:id).
   Exemplo:
 
  <img src="./images/partnerShow.png">




 * Rota para deleção de um sócios (DELETE - /partner/update/:id). 
  Exemplo:
 
  <img src="./images/deletePartner.png">

  
  
  
