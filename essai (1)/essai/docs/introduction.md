# Introduction

Comme dit dans le `README`, il s'agit d'un skeleton `slim 3` avec une architecture MVC.<br>
Disposant de [doctrine](https://github.com/doctrine/doctrine2) comme ORM, il est important pour vous de bien comprendre les principes d'entités, je vous invite à aller voir la [documentation](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/).<br>

Pour bien comprendre l'organisation de cette arborescence, voici un petit chemin :

- `app/` dossier de l'application
  - `cache/` cache [twig](https://github.com/slimphp/Twig-View) de l'application
  - `commands/` les commandes [console](https://github.com/symfony/console)
  - `logs/` les logs de [monolog](https://github.com/Seldaek/monolog)
  - `src/` dossier des controllers/middlewares/views
    - `Controllers/` dossier des controllers
    - `Entity/` dossier des entités doctrine
    - `Middlewares/` dossier des middlewares
    - `Views/` dossier des vues [twig](https://github.com/slimphp/Twig-View)
- `config/` dossier comportant des fichiers de configuration de l'application (container, routes ...)
    - `translations` dossier comportant les traductions utilisé pour twig
    - `container.php` le container de dépendences
    - `middlewares.php` appel des middlewares
    - `routes.php` le fichier de routes
- `assets/` dossier que vous utilisez pour le développement front-end
- `public/` dossier public de l'application
- `.env` fichier de configuration d'environnement base de données
- `console` fichier php des commandes [console](https://github.com/symfony/console)
- `webpack.config.js` fichier de configuration de webpack


## Guide d'utilisation :
- [Installation/Configuration](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter01.md)
- [Routeur/Container](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter02.md)
- [Controllers/Views/Middlewares](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter03.md)
- [Doctrine/Commandes](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter04.md)
- [Csrf/Token](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter05.md)
- [Front-End & Webpack](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter06.md)
- [RouterJS](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter07.md)
- [Debugger Bar](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter08.md)
- [Multilingue](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter09.md)
