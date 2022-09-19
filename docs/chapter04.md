# Doctrine/Commandes
Maintenant que nous avons vu les bases de fonctionnement du skeleton, attaquons maintenant doctrine et les diverses commandes utiles.


### Doctrine
Doctrine est l'ORM utilisée dans ce skeleton, vous avez à votre disposition les entités dans le dossier `app/src/Entity`.<br>
Vous avez accès à  l'`EntityManager` dans vos controller via un `$this->em;`

Voici la commande doctrine que vous aurez besoin pour déclarer vos entités en base (Vérifiez avoir bien créé votre base de données au préalable):

``` bash
$ php vendor/bin/doctrine orm:schema-tool:update
```

Pour les développeurs sous windows, il se peut que cette commande ne fonctionne pas selon la console que vous utilisez.<br>
Vous avez cette commande alternative:

``` bash
$ php vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:update
```

Vous pouvez envoyer des données fictives en base de données via la librairie `data-fixtures` de doctrine, créez vos fixtures dans le dossier `app/src/Entity/DataFixtures` puis lancez la commande :

``` bash
$ php console data:fixtures
```

L'implémentation des DataFixtures se fait via l'EntityManager de doctrine.<br>
Pour plus d'infos sur l'ORM, je vous invite à aller voir la [documentation](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/).


### Commandes

Grâce à la librairie [Console](https://github.com/symfony/console), le skeleton vous offre des commandes pour la création rapide de `Controllers` ,`Middlewares` ,`Entity`, `fixture` et vidage du cache twig.

C'est le fichier `console` qui vous permet d'utiliser les commandes suivantes :

Pour voir la liste des commandes disponibles :
``` bash
$ php console list
```

Pour vider le cache de twig
``` bash
$ php console cache:clear
```

Pour générer un controller ou middleware
``` bash
$ php console generate:controller TestController
```
`app/src/Controllers/TestController.php`

``` bash
$ php console generate:middleware TestMiddleware
```
`app/src/Middlewares/TestMiddleware.php`

``` bash
$ php console generate:entity Test
```
`app/src/Entity/Test.php`

``` bash
$ php console generate:fixture TestFixture
```
`app/src/Entity/DataFixtures/TestFixture.php`

### Cli-menu

Grâce à la librairie [cli-menu](https://github.com/php-school/cli-menu), vous disposez d'un menu via la console pour exécuter les divers commandes cités plus haut, pratique en cas d'oubli !

#### NOTE
[cli-menu](https://github.com/php-school/cli-menu) utilise l'extension php posix qui n'est pas supporté sur windows, cette partie est donc utile pour les développeurs Linux et Mac OS seulement.

Pour accèder au menu, lancer juste :
``` bash
$ php console
```

Pour la génération des `Controllers` ,`Middlewares`, `Entity` et `fixture`, leur nom vous sera demandé par la suite.

| Introduction | Chapitre précédent | Chapitre suivant |
| :---------------------: | :--------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [Controllers/Views/Middlewares](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter03.md) | [Csrf/Token](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter05.md) |
