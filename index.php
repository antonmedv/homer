<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app['debug'] = false;

$app['db'] = $app->share(function () {
    return new PDO(HOMER_DNS);
});

$app['queue'] = $app->share(function () use ($app) {
    return new Homer\Queue($app['db']);
});

$app['search'] = $app->share(function () use ($app) {
    return new Homer\Search($app['db']);
});

$app->get('/', function () use ($app) {
    $search = $app['request']->get('search', false);

    $result = $app['search']->search($search, 20);

    ob_start();
    include 'view/index.phtml';
    return ob_get_clean();

})->bind('search');

$app->post('/add', function () use ($app) {
    $url = filter_var($app['request']->get('url', ''), FILTER_VALIDATE_URL);
    if ($url) {
        $app['queue']->push($url, HOMER_DEEP);
    }
    return $app->redirect($app['url_generator']->generate('search', ['success' => $url !== false]));
})->bind('add');

$app->get('/statistic', function () use ($app) {
    ob_start();
    include 'view/statistic.phtml';
    return ob_get_clean();
})->bind('statistic');

$app->run();