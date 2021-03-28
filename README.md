# Extreemly Simple Router
This is an PHP class that can help to add router on your PHP project. Main ideas is to create PHP router as simple as it gets, that can be integrated in your project and easy to be edited for your needs.

## Usage
Import `router.php` in your project and create an instance of `Router` class.
Add route to the instance using `addRoute` method by providing:
- Route     : define a route to match
- Method    : method of request to apply for
- Handler   : handler function that accepts array of arguments, this arry will be provided as key value pair when route matches
```PHP
//import Router class
require_once("./router.php");

//create instance of Router
$router = new \ESRouter\Router();

//define new route
$router->addRoute("/user/{name}/post/{id:[0-9]}", "get", function ($args) {
    //handle request as you need
    $name = $args['name'];
    $id = $args['id'];
    print $name.$id ;
});

//define another route
$router->addRoute("/", "get", function ($args) {
    //handle request as you need
    print "Welcome home" ;
});

//more routes
//...

//optional
//Setting 404 handler
$router->setE404(function(){
    echo "this is 404";
});


$router->run();

```

## Defining Routes
To define route, start with `/` and for static rout use plain text example `user` or for variable use `{name}` ad it fill match anything in between `/` and `/` or end of route, in case you want to use RegEx use `{varname:RegEx}` where *name* is the name of variable and *RegEx* is a RegEx expression to match

## Matching Routes
Routes are matchef rom the first defined route to the last, if a route is matched than it will execute the handler and will stop searching for other routes.
```PHP
//Example
//In this case if we call http://<server>/user/nameOfTheUser/post/1234

$router->addRoute("/user/{name}/post/{id:\d{4}}", "get", function ($args) {
    print 'First route';
});

$router->addRoute("/user/{name}/post/{id:[0-9]}", "get", function ($args) {
    print 'Second route';
});

//First Route will be called
```
