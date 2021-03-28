<?php
/**
 * Extreemly Simple Router
 * @author Halil Qerimi
 * @version 0.1
 * 
 */

namespace ESRouter;
/**
 * Route class that holds predefined routes
 * @access public
 */
class Route {
    /**
     * 
     * @var string
     * @access public
     */
    public $route;

    /**
     * @var string
     * @access public
     */
    public $regExpression;

    /**
     * @var string
     * @access public
     */
    public $method;

    /**
     * @var \Closure
     * @access public
     */
    public $handler;

    /**
     * 
     */
    public function __construct(string $route, string $regExpression, string $method, \Closure $handler) {
        $this->route = $route;
        $this->regExpression = $regExpression;

        //Set method name to uppercase for comparison 
        $this->method = strtoupper($method);
        $this->handler = $handler;
    }

    /**
     * 
     */
    public function __toString(): string {
        return (string) $this->method.$this->route;
    }
}

/**
 * @internal Class usess private property {@routes} to store all predefined routes
 * @internal Class usess private methods {@prepareRegex(string $route)} to generate RegEx expression that can be uses to match Ure with predefined routes
 * @access public
 */
class Router {

    /**
     * @var \Closure
     * @access private
     */

     private $e404;

    /**
     * @var array Array of Route Objects
     * @access private
     */
    private $routes = array();

    /**
     * Appends anonther route to the list of defined routes
     * @param string $route Define route
     * @param string $method Request method (POST, GET, PUT, DELETE ...)
     * @param string $handler Function that will handle the request when route matches, it will call this function with only one parameter of array type
     * @return void 
     * @access public
     */
    public function addRoute(string $route, string $method, \Closure $handler): void {
        $regExpression = "/" . $this->prepareRegex($route) . "/";
        $route = new Route($route, $regExpression, $method, $handler);
        foreach($this->routes as $rt) {
            if((string)$rt == (string)$route) {
                throw new \Exception('Route "'.$route->route.'" already exists for method "'.$route->method.'"');
            }
        }
        array_push($this->routes, $route);
    }

    /**
     * Appends anonther route to the list of defined routes
     * @param string $handler E404 handler
     * @return void 
     * @access public
     */
    public function setE404(\Closure $handler) {
        $this->e404 = $handler;
    }

    /**
     * Searches for a match of current url with predefined routes
     * @return void
     * @access public
     */
    public function run() {
        $e404 = true;
        $method = $_SERVER["REQUEST_METHOD"];
        $url = $_SERVER["REQUEST_URI"];
        if (substr_count($url, "?") > 0) {
            $url = explode('?', $url)[0];
        }
        $url = rtrim($url, "/");

        foreach ($this->routes as $route) {
            if ($route->method == $method) {
                preg_match($route->regExpression, $url, $matches);
                if (count($matches) > 0) {
                    $e404 = false;
                    call_user_func_array($route->handler, array($matches));

                    //In case match is found, break foreach loop not to continue futher
                    //break;
                }
            }
        }

        if ($e404) {
            if($this->e404 == null) {
                http_response_code(404);
                echo "404";
                return;
            }
            call_user_func($this->e404);       
        }
    }

    /**
     * Prepares RegEx expression from given route
     * @param string $route Define route
     * @return string RegEx expression
     * @access private
     */
    private function prepareRegex(string $route): string {
        $regex = "^";
        $routeParts = array_filter(explode('/', $route));
        foreach ($routeParts as $part) {
            if ($part[0] === "{" && $part[strlen($part) - 1] === "}") {
                $expression = substr($part, 1, -1);
                if (substr_count($expression, ":") == 1) {
                    $parts = explode(':', $expression);

                    $regex = $regex . "\\/(?P<" . $parts[0]  . ">" . $parts[1] . "+)";
                } else {
                    $regex = $regex . "\\/(?P<" . $expression . ">[^\\/]+)";
                }
            } else {
                $regex = $regex . "\\/" . $part;
            }
        }
        $regex = $regex . "$";
        return $regex;
    }
}

