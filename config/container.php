<?php

$container = $app->getContainer();

// Session helper
$container['session'] = function () {
    return new \Adbar\Session("app");
};

// Monolog
$container['logger'] = function ($container) {
    $settings = [
      'name' => 'slim-app',
      'path' => dirname(__DIR__).'/app/logs/app.log'
    ];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

if (getenv('ENV') == 'dev') {
    $container['twig_profile'] = function () {
        return new Twig_Profiler_Profile();
    };
}

// Twig
$container['view'] = function ($container) {
    $pathView = dirname(__DIR__);

    if (slim_env('CACHE')) {
        $cache = $pathView.'/app/cache';
    } else {
        $cache = false;
    }
    $view = new \Slim\Views\Twig($pathView.'/app/src/Views', [
        'cache' => $cache,
        'debug' => true
    ]);

    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $uri));

    if (getenv('ENV') == 'dev') {
        $view->addExtension(new Twig_Extension_Profiler($container['twig_profile']));
        $view->addExtension(new Twig_Extension_Debug());
    }
    $twig = $view->getEnvironment();

    $defaultLang = 'en';
    $session = $container['session'];
    $twig->addGlobal('username', isset($_SESSION['username']) ? $_SESSION['username'] : 'Visiteur' );
    $twig->addGlobal('typeuser', isset($_SESSION['estconnecte']) ? $_SESSION['typeuser'] : 'Non connecté' );

    if (!$session->has('lang')) {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !is_null($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            if (file_exists(dirname(__DIR__).'/config/translations/'.$lang.".yml")) {
                $session->set('lang', $lang);
            } else {
                $session->set('lang', $defaultLang);
            }
        } else {
            $session->set('lang', $defaultLang);
        }
    }
    $translator = new \Symfony\Component\Translation\Translator(
        $session->get('lang'),
        null
    );
    $translator->setFallbackLocales([$defaultLang]);
    $translator->addLoader('yml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
    $directory = new \RecursiveDirectoryIterator(
        dirname(__DIR__).'/config/translations/',
        \FilesystemIterator::SKIP_DOTS
    );
    $it = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);
    $it->setMaxDepth(1);
    foreach ($it as $fileinfo) {
        if ($fileinfo->isFile() && $fileinfo->getFilename() != ".gitkeep") {
            $lang = explode(".", $fileinfo->getFilename());
            $translator->addResource(
                'yml',
                dirname(__DIR__).'/config/translations/'.$fileinfo->getFilename(),
                $lang[0]
            );
        }
    }
    $view->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator));
    return $view;
};

// EntityManager de doctrine
$container['em'] = function () {

    $typeDatabase = strtoupper(getenv('ENV'));
    $chaineConnexion = getenv('TYPE_BASE') . '://' . getenv('USER_' . $typeDatabase) . ':' . getenv('PW_' . $typeDatabase) . '@' . getenv('HOST_' . $typeDatabase) . ':' . getenv('PORT_' . $typeDatabase) . '/' . getenv('BASE_' . $typeDatabase);

    $connection = [
        'url' => $chaineConnexion
    ];
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        ['app/src/Entity'],
        true,
        dirname(__DIR__).'/app/cache/doctrine',
        null,
        false
    );
    return \Doctrine\ORM\EntityManager::create($connection, $config);
};

// Accès MAIL
$container['mail'] = function ($container) {
  $mail = new \SMTP\Mail();
	return $mail;
};

$container['slim'] = function ($container) {
   global $app;
   return $app;
};

// Accès PDO
$container['pdo'] = function ($container) {
  // On récupère la chaîne de connexion à l'environnement de production souhaité
  $typeDatabase = strtoupper(getenv('ENV'));
  //$pdo = new PDO("mysql:host=localhost;dbname=cnam;port=3306", "cnam", "iWSUvBLyPmIojJJR");

  $chainePDO = getenv('TYPE_BASE') . ':host=' . getenv('HOST_' . $typeDatabase) . ';dbname=' . getenv('BASE_' . $typeDatabase) . ';port=' . getenv('PORT_' . $typeDatabase) . ';charset=utf8';
	$pdo = new \PDO( $chainePDO , getenv('USER_' . $typeDatabase) , getenv('PW_' . $typeDatabase) , array(PDO::ATTR_PERSISTENT => false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION) );
	return $pdo;
};

$container['auth'] = function ($container)
{
  // On a besoin de savoir à partir de la session si l'utilisateur est connecté $_SESSION['estconnecte']
  // On va stocker de quelque part le nom de l'usager connecté $_SESSION['username']
  // On va stocker le type de l'usager connecté $_SESSION['usertype']

  // login, password, nom, mail, typeusager
  $verif = function( $request, $response, $next)
  {
    global $app;

    // Si la variable de session "estconnecte" existe alors on continue vers la route demandée...
    if (array_key_exists('estconnecte', $_SESSION))
      $response = $next($request, $response);
    else {
      // Sinon, on affiche le formulaire de connexion
      $_SESSION['routedemandee'] = $request->getUri()->getPath();
      $response = $response->withRedirect($app->getContainer()->get('router')->pathFor('authentification'));
      }
    return $response;
  };
  return $verif;
};

$container['authadmin'] = function ($container)
{
  // login, password, nom, mail, typeusager
  $verif = function( $request, $response, $next)
  {
    global $app;

    // Si la variable de session "estconnecte" existe alors on continue vers la route demandée...
    if (array_key_exists('estconnecte', $_SESSION)) {
      // On vérifie que l'usager est bien Admin
      if ($_SESSION['typeuser'] == 'Admin')
        $response = $next($request, $response);
      else {
          $response = $response->withRedirect($app->getContainer()->get('router')->pathFor('droitinsuffisant'));
        }
      }
    else {
      // Sinon, on affiche le formulaire de connexion
      $_SESSION['routedemandee'] = $request->getUri()->getPath();
      $response = $response->withRedirect($app->getContainer()->get('router')->pathFor('authentification'));
      }
    return $response;
  };
  return $verif;
};

// Csrf
$container['csrf'] = function () {
  $null = null;
    $guard = new \Slim\Csrf\Guard('crsf', $null, null, 200, 16, true);
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
    return $guard;
};
