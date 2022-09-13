<?php

use Dotenv\Dotenv;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once dirname(__DIR__) . '/vendor/autoload.php';
(new Entrypoint())->main();

class Entrypoint {
    function main(): void {
        Dotenv::createImmutable(dirname(__DIR__))->load();
        $pdo = new PDO(
                dsn: "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}",
                username: $_ENV['DB_USERNAME'],
                password: $_ENV['DB_PASSWORD'],
                options: array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                )
        );

        // Server

        $app = AppFactory::create();
        $app->add(array($this, 'authenticate'));

        $app->any('/ping', function(Request $req, Response $r) {
            return $this->response($r, [
                    'get' => $this->getGetParams($req),
                    'post' => $this->getPostParams($req),
            ]);
        });

        $app->post('/admin/initDatabase', function(Request $req, Response $r) {
            return $this->response($r, ['status' => 'admin']);
        });

        $app->run();
    }

    function response(Response $r, $body = array(), $code = 200): Response {
        $r->getBody()->write(json_encode($body));
        $r = $r->withHeader('Content-Type', 'application/json');
        $r = $r->withStatus($code);
        return $r;
    }

    function getPostParams(Request $req): ?array {
        return json_decode($req->getBody(), true);
    }

    function getGetParams(Request $req): ?array {
        return $req->getQueryParams();
    }

}
