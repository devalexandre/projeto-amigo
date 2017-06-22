<?php
require_once '../vendor/autoload.php';

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Middleware\JwtAuthentication;
use Slim\Middleware\JwtAuthentication\RequestPathRule;
use Slim\Middleware\JwtAuthentication\RequestMethodRule;
use WebService\Configuration\Configuration;
use WebService\Model\Usuario;
// use WebService\Model\Produto;
// use WebService\Model\Mensagem;
use WebService\Model\Grupo;
use WebService\Util\Retorno;

//Configurações do Slim
$settings = [
  'settings' => [
    'displayErrorDetails' => true,
    'determineRouteBeforeAppMiddleware' => true,
    'addContentLengthHeader' => false
  ]
];

$app = new App($settings);

$container = $app->getContainer();

$container["jwt"] = function ($container) {
    return new StdClass;
};

/**
 * Validações de rota e token
 */
$app->add(function (Request $request, Response $response, callable $next) {
    $responseInterface = $next($request, $response);
    if ($request->getMethod() === "OPTIONS") {
        return $responseInterface
            ->withStatus(200)
            ->withJson(Retorno::sucesso('Ok.'));
    }
    // Rotas de acesso administrativo
    $arrRotasAdmin = [
        'usuario/retornarTodosUsuarios',
        'usuario/atualizarUsuario'
    ];

    // Se houver um token no cabeçalho, verificar o tempo de validade dele.
    if (!empty($request->getHeader('Authorization')[0])) {
        if (!empty((array)$this->jwt)) {
            if (strtotime('-12 hours') > $this->jwt->dataCriacao) {
                return $responseInterface
                ->withStatus(401)
                ->withJson(Retorno::outro('Token Expirado.'));
            }
        }
    }

    // Se o usuário não for Gerente e tentar acessar uma rota de gerencia,
    // ele receberá uma mensagem de erro.
    //nivel de acesso 1 basico, 2 - intermediario, 3 Admin
    if (in_array($request->getUri()->getPath(), $arrRotasAdmin)) {
        if ($this->jwt->intNivel != 3 ) {
            return $responseInterface
                ->withStatus(403)
                ->withJson(Retorno::erro('Acesso não autorizado'));
        }
    }

    return $responseInterface;
});

/**
 * Configurações de automação do JWT
 */
$app->add(new JwtAuthentication([
    "path" => ['/'],
    "secure" => false,
    "passthrough" => ['/usuario/login', '/usuario/registrarUsuario'],
    "secret" => Configuration::read("authentication")['jwtKey'],
    "algorithm" => Configuration::read("authentication")['algorithm'],
    "callback" => function ($request, $response, $arguments) use ($container) {
        $container["jwt"] = $arguments["decoded"];
    }
]));

/**
 * Cabeçalhos
 */
