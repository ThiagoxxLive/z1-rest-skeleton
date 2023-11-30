### Z1Tec - Rest Skeleton

#### Ferramenta para desenvolvimento de APIs da Z1Tec.

O intuito desta ferramenta é desenvolver APIs REST de forma rápida e enxuta.

A única dependência externa desta ferramenta, é o container de injeção de dependências do Symfony.

Esta ferramenta foi desenvolvida pensando em uso em servidores compartilhados (shared hostings).

## Instalação


1 - Para instalar, basta fazer o clone do repositório e rodar o comando:
`composer install`.

2 - Configurar as variáveis de ambiente no arquivo .env.dist.

3 - Setar o nome da base de dados no arquivo **Config/config.yml**

Após instalação, basta colocar na raiz do servidor, o arquivo **.htaccess** já está configurado.

Caso queira utilizar **Docker**, a ferramenta já possui o **docker-compose.yml**, basta rodar o comando:
`docker-compose up -d`

E já irá subir uma instância da aplicação, juntamente com o banco MySQL. Caso necessite utilizar outro banco, basta ajustar o arquivo **docker-compose.yml**.

# Utilização


### Rotas

Para fazer uma rota, basta criar o Controller, Service e Repository da rota. Existe também um utilitario `make.php` na raiz da aplicação que facilita a criação dos mesmos acima citados.

Exemplo de uso do Make:
`php make.php api_endpoint Namespace NomeDoServico`

Automaticamente irá criar as classes no devido namespace.

Caso queira criar manualmente, basta criar o Controller, Service e Repository nas devidas pastas da aplicação.

### Injeção de Dependências

Está sendo utilizado o container de injeção de dependências do **Symfony**.

Após criado os serviços da rota, é necessário registrar o Service e o Repository no container de injeção de dependências, que fica em `Config/services.yml`

Basta fazer algo como:

    ##Services
    App\Services\Example\ExampleService:
        class: App\Services\Example\ExampleService
        public: true
        arguments:            
            - '@ApiConnection'
            
    ##Repositories
    App\Repositories\Example\ExampleRepository:
        class: App\Repositories\Example\ExampleRepository
        public: true
        arguments:            
            - '@ApiConnection'

    ##Connections
    ApiConnection:
        class: App\Db\Connections\Connection
        public: true
        arguments:            
            - '%db_api%'

E assim por diante, conforme a necessidade da aplicação.

Após a configuração dos arquivos da rota e do container de injeção de dependências, já é possível criar as rotas.

Para criar as rotas, é necessário ir no arquivo `Http/Routes/Routes.php` e registrar a rota e o tipo.

**Exemplo de Rota GET**


```php

use App\Controllers\Example\ExampleController;

/*
    Restante do código
*/

$router->get('/v1/example', [
    function ($request) {
        return new Response(ExampleController::getAction($request), Response::OK);
    }
]);

```

O retorno será no status HTTP 200(OK) caso OK, e irá retornar os dados selecionados na query do Repository.

**Exemplo de Rota POST**

```php

use App\Controllers\Example\ExampleController;

/*
    Restante do código
*/


use App\Controllers\Example\ExampleController;

/*
    Restante do código
*/

$router->post('/v1/example', [
    function ($request) {
        return new Response(ExampleController::postAction($request), Response::CREATED);
    }
]);


```
Para a rota post, o conteúdo da requisição fica no objeto Request, no seu service, para receber a requisição basta transformar em um array antes de enviar para o banco.

O retorno deverá ser, caso positivo, status 201 (CREATED) e os dados da requisição, mais o ultimo ID da tabela no banco.

**Exemplo Rota PUT**

```php
$router->put('/v1/example/{id}', [
    function ($id, $request) {
        return new Response(ExampleController::putAction($id, $request), Response::NO_CONTENT);
    }
]);

```

A rota PUT tem o comportamento semelhante do POST, porém, é necessário informar o ID na URL da rota.

Caso sucesso, deve retornar o status 204 (NO CONTENT).

**As rotas do tipo DELETE e PATCH ainda não foram implementadas.**


**Middlewares**

Para a utilização de middlewares, basta criar o array antes da função de callback do roteador, exemplo:

```php
$router->get('/v1/example', [
    ['middlewares ' => 'nome-do-middleware']
    function ($request) {
        return new Response(ExampleController::getAction($request), Response::OK);
    }
]);

```

Para efetuar o registro dos middlewares, no arquivo routes, existe uma classe chamada **MiddlewareQueue**, basta invocar o método setMap, vide exemplo abaixo:

```php
MiddlewareQueue::setMap([
    'seu-middleware' => \App\Http\Middleware\SeuMiddleWare::class
]);

```

Ou seja, qualquer coisa que seja necessária antes de chamar o controller em si, deve ser registrada como um middleware.
Exemplo: utilização de JWT.

**Utilitários**
A ferramenta possui alguns utilitários, por exemplo o validador de campos de formulários.

Exemplo de utilização:

```php
public static function postAction(Request $request) : array {
    //Campos a serem validados.
    $requiredKeys = ['name', 'id_tenant'];
    
    if(!$request->validate($request, $requiredKeys)) {
        throw new HttpMissingKeyException(Form::getMessage(), 400);
    }
    
    return self::getService()->create($request);
}
```

o array `$requiredKeys` é onde mapeamos os campos obrigatórios. 
O método validate, no objeto request é responsável por fazer a comparação dos dados.

Também, é possível validar alguns tipos de dados, vide exemplo:

**SeuService.php**
```php
public function create(array $data) : array {

    if(!FormValidator::validateLength($data['name'], 3)) {
        throw new InvalidNameException('O nome deve conter pelo menos 3 caracteres.', 400);
    }

    if(!FormValidator::validateEmail($data['email'])) {
        throw new InvalidEmailException('O email informado é inválido.', 400);
    }

    if (!FormValidator::validatePhone($data['phone'])) {
        throw new InvalidPhoneException('O telefone informado é inválido.', 400);
    } 

    $id = $this->getRepository()->create($data);

    return array_merge(
        ["id" => $id], $data, ["status" => "ok"]
    );

}
```

No exemplo acima, fazemos a validação do tamanho do campo 'name', email e phone.

Todos os validadores estão na classe **FormValidator**, caso necessite de algum customizado, basta implementar como método estatico e utilizar.


**Por fim, basta desenvolver seus serviços. :)**
