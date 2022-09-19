<?php

use App\Controllers\HomeController;
use App\Controllers\VehiculesController;
use App\Controllers\ForceController;
use App\Controllers\ForceinsertController;
use App\Controllers\ForcenewController;
use App\Controllers\ForcemodifController;
use App\Controllers\BlogController;
use App\Controllers\ArticleController;
use App\Controllers\UserController;
use App\Controllers\ImagesController;

$app->get('/login',                             UserController::class.':Authentification')          ->setName('authentification');
$app->post('/login',                            UserController::class.':VerifAuth')                 ->setName('verifauth');
$app->get('/changepwd',                         UserController::class.':changepwd')                 ->setName('changepwd');
$app->get('/deconnexion',                       UserController::class.':Logout')                    ->setName('deconnecte');
$app->get('/session',                           UserController::class.':Session')                   ->setName('session');
$app->get('/droitinsuffisant',                  UserController::class.':DroitInsuffisant')          ->setName('droitinsuffisant');

$app->get('/',                                  ForceController::class. ':accueil')                 ->setName('home');

// Route protégée exemple permettant de charger une image ou un fichier PDF en dehors de public

$app->group('/images', function() {
  $this->get('/charge/{nom}',                   ImagesController::class.':Charge')                  ->setName('chargeimage');
  $this->get('/chargepdf/{nom}',                ImagesController::class.':ChargePDF')               ->setName('chargepdf');
  $this->get('/ousuisje',                       ImagesController::class.':OuSuisJe')                ->setName('ouisuisje');
})->add($container->get('auth'));

// Route protégée permettant de créer des usagers et créer les saisies table

$app->group('/user', function() {
  $this->get('/liste',                          UserController::class.':Liste')                     ->setName('listeuser');
  $this->get('/DocList',                        UserController::class.':DocList')                   ->setName('DocList');
  $this->get('/permis',                         UserController::class.':permis')                    ->setName('permis');
  $this->get('/formation',                      UserController::class.':formation')                 ->setName('formation');
  $this->get('/locomotion',                     UserController::class.':locomotion')                ->setName('locomotion');
  $this->get('/etudes',                         UserController::class.':etudes')                    ->setName('etudes');
  $this->get('/situation',                      UserController::class.':situation')                 ->setName('situation');
  $this->get('/ressources',                     UserController::class.':ressources')                ->setName('ressources');
  $this->get('/pieceList',                      UserController::class.':pieceList')                 ->setName('pieceList');
  $this->get('/typesortie',                     UserController::class.':typesortie')                ->setName('typesortie');
  $this->get('/detailsortie',                   UserController::class.':detailsortie')              ->setName('detailsortie');
  $this->get('/motifsortie',                    UserController::class.':motifsortie')               ->setName('motifsortie');
  $this->get('/typedemarche',                   UserController::class.':typedemarche')              ->setName('typedemarche');
  $this->get('/detaildemarche',                 UserController::class.':detaildemarche')            ->setName('detaildemarche');
  $this->get('/detailtypedemarche',             UserController::class.':detailtypedemarche')        ->setName('detailtypedemarche');
  $this->get('/presence',                       UserController::class.':presence')                  ->setName('presence');
  $this->get('/zone',                           userController::class. ':zone')                     ->setName('zone');
  $this->get('/sanction',                       userController::class. ':sanction')                 ->setName('sanction');
  $this->get('/motifsanction',                  userController::class. ':motifsanction')            ->setName('motifsanction');
  $this->get('/ajouter',                        UserController::class.':FormAjout')                 ->setName('formajoutuser');
  $this->post('/ajouter',                       UserController::class.':Ajout')                     ->setName('ajouteruser');
  $this->get('/frein',                          UserController::class.':frein')                     ->setName('frein');
  $this->get('/projet',                         UserController::class.':projet')                    ->setName('projet');
  $this->get('/objectif',                       UserController::class.':objectif')                  ->setName('objectif');
  


})->add($container->get('authadmin'));


// Route permettant de changer mot de passe
$app->group('/user', function() {
    $this->post('/changepwd',                   UserController::class.':changepwd')                 ->setName('changepwd');
    $this->post('/modifpwd',                    UserController::class.':modifpwd')                  ->setName('modifpwd');
})->add($container->get('auth'));


