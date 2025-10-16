<?php
// core/Router.php

class Router
{
    protected $routes = [];

    public function add($method, $uri, $controllerAction)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controllerAction[0],
            'action' => $controllerAction[1],
            'method' => $method
        ];
    }

    public function dispatch($uri, $method)
    {
        foreach ($this->routes as $route) {
            // Converte a URI da rota para uma expressão regular
            // Ex: /servicos/editar/{id} -> #^/servicos/editar/([a-zA-Z0-9_]+)$#
            $pattern = "#^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $route['uri']) . "$#";

            // Verifica se a URI atual corresponde ao padrão da rota e ao método
            if (preg_match($pattern, $uri, $matches) && $route['method'] === strtoupper($method)) {
                // Remove o primeiro elemento ($matches[0]), que é a string completa
                array_shift($matches);
                $params = $matches;

                // Instancia o controller e chama a ação, passando os parâmetros
                $controller = new $route['controller']();
                $action = $route['action'];

                // Chama a ação no controller, passando os parâmetros da URL
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        $this->abort(404);
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        echo "<h1>{$code} - Página não encontrada</h1>";
        die();
    }
}
