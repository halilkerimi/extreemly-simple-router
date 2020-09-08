# Extreemly Simple Router
This is an PHP class that can help to add router on your PHP project. Main ideas is to create PHP router as simple as it gets, that can be integrated in your project and easy to be edited for your needs.

## Usage
Import `router.php` in your project and create an instance of `Router` class.
Add route to the instance using `addRoute` method by providing:
- Route     : definea route to match
- Method    : method of request to apply for
- Handler   : handler function that accepts array of arguments, this arry will be provided as key value pair when route matches
```PHP
//import Router class
require_once("./router.php");

//create instance of Router
$router = new \ESRouter\Router();

//define new route
$route->addRoute("/user/{name}/post/{id:[0-9]}", "get", function ($args) {
    //handle request as you need
    $name = $args['name'];
    $id = $args['id'];
    print $name $id ;
});
```

## Defining Routes
To define route, start with `/` and for static rout use plain text example `user` or for variable use `{name}` ad it fill match anything in between `/` and `/` or end of route, in case you want to use RegEx use `{varname:RegEx}` where *name* is the name of variable and *RegEx* is a RegEx expression to match