$app->group('/Force', function ()  {

  $this->get('/twig',                                   VehiculesController::class. ':TestPDO')              ->setName('pdo');
  $this->get('/vide',                                   VehiculesController::class. ':Vide')                 ->setName('vide');
  
  $this->get('/lieu',                                           ForceController::class. ':lieu')                     ->setName('lieu');
  $this->get('/societe',                                        ForceController::class. ':societe')                  ->setName('societe');
  $this->get('/suivi',                                          ForceController::class. ':suivi')                    ->setName('suivi');
  $this->get('/suivisalarie/{idsalarie}',                       ForceController::class. ':suivisalarie')             ->setName('suivisalarie');
  $this->get('/suivisalarie',                                   ForceController::class. ':suivisalarie')             ->setName('suivisalarie');
  $this->get('/entretiensalarie/{idsalarie}',                   ForceController::class. ':entretiensalarie')         ->setName('entretiensalarie');
  $this->get('/demarchesalarie/{idsalarie}',                    ForceController::class. ':demarchesalarie')          ->setName('demarchesalarie');
  $this->get('/entretiensalarie',                               ForceController::class. ':entretiensalarie')         ->setName('entretiensalarie');
  $this->get('/demarchesalarie',                                ForceController::class. ':demarchesalarie')          ->setName('demarchesalarie');
  $this->get('/diagnosticsalarie/{idsalarie}',                  ForceController::class. ':diagnosticsalarie')        ->setName('diagnosticsalarie');
  $this->get('/diagnosticsalarie',                              ForceController::class. ':diagnosticsalarie')        ->setName('diagnosticsalarie');
  $this->get('/salariechantier/{idlieu}',                       ForceController::class. ':salariechantier')          ->setName('salariechantier');
  $this->get('/salariechantier',                                ForceController::class. ':salariechantier')          ->setName('salariechantier');
  $this->get('/listdocsal/{idlieu}',                            ForceController::class. ':listdocsal')               ->setName('listdocsal');
  $this->get('/newdiag',                                        ForceController::class. ':newdiag')                  ->setName('newdiag');
  $this->get('/pmsmpquery',                                     ForceController::class. ':pmsmpquery')               ->setName('pmsmpquery');
  $this->get('/fincontrat',                                     ForceController::class. ':fincontrat')               ->setName('fincontrat');
  $this->get('/finsortie',                                      ForceController::class. ':finsortie')                ->setName('finsortie');
  $this->get('/finmutuelle',                                    ForceController::class. ':finmutuelle')              ->setName('finmutuelle');
  $this->get('/finsejour',                                      ForceController::class. ':finsejour')                ->setName('finsejour');
  $this->get('/finametra',                                      ForceController::class. ':finametra')                ->setName('finametra');
  $this->get('/signataireC/{idsalarie},{typecontrat}',          ForceController::class. ':signataireC')              ->setName('signataireC');
  $this->get('/signataireA/{idsalarie},{newdebdate},{newfin}',  ForceController::class. ':signataireA')              ->setName('signataireA');
  $this->get('/recapentretien',                                 ForceController::class. ':recapentretien')           ->setName('recapentretien');

  $this->post('/insertentretien',                      ForceController::class. ':insertentretien')           ->setName('insertentretien');
  $this->post('/insertsuivi',                           ForceController::class. ':insertsuivi')               ->setName('insertsuivi');
  $this->post('/insertdiagnostic',                      ForceController::class. ':insertdiagnostic')          ->setName('insertdiagnostic');
  $this->post('/insertdemarchesalarie',                 ForceController::class. ':insertdemarchesalarie')     ->setName('insertdemarchesalarie');
  $this->post('/insertdiag',                            ForceController::class. ':insertdiag')                ->setName('insertdiag');
 
////////modif suivi et entretien//////////////

  $this->get('/modifsuivi/{idsuivi},{idsala}',          ForceController::class. ':modifsuivi')                ->setName('modifsuivi');
  $this->get('/supprsuivi/{idsuivi},{idsala}',          ForceController::class. ':supprsuivi')                ->setName('supprsuivi');
  $this->get('/supprentretien/{identretien},{idsala}',  ForceController::class. ':supprentretien')            ->setName('supprentretien');
  $this->get('/modifentretien/{identretien},{idsala}',  ForceController::class. ':modifentretien')            ->setName('modifentretien');
  $this->get('/supprobjectif/{idobjsal},{idsala}',      ForceController::class. ':supprobjectif')             ->setName('supprobjectif');
  $this->get('/supprprojet/{idprojsal},{idsala}',       ForceController::class. ':supprprojet')               ->setName('supprprojet');
  $this->post('/insertmodifsuivi',                      ForceController::class. ':insertmodifsuivi')          ->setName('insertmodifsuivi');
  $this->post('/updatemodifentretien',                  ForceController::class. ':updatemodifentretien')      ->setName('updatemodifentretien');
  $this->post('/updatepmsmp',                           ForceController::class. ':updatepmsmp')               ->setName('updatepmsmp');



  ///////Suppression et modification sanction salarié//////////////
  $this->get('/supprsanction/{idsanctionsal},{idsala}', ForceController::class. ':supprsanction')             ->setName('supprsanction');
  $this->get('/modifsanction/{idsanctionsal},{idsala}', ForceController::class. ':modifsanction')             ->setName('modifsanction');

///////Suppression formation salarié//////////////
$this->get('/supprformationsal/{idcqp},{idsalarie}',    ForceController::class. ':supprformationsal')         ->setName('supprformationsal');


///////Suppression et modification pmsmp salarié//////////////
$this->get('/supprpmsmp/{id},{idsalarie}',              ForceController::class. ':supprpmsmp')                ->setName('supprpmsmp');
$this->get('/modifpmsmp/{id},{idsalarie}',              ForceController::class. ':modifpmsmp')                ->setName('modifpmsmp');
  

///////Suppression et modification renouvellement salarié//////////////

  $this->get('/supprrenewsal/{idrenewsal},{idsalarie}', ForceController::class. ':supprrenewsal')             ->setName('supprrenewsal');
  $this->get('/modifrenewsal/{idrenewsal},{idsalarie}', ForceController::class. ':modifrenewsal')             ->setName('modifrenewsal');

///////Suppression démarche salarié//////////////

$this->get('/supprdemarchesal/{iddemsal},{idsala}',     ForceController::class. ':supprdemarchesal')          ->setName('supprdemarchesal');

///////Suppression formation chantier//////////////

$this->get('/supprdatechantier/{iddatechant},{idlieut}', ForceController::class. ':supprdatechant')           ->setName('supprdatechant');

///////liste documents à fournir salarié
$this->get('/lisdocsal',                                 ForceController::class. ':listdocsal')               ->setName('listdocsal');




  $this->post('/search',                                ForceController::class. ':Search')                   ->setName('recherche');
  $this->post('/freinsalarie',                          ForceController::class. ':freinsalarie')             ->setName('freinsalarie');
  $this->post('/objectifsalarie',                       ForceController::class. ':objectifsalarie')          ->setName('objectifsalarie');
  $this->post('/projetsalarie',                         ForceController::class. ':projetsalarie')            ->setName('projetsalarie');
  $this->post('/trisociete',                            ForceController::class. ':trisociete')               ->setName('trisociete')   ;
  $this->post('/modifrenewsal',                         ForceController::class. ':modifrenewsal')            ->setName('modifrenewsal')   ;




})->add($container->get('auth'));

