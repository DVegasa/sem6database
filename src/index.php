<?php

use Dotenv\Dotenv;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once dirname(__DIR__) . '/vendor/autoload.php';
(new Entrypoint())->main();


class Entrypoint {

    public const SQL_INIT = <<<SQL
create table if not exists mineral
(
    id   int          not null auto_increment,
    name varchar(255) not null,

    primary key (id)
);


create table if not exists supplier
(
    id      int          not null auto_increment,
    name    varchar(255) not null,
    country varchar(3)   not null default 'RUS',
    city    varchar(255),

    primary key (id)
);


create table if not exists shaft
(
    id          int          not null auto_increment,
    name        varchar(255) not null,
    id_mineral  int          not null,
    city        varchar(255),
    id_supplier int          not null,

    primary key (id),
    foreign key (id_mineral) references mineral (id),
    foreign key (id_supplier) references supplier (id)
);


create table if not exists report
(
    id       int   not null auto_increment,
    id_shaft int   not null,
    year     int   not null,
    amount   float not null,

    primary key (id),
    foreign key (id_shaft) references shaft (id)
);


create table if not exists shipment
(
    id          int  not null auto_increment,
    id_supplier int  not null,
    id_mineral  int  not null,
    date        date not null default(CURRENT_DATE),

    primary key (id),
    foreign key (id_supplier) references supplier (id),
    foreign key (id_mineral) references mineral (id)
);
SQL;

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

        $app->any('/ping', function(Request $req, Response $r) {
            return $this->response($r, [
                    'get' => $this->getGetParams($req),
                    'post' => $this->getPostParams($req),
            ]);
        });

        $app->post('/initdb', function(Request $req, Response $r) use ($pdo) {
            $params = $this->getPostParams($req);
            if ($params['p'] !== $_ENV['ADMIN_PASSWORD']) return $this->response($r, [], 403);
            $pdo->exec(self::SQL_INIT);
            return $this->response($r, [], 200);
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
