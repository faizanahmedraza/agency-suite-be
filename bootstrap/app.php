<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('cors');
$app->configure('auth');
$app->configure('database');
$app->configure('permission');
$app->configure('cloudinary');
$app->configure('sentry');
$app->configure('segment');
$app->configure('agency_events');
$app->configure('portal_settings');
$app->configure('mail');

if (env('APP_ENV') === "local") {
    //scribe for documentation
    if (class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
        $app->configure('scribe');
    }
}

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    'Nord\Lumen\Cors\CorsMiddleware',
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'admin_auth' => App\Http\Middleware\AdminAuthenticate::class,
    'client_credentials' => App\Http\Middleware\ClientCredentialsVerification::class,
    'permission' => Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role' => Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \App\Http\Middleware\PermissionMiddlware::class,
    'admin' => \App\Http\Middleware\AdminAllowedMiddleware::class,
    'agency' => \App\Http\Middleware\AgencyAllowedMiddleware::class,
    'customer' => \App\Http\Middleware\CustomerAllowedMiddleware::class,
    'agency_domain' => \App\Http\Middleware\AgencyDomainMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(Pearl\RequestValidate\RequestServiceProvider::class);
$app->register(Spatie\Permission\PermissionServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
$app->register(Nord\Lumen\Cors\CorsServiceProvider::class);
$app->register(Sentry\Laravel\ServiceProvider::class);
$app->register(Illuminate\Mail\MailServiceProvider::class);

if (env('APP_ENV') === "local") {
    //scribe for documentation
    if (class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
        $app->register(\Knuckles\Scribe\ScribeServiceProvider::class);
    }
}

$app->alias('cache', \Illuminate\Cache\CacheManager::class);
$app->alias('Cloudinary', \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::class);
$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
    require __DIR__ . '/../routes/RouteV1.php';
});

return $app;