$app->add(function (Request $request, Response $response, callable $next) {
    $responseInterface = $next($request, $response);
    return $responseInterface
      ->withHeader('Content-Type', 'application/json')
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Agrupa as rotas referente a manipulação de usuarios
$app->group('/usuario', function () use ($app) {
    $objUsuario = new Usuario();

    $app->post('/login', function (Request $request, Response $response) use ($objUsuario) {
        $objRequest = $request->getParsedBody();
        $token = $objUsuario->login($objRequest['strLogin'], $objRequest['strSenha']);
        return $response->withJson($token);
    });

    // $app->post('/recuperarSenha', function (Request $request, Response $response) use ($objUsuario) {
    //     $objRequest = $request->getParsedBody();
    //     $token = $objUsuario->recuperarSenha($objRequest['strEmail']);
    //     return $response->withJson($token);
    // });

    $app->post('/alterarSenha', function (Request $request, Response $response) use ($objUsuario){
        $data = $request->getParsedBody();
        // $dadosUsuario = ValidateHeaders::getDataToken('dadosUsuario');
        $dados = $objUsuario->alterarSenha($this->jwt->intIdUsuario, $data);
        return $response->withJson($dados);
    });

    $app->post('/retornarTodosUsuarios', function (Request $request, Response $response) use ($objUsuario) {
        $dados = $objUsuario->retornarTodosUsuarios();
        return $response->withJson($dados);
    });

    $app->post('/registrarUsuario', function (Request $request, Response $response) use ($objUsuario){
        $data = $request->getParsedBody();
        $dados = $objUsuario->registrarUsuario($data);
        return $response->withJson($dados);
    });

    $app->post('/atualizarUsuario', function (Request $request, Response $response) use ($objUsuario){
        $data = $request->getParsedBody();
        $dados = $objUsuario->atualizarUsuario($data);
        return $response->withJson($dados);
    });
});

$app->group('/grupo', function () use ($app) {
    $objGrupo = new Grupo();
    $app->post('/criarGrupo', function (Request $request, Response $response) use ($objGrupo) {
        $data = $request->getParsedBody();
        $dados = $objGrupo->criarGrupo($data, $this->jwt);
        return $response->withJson($dados);
    });

    $app->post('/retornarTodosGrupos', function (Request $request, Response $response) use ($objGrupo) {
        $dados = $objGrupo->retornarTodosGrupos();
        return $response->withJson($dados);
    });

    $app->post('/retornarGrupos', function (Request $request, Response $response) use ($objGrupo) {
        $dados = $objGrupo->retornarGrupos($this->jwt);
        return $response->withJson($dados);
    });

    $app->post('/retornarGrupoId', function (Request $request, Response $response) use ($objGrupo) {
        $data = $request->getParsedBody();
        $dados = $objGrupo->retornarGrupoId($data, $this->jwt);
        return $response->withJson($dados);
    });

    $app->post('/atualizarGrupo', function (Request $request, Response $response) use ($objGrupo){
        $data = $request->getParsedBody();
        $dados = $objGrupo->atualizarGrupo($data);
        return $response->withJson($dados);
    });
    // $app->post('/tiposGrupo', function (Request $request, Response $response) use ($objGrupo) {
    //     $data = $request->getParsedBody();
    //     $dados = $objGrupo->tiposGrupo($data);
    //     return $response->withJson($dados);
    // });
    //
    $app->post('/entrarGrupo', function (Request $request, Response $response) use ($objGrupo) {
        $data = $request->getParsedBody();
        $dados = $objGrupo->entrarGrupo($data, $this->jwt);
        return $response->withJson($dados);
    });
    //
    // $app->post('/sairGrupo', function (Request $request, Response $response) use ($objGrupo) {
    //     $data = $request->getParsedBody();
    //     $dados = $objGrupo->entrarGrupo($data, $this->jwt);
    //     return $response->withJson($dados);
    // });
    //
    $app->post('/enviarConvite', function (Request $request, Response $response) use ($objGrupo) {
        $data = $request->getParsedBody();
        $dados = $objGrupo->enviarConvite($data, $this->jwt);
        return $response->withJson($dados);
    });

    $app->post('/sortearGrupo', function (Request $request, Response $response) use ($objGrupo) {
        $data = $request->getParsedBody();
        $dados = $objGrupo->sortearGrupo($data, $this->jwt);
        return $response->withJson($dados);
    });

    $app->post('/resultado', function (Request $request, Response $response) use ($objGrupo) {
        $data = $request->getParsedBody();
        $dados = $objGrupo->resultado($data);
        return $response->withJson($dados);
    });
});
//
// $app->group('/produtos', function () use ($app) {
//     $objGrupo = new Produto();
//     $app->post('/cadastrarProduto', function (Request $request, Response $response) use ($objGrupo) {
//         $data = $request->getParsedBody();
//         $dados = $objGrupo->cadastrarProduto($data);
//         return $response->withJson($dados);
//     });
//
//     $app->post('/editarProduto', function (Request $request, Response $response) use ($objGrupo) {
//         $data = $request->getParsedBody();
//         $dados = $objGrupo->entrarGreditarProdutoupo($data);
//         return $response->withJson($dados);
//     });
//
//     $app->post('/listaDesejos', function (Request $request, Response $response) use ($objGrupo) {
//         $data = $request->getParsedBody();
//         $dados = $objGrupo->listaDesejos($data);
//         return $response->withJson($dados);
//     });
//
//     $app->post('/comprar', function (Request $request, Response $response) use ($objGrupo) {
//         $data = $request->getParsedBody();
//         $dados = $objGrupo->comprar($data);
//         return $response->withJson($dados);
//     });
//
// });
//
// $app->group('/mensagem', function () use ($app) {
//     $objGrupo = new Mensagem();
//     $app->post('/enviar', function (Request $request, Response $response) use ($objGrupo) {
//         $data = $request->getParsedBody();
//         $dados = $objGrupo->enviar($data);
//         return $response->withJson($dados);
//     });

// });


$app->run();
