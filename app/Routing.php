<?php
declare(strict_types=1);

namespace App;

use App\Controllers\ArticlesController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Carbon\Carbon;
use function FastRoute\simpleDispatcher;

class Routing
{
    public static function dispatch(): void
    {
        $dbService = new DatabaseService();
        $twig = $dbService->getTwig();

        $currentTime = Carbon::now('Europe/Riga')->format('Y-m-d | H:i');
        $twig->addGlobal('globalTime', $currentTime);

        $controller = new ArticlesController($dbService, new ArticlesView($twig));

        $dispatcher = simpleDispatcher(function (RouteCollector $r) use ($controller) {
            $r->addRoute('GET', '/', [$controller, 'index']);
            $r->addRoute('POST', '/insert', [$controller, 'insertArticle']);
            $r->addRoute('GET', '/article/{id:\d+}', [$controller, 'viewArticle']);
            $r->addRoute('POST', '/article/{id:\d+}', [$controller, 'deleteArticle']);
            $r->addRoute('POST', '/update/{id:\d+}', [$controller, 'updateArticle']);
        });




        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = rawurldecode($_SERVER['REQUEST_URI']);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                echo '404 Not Found';
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                echo '405 Method Not Allowed';
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $controllerClass = $handler[0];
                $action = $handler[1];

                $controller = new $controllerClass($dbService, new ArticlesView($twig));
                $response = $controller->$action($vars);

                echo $response;

                break;

        }

    }
}

