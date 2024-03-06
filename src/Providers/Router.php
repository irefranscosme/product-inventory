<?php

namespace App\Providers;

use Closure;
use Exception;

class Router {
    public $routes = []; // stores routes

    public function addRoute(string $method, string $url, Closure $target) {
        $this->routes[$method][$url] = $target;
    }

    public function matchRoute() {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];

        // Check if the routes array is empty
        if(count($this->routes) === 0) {
            throw new Exception('Route not found');
        }

        foreach($this->routes[$method] as $route => $callback) {

            $newRoute = explode("/", trim($route, "/"));
            $newUrl = explode("/", trim($url, "/"));

            $routeWithoutBackSlash = str_replace("/", "", $route);
            $urlWithoutBackSlash = str_replace("/", "", $url);


            // echo $urlWithoutBackSlash;
            // echo "</br>";
            // echo $routeWithoutBackSlash;
            // echo "</br>";

            $queryStrings = "?";
            $position = strpos($urlWithoutBackSlash, $queryStrings);

            if ($position !== false) {
               $urlWithoutBackSlashQueryParams = substr($urlWithoutBackSlash, 0, $position);
            } else {
                $urlWithoutBackSlashQueryParams = $urlWithoutBackSlash;
            }

            // Check if the route equal to url regardless of backslah
            if($routeWithoutBackSlash === $urlWithoutBackSlashQueryParams) {
                call_user_func($callback);
                return;
                // echo $route;
            }

             // Check if the route equal to url regardless of backslah
             if($route === $url) {
                call_user_func($callback);
                return;
                // echo $route;
            }

            if(str_contains(end($newRoute), ":")) {
                // echo "has semicolon";

                $routeNoSemiColon = str_replace(":", "", end($newRoute));

                $_GET[$routeNoSemiColon] = end($newUrl);

                if(count($newRoute) === count($newUrl)) {
                    $newUrl[count($newUrl) - 1] = end($newRoute);

                    // echo "Current url is" . $url . count($newRoute) . count($newUrl);
                    // echo "</br>";

                    $implodeRoute = implode("/", $newRoute);
                    $implodeUrl = implode("/", $newUrl);

                    // echo $implodeRoute;
                    // echo "</br>";
                    // echo $implodeUrl;

                    if($implodeRoute === $implodeUrl) {
                        call_user_func($callback);
                    } else {
                        throw new Exception("Route not found..");
                    }
                }

            }


            // echo $route;
            // echo "</br>";

        }

        // echo count($this->routes);

    }

}