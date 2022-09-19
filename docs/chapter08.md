# Debugger Bar

Le skeleton dispose du debugger bar `tracy`, il vous permet de voir en un clin d'œil divers informations sur vos vues de votre application, les requêtes doctrine effectuées, le chargement de twig, les redirections ect...

Lors du premier lancement, `tracy` affiche uniquement les millisecondes et des infos systèmes, pour afficher la totalité des informations que vous aurez besoin cliquez sur l'icone à coté de la petite croix pour qu'un fenêtre avec une liste de checkbox s'affiche. Cliquez sur `Toggle All` puis sur le bouton `Set`.

Il est à noté que l'option `Console Panel` ne fonctionne pas, et il est inutile de cocher les options `Eloquent ORM Panel` et `Idiorm Panel ` vu que dans ce skeleton nous utilisont doctrine comme orm.

![tracy](https://miroir.horyzone.fr/upload/tracy.png)

### Déboguer
Pour déboguer pendant le développement de votre application, dans vos `controllers/middlewares` je vous recommande d'utiliser la fonction `r()` qui est un `var_dump` amélioré, en revanche si vous voulez déboguer directement sur twig vous avez la function `dump()` qui est activé quand vous êtes en environnement de développement `dev`.

| Introduction | Chapitre précédent | Chapitre suivant |
| :----------: | :----------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [RouterJS](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter07.md) | [Multilingue](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter09.md) |
