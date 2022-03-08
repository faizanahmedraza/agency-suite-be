<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'v1', 'namespace' => 'V1'], function () use ($router) {

    // Administration
    $router->group(['prefix' => 'admins', 'namespace' => 'Admin'], function () use ($router) {
        //Authentication
        $router->group(['prefix' => 'auth', 'middleware' => 'client_credentials'], function () use ($router) {
            $router->post('/login', 'AuthenticationController@login');
        });

        //Protected Routes
        $router->group(['middleware' => ['admin_auth','admin']], function () use ($router) {
            $router->post('/change-password', 'AuthenticationController@changePassword');
            $router->delete('/logout', 'AuthenticationController@logout');
//            $router->get('/dashboard', 'DashboardController@index');

            //User Management apis
            $router->group(['prefix' => 'users'], function () use ($router) {
                $router->get('/', 'UserController@get');
                $router->get('/{id}', 'UserController@first');
                $router->post('/', 'UserController@store');
                $router->put('/{id}', 'UserController@update');
                $router->delete('/{id}', 'UserController@destroy');
                $router->post('/change-any-password/{id}', 'UserController@changeAnyPassword');
            });

            // Roles apis
            $router->group(['prefix' => 'roles'], function () use ($router) {
                $router->get('/', 'RoleController@get');
                $router->get('/{id}', 'RoleController@first');
                $router->post('/', 'RoleController@store');
                $router->put('/{id}', 'RoleController@update');
                $router->delete('/{id}', 'RoleController@destroy');
            });

            // Permissions apis
            $router->get('/permissions', 'PermissionController@get');
        });
    });

    // Agencies
//    $router->group(['prefix' => 'agencies', 'namespace' => 'Agency'], function () use ($router) {
//        //Authentication
//        $router->group(['prefix' => 'auth', 'middleware' => 'client_credentials'], function () use ($router) {
//            $router->post('/login', 'AuthenticationController@login');
//            $router->post('/register', 'AuthenticationController@register');
//            $router->post('/verify-token', 'AuthenticationController@userVerification');
//            $router->post('/forget-password', 'AuthenticationController@forgetPassword');
//            $router->post('/create-new-password', 'AuthenticationController@createNewPassword');
//        });
//
//        //Protected Routes
//        $router->group(['middleware' => 'agency'], function () use ($router) {
//            $router->delete('/logout', 'AuthenticationController@logout');
//            $router->get('/dashboard', 'DashboardController@index');
//            $router->post('/verification', 'AuthenticationController@generateToken');
//            $router->put('/change-password', 'UserController@changePassword');
//        });
//    });

    // Agencies Customers
//    $router->group(['prefix' => 'users'], function () use ($router) {
//        $router->group(['prefix' => 'auth', 'middleware' => 'client_credentials'], function () use ($router) {
//            $router->post('/login', 'AuthenticationController@login');
//            $router->post('/register', 'AuthenticationController@register');
//            $router->post('/verify-token', 'AuthenticationController@userVerification');
//            $router->post('/forget-password', 'AuthenticationController@forgetPassword');
//            $router->post('/create-new-password', 'AuthenticationController@createNewPassword');
//        });
//
//        $router->group(['middleware' => 'auth'], function () use ($router) {
//            $router->delete('/logout', 'AuthenticationController@logout');
//            $router->post('/verification', 'AuthenticationController@generateToken');
//            $router->put('/change-password', 'UserController@changePassword');
//        });
//    });
});