$app->group('/Force', function ()  {

  /* ICI COMMENCE LES ROUTES POUR LES USERS ADMIN */

      /* ROUTE Groupe INSERT */

  $this->post('/insertsal',                             ForceController::class. ':insertsal')                ->setName('insertsal');
  $this->post('/updatesal',                             ForceController::class. ':updatesal')              ->setName('updatesal');
  $this->post('/insertsortiesal',                       ForceController::class. ':insertsortiesal')        ->setName('insertsortiesal');
  $this->post('/insertsanctionsal',                     ForceController::class. ':insertsanctionsal')      ->setName('insertsanctionsal');
  $this->post('/insertchantier',                        ForceController::class. ':insertchantier')         ->setName('insertchantier');
  $this->post('/insertmodifchantier',                   ForceController::class. ':insertmodifchantier')    ->setName('insertmodifchantier');
  $this->post('/insertdoc',                             ForceController::class. ':insertdoc')              ->setName('insertdoc');
  $this->post('/insertpresence',                        ForceController::class. ':insertpresence')         ->setName('insertpresence');
  $this->post('/insertpermis',                          ForceController::class. ':insertpermis')           ->setName('insertpermis');
  $this->post('/insertzone',                            ForceController::class. ':insertzone')             ->setName('insertzone');
  $this->post('/insertsanction',                        ForceController::class. ':insertsanction')         ->setName('insertsanction');
  $this->post('/insertmotifsanction',                   ForceController::class. ':insertmotifsanction')    ->setName('insertmotifsanction');
  $this->post('/insertsortie',                          ForceController::class. ':insertsortie')           ->setName('insertsortie');
  $this->post('/insertmotif',                           ForceController::class. ':insertmotif')            ->setName('insertmotif');
  $this->post('/insertdetailsortie',                    ForceController::class. ':insertdetailsortie')     ->setName('insertdetailsortie');
  $this->post('/insertdetaildemarche',                  ForceController::class. ':insertdetaildemarche')   ->setName('insertdetaildemarche');
  $this->post('/insertdemarche',                        ForceController::class. ':insertdemarche')   ->setName('insertdemarche');
  $this->post('/insertdetailtypedemarche',              ForceController::class. ':insertdetailtypedemarche')->setName('insertdetailtypedemarche');
  $this->post('/insertformation',                       ForceController::class. ':insertformation')        ->setName('insertformation');
  $this->post('/insertlocomotion',                      ForceController::class. ':insertlocomotion')       ->setName('insertlocomotion');
  $this->post('/insertetudes',                          ForceController::class. ':insertetudes')           ->setName('insertetudes');
  $this->post('/insertsituation',                       ForceController::class. ':insertsituation')        ->setName('insertsituation');
  $this->post('/insertressources',                      ForceController::class. ':insertressources')       ->setName('insertressources');
  $this->post('/insertrenouv',                          ForceController::class. ':insertrenouv')           ->setName('insertrenouv');
  $this->post('/inserttitre',                           ForceController::class. ':inserttitre')            ->setName('inserttitre');
  $this->post('/insertcqpchantier',                     ForceController::class. ':insertcqpchantier')      ->setName('insertcqpchantier');
  $this->post('/inserttablecqp',                        ForceController::class. ':inserttablecqp')         ->setName('inserttablecqp');
  $this->post('/insertpmsmp',                           ForceController::class. ':insertpmsmp')            ->setName('insertpmsmp');
  $this->post('/insertprolpmsmp',                       ForceController::class. ':insertprolpmsmp')        ->setName('insertprolpmsmp');
  $this->post('/insertobjectif',                        ForceController::class. ':insertobjectif')         ->setName('insertobjectif');
  $this->post('/insertprojet',                          ForceController::class. ':insertprojet')           ->setName('insertprojet');
  $this->post('/statistique',                           ForceController::class. ':statistique')            ->setName('statistique');
  $this->post('/photo/{idsalarie}',                     ForceController::class. ':photo')                  ->setName('photo');
  $this->post('/contrat/{idsalarie}',                   ForceController::class. ':contrat')                ->setName('contrat');
  $this->post('/inmodifsalform/{idcqp}',                ForceinsertController::class. ':inmodifsalform')   ->setName('inmodifsalform');
  $this->get('/avenant/{idsalarie},{idrenewsal}',       ForceController::class. ':avenant')                ->setName('avenant');
  $this->post('/updaterenewsal',                        ForceController::class. ':updaterenewsal')         ->setName('updaterenewsal');



      /* fin INSERT  */

      /* ROUTE Groupe NEW  */


  $this->get('/newsalarie',                             ForceController::class. ':newsalarie')             ->setName('newsalarie');
  $this->get('/newchantier',                            ForceController::class. ':newchantier')            ->setName('newchantier');
  $this->get('/newdoc',                                 ForceinsertController::class. ':newdoc')           ->setName('newdoc');
  $this->get('/newpres',                                ForceController::class. ':newpres')                ->setName('newpres');
  $this->get('/newpermis',                              ForceController::class. ':newpermis')              ->setName('newpermis');
  $this->get('/newzone',                                ForceController::class. ':newzone')                ->setName('newzone');
  $this->get('/newsanction',                            ForceController::class. ':newsanction')            ->setName('newsanction');
  $this->get('/newmotifsanction',                       ForceController::class. ':newmotifsanction')       ->setName('newmotifsanction');
  $this->get('/newsortie',                              ForceController::class. ':newsortie')              ->setName('newsortie');
  $this->get('/newdemarche',                            ForceController::class. ':newdemarche')            ->setName('newdemarche');
  $this->get('/newidentite',                            ForceController::class. ':newidentite')            ->setName('newidentite');
  $this->get('/newmotif',                               ForceController::class. ':newmotif')               ->setName('newmotif');
  $this->get('/newdetailsortie',                        ForceController::class. ':newdetailsortie')        ->setName('newdetailsortie');
  $this->get('/newdetaildemarche',                      ForceController::class. ':newdetaildemarche')      ->setName('newdetaildemarche');
  $this->get('/newdetailtypedemarche',                  ForceController::class. ':newdetailtypedemarche')  ->setName('newdetailtypedemarche');
  $this->get('/newformation',                           ForceController::class. ':newformation')           ->setName('newformation');
  $this->get('/newlocomotion',                          ForceController::class. ':newlocomotion')          ->setName('newlocomotion');
  $this->get('/newetudes',                              ForceController::class. ':newetudes')              ->setName('newetudes');
  $this->get('/newsituation',                           ForceController::class. ':newsituation')           ->setName('newsituation');
  $this->get('/newressources',                          ForceController::class. ':newressources')          ->setName('newressources');
  $this->get('/newobjectif',                            ForceController::class. ':newobjectif')            ->setName('newobjectif');
  $this->get('/newprojet',                              ForceController::class. ':newprojet')              ->setName('newprojet');
  

      /*   Fin NEW  */

      /* ROUTE MODIFICATION  */

  $this->get('/modifsalarie/{idsalarie}',      ForceController::class. ':modifsalarie')           ->setName('modifsalarie');
  $this->get('/modifsalarie',                  ForceController::class. ':modifsalarie')           ->setName('modifsalarie');
  $this->get('/modifchantier/{idlieu}',        ForceController::class. ':modifchantier')          ->setName('modifchantier');

      /* FIN ROUTE MODIFICATION */

      /* route manipulation SALARIE  */

  $this->post('/docsalarie',                   ForceController::class. ':docsalarie')             ->setName('docsalarie');
  $this->post('/okdelsalarie',                 ForceController::class. ':okdelsalarie')           ->setName('okdelsalarie');
  $this->get('/editsalarie/{idsalarie}',       ForceController::class. ':editsalarie')            ->setName('editsalarie');
  $this->get('/editsalarie',                   ForceController::class. ':editsalarie')            ->setName('editsalarie');
  $this->get('/sortiesalarie/{idsalarie}',     ForceController::class. ':sortiesalarie')          ->setName('sortiesalarie');
  $this->get('/sanctionsalarie/{idsalarie}',   ForceController::class. ':sanctionsalarie')        ->setName('sanctionsalarie');
  $this->get('/delsalarie/{idsalarie}',        ForceController::class. ':delsalarie')             ->setName('delsalarie');
  $this->get('/sortiesalarie',                 ForceController::class. ':sortiesalarie')          ->setName('sortiesalarie');
  $this->get('/renouvellement',                ForceController::class. ':renouvellement')         ->setName('renouvellement');
  $this->get('/formationsalarie',              ForceController::class. ':formationsalarie')       ->setName('formationsalarie');
  $this->get('/statfiltre',                    ForceController::class. ':statfiltre')             ->setName('statfiltre');
  $this->get('/salarie',                       ForceController::class. ':salarie')                ->setName('salarie');

  /// Supression sanction salarié

  $this->get('/delsanctionsalarie/{idsanctionsal}', ForceController::class. ':delsanctionsalarie') ->setName('delsanctionsalarie');


      /* FIN ROUTE SALARIE */

      /* ROUTES DIVERSES  */

  $this->get('/search',                                           ForceController::class. ':Requete')                ->setName('executionrecherche');
  $this->get('/stat',                                             ForceController::class. ':stat')                   ->setName('stat');
  $this->get('/DocList',                                          ForceController::class. ':DocList')                ->setName('DocList');
  $this->get('/permis',                                           ForceController::class. ':permis')                 ->setName('permis');
  $this->get('/formation',                                        ForceController::class. ':formation')              ->setName('formation');
  $this->get('/locomotion',                                       ForceController::class. ':locomotion')             ->setName('locomotion');
  $this->get('/etudes',                                           ForceController::class. ':etudes')                 ->setName('etudes');
  $this->get('/document',                                         ForceController::class. ':document')               ->setName('document');
  $this->get('/cqpchantier/{idlieu}',                             ForceController::class. ':cqpchantier')            ->setName('cqpchantier');
 
  $this->get('/salarieformation/{idsalarie}',                     ForceController::class. ':salarieformation')       ->setName('salarieformation');
  $this->get('/updatesalarieformation/{idcqp}',                   ForceController::class. ':updatesalarieformation') ->setName('updatesalarieformation');
  $this->get('/supprsalarieformation/{idcqp}',                    ForceController::class. ':supprsalarieformation')  ->setName('supprsalarieformation');
  $this->get('/realformation',                                    ForceController::class. ':realformation')          ->setName('realformation');
  
  
      /* FIN ROUTES DIVERSES  */

    /* FIN ROUTES POUR USER ADMIN  */

})->add($container->get('authadmin'));

// $app->get('/', HomeController::class. ':getHome')->setName('home');
// Exemple pour le RouterJS
// $app->get('/hello/{name}', HomeController::class. ':getHome')->setName('hello');
?>
