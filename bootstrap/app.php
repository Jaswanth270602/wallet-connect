<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Manually register config repository (needed before other providers)
$app->singleton('config', function ($app) {
    return new \Illuminate\Config\Repository(
        require __DIR__.'/../config/app.php'
    );
});

// Load all config files
$configPath = __DIR__.'/../config';
if (is_dir($configPath)) {
    $config = $app['config'];
    foreach (glob($configPath.'/*.php') as $configFile) {
        $key = basename($configFile, '.php');
        if ($key !== 'app') {
            $config->set($key, require $configFile);
        }
    }
}

// Register filesystem service provider (required by many services)
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);

// Register database service provider (required by MigrationServiceProvider)
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);

// Register encryption service provider (required by cookie middleware)
$app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);

// Register cookie service provider (required by web middleware)
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);

// Register session service provider (required by web middleware)
$app->register(\Illuminate\Session\SessionServiceProvider::class);

// Register translation service provider (required for __() helper and trans())
$app->register(\Illuminate\Translation\TranslationServiceProvider::class);

// Register view service provider (required for Blade templates)
$app->register(\Illuminate\View\ViewServiceProvider::class);

// Register routing service provider (required for Route facade)
$app->register(\Illuminate\Routing\RoutingServiceProvider::class);

// Register console support service provider (includes ArtisanServiceProvider with serve command)
$app->register(\Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class);

// Set facade root (required for facades to work)
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Load routes manually (since we don't have RouteServiceProvider)
if (file_exists($routesPath = __DIR__.'/../routes/web.php')) {
    require $routesPath;
}

return $app;

