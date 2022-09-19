# Routeur/Container


### Routeur
Attaquons maintenant le `routeur`, il s'agit tout simplement de [slim 3](https://www.slimframework.com/).<br>
Dans le fichier `config/routes.php` sont définies les routes de votre application.

Vous faites un `use` pour chaque controllers utilisés, puis vous les définissez en dessous comme ceci :

```php
<?php
use App\Controllers\HomeController;

// Request de type 'get'
$app->get('/', HomeController::class. ':getHome')->setName('home');

// Request de type 'post'
//$app->post('/', HomeController::class. ':postHome');
```
- Premièrement, on appelle `$app` votre application slim, puis la méthode `get` ou `post`.
- Dans votre méthode en premier paramètre vous choisissez le controller à appeler avec le nom de l'action, exemple `getHome`.

Ce fichier est appelé dans `public/index.php`, vous pouvez très bien créer plusieurs fichiers routes pour séparer les différentes partie de votre application.


### Container
Maintenant, le gestionnaire de dépendances de [slim 3](https://www.slimframework.com/), le `container`.<br>
Dans le fichier `config/container.php` sont insérées les dépendances/librairies utilisées, exemple [Monolog](https://github.com/Seldaek/monolog).<br>
Si vous avez des dépendances à rajouter pour pouvoir les utiliser dans les controllers/middlewares, il vous suffit d'ajouter ce type de ligne :

```php
<?php
$container['librairie'] = function () {
    // Appel de la librairie, opération...
    return $librairieObjet;
};
```

N'hésitez pas à aller voir sur la documentation de [slim 3](https://www.slimframework.com/docs/) pour des choses plus avancées.

| Introduction | Chapitre précédent | Chapitre suivant |
| :---------------------: | :--------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [Installation/Configuration](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter01.md) | [Controllers/Views/Middlewares](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter03.md) |
