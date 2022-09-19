# Csrf/Token

Dans ce chapitre nous allons parler de 2 middlewares mis en place dans ce skeleton.<br>
Il s'agit des middlewares `CsrfMiddleware` et `TokenMiddleware`.


### CsrfMiddleware
Ce middleware est implémenté via composer, il s'agit du middleware pour les failles csrf que slim nous propose d'utiliser.

Pour ceux qui ne savent pas ce que sont les failles csrf, je vous envoie [ici](https://fr.wikipedia.org/wiki/Cross-Site_Request_Forgery).


##### Les requêtes Post
Pour gérer la sécurité des formulaires, le middleware est activé sur toute les vues, vous le trouverez dans le fichier `app/config/routes.php`.<br>
Une fois ceci vérifié, vous n'avez qu'une chose à faire, c'est de faire appel à la fonction `csrf()` dans twig :
```html
<form class="" action="index.html" method="post">
  <input type="text" name="test" value="">
  <input type="submit" value="Envoyer">
  {{ csrf() }}
</form>
```
Cette fonction vas ajouter 2 input hidden comportant le token et la clef.

Une fois le formulaire rempli et envoyé, dans la partie controller vous aurez à faire un simple `if()` pour vérifier la validité du token :
```php
<?php
if (false === $request->getAttribute('csrf_status')) {
    // En cas d'erreur (token invalide)
} else {
    // En cas de token valide
}
```

##### Les requêtes Get
Il est aussi possible d'utiliser le middleware pour les requêtes get, mais celà peut sembler compliqué pour certains, du coup j'ai écris un middleware pour gérer cette partie.<br>
Toutefois si vous souhaitez utiliser celle-ci, je vous renvoie au [dépot github](https://github.com/slimphp/Slim-Csrf) qui en parle.


### TokenMiddleware
Middleware pour la gestion de token des requêtes `get`.<br>
Ce middleware permet de générer un token, utilisable dans votre vue twig avec la fonction `token()` qui retourne tout simplement le token de la session en cours.

Pour pouvoir vérifier la validitée du token côté controller, vous avez à votre disposition la fonction `tokenCheck($token)` qui se trouve dans le controller parent.<br>
Il vous retourne tout simplement `true` en cas de succès, `false` sinon.

| Introduction | Chapitre précédent | Chapitre suivant |
| :---------------------: | :--------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [Doctrine/Commandes](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter04.md) | [Front-End & Webpack](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter06.md) |
