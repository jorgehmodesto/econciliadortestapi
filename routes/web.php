<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => '/api', 'middleware' => ['cros']], function() use ($router) {

    $router->get('/', function() {
        return "Github Repository commits API";
    });

    $router->get('import/owner/{owner}/repo/{repository}', [
        'as' => 'import',
        'uses' => 'ApiController@import'
    ]);

    $router->get('export/owner/{owner}/repo/{repository}', [
        'as' => 'export',
        'uses' => 'ApiController@export'
    ]);

});