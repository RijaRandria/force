<?php

use App\Controllers\HomeController;
use App\Controllers\VehiculesController;
use App\Controllers\ForceController;
use App\Controllers\BlogController;
use App\Controllers\ArticleController;
use App\Controllers\UserController;
use App\Controllers\ImagesController;

$app->get('/login',                             UserController::class.':Authentification')   ->setName('authentification');
$app->post('/login',                            UserController::class.':VerifAuth')          ->setName('verifauth');
$app->get('/changepwd',                         UserController::class.':changepwd')          ->setName('changepwd');
$app->get('/deconnexion',                       UserController::class.':Logout')             ->setName('deconnecte');
$app->get('/session',                           UserController::class.':Session')            ->setName('session');
$app->get('/droitinsuffisant',                  UserController::class.':DroitInsuffisant')   ->setName('droitinsuffisant');

$app->get('/',                                  ForceController::class. ':accueil')         ->setName('home');
$app->get('/sousfamille/{id}',                  BlogController::class.':SousFamille')       ->setName('sousfamille');
$app->get('/select',                            BlogController::class.':Select')            ->setName('select');
$app->get('/article/{id}',                      ArticleController::class.':Article')        ->setName('article');
$app->get('/article/carrousel/{id}',            ArticleController::class.':Carrousel')      ->setName('carousel');

// Route protégée permettant de modifier les articles
$app->group('/article', function() {
  $this->get('/ajouter/{idsousfamille}',        ArticleController::class.':FormNouvArticle')   ->setName('formnouvelarticle');
  $this->get('/supprimer/{id}/{sousfam}',       ArticleController::class.':SupprimerArticle')  ->setName('supprimerarticle');
  $this->post('/enregistrer',                   ArticleController::class.':EnregistreArticle') ->setName('enregistrerarticle');
  $this->get('/modifier/{id}',                  ArticleController::class.':Modifier')       	 ->setName('modifierarticle');
  $this->get('/ajoutimage/{id}',                ArticleController::class.':FormAjoutImage') 	 ->setName('affajouterimage');
  $this->post('/ajoutimage',        	          ArticleController::class.':AjoutImage')        ->setName('ajouterimage');
})->add($container->get('authadmin'));

// Route protégée exemple permettant de charger une image ou un fichier PDF en dehors de public
$app->group('/images', function() {
  $this->get('/charge/{nom}',                   ImagesController::class.':Charge')         ->setName('chargeimage');
  $this->get('/chargepdf/{nom}',                ImagesController::class.':ChargePDF')      ->setName('chargepdf');
  $this->get('/ousuisje',                       ImagesController::class.':OuSuisJe')       ->setName('ouisuisje');
})->add($container->get('auth'));

// Route protégée permettant de créer des usagers
$app->group('/user', function() {
  $this->get('/liste',                          UserController::class.':Liste')           ->setName('listeuser');
  $this->get('/ajouter',                        UserController::class.':FormAjout')       ->setName('formajoutuser');
  $this->post('/ajouter',                       UserController::class.':Ajout')           ->setName('ajouteruser');
})->add($container->get('authadmin'));

// Route permettant de changer mot de passe
$app->group('/user', function() {
    $this->post('/changepwd',                   UserController::class.':changepwd')   ->setName('changepwd');
    $this->post('/modifpwd',                    UserController::class.':modifpwd')    ->setName('modifpwd');
})->add($container->get('auth'));


$app->group('/marques', function ()  {

  $this->get('/twig',                           VehiculesController::class. ':TestPDO')     ->setName('pdo');
  $this->get('/vide',                           VehiculesController::class. ':Vide')        ->setName('vide');
  $this->get('/salarie',                        ForceController::class. ':salarie')         ->setName('salarie');
  $this->get('/lieu',                           ForceController::class. ':lieu')            ->setName('lieu');
  $this->get('/suivi',                          ForceController::class. ':suivi')           ->setName('suivi');



})->add($container->get('auth'));

$app->group('/marques', function ()  {
  $this->post('/search',                       ForceController::class. ':Search')        ->setName('recherche');
  $this->post('/insertsal',                    ForceController::class. ':insertsal')     ->setName('insertsal');
  $this->post('/insertchantier',               ForceController::class. ':insertchantier')->setName('insertchantier');
  $this->get('/search',                        ForceController::class. ':Requete')       ->setName('executionrecherche');
  $this->get('/newsalarie',                    ForceController::class. ':newsalarie')    ->setName('newsalarie');
  $this->get('/modifsalarie',                  ForceController::class. ':modifsalarie')  ->setName('modifsalarie');
  $this->get('/newchantier',                   ForceController::class. ':newchantier')   ->setName('newchantier');
  $this->get('/stat',                          ForceController::class. ':stat')          ->setName('stat');

})->add($container->get('authadmin'));

// $app->get('/', HomeController::class. ':getHome')->setName('home');
// Exemple pour le RouterJS
// $app->get('/hello/{name}', HomeController::class. ':getHome')->setName('hello');
?>
