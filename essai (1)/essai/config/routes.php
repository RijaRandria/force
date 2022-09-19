<?php

use App\Controllers\HomeController;
use App\Controllers\VehiculesController;
use App\Controllers\BlogController;
use App\Controllers\UserController;
//$_SESSION['estconnecte'] = true;
//unset($_SESSION['estconnecte']);

$app->get('/login',             UserController::class.':Authentification')  ->setName('authentification');
$app->post('/login',            UserController::class.':VerifAuth')         ->setName('verifauth');
$app->get('/deconnexion',       UserController::class.':Logout')            ->setName('deconnecte');
$app->get('/session',           UserController::class.':Session')            ->setName('session');
$app->get('/droitinsuffisant',  UserController::class.':DroitInsuffisant')   ->setName('droitinsuffisant');

$app->get('/',                                BlogController::class.':Famille')           ->setName('home');
$app->get('/sousfamille/{id}',                BlogController::class.':SousFamille')       ->setName('sousfamille');
$app->get('/article/ajouter/{idsousfamille}', BlogController::class.':FormNouvArticle')   ->setName('formnouvelarticle');
$app->get('/article/supprimer/{id}/{sousfam}',BlogController::class.':SupprimerArticle')  ->setName('supprimerarticle');
$app->post('/article/ajouter',                BlogController::class.':AjoutArticle')      ->setName('ajouterarticle');
$app->get('/article/ajoutimage/{id}',         BlogController::class.':FormAjoutImage') 	  ->setName('affajouterimage');
$app->post('/article/ajoutimage',        	    BlogController::class.':AjoutImage')        ->setName('ajouterimage');
$app->get('/article/{id}',                    BlogController::class.':Article')           ->setName('article');
$app->get('/article/carrousel/{id}',          BlogController::class.':Carrousel')         ->setName('carousel');
$app->get('/select',                          BlogController::class.':Select')            ->setName('select');

$app->group('/marques', function ()  {
  $this->get('/renault', VehiculesController::class. ':Renault')   ->setName('nomrenault');
  $this->get('/citroen', VehiculesController::class. ':Citroen')   ->setName('nomcitroen');
  $this->get('/audi',    VehiculesController::class. ':Audi')      ->setName('nomaudi');
  $this->get('/skoda',   VehiculesController::class. ':Skoda')     ->setName('nomskoda');
  $this->get('/twig',    VehiculesController::class. ':TestPDO')   ->setName('pdo');
  $this->get('/vide',    VehiculesController::class. ':Vide')      ->setName('vide');
})->add($container->get('auth'));

$app->group('/marques', function ()  {
  $this->get('/search',  VehiculesController::class. ':Search')    ->setName('recherche');
  $this->post('/search', VehiculesController::class. ':Requete')   ->setName('reponse');
})->add($container->get('authadmin'));

// $app->get('/', HomeController::class. ':getHome')->setName('home');
// Exemple pour le RouterJS
// $app->get('/hello/{name}', HomeController::class. ':getHome')->setName('hello');
?>
