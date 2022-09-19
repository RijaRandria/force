# Controllers/Views/Middlewares

Maintenant que nous avons vu comment créer des routes, il reste plus qu'à faire la partie logique et vue de ces routes.


### Controllers
Commençons par les `controllers`, si vous vous rappelez dans la partie `routeur` du chapitre précédent, vous deviez déclarer un controller pour chaque route.<br>
Il s'agit de la partie logique d'une vue, ou vous allez effectuer diverses opérations, des requêtes en base de données par exemple pour ensuite les envoyer à la vue `views`.

Dans un controller vous avez des `actions`, ou `fonctions` dans la logique du code, ces fonctions ont pour rôle de gérer la logique d'une route avant de retourner la vue.<br>
Prenons exemple de cette route :
```php
<?php
use App\Controllers\HomeController;
$app->get('/', HomeController::class. ':getHome')->setName('home');
```
Ma route est `/home`, donc l'url correspond à `http://exemple.net/home`, pour cette url je désigne le controller `HomeController` avec comme action `getHome`.

Si on regarde cette `action` dans le controller `app/src/Controllers/HomeController.php` :
```php
<?php

public function getHome(RequestInterface $request, ResponseInterface $response)
{
    $title = "Hello World!";

    if (isset($title) && $title === "Hello World!") {
        $this->logger->addInfo("Message de bienvenue envoyé");
    } else {
        $title = "Nop";
    }

    $params = compact("title");
    $this->render($response, 'pages/home.twig', $params);
}
```
Ici je définis une variable `$title`, par la suite j'effectue une opération avec `monolog`, j'y ai accès car il est déclaré dans le `container`, puis j'envoie cette variable dans ma vue vers le fichier `pages/home.twig`.


### Views
Allons voir cette vue `app/src/Views/pages/home.twig` :
```twig
{% extends "layout.twig" %}
{% block content %}
<h1>{{ title }}</h1>
{% endblock %}
```
Si vous connaissez un peu twig, vous aurez très vite compris que la première ligne sert à appeler la page parent de ma vue `layout.twig`.<br>
Dans ma vue je souhaite simplement afficher la variable `$title` que le controller lui a envoyé, rien de bien compliqué.<br>
Si vous connaissez à peine twig, je vous invite à aller voir la [documentation](https://twig.symfony.com/doc/2.x/) pour que vous puissiez l'utiliser, ici je guide surtout sur l'utilisation du skeleton et non des librairies utilisées.


### Middlewares
On attaque la partie qui rend slim très intéressant, les `middlewares`.<br>
Pour faire simple, il s'agit un peu comme des controllers, mais contrairement à effectuer des actions précises pour des routes, ils effectuent des opérations que vous allez définir à exécuter où et quand.

Par exemple, vous développez une application avec une interface administrateur sur la route `/admin`, vous avez d'autres routes avec cette url, `/admin/users`, `/admin/user/3` ect...

Vous devez sur chacune de ces vues vérifier les droits d'accès de l'utilisateur qui souhaite accéder à ces pages, en temps normal vous écrivez le code pour cette vérification sur chaque action dans vos controllers qui serait du copier/coller ce qui n'est pas bon.<br>
Les `middlewares` vous permettent de faire ceci dans un seul endroit pour ensuite le faire exécuter sur les vues souhaitées et ce avant l'exécution de vos controllers.

C'est dans le fichier `config/middlewares.php` que vous ajoutez à l'exécution les middlewares à exécuter avant les controllers, et ce pour toutes les vues :
```php
<?php

use App\Middlewares;

// Middleware pour les message d'alert en session
$app->add(new Middlewares\AlertMiddleware($container->view->getEnvironment()));

// Middleware pour la sauvegarde des champs de saisie
$app->add(new Middlewares\OldMiddleware($container->view->getEnvironment()));

// Middleware pour la génération de token
$app->add(new Middlewares\TokenMiddleware($container->view->getEnvironment()));

// Middleware pour la vérification csrf
$app->add(new Middlewares\CsrfMiddleware($container->view->getEnvironment(), $container->csrf));
$app->add($container->csrf);
```

Allons voir par exemple le middleware `app/src/Middlewares/AlertMiddleware.php` :
```php
<?php
namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class AlertMiddleware
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $this->twig->addGlobal('alert', isset($_SESSION['alert']) ? $_SESSION['alert'] : []);
        if (isset($_SESSION['alert'])) {
            unset($_SESSION['alert']);
        }
        return $next($request, $response);
    }
}
```
Pour commencer, il s'agit d'un middleware qui permet de gérer les messages flash, par exemple en cas d'erreur de formulaire, de droits d'accès ect... on peux envoyer à notre vue twig la variable `alert`, un tableau comportant son type et son message.

Dans le controller parent `app/src/Controllers/Controller.php` vous avez la fonction permettant de créer des messages d'alerte :
```php
<?php
public function alert($message, $type = "success")
{
    if (!isset($_SESSION['alert'])) {
        $_SESSION['alert'] = [];
    }
    return $_SESSION['alert'][$type] = $message;
}
```
Le middleware `app/src/Middlewares/OldMiddleware.php` qui est utilisé pour garder en mémoire les informations saisies dans les formulaires, utiles en cas d'echec, fonctionne de la même manière que le middleware pour les messages flash.

Il envoie les informations des champs dans la vue sous forme de tableau nommé `old`, il n'y a pas de fonction pour celà, vous devez juste enregistrer sous forme de tableau vos champs dans la variable de session `$_SESSION['old']`.

Si vous souhaitez créer des middlewares et les exécuter pour des routes bien spécifiques, vous ne devez pas les déclarer dans `config/middlewares.php` mais directement dans `config/routes.php` comme ceci :
```php
<?php
$app->group('', function () {
  $this->get('/admin', AdminController::class. ':getHome')->setName('admin');
  $this->get('/admin/users', AdminController::class. ':getUsers')->setName('users');
})->add(new App\Middlewares\AlertMiddleware($container->view->getEnvironment(), $container));
```

Pour comprendre le fonctionnement précis d'un middleware avec slim, je vous invite à aller voir la [documentation](https://www.slimframework.com/docs/concepts/middleware.html).

| Introduction | Chapitre précédent | Chapitre suivant |
| :---------------------: | :--------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [Routeur/Container](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter02.md) | [Doctrine/Commandes](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter04.md) |
