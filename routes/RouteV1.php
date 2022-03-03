<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'v1', 'namespace' => 'V1'], function () use ($router) {

    $router->group(['prefix' => 'auth', 'middleware' => 'client_credentials'], function () use ($router) {
        $router->post('/login', 'AuthenticationController@login');
        $router->post('/register', 'AuthenticationController@register');
        $router->post('/verify-token', 'AuthenticationController@userVerification');
        $router->post('/forget-password', 'AuthenticationController@forgetPassword');
        $router->post('/create-new-password', 'AuthenticationController@createNewPassword');
    });

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->delete('/logout', 'AuthenticationController@logout');
        $router->post('/verification', 'AuthenticationController@generateToken');
        $router->put('/change-password', 'UserController@changePassword');
    });
});
