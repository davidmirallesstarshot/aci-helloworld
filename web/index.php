<?php

namespace Dragonbe;

use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../templates',
]);

$app->register(new SessionServiceProvider(), [
    'session.storage.options' => [
        'name' => 'ACISESID',
        'cookie_lifetime' => 3600,
        'cookie_path' => '/',
        'cookie_httponly' => true,
    ],
]);

$app->register(new MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../data/logs/development.log',
]);

$pdo = new \PDO('sqlite:' . __DIR__ . '/../data/db/demo.db');
$app['pdo'] = $pdo;

$app->get('/', function () use ($app) {
    return $app['twig']->render('home.twig', [

    ]);
});

$app->get('/books', function () use ($app) {

    $bStmt = $app['pdo']->query('SELECT b.title, b.abstract, b.isbn, b.image, GROUP_CONCAT(a.name) authors FROM book b JOIN author_book ab ON b.id = ab.book_id LEFT JOIN author a ON ab.author_id = a.id GROUP BY b.id ORDER BY b.title');
    $books = $bStmt->fetchAll();

    return $app['twig']->render('books.twig', [
        'books' => $books,
    ]);
});

$app->run();