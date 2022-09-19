# Installation/Configuration

Comme dit dans le fichier `README`, l'installation du skeleton ce fait par composer :
``` bash
$ composer create-project SimonDevelop/slim-doctrine <directory>
```

Une fois le projet téléchargé, rendez-vous dedans et effectuez un `composer install` pour installer les diverses librairies et assurez-vous que le projet est disponible en écriture pour votre serveur web (pour les utilisateurs linux/mac).

Dupliquez le fichier `.env.example` en `.env` et indiquez les informations concernant votre base de données.<br>
Vous disposez d'une configuration pour 2 bases de données, une pour le `dévelopement`, et l'autre pour la `production`, mais vous pouvez très bien vous en passer pour en utiliser qu'une seule :
```
# Database development
DBD_TYPE = "mysql" (votre type de base de données)
DBD_NAME = "example" (nom de votre base de données)
DBD_SERVER = "localhost" (host de votre base)
DBD_USER = "root" (login de votre base)
DBD_PWD = "root" (password de votre base)

# Database production
DBP_TYPE = "mysql"
DBP_NAME = "example"
DBP_SERVER = "localhost"
DBP_USER = "root"
DBP_PWD = "root"
```

Vous avez aussi les variables `ENV` et `CACHE`, la première vous permet de déterminer sur quelle base de données travailler, en développement ou production (`dev` ou `prod`).<br>

Pour le moment, la configuration doctrine est spécialement conçu pour l'utilisation d'une base de données MySQL ou MariaDB. L'utilisation de SQLite est possible, mais il vous faudra modifier certaines choses dans le container et la partie console (sujet hors tuto).<br>

La seconde détermine l'utilisation du cache de twig, il est préférable de le désactiver en développement (`false` pour désactiver, `true` pour activer).
```
# Environment mode
# ("dev" pour developpement | "prod" pour production)
ENV = "dev"

# Cache twig
CACHE = false
```

Vérifiez que votre base de données soit créée (via phpmyadmin ou autre) et qu'elle corresponde à votre configuration.<br>
Pour faire fonctionner votre application, voici les configurations coté serveur web :

#### apache2
```
<VirtualHost *:8080>
    ServerName 127.0.0.1:8080
    DocumentRoot "/path/to/project/slim-doctrine/public/"
    <Directory "/path/to/project/slim-doctrine/public/">
        Options -Indexes +FollowSymLinks
        AllowOverride all
        Order allow,deny
        Allow from all
        Require all granted
    </Directory>
</VirtualHost>
```

#### nginx
```
server {
    listen 8080;
    server_name localhost;
    index index.php;
    error_log /path/to/example.error.log;
    access_log /path/to/example.access.log;
    root /path/to/project/slim-doctrine/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^/.+\.php(/|$) {
        try_files $uri /index.php = 404;
	      fastcgi_split_path_info ^(.+\.php)(/.+)$;

        fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;

        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Petit rappel, ce skeleton à besoin d'une version php 7.1 ou plus pour fonctionner correctement.

| Introduction | Chapitre suivant |
| :---------------------: | :--------------: |
| [Introduction](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/introduction.md) | [Routeur/Container](https://github.com/SimonDevelop/slim-doctrine/blob/master/docs/chapter02.md) |
