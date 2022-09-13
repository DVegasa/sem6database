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
    id          int   not null auto_increment,
    id_supplier int   not null,
    id_mineral  int   not null,
    date        date  not null default(CURRENT_DATE),
    amount      float not null,

    primary key (id),
    foreign key (id_supplier) references supplier (id),
    foreign key (id_mineral) references mineral (id)
);
SQL;


    public const SQL_FAKE_DATA = <<<SQL
INSERT INTO mineral (name) VALUES ('Уголь');
INSERT INTO mineral (name) VALUES ('Медь');
INSERT INTO mineral (name) VALUES ('Железо');
INSERT INTO mineral (name) VALUES ('Бронза');
INSERT INTO mineral (name) VALUES ('Алмаз');
INSERT INTO mineral (name) VALUES ('Алюминий');
INSERT INTO mineral (name) VALUES ('Серебро');
INSERT INTO mineral (name) VALUES ('Золото');
INSERT INTO mineral (name) VALUES ('Карнеол');
INSERT INTO mineral (name) VALUES ('Лазурит');

INSERT INTO db.supplier (name, country, city) VALUES ('Волгоградский минеральный завод', 'RUS', 'Волгоград');
INSERT INTO db.supplier (name, country, city) VALUES ('Уголь России', 'RUS', null);
INSERT INTO db.supplier (name, country, city) VALUES ('Уголь Белоруссии', 'BLR', null);
INSERT INTO db.supplier (name, country, city) VALUES ('Бронзовые слитки', 'RUS', null);
INSERT INTO db.supplier (name, country, city) VALUES ('Медалепроизодственный цех №3', 'RUS', 'Москва');
INSERT INTO db.supplier (name, country, city) VALUES ('ПЛК ВБ-СПБ', 'RUS', 'Санкт-Петербург');
INSERT INTO db.supplier (name, country, city) VALUES ('ПЛК ВБ-А', 'RUS', 'Астрахань');
INSERT INTO db.supplier (name, country, city) VALUES ('ПЛК ВБ-ВЛЖ', 'RUS', 'Волжский');
INSERT INTO db.supplier (name, country, city) VALUES ('Горнодобывающая компания им. Александрова', 'RUS', null);
INSERT INTO db.supplier (name, country, city) VALUES ('ПЛК ВБ-ГЛОБАЛ ИНК', 'GER', null);

INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Волгоград - Центр', 1, 'Волгоград', 1);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Волгоград - Юг', 10, 'Волгоград', 1);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Волгоград - Север', 1, 'Волгоград', 2);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Минск - Запад', 1, 'Минск', 3);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Минск - Центр', 1, 'Минск', 3);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Астрахань А', 5, 'Астрахань', 7);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Астрахань Б', 7, 'Астрахань', 7);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Астрахань В', 9, 'Астрахань', 7);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Курск - Центр', 4, 'Курск', 9);
INSERT INTO db.shaft (name, id_mineral, city, id_supplier) VALUES ('Курск - Юг', 8, 'Курск', 5);

INSERT INTO db.report (id_shaft, year, amount) VALUES  (1, 2022, 14030);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (2, 2022, 630);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (3, 2022, 14699);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (4, 2022, 24699);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (5, 2022, 7699);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (6, 2022, 14699);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (7, 2022, 147599);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (8, 2022, 14849);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (9, 2022, 14499);
INSERT INTO db.report (id_shaft, year, amount) VALUES (10, 2022, 314699);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (1, 2021, 1440);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (2, 2021, 3305);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (3, 2021, 34699);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (4, 2021, 3099);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (5, 2021, 3719);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (6, 2021, 346299);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (7, 2021, 34799);
INSERT INTO db.report (id_shaft, year, amount) VALUES  (8, 2021, 3409);

INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (1, 1, '2022-05-10', 1000);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (2, 4, '2022-05-20', 444);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (2, 3, '2022-08-17', 700);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (1, 1, '2022-05-04', 1000);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (3, 2, '2022-05-10', 60);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (5, 6, '2022-05-05', 34);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (1, 1, '2022-05-10', 222);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (3, 8, '2022-05-10', 1000);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (1, 2, '2022-05-10', 50600);
INSERT INTO db.shipment (id_supplier, id_mineral, date, amount) VALUES (1, 1, '2022-05-10', 53555);

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

        // t - имя таблицы
        // a - действие ('c', 'r', 'u', 'd')
        // v - ассоциативный массив поле => значение
        // p - админский пароль
        // s - сырая SQL команда

        $app = AppFactory::create();

        $app->any('/ping', function(Request $req, Response $r) {
            return $this->response($r, [
                    'get' => $this->getGetParams($req),
                    'post' => $this->getPostParams($req),
            ]);
        });


        $app->post('/initdb', function(Request $req, Response $res) use ($pdo) {
            $params = $this->getPostParams($req);
            if ($params['p'] !== $_ENV['ADMIN_PASSWORD']) return $this->response($res, [], 403);
            $pdo->exec(self::SQL_INIT);
            return $this->response($res, [], 200);
        });

        $app->post('/fakedata', function(Request $req, Response $res) use ($pdo) {
            $params = $this->getPostParams($req);
            if ($params['p'] !== $_ENV['ADMIN_PASSWORD']) return $this->response($res, [], 403);
            $pdo->exec(self::SQL_FAKE_DATA);
            return $this->response($res, [], 200);
        });

        $app->post('/exec', function ($req, $res) use ($pdo) {
            $params = $this->getPostParams($req);
            if ($params['p'] !== $_ENV['ADMIN_PASSWORD']) return $this->response($res, [], 403);
            $result = $pdo->exec($params['s']);
            return $this->response($res, ['result' => $result], 200);
        });

        $app->post('/query', function ($req, $res) use ($pdo) {
            $params = $this->getPostParams($req);
            if ($params['p'] !== $_ENV['ADMIN_PASSWORD']) return $this->response($res, [], 403);

            $result = [];
            foreach ($pdo->query($params['s']) as $row) {
                $result[] = $row;
            }
            return $this->response($res, ['result' => $result], 200);
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
