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
        $router->group(['middleware' => ['admin_auth', 'admin']], function () use ($router) {
            $router->put('/change-password', 'AuthenticationController@changePassword');
            $router->delete('/logout', 'AuthenticationController@logout');

            //User Management apis
            $router->group(['prefix' => 'users'], function () use ($router) {
                $router->get('/', 'UserController@get');
                $router->get('/{id}', 'UserController@first');
                $router->post('/', 'UserController@store');
                $router->put('/{id}', 'UserController@update');
                $router->delete('/{id}', 'UserController@destroy');
                $router->put('/change-status/{id}', 'UserController@toggleStatus');
                $router->put('/change-password/{id}', 'UserController@changeAnyPassword');
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
    $router->group(['namespace' => 'Agency'], function () use ($router) {
        //Agency + Customer Authentication
        $router->group(['prefix' => 'auth', 'middleware' => 'client_credentials'], function () use ($router) {
            $router->group(['prefix' => 'agencies'], function () use ($router) {
                $router->post('/register', 'AuthenticationController@register');
            });

            $router->group(['middleware' => 'agency_domain'], function () use ($router) {
                $router->post('/login', 'AuthenticationController@login');
                $router->post('/verify-token', 'AuthenticationController@userVerification');
                $router->post('/forget-password', 'AuthenticationController@forgetPassword');
                $router->post('/create-new-password', 'AuthenticationController@createNewPassword');
            });
        });

        $router->group(['middleware' => 'auth'], function () use ($router) {
            $router->delete('/logout', 'AuthenticationController@logout');
            $router->post('/verification', 'AuthenticationController@generateToken');
            $router->put('/change-password', 'AuthenticationController@changePassword');
            $router->get('/profile', 'AuthenticationController@profile');
            $router->put('/profile', 'AuthenticationController@profileUpdate');
        });

        //public agency routes
        $router->group(['prefix' => 'public'], function () use ($router) {
            $router->group(['middleware' => 'agency_domain'], function () use ($router) {
                //portal-settings
                $router->get('/portal-settings', 'PublicPortalSettingController@index');
            });
            //domain launch
            $router->post('/domain-launch', 'DomainLaunchController@index');
        });

        //Protected Routes
        $router->group(['prefix' => 'agencies', 'middleware' => ['agency_domain', 'auth', 'agency']], function () use ($router) {
            //User Management apis
            $router->group(['prefix' => 'users'], function () use ($router) {
                $router->get('/', 'UserController@get');
                $router->get('/{id}', 'UserController@first');
                $router->post('/', 'UserController@store');
                $router->put('/{id}', 'UserController@update');
                $router->delete('/{id}', 'UserController@destroy');
                $router->put('/change-status/{id}', 'UserController@toggleStatus');
                $router->put('/change-password/{id}', 'UserController@changeAnyPassword');
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

            //Services apis
            $router->group(['prefix' => 'services'], function () use ($router) {
                $router->get('/', 'ServiceController@get');
                $router->get('/{id}', 'ServiceController@first');
                $router->post('/', 'ServiceController@store');
                $router->put('/{id}', 'ServiceController@update');
                $router->delete('/{id}', 'ServiceController@destroy');
                $router->put('/change-status/{id}', 'ServiceController@toggleStatus');
                $router->put('/change-catalog-status/{id}', 'ServiceController@toggleCatalogStatus');
            });

            //portal-settings
            $router->put('/portal-settings', 'PortalSettingController@update');

            //customers
            $router->group(['prefix' => 'customers'], function () use ($router) {
                $router->get('/', 'CustomerController@get');
                $router->get('/{id}', 'CustomerController@first');
                $router->post('/', 'CustomerController@store');
                $router->put('/{id}', 'CustomerController@update');
                $router->delete('/{id}', 'CustomerController@destroy');
                $router->put('/change-status/{id}', 'CustomerController@toggleStatus');
            });

            // request services
            $router->group(['prefix' => 'request-services'], function () use ($router) {
                $router->get('/', 'RequestServiceController@get');
                $router->get('/{id}', 'RequestServiceController@first');
                $router->post('/', 'RequestServiceController@create');
                $router->put('/change-status/{id}', 'RequestServiceController@changeStatus');
            });
        });
    });

    // Agencies Customers
    $router->group(['namespace' => 'Customer', 'middleware' => 'agency_domain'], function () use ($router) {
        $router->group(['prefix' => 'auth/customers', 'middleware' => 'client_credentials'], function () use ($router) {
            $router->post('/register', 'AuthenticationController@register');
        });

        $router->group(['prefix' => 'customers', 'middleware' => ['customer']], function () use ($router) {
            $router->group(['prefix' => 'services'], function () use ($router) {
                $router->get('/', 'ServiceController@get');
                $router->get('/{id}', 'ServiceController@first');
            });

            $router->group(['prefix' => 'request-services'], function () use ($router) {
                $router->get('/', 'RequestServiceController@get');
                $router->get('/{id}', 'RequestServiceController@first');
                $router->post('/', 'RequestServiceController@create');
            });

            //customer billing information
            $router->group(['prefix' => 'billing-information'], function () use ($router) {
                $router->get('/', 'BillingInformationController@first');
                $router->post('/', 'BillingInformationController@store');
                $router->put('/', 'BillingInformationController@update');
                $router->delete('/', 'BillingInformationController@destroy');
            });
        });
    });

});
