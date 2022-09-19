# RouterJS

Le skeleton dispose d'un router pour le javascript, pour faire simple vous pouvez générer les urls des routes de slim grace à ce router dans votre code javascript (les fichiers twig uniquement).

Un fichier javascript va-t-être généré à partir d'une route nommé `routerjs`, il vous faudra ensuite l'ajouter au début de votre code javascript pour pouvoir l'utiliser :
```js
<script src="{{ path_for('routerjs') }}"></script>
```

Pour générer votre url il faudra utiliser la fonction `pathFor` dans l'objet javascript `Slim.Router` comme ceci :
```js
var url = Slim.Router.pathFor('leNomDeVotreRoute', {NomDeArgument: valeurDeArgument});
```

N'hésitez pas à aller voir l'exemple qui ce trouve dans le fichier [app/src/Views/pages/home.twig](https://github.com/SimonDevelop/slim-doctrine/blob/master/app/src/Views/pages/home.twig).

### NOTE
Si vous souhaitez renommé le nom du router, vous pouvez le faire dans le fichier [public/index.php](https://github.com/SimonDevelop/slim-doctrine/blob/master/public/index.php).

| Introduction | Chapitre précédent | Chapitre suivant |
| :----------: | :----------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [Front-End & Webpack](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter06.md) | [Multilingue](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter08.md) |
