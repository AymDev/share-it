<?php

/**
 * This is the main configuration file
 */

use DI\Bridge\Slim\Bridge;
use DI\Container;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Error handling
// $whoops = (new Run())
//     ->pushHandler(new PrettyPageHandler())
//     ->register();

$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

// Application & Service Container
$app = Bridge::create();
/** @var Container $container */
$container = $app->getContainer();

// Routing
$container->set(RouteParserInterface::class, function () use ($app) {
    return $app->getRouteCollector()->getRouteParser();
});

// PSR-7 response factories
$container->set(
    ResponseFactoryInterface::class, function () {
    return new ResponseFactory();
});

$container->set(StreamFactoryInterface::class, function () {
    return new StreamFactory();
});

// Templating
$container->set(Environment::class, function (ContainerInterface $container) {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    $twig = new Environment($loader, [
        'debug' => true,
    ]);

    // Add a Twig function to generate links
    $linkFunction = new TwigFunction('link', [$container->get(RouteParserInterface::class), 'urlFor']);
    $twig->addFunction($linkFunction);

    return $twig;
});

// Database
$container->set(Connection::class, function () {
    $connectionParams = include __DIR__ . '/doctrine.php';
    return DriverManager::getConnection($connectionParams['connection']);
});