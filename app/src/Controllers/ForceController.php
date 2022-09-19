<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;


class ForceController extends Controller
{

  //Méthode accueil
  // Affichage interface accueil

  ///requête préparée
  /**
   * @param 
   * 
   * 
   * 
   */
  public function accueil(RequestInterface $request, ResponseInterface $response)
  {
   
   
    setlocale(LC_TIME, "fr_FR");
    $dateout        = strtotime("today");
    $datein         = strtotime("today");
    $datefincontrat = strtotime("+60 days");
    $vdatefin       = date("Y/m/d",$datefincontrat);
    $mutuelle       = strtotime("+60 days");
    $vdateametra    = strtotime("-7 days");
    $vdatein        = date("d/m/Y",$datein);
    $vdateout       = date("d/m/Y",$dateout);
    $vdate          = date("d/m/y",today);
    $vvdate         = date("Y/m/d",today);
    $vdate1         = utf8_encode(strftime('%A %d %B %Y'));
    $annee          = date_parse($vdateout);
    $vannee1        = date('Y',$dateout);
    $vannee         = $annee[year];
    
    $requete = $this->pdo->prepare("SELECT COUNT(id) FROM salarie
                                    
                                  ");
    $requete->execute();
    $genre = $requete->fetch();
    $tout=$genre[0];

    $requete = $this->pdo->prepare("SELECT COUNT(id) FROM salarie WHERE genre = 'Mr'");
    $requete->execute();
    $man = $requete->fetch();
    $homme=$man[0];
   
    $dame=$tout-$homme;
    $pdame=($dame*100)/$tout;
    $phomme=($homme*100)/$tout;

    $pdame=sprintf("%.2f",$pdame);
    $phomme=sprintf("%.2f",$phomme);


    $requete = $this->pdo->prepare("SELECT * FROM lieutravail order by lieu ASC"  );
    $requete->execute();
    $lieux = $requete->fetchAll();
    foreach($lieux as $lieu){
      $chantier=$lieu[idlieu];
      $nomchantier=$lieu[lieu];

    }
    /////////

     $requete = $this->pdo->prepare("SELECT * FROM salarie
                                    
                                   ");
     $requete->execute();
     $salaries = $requete->fetchAll();

     $requete = $this->pdo->prepare("SELECT * FROM trancheage");
     $requete->execute();
     $trancheages = $requete->fetchAll();

     $requete = $this->pdo->prepare("DROP table IF EXISTS new_salarie ");
     $requete->execute();
     $requete = $this->pdo->prepare("CREATE Table new_salarie as SELECT id, datesejour, nom, prenom,dateametra,datemutuelle,datefinreelle,datesortie FROM salarie
     
                                  ");
     $requete->execute();


    $requete = $this->pdo->prepare("SELECT datesejour,nom, prenom,dateametra,datemutuelle,datefinreelle,datesortie FROM new_salarie
                                  ");
    $requete->execute();
    $new_salarie = $requete->fetchAll();
    $requete = $this->pdo->prepare("SELECT count(id) FROM new_salarie
                                    WHERE  datefinreelle >= 0 AND datefinreelle <= $vdatefin
                                   ");
    $requete->execute();
    $countsortie = $requete->fetch();
   

   
    
    $requete = $this->pdo->prepare("SELECT datesejour,nom, prenom,dateametra,datemutuelle,lieu,datefinreelle,datesortie FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC

                                  ");
    $requete->execute();
    $sejours = $requete->fetchAll();


    $requete = $this->pdo->prepare("SELECT * FROM sortie
                                                                       
                                  ");
    $requete->execute();
    $sorties = $requete->fetchAll();
        
    
    /////////
    // $requete = $this->pdo->prepare("SELECT * FROM niveauetude");
    // $requete->execute();
    // $niveaux = $requete->fetchAll();

     $requete = $this->pdo->prepare("SELECT * FROM locomotion");
     $requete->execute();
     $locomotions = $requete->fetchAll();

     $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie");
     $requete->execute();
     $locosalaries = $requete->fetchAll();




    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/force.twig', ['titre'=>'Page d\'Accueil',
                                                  'homme'           =>  $homme,
                                                  'dame'            =>  $dame,
                                                  'pdame'           =>  $pdame,
                                                  'phomme'          =>  $phomme,
                                                  'tout'            =>  $tout,
                                                  'lieux'           =>  $lieux,
                                                  'nomchantier'     =>  $nomchantier,
                                                  'sales'           =>  $sales,
                                                  'salaries'        =>  $salaries,
                                                  'locosalaries'    =>  $locosalaries,
                                                  'locomotions'     =>  $locomotions,
                                                  'datein'          =>  $datein,
                                                  'dateout'         =>  $dateout,
                                                  'datefincontrat'  =>  $datefincontrat,
                                                  'vdateametra'     =>  $vdateametra,
                                                  'vdatein'         =>  $vdatein,
                                                  'vdateout'        =>  $vdateout,
                                                  'ametras'         =>  $ametras,
                                                  'sejours'         =>  $sejours,
                                                  'vdate'           => $vdate,
                                                  'vdate1'          => $vdate1,
                                                  'sorties'         => $sorties,
                                                  'trancheages'     => $trancheages
                                                  

                                                ]);
  }
  ///Méthode listdocsal
  /// affichage ou impression liste des documents à fournir par salariés
  
  public function listdocsal(RequestInterface $request, ResponseInterface $response,$args)
  {

    $datein  = strtotime("today");
    $vdatein = date("d/m/Y",$datein);
   
   
   
   //////
     //préparation requête table lieu pour extraction id
     $requete = $this->pdo->prepare("SELECT * FROM salarie LEFT JOIN lieutravail on salarie.id_lieu_travail = lieutravail.idlieu
                                       
     WHERE id_lieu_travail = ?
     ORDER BY DATESORTIE ASC
     
   ");
   
$requete->execute([$args['idlieu']]);
//$requete->execute();
$lignes = $requete->fetchAll();
foreach ($lignes as $ligne){

  $lieu = $ligne[lieu];
}
 
$requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie 
LEFT JOIN documents on docsafournirsalarie.iddocuments = documents.iddoc
");
$requete->execute();
$docs = $requete->fetchAll();

//die();


$titre = "Test Liste des Salariés Pour documents à fournir ".$lieu;

$this->render($response, 'force/listdocsalarie.twig', [ 'titre'   => $titre,
                                                        'lignes'  => $lignes,
                                                        'docs'    => $docs,
                                                        'lieu'    => $lieu,
                                                        'vdatein' => $vdatein
                                                      ] );
  }


  //Méthode fincontrat
  // Affichage la liste de salariés proche de la fin de contrat
  public function finsortie(RequestInterface $request, ResponseInterface $response)
  {

    $datein  = strtotime("today");
    $vdatein = date('01/m/Y',$datein);
    /*dump($vdatein);
    die();*/
    $dateout = strtotime("+60 days");
    /////////
    $requete = $this->pdo->prepare("SELECT nom, prenom,id_lieu_travail,datesortie FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC

                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/finsortie.twig', ['titre'=>'Liste Sortie ',
                                                      'salaries'          => $salaries,
                                                      'lieux'             => $lieux,
                                                      'datein'            => $datein,
                                                      'dateout'           => $dateout,
                                                      'vdatein'           => $vdatein 
                                                     ]);
  }  


  //Méthode fincontrat
  // Affichage la liste de salariés proche de la fin de contrat


  public function fincontrat(RequestInterface $request, ResponseInterface $response)
  {
    $datein  = strtotime("today");
    
    $vdatein = date('Y/m/01',$datein);
    
    //dump($vdatein);
    /*dump($vdatein);
    die();*/
    $dateout = strtotime("+60 days");
    $vdateout = date('Y/m/01',$dateout);
   
  
    /////////
    $requete = $this->pdo->prepare("SELECT nom, prenom,id_lieu_travail,datefinreelle,datesortie FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by datefinreelle ASC

                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/fincontrat.twig', ['titre'=>'Fin de contrat',
                                                  'salaries'          => $salaries,
                                                  'lieux'             => $lieux,
                                                  'datein'            => $datein,
                                                  'dateout'           => $dateout,
                                                  'vdatein'           => $vdatein,
                                                  'vdateout'          => $vdateout,

                                                                                                ]);
  }  

  //Méthode finmutuelle
  // Affichage la liste de salariés proche de la fin de date mutuelle
  public function finmutuelle(RequestInterface $request, ResponseInterface $response)
  {

    $html2pdf = new Html2Pdf('L', 'A4', 'fr');  
    $html2pdf->pdf->SetDisplayMode('fullpage');
   
    $datein  = strtotime("today");
    $vdatein = date("d/m/Y",$datein);
    $dateout = strtotime("+60 days");
    /////////
    $requete = $this->pdo->prepare("SELECT nom, prenom,id_lieu_travail,datemutuelle FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC
                                    
                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/finmutuelle.twig', ['titre'             => 'Alerte fin date mutuelle le',
                                                        'salaries'          => $salaries,
                                                        'lieux'             => $lieux,
                                                        'datein'            => $datein,
                                                        'dateout'           => $dateout
                                                                                                ]);
  }  
//Méthode finsejour
  // Affichage la liste de salariés proche de la fin de titre de séjour
  public function finsejour(RequestInterface $request, ResponseInterface $response)
  {

    $datein  = strtotime("today");
    $dateout = strtotime("+60 days");
    /////////
    $requete = $this->pdo->prepare("SELECT nom, prenom,id_lieu_travail,datesejour FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC

                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/finsejour.twig', ['titre'             => 'Alerte fin titre de séjour le',
                                                      'salaries'          => $salaries,
                                                      'lieux'             => $lieux,
                                                      'datein'            => $datein,
                                                      'dateout'           => $dateout
                                                                                                ]);
  }  
//Méthode finametra
  // Affichage la liste de salariés proche de la date ametra
  public function finametra(RequestInterface $request, ResponseInterface $response)
  {

    $datein  = strtotime("today");
    $dateout = strtotime("+7 days");
    /////////
    $requete = $this->pdo->prepare("SELECT nom, prenom,id_lieu_travail,dateametra FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC

                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/finametra.twig', ['titre'=>'Alerte date AMETRA le',
                                                  'salaries'          => $salaries,
                                                  'lieux'             => $lieux,
                                                  'datein'            => $datein,
                                                  'dateout'           => $dateout
                                                                                                ]);
  }  
///// Méthode statfiltre
/////choix d'un chantier pour extraction statistique

public function statfiltre(RequestInterface $request, ResponseInterface $response)
{
  $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();

  $this->render($response, 'force/statfiltre.twig', ['titre'=>'Sélection chantier pour statistique',
                                                   'lieux' => $lieux
                                                  ]);

}
  //Méthode statistique
  // Calcul et Affichage du stat global
  public function statistique(RequestInterface $request, ResponseInterface $response,$args)
  {
    $this->logger->info("==>satistique : ".$request->getParsedBody()['mail']);
    $idlieu = $request->getParsedBody()['idlieu'] ;
    $datedeb = $request->getParsedBody()['datedeb'] ;
    $datefin = $request->getParsedBody()['datefin'] ;
    dump($datedeb);
    dump($datefin);

    
    $requete = $this->pdo->prepare("SELECT * FROM lieutravail
                                    WHERE idlieu = $idlieu

                                  ");

    $requete->execute();
    $lieux = $requete->fetchAll();
    foreach ($lieux as $lieu) {
 
      $vlieu = $lieu[lieu];
    }
      
    $titre = "Statistique Global " .$vlieu;
    
    
   
    $requete = $this->pdo->prepare("SELECT * FROM niveauetude");
    $requete->execute();
    $niveaux = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM permis");
    $requete->execute();
    $permis = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM locomotion");
    $requete->execute();
    $locomotions = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM ressources");
    $requete->execute();
    $ressources = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM salarie
                                    
                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM trancheage");
    $requete->execute();
    $trancheages = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM situfamiliale");
    $requete->execute();
    $situfamiliales = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie");
    $requete->execute();
    $locomotionsalaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM permissalarie");
    $requete->execute();
    $permissalaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM dureechomage");
    $requete->execute();
    $chomages = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM objectifs");
    $requete->execute();
    $objectifs = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM projets");
    $requete->execute();
    $projets = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM objectifsalarie");
    $requete->execute();
    $objectifsalarie = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM projetsalarie
                                    ORDER BY idproj ASC");
    $requete->execute();
    $projetsalarie = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM demarche");
    $requete->execute();
    $demarches = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                    ORDER BY idtypedemarche ASC
                                  ");
    $requete->execute();
    $typedemarches = $requete->fetchAll();


    $requete = $this->pdo->prepare("SELECT * FROM demarchesalarie
                                    WHERE idlieu = $idlieu
                                    ORDER by idtypedemarsal ASC
                                   ");
    $requete->execute();
    $demarchesalarie = $requete->fetchAll();
   
    
    $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                    
                                  ");
    $requete->execute();
    $typedemarche = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM sortie");
    $requete->execute();
    $sorties = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM typesortie
                                    LEFT JOIN sortie ON typesortie.idsortie = sortie.idsortie
    ");
    $requete->execute();
    $typesorties = $requete->fetchAll();
    
    
    $requete = $this->pdo->prepare("SELECT * FROM motifsortie");
    $requete->execute();
    $motifsorties = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM zone");
    $requete->execute();
    $zones = $requete->fetchAll();
    $dateout        = strtotime("today");
    $datein         = strtotime("today");
    $datefincontrat = strtotime("+60 days");
    $mutuelle   = strtotime("+60 days");
    $vdateametra    = strtotime("-7 days");
    $vdatein        = date("Y",$datein);
    
    $vdateout       = date("d/m/Y",$dateout);
    $vdate          = date("d/m/y",today);
    $vdate1          = utf8_encode(strftime('%A %d %B %Y'));
    $annee          = date_parse($vdateout);
    $vannee1        = date('Y',$dateout);
    $vannee         = $annee[year];
   
    


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/stat.twig', ['titre'=> $titre ,
                                                  'ressources'          => $ressources,
                                                  'niveaux'             => $niveaux,
                                                  'salaries'            => $salaries,
                                                  'locomotions'         => $locomotions,
                                                  'permis'              => $permis,
                                                  'lieux'               => $lieux,
                                                  'chomages'            => $chomages,
                                                  'locomotionsalaries'  => $locomotionsalaries,
                                                  'permissalaries'      => $permissalaries,
                                                  'trancheages'         => $trancheages,
                                                  'situfamiliales'      => $situfamiliales,
                                                  'objectifs'           => $objectifs,
                                                  'projets'             => $projets,
                                                  'objectifsalarie'     => $objectifsalarie,
                                                  'projetsalarie'       => $projetsalarie,
                                                  'demarches'           => $demarches,
                                                  'typedemarches'       => $typedemarches,
                                                  'demarchesalarie'     => $demarchesalarie,
                                                  'typedemarche'        => $typedemarche,
                                                  'sorties'             => $sorties,
                                                  'typesorties'         => $typesorties,
                                                  'motifsorties'        => $motifsorties,
                                                  'typesalaries'        => $typesalaries,
                                                  'zones'               => $zones,
                                                  'vannee'              => $vannee,
                                                  'vlieu'               => $vlieu,
                                                  'idlieu'              => $idlieu,



                                                ]);
  }

  //Méthode statistique1
  // Affichage le stat global
  public function statistique1(RequestInterface $request, ResponseInterface $response)
  {
    /////////
   

    $requete = $this->pdo->prepare("SELECT * FROM niveauetude");
    $requete->execute();
    $niveaux = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM permis");
    $requete->execute();
    $permis = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM locomotion");
    $requete->execute();
    $locomotions = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM ressources");
    $requete->execute();
    $ressources = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM trancheage");
    $requete->execute();
    $trancheages = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM situfamiliale");
    $requete->execute();
    $situfamiliales = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie");
    $requete->execute();
    $locomotionsalaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM permissalarie");
    $requete->execute();
    $permissalaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM dureechomage");
    $requete->execute();
    $chomages = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM objectifs");
    $requete->execute();
    $objectifs = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM projets");
    $requete->execute();
    $projets = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM objectifsalarie
                                    WHERE idtravail = 3
                                  ");
    $requete->execute();
    $objectifsalarie = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM projetsalarie
                                    ORDER BY idproj ASC
                                    WHERE idtravail = 3
                                    ");
    $requete->execute();
    $projetsalarie = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM demarche");
    $requete->execute();
    $demarches = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM typedemarche");
    $requete->execute();
    $typedemarches = $requete->fetchAll();


    $requete = $this->pdo->prepare("SELECT * FROM demarchesalarie");
    $requete->execute();
    $demarchesalarie = $requete->fetchAll();


    $requete = $this->pdo->prepare("SELECT * FROM typedemarche");
    $requete->execute();
    $typedemarche = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM sortie");
    $requete->execute();
    $sorties = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM typesortie
                                    LEFT JOIN sortie ON typesortie.idsortie = sortie.idsortie
    ");
    $requete->execute();
    $typesorties = $requete->fetchAll();
    
    
    $requete = $this->pdo->prepare("SELECT * FROM motifsortie");
    $requete->execute();
    $motifsorties = $requete->fetchAll();

        $requete = $this->pdo->prepare("SELECT * FROM zone");
    $requete->execute();
    $zones = $requete->fetchAll();
    

   


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/stat.twig', ['titre'=>'Statistique Global',
                                                  'ressources'          => $ressources,
                                                  'niveaux'             => $niveaux,
                                                  'salaries'            => $salaries,
                                                  'locomotions'         => $locomotions,
                                                  'permis'              => $permis,
                                                  'lieux'               => $lieux,
                                                  'chomages'            => $chomages,
                                                  'locomotionsalaries'  => $locomotionsalaries,
                                                  'permissalaries'      => $permissalaries,
                                                  'trancheages'         => $trancheages,
                                                  'situfamiliales'      => $situfamiliales,
                                                  'objectifs'           => $objectifs,
                                                  'projets'             => $projets,
                                                  'objectifsalarie'     => $objectifsalarie,
                                                  'projetsalarie'       => $projetsalarie,
                                                  'demarches'           => $demarches,
                                                  'typedemarches'       => $typedemarches,
                                                  'demarchesalarie'     => $demarchesalarie,
                                                  'typedemarche'        => $typedemarche,
                                                  'sorties'             => $sorties,
                                                  'typesorties'         => $typesorties,
                                                  'motifsorties'        => $motifsorties,
                                                  'typesalaries'        => $typesalaries,
                                                  'zones'               => $zones,



                                                ]);
  }

  //Méthode newsalarie
  // Affichage interface pour saisie new salarié

  public function newsalarie(RequestInterface $request, ResponseInterface $response)
  {
    //Préparation liste chantiers
    $requete = $this->pdo->prepare("SELECT idlieu, lieu FROM lieutravail ");
    $requete->execute();
    $chantiers = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT nom, prenom from salarie");
    $requete->execute();
    $salaries = $requete->fetchAll();
    $sals = json_encode($salaries);
    
    
    //Préparation liste pièces d'identité
    $requete = $this->pdo->prepare("SELECT * FROM titre ");

    $requete->execute();
    $titres = $requete->fetchAll();
    //Préparation idtitre liste pièces d'identité
    foreach ($titres as $titre=>$idtitre) {

    }

    // var_dump($idtitre[idtitre]);
     $idtitre=$idtitre[idtitre];
     $idtitre=$idtitre+1;
    // var_dump($idtitre);
    //die();
    //Préparation liste tranche d'âge
    $requete = $this->pdo->prepare("SELECT * FROM trancheage ");
    $requete->execute();
    $tranches = $requete->fetchAll();

    //Préparation liste Niveau d'études
    $requete = $this->pdo->prepare("SELECT idetude, niveau FROM niveauetude ");
    $requete->execute();
    $niveaux = $requete->fetchAll();
    //Préparation liste durée de chômage
    $requete = $this->pdo->prepare("SELECT idchomage, duree FROM dureechomage ");
    $requete->execute();
    $durees = $requete->fetchAll();
    //Préparation liste ressources
    $requete = $this->pdo->prepare("SELECT idressources, ressources FROM ressources ");
    $requete->execute();
    $ressources = $requete->fetchAll();
    //Préparation liste situation familiale
    $requete = $this->pdo->prepare("SELECT idsitu, situation FROM situfamiliale ");
    $requete->execute();
    $situations = $requete->fetchAll();
    //Préparation liste locomotion
    $requete = $this->pdo->prepare("SELECT id, locomotion FROM locomotion ");
    $requete->execute();
    $locomotions = $requete->fetchAll();
    //Préparation permis
    $requete = $this->pdo->prepare("SELECT idpermis, permis FROM permis ");
    $requete->execute();
    $permis = $requete->fetchAll();
    //Préparation zone
    $requete = $this->pdo->prepare("SELECT * FROM zone ");
    $requete->execute();
    $zones = $requete->fetchAll();






    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newsalarie.twig', ['titre'      => 'Saisie Nouveau salarié' ,
                                                      'chantiers'   => $chantiers,
                                                      'niveaux'     => $niveaux,
                                                      'durees'      => $durees,
                                                      'ressources'  => $ressources,
                                                      'situations'  => $situations,
                                                      'locomotions' => $locomotions,
                                                      'permis'      => $permis,
                                                      'idtitre'     => $idtitre,
                                                      'tranches'    => $tranches,
                                                      'titres'      => $titres,
                                                      'zones'       => $zones,
                                                      'sals'        => $sals,
                                                      ]);


  }
 

  //Méthode newpres
  // Affichage interface pour saisie nouveau document à fournir
  public function Newpres(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/pres.twig', ['titre'=>'Saisie Nouveau Document à Fournir']);
  }

  //Méthode newdiag
  // Affichage interface pour saisie nouveau document à fournir
  public function Newdiag(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/insertdiag.twig', ['titre'=>'Saisie Nouvelle frein à l\'entrée en action']);
  }
  //Méthode newpermis
  // Affichage interface pour saisie nouveau type de permis
  public function Newpermis(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/permis.twig', ['titre'=>'Saisie type de permis']);
  }
  //Méthode newzone
  // Affichage interface pour saisie nouveau type de zone
  public function Newzone(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/zone.twig', ['titre'=>'Saisie type de zone']);
  }

  //Méthode newobjectif
  // Affichage interface pour saisie nouveau type de zone
  public function Newobjectif(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newobjectif.twig', ['titre'=>'Saisie type d\'objectif']);
  }

  //Méthode newprojet
  // Affichage interface pour saisie nouveau type de zone
  public function Newprojet(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newprojet.twig', ['titre'=>'Saisie type de projet']);
  }

  //Méthode newsanction
  // Affichage interface pour saisie nouveau type de sanction
  public function Newsanction(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/sanction.twig', ['titre'=>'Saisie sanction']);
  }

  //Méthode newmotifsanction
  // Affichage interface pour saisie nouveau type de sanction
  public function Newmotifsanction(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/motifsanction.twig', ['titre'=>'Saisie type de motif de sanction']);
  }


  //Méthode newsortie
  // Affichage interface pour saisie nouveau type de sortie
  public function Newsortie(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newsortie.twig', ['titre'=>'Saisie type de sortie']);
  }
  //Méthode newdemarche
  // Affichage interface pour saisie nouveau type de démarche
  public function Newdemarche(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newdemarche.twig', ['titre'=>'Saisie démarche']);
  }
  //Méthode newmotif
  // Affichage interface pour saisie nouveau type de motif de sortie
  public function Newmotif(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newmotif.twig', ['titre'=>'Saisie type de motif de sortie']);
  }
  //Méthode newdetailsortie
  // Affichage interface pour saisie nouveau type de sortie
  public function Newdetailsortie(RequestInterface $request, ResponseInterface $response)
  {
    //Préparation liste sortie
    $requete = $this->pdo->prepare("SELECT idsortie, sortie FROM sortie ");
    $requete->execute();
    $sorties = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newdetailsortie.twig', ['titre'=>'Saisie Détails de sorties','sorties'=>$sorties]);
  }
  //Méthode newdetaildemarche
  // Affichage interface pour saisie nouveau type de démarche
  public function Newdetaildemarche(RequestInterface $request, ResponseInterface $response)
  {
    //Préparation liste sortie
    $requete = $this->pdo->prepare("SELECT iddemarche, demarche FROM demarche ");
    $requete->execute();
    $demarches = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newdetaildemarche.twig', ['titre'=>'Saisie Type de démarches','demarches'=>$demarches]);
  }

  //Méthode newdetailtypedemarche
  // Affichage interface pour saisie nouveau type de démarche
  public function Newdetailtypedemarche(RequestInterface $request, ResponseInterface $response)
  {
    //Préparation liste demarche
    $requete = $this->pdo->prepare("SELECT * FROM demarche

                                  ");
    $requete->execute();
    $demarches = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM typedemarche

                                  ");
    $requete->execute();
    $typedemarches = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newdetailtypedemarche.twig', ['titre'=>'Saisie Détails Type de démarches','demarches'=>$demarches,'typedemarches'=>$typedemarches]);
  }

  //Méthode newformation
  // Affichage interface pour saisie nouveau type de formation
  public function Newformation(RequestInterface $request, ResponseInterface $response,$args)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/formation.twig', ['titre'=>'Saisie type de formation']);
  }

  //Méthode newlocomotion
  // Affichage interface pour saisie nouveau type de permis
  public function Newlocomotion(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/locomotion.twig', ['titre'=>'Saisie type de moyen de locomotion']);
  }
  //Méthode newetudes
  // Affichage interface pour saisie nouveau niveau d'études
  public function Newetudes(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/etudes.twig', ['titre'=>'Saisie niveaux d\'études']);
  }

  //Méthode newidentite
  // Affichage interface pour saisie nouvelle identité
  public function Newidentite(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/identite.twig', ['titre'=>'Saisie nouvelles pièces d\'identité ']);
  }

  //Méthode newressources
  // Affichage interface pour saisie nouvelle ressource
  public function Newressources(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/ressources.twig', ['titre'=>'Saisie nouvelles Ressources ']);
  }

  //Méthode renouvellement
  // Affichage interface pour saisie nouvelle date fe fin de contrat
  public function renouvellement(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/renouvellement.twig/#overview', ['titre'=>'Saisie niveaux d\'études','idsalarie'=>$idsalarie]);
  }

  //Méthode newsituation
  // Affichage interface pour saisie nouveau niveau d'études
  public function Newsituation(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/situation.twig', ['titre'=>'Saisie Nouvelle Situation familiale ']);
  }

  //Méthode newchantier
  // Affichage interface pour saisie new chantier
  public function newchantier(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newchantier.twig', ['titre'=>'Saisie Nouveau Chantier']);
  }

  //Méthode modifchantier
  // Affichage interface pour saisie new chantier
  public function modifchantier(RequestInterface $request, ResponseInterface $response,$args)
  {
    dump($args['idlieu']);

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail
                                    WHERE idlieu = ?

                                  ");

    $requete->execute([$args['idlieu']]);
    $lignes     = $requete->fetchAll();

    $idlieu     = $args['idlieu'];
    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/modifchantier.twig', ['titre'     =>'Modification Chantier',
                                                          'idlieu'    => $idlieu,
                                                          'lignes'    => $lignes,

                                                        ]);
  }

  //Méthode stat : création requête / statistique
  // Affichage interface pour saisie new chantier
  public function stat(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newstat.twig', ['titre'=>'Statistiques']);
  }

  // Méthode lieu
  // permet d'afficher les lieux de travail
  public function lieu(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT * FROM lieutravail
                                    ORDER BY lieu ASC");
    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/lieu.twig', ['titre'=>'Liste des Chantiers', 'lignes'=> $lignes]);
  }
  // Méthode societe
  // permet d'afficher les lieux de travail
  public function societe(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT *
                                    FROM pmsmp
                                    LEFT JOIN salarie on idsalarie = salarie.id
                                    LEFT JOIN lieutravail on id_lieu_travail = lieutravail.idlieu
                                    ORDER BY idlieu ASC, siret ASC

                                  ");
    $requete->execute();
    $liges = $requete->fetchAll();
    
   
    $this->render($response, 'force/societe.twig', ['titre'=>'Liste des Entreprises PMSMP', 'liges'=> $liges]);
  }
  // Méthode trisociete
  // permet d'afficher les sociétés (pmsmp) suivant des recherches
  public function trisociete(RequestInterface $request, ResponseInterface $response, $args)
  {
    $this->logger->info("==>tri société : ".$request->getParsedBody()['mail']);
    $cherche = $request->getParsedBody()['cherche'];
    $requete = $this->pdo->prepare("SELECT *
                                    FROM pmsmp
                                    LEFT JOIN salarie on idsalarie = salarie.id
                                    LEFT JOIN lieutravail on id_lieu_travail = lieutravail.idlieu
                                    WHERE (entreprise LIKE ? OR activite LIKE ? OR ville  LIKE ? OR departement  LIKE ? )    
                                    ORDER BY departement ASC

                                  ");
    $requete->execute(array('%'.$cherche.'%','%'.$cherche.'%','%'.$cherche.'%','%'.$cherche.'%'));
    $societes = $requete->fetchAll();
    $this->render($response, 'force/societe.twig', ['titre'=>'Liste des Entreprises PMSMP', 'societes'=> $societes]);
  }

  /////méthode pmsmpquery
  //  Saisie critère de recherche pour les sociétés pmsmp
  public function pmsmpquery(RequestInterface $request, ResponseInterface $response)
  {

    $this->render($response, 'force/querysociete.twig', ['titre'=>'Requête sur les Entreprises PMSMP', 'societes'=> $societes]);
  }


 

  // Méthode salarie
  // permet d'afficher les salariés en insertion
  public function salarie(RequestInterface $request, ResponseInterface $response)
  {

    $vdate=getdate();
    $vdate = date("d-m-y");
 
    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM salarie
                                        LEFT JOIN lieutravail on salarie.id_lieu_travail = lieutravail.idlieu
                                        LEFT JOIN trancheage on salarie.trancheage = trancheage.idage
                                        LEFT JOIN situfamiliale on salarie.situfamiliale = situfamiliale.idsitu
                                        LEFT JOIN ressources on salarie.ressources = ressources.idressources
                                        LEFT JOIN dureechomage on salarie.dureechomage = dureechomage.idchomage
                                        LEFT JOIN niveauetude on salarie.niveauetude = niveauetude.idetude
                                        ORDER by lieu ASC, datesortie ASC
                                       
                                        ");

        $requete->execute();

        $lignes = $requete->fetchAll();
        //dump($date,$lignes=>datefininitial);die();

        $this->render($response, 'force/salarie.twig', ['titre'=>'Liste des Salariés en insertion', 
                                                        'lignes'=> $lignes,
                                                        'vdate'=>$vdate
                                                       ]);
  }

  // Méthode salariechantier
  // permet d'afficher les salariés en insertion d'un chantier défini
  public function salariechantier(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idlieu = $args[idlieu];
  
    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM salarie LEFT JOIN lieutravail on salarie.id_lieu_travail = lieutravail.idlieu
                                        LEFT JOIN trancheage on salarie.trancheage = trancheage.idage
                                        LEFT JOIN situfamiliale on salarie.situfamiliale = situfamiliale.idsitu
                                        LEFT JOIN ressources on salarie.ressources = ressources.idressources
                                        LEFT JOIN dureechomage on salarie.dureechomage = dureechomage.idchomage
                                        LEFT JOIN niveauetude on salarie.niveauetude = niveauetude.idetude
                                        WHERE id_lieu_travail = $idlieu
                                        ORDER BY DATESORTIE ASC
                                        
                                      ");
        $requete->execute();
        //$requete->execute();
        $lignes = $requete->fetchAll();
        foreach($lignes as $ligne){
          $titre = "Liste des Salariés en insertion chantier de ".$ligne[lieu];
        }
        $this->render($response, 'force/salarie.twig', ['titre' => $titre,'lignes'=> $lignes] );
  }

  // Méthode cqpchantier
  // permet d'afficher la liste des dates de foramtion d'un chantier défini
  public function cqpchantier(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idlieu = $args['idlieu'];


    //préparation requête table datechantier pour extraction par idlieu
      $requete = $this->pdo->prepare("SELECT lieu FROM lieutravail
                                    WHERE idlieu = ?
                                  ");
      $requete->execute([$args['idlieu']]);
      $lieu = $requete->fetch();
      $lieu = $lieu[0];

      $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                      LEFT JOIN formation on datechantier.idformation = formation.idform
                                      WHERE idlieut = ?
                                    ");
      $requete->execute([$args['idlieu']]);
      $lignes = $requete->fetchAll();

      $requete = $this->pdo->prepare("SELECT * FROM formation

                                      ");
      $requete->execute();
      $formations = $requete->fetchAll();

      $idform=$idform+1;
      // dump($idform);

        $this->render($response, 'force/cqpchantier.twig', ['titre'=>'Formations ','lignes'=> $lignes, 'formations'=>$formations,'idform'=>$idform ,'idlieu'=>$idlieu, 'lieu'=>$lieu] );
  }


  public function insertlieu(RequestInterface $request, ResponseInterface $response)
  {

    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM lieutravail ");
        $requete->execute();
        $lieux = $requete->fetchAll();

        $this->render($response, 'force/newsalarie.twig',  ['lieux'=> $lieux] );
  }



  // méthode insertcqpchantier
  // permet de saisir les dates formations
  public function insertcqpchantier(RequestInterface $request, ResponseInterface $response, $args)
  {
    $this->logger->info("==>Ajout salarié : ".$request->getParsedBody()['mail']);
    $idlieu = $request->getParsedBody()['idlieu'];
    // dump($request->getParsedBody()['formation']);
    // dump(isset($request->getParsedBody()['formation']));

    //préparation requête table datechantier

        $requete = $this->pdo->prepare("INSERT INTO `datechantier` (
                                                                idlieut,
                                                                datedeb,
                                                                datefin,
                                                                idformation,
                                                                heures,
                                                                jours,
                                                                deviscert,
                                                                organisme,
                                                                id_user
                                                               )
                                        VALUES (
                                                ?, ?, ?, ? ,?, ?, ?, ?,?)");
        $requete->execute([
          $request->getParsedBody()['idlieu'],
          $request->getParsedBody()['datedeb'],
          $request->getParsedBody()['datefin'],
          $request->getParsedBody()['idformation'],
          $request->getParsedBody()['nbheures'],
          $request->getParsedBody()['jours'],
          $request->getParsedBody()['deviscert'],
          $request->getParsedBody()['organisme'],
          $_SESSION['userid'],
          ]);
          $form=$request->getParsedBody()['formation'];
          if($form!=null){

            $this->logger->info("==>Ajout type de permis : ".$request->getParsedBody()['mail']);
            $requete = $this->pdo->prepare("INSERT INTO `formation` (
                                                                      formation
                                                                    )
            VALUES (?)");
            $requete->execute([
                              $request->getParsedBody()['formation']
                             ]);

          }
          $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('cqpchantier',['idlieu' =>$request->getParsedBody()['idlieu']]));
          return $response;
  }

  // méthode insertpmsmp

  public function insertpmsmp(RequestInterface $request, ResponseInterface $response, $args)
  {
    $this->logger->info("==>Ajout salarié : ".$request->getParsedBody()['mail']);

    $prol = $request->getParsedBody()['prolongation'];
  
    //préparation requête table pmsmp


    $requete = $this->pdo->prepare("INSERT INTO `pmsmp` (
                                                                idsalarie,
                                                                datedeb,
                                                                datefin,
                                                                entreprise,
                                                                siret,
                                                                activite,
                                                                ville,
                                                                departement,
                                                                contactpmsmp,
                                                                telephone,
                                                                mail,
                                                                nbheures,
                                                                bilan,
                                                                prolongation,
                                                                id_user
                                                        )
                                        VALUES (
                                                ?, ?, ?, ? ,?,
                                                ?, ?, ?, ? ,?,
                                                ?, ?, ?,?,?)
                                              ");
        $requete->execute([
          $request->getParsedBody()['idsalarie'],
          $request->getParsedBody()['datedeb'],
          $request->getParsedBody()['datefin'],
          $request->getParsedBody()['entreprise'],
          $request->getParsedBody()['siret'],
          $request->getParsedBody()['activite'],
          $request->getParsedBody()['ville'],
          $request->getParsedBody()['dpt'],
          $request->getParsedBody()['contact'],
          $request->getParsedBody()['telephone'],
          $request->getParsedBody()['mail'],
          $request->getParsedBody()['nbreh'],
          $request->getParsedBody()['bilan'],
          $request->getParsedBody()['prolongation'],
          $_SESSION['userid'],
          ]);
          $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
          return $response;
  }

  // méthode salarieformation

  public function salarieformation(RequestInterface $request, ResponseInterface $response, $args)
  {
    
    include 'fichier.php';
    $this->render($response, 'force/salarieformation.twig',
                              [
                                  'titre'                 =>'Formation suivie et heures effectuées par le salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'datesortie'            => $datesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'poleemploi'            => $poleemploi,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'perm'                  => $perm,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'pmsmps'                => $pmsmps,
                                  'pmsmpprols'            => $pmsmpprols,
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'datefse'               => $datefse,
                                  'datesortiefse'         => $datesortiefse,
                                  'idfse'                 => $idfse,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'obspermis'             => $obspermis,
                                  'permis'                => $permis,
                                  'trancheage'            => $trancheage,
                                  'idextranet'            => $idextranet,
                                  'lignes'                => $lignes,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'situ'                  => $situ,
                                  'droitimage'            => $droitimage
                             ]
                 );
  }
   
  

  // méthode inserttablecqp
  // permet d'insérer' la table cqp (les formations)
  public function inserttablecqp(RequestInterface $request, ResponseInterface $response, $args)
  {
    $this->logger->info("==>Ajout cqp : ".$request->getParsedBody()['mail']);

          $form=$request->getParsedBody()['formation'];

          if($form!=null){
            $requete = $this->pdo->prepare("INSERT INTO `formation` (
                                                                      formation
                                                                    )
            VALUES (?)");
            $requete->execute([
                              $request->getParsedBody()['formation']
                             ]);
            $idform = $this->pdo->lastInsertId();
            // dump($idform);

            $requete = $this->pdo->prepare("INSERT INTO `datechantier` (
                                                                        idlieut,
                                                                        datedeb,
                                                                        datefin,
                                                                        idformation,
                                                                        id_user
                                                                      )
                                                                     VALUES (
                                                                             ?, ?, ?, ? ,?)");
            $requete->execute([$request->getParsedBody()['idlieu'],
                               $request->getParsedBody()['datedeb'],
                               $request->getParsedBody()['datefin'],
                               $idform,
                               $_SESSION['userid'],
                               ]);
            $iddate = $this->pdo->lastInsertId();
            //dump($iddate);

          }else{

            $iddate = $request->getParsedBody()['iddate'];
          }
          //dump($iddate);die();
          $requete = $this->pdo->prepare("INSERT INTO `cqp` (
                                                                  idsalarie,
                                                                  iddate,
                                                                  nbheures,
                                                                  id_user
                                                                 )
                                          VALUES (
                                                  ?, ?, ?, ? )");
          $requete->execute([
            $request->getParsedBody()['idsalarie'],
            $iddate,
            $request->getParsedBody()['nbreh'],
            $_SESSION['userid'],
            ]);


          $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
          return $response;
  }


  // Méthode modifsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function modifsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';
   
        //table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT * FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date ASC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();


        //table documents => liste des documents à fournir par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM documents
                                       ");
         $requete->execute();
         $docs = $requete->fetchAll();
         foreach ($docs as $doc) {
           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = null
                                        ");
           $requete->execute();
         }
         //table docsafournirsalarie => liste des documents déjà fournis par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $salers = $requete->fetchAll();
         foreach ($salers as $saler){


           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = $idsalarie
                                          WHERE documents.iddoc = $saler[iddocuments]
                                        ");
           $requete->execute();
         
         }


         ////table datechantier contenant les dates de formation par chantier
          $requete = $this->pdo->prepare("SELECT * FROM datechantier as datchant
                                          LEFT JOIN lieutravail on idlieut = lieutravail.idlieu
                                          LEFT JOIN formation on idformation = idform
                                          WHERE idlieu = ?
                                         ");
          $requete->execute([$id_lieu_travail]);
          $datechantier = $requete->fetchAll();






        ////table cqp contenant liste formation effectuée par salariés
         $requete = $this->pdo->prepare("SELECT * FROM cqp
                                         LEFT JOIN datechantier  on iddate = iddatechant
                                         LEFT JOIN formation on idformation = idform
                                         WHERE idsalarie = ?
                                        ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();


         ////table pmsmp contenant pmsmp effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                          WHERE idsalarie = ?
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmps = $requete->fetchAll();

 ////table pmsmp contenant pmsmpprol effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmpprol
                                          WHERE idsalarie = ?
                                          ORDER BY idpmsmp ASC
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmpprols = $requete->fetchAll();
           
          ////table zone
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                          ");
          $requete->execute();
          $zones = $requete->fetchAll();

        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                        ORDER by documents ASC  
                                     ");
        $requete->execute();
        $docs = $requete->fetchAll();

        // dump($idform);
        $idform=$idform+1;
        // dump($idform);

        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();



        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

    

        ////

       
    $this->render($response, 'force/fichesalarie.twig',
                              [
                                  'titre'                 =>'Dossier salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'datesortie'            => $datesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'         => $renew,
                                  'perm'                  => $perm,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'pmsmps'                => $pmsmps,
                                  'pmsmpprols'            => $pmsmpprols,
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'datefse'               => $datefse,
                                  'datesortiefse'         => $datesortiefse,
                                  'idfse'                 => $idfse,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'obspermis'             => $obspermis,
                                  'permis'                => $permis,
                                  'trancheage'            => $trancheage,
                                  'idextranet'            => $idextranet,
                                  'lignes'                => $lignes,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage

                             ]
                 );
  }
    // Méthode avenant
  // permet d'imprimer le contrat d'avenant d'un salarié
  public function avenant(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';

        
   

        $requete = $this->pdo->prepare(" SELECT * FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieux = $requete->fetchAll();
       
        foreach ($lieux as $lieu) {

          $lieutravail = $lieu[lieu];
          $fse = $lieu[fse];
        }
        
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
        //table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];
        //table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT * FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date ASC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();


        //table documents => liste des documents à fournir par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM documents
                                       ");
         $requete->execute();
         $docs = $requete->fetchAll();
         foreach ($docs as $doc) {
           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = null
                                        ");
           $requete->execute();
         }
         //table docsafournirsalarie => liste des documents déjà fournis par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $salers = $requete->fetchAll();
         foreach ($salers as $saler){


           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = $idsalarie
                                          WHERE documents.iddoc = $saler[iddocuments]
                                        ");
           $requete->execute();
         
         }


         ////table datechantier contenant les dates de formation par chantier
          $requete = $this->pdo->prepare("SELECT * FROM datechantier as datchant
                                          LEFT JOIN lieutravail on idlieut = lieutravail.idlieu
                                          LEFT JOIN formation on idformation = idform
                                          WHERE idlieu = ?
                                         ");
          $requete->execute([$id_lieu_travail]);
          $datechantier = $requete->fetchAll();






        ////table cqp contenant liste formation effectuée par salariés
         $requete = $this->pdo->prepare("SELECT * FROM cqp
                                         LEFT JOIN datechantier  on iddate = iddatechant
                                         LEFT JOIN formation on idformation = idform
                                         WHERE idsalarie = ?
                                        ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();


         ////table pmsmp contenant pmsmp effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                          WHERE idsalarie = ?
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmps = $requete->fetchAll();

 ////table pmsmp contenant pmsmpprol effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmpprol
                                          WHERE idsalarie = ?
                                          ORDER BY idpmsmp ASC
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmpprols = $requete->fetchAll();
           
          ////table zone
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                          ");
          $requete->execute();
          $zones = $requete->fetchAll();

        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                        ORDER by documents ASC  
                                     ");
        $requete->execute();
        $docs = $requete->fetchAll();

        // dump($idform);
        $idform=$idform+1;
        // dump($idform);

        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();



        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

        if ($rqth!="O"){
          $rqth="NON";
        }else{
          $rqth="OUI";
        }
        if ($btp!="N"){
          $btp="OUI";
        }else{
          $btp="NON";
        }
        
       
       
        
       $datein = utf8_encode(strftime('%d %B %Y',strtotime("today"))); 
        
       $dateessai= date("Y-m-d", strtotime($datedebcontrat."+14 days"));
       $datenaissance = utf8_encode(strftime('%d %B %Y',strtotime($datenaissance)));  
       $datedebcontrat = utf8_encode(strftime('%d %B %Y',strtotime($datedebcontrat))); 
       $datefininitial = utf8_encode(strftime('%d %B %Y',strtotime($datefininitial))); 
    
       $idrenewsal = $args['idrenewsal'];
      
       $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                       WHERE idrenewsal = ?
                                     ");
        $requete->execute([$idrenewsal]);
        $renouvellement = $requete->fetchAll();
        
        foreach($renouvellement as $renew ){
          $newfin = $renew[nouvelle_date];
          $newdebdate =  $renew[newdebdate];
        }

      
       
       $datefin    = strtotime($newdebdate."-1 days");
      
       $datefin = utf8_encode(strftime('%d %B %Y',$datefin)); 
               
       $newfin = utf8_encode(strftime('%d %B %Y',strtotime($newfin))); 
      
       $datenass =strval($datenaissance);
            
      
       $dateessai = utf8_encode(strftime('%d %B %Y',strtotime($dateessai))); 
       
      
       $this->render($response, 'force/avenant.twig',
                              [
                                  'titre'                 =>'Impression avenant salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'perm'                  => $perm,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'pmsmps'                => $pmsmps,
                                  'pmsmpprols'            => $pmsmpprols,
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'datefse'               => $datefse,
                                  'idfse'                 => $idfse,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'dateessai'             => $dateessai,
                                  'datein'                => $datein,
                                  'lieutravail'           => $lieutravail,
                                  'fse'                   => $fse,
                                  'newdebdate'            => $newdebdate,
                                  'newfin'                => $newfin,
                                  'datefin'               => $datefin,
                                  'typecontrat'           => $typecontrat,  
                                  'ress'                  => $ress,
                             ]
                 );
  }



/////////////////////
   // Méthode signataire contrat
  // permet d'imprimer le contrat d'un salarié
  public function signataireC(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idsalarie = $args[idsalarie];
    $idtypecontrat = $args[typecontrat];
   // dump($idsalarie);
   
    
    $requete = $this->pdo->prepare(" SELECT * FROM signataire ");
    $requete->execute();
    $signataires = $requete->fetchAll();
    

        $this->render($response, 'user/signataireC.twig',['titre' => 'signataire contrat salarié','signataires'=> $signataires,'idsalarie' => $idsalarie]);
  }

    // Méthode signataire contrat
  // permet d'imprimer le contrat d'un salarié
  public function signataireA(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idsalarie = $args[idsalarie];
    $idtypecontrat = $args[typecontrat];
   // dump($idsalarie);
   
    
    $requete = $this->pdo->prepare(" SELECT * FROM signataire ");
    $requete->execute();
    $signataires = $requete->fetchAll();
    

        $this->render($response, 'user/signataireA.twig',['titre' => 'signataire contrat salarié','signataires'=> $signataires,'idsalarie' => $idsalarie]);
        
  }
    // Méthode contrat
  // permet d'imprimer le contrat d'un salarié
  public function contrat(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';
    //include 'ChiffresEnLettres.php';


    $prenom = ucwords(strtolower($prenom));
    $idsalarie = $args[idsalarie];
    $typecontrat = "c";
    /*$essai1 = debug_backtrace();
    $appel = $essai1[0];
    dump($essai1);
    dump($appel);
    
   dump( $args[idsalarie]);
   die();*/

    $idsignataire = $request->getParsedBody()['idsignataire'];
    //dump($idsignataire);
    $requete = $this->pdo->prepare(" SELECT * FROM signataire WHERE idsignataire = ?");
    $requete->execute([$idsignataire]);
    $signataires = $requete->fetchAll();
    
    foreach ($signataires as $signataire) {
     // echo (civilite.$signataire[civilite]);
      $civilitesignataire = $signataire[civilite];
      if ($civilitesignataire === "0"){
        $civ = "Madame";
        $article = "La";
      } elseif ($civilitesignataire === "1") {
        $civ = "Monsieur";
        $article = "Le";
      }
      $fonctionsignataire = $signataire[fonction];
      $prenomsignataire = $signataire[prenom];
      $nomsignataire = $signataire[nom];
     
      

    }
        $requete = $this->pdo->prepare(" SELECT * FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieux = $requete->fetchAll();
       
        foreach ($lieux as $lieu) {

          $lieutravail = $lieu[lieu];
          $fse = $lieu[fse];
          $smic = $lieu[smic];
          $contrat = $lieu[contratinitial];
          $essai = $lieu[essai];

        }
          ////Salaire chiffre en lettre

          $tablettre = array ("","","Deux","Trois","Quatre","Cinq","Six","Sept","Huit","Neuf","Dix","Onze","Douze","Treize","Quatorze","Quinze","Seize","Dix-Sept","Dix-huit","Dix-Neuf");
          $tablet = array("","","Vingt","Trente","Quarante","Cinquante","Soixante","Soixante");
          $euros = explode(".",$smic);
          $euro = $euros[0];
          $centime = $euros[1];
         
          ////Centimes en chiffre

          if ($centime >0)
          {
            $long = strlen($centime);
            for($i=0;$i<=$long;$i++)
            {
              switch ($i) 
              {
                    case 0:
                      
                      $dixcent1 = $centime[$i];
                      $dixcent = $centime[$i];
                      if($dixcent ==1)
                      {
                      
                      }
                      if($dixcent <=7 )
                      {
                        $ldixcent = $tablet[$dixcent];

                      }elseif($dixcent == 8 || $dixcent == 9)
                      {
                        $ldixcent = "Quatre-Vingt-";
  
                      }
                      break;

                    case 1:
                    
                      $unitecent = $centime[$i];
                      if($dixcent1 ==1)
                      {
                        if($unitecent == null)
                        {
                          $unitecent = 0;
                        }
                        $dixcent = $dixcent.$unitecent;
                        $ldixcent = $tablettre[$dixcent]. " Centimes";
                      }elseif ($dixcent1 == 8)
                      {   
                          if ($unitecent == 1)
                          {
                              $lunitecent ="Un Centimes";
                          }else
                          {
                            $lunitecent =" ".$tablettre[$unitecent]." Centimes";
                          }    
                              $ldixcent = $ldixcent.$lunitecent;
                      }else
                      {            
                          if($unitecent != 0)
                          {
                            if($dixcent1<7 )
                            {
                              if($unitecent ==1)
                              {
                                $lunitecent = "-et-Un Centimes";
                              }else
                              {
                                $lunitecent = "-".$tablettre[$unitecent]." Centimes";
                              }
                            }elseif($dixcent1 == 7 || $dixcent1 == 9)
                            {
                                $unitecent = 10+$unitecent;
                                $lunitecent ="-".$tablettre[$unitecent]." Centimes";
                            }
                          }else
                          {
                            if($dixcent1== 7 || $dixcent1 == 9)
                            {
                                $lunitecent = $tablettre[10]." Centimes";
                            }
        
                          }    

                      $ldixcent = $ldixcent.$lunitecent;
                    }
                      break;
  
                   
              }
             
  
                           
            }
          }
          /////euro en chiffre


          $long = strlen($euro);
          for($i=0;$i<=$long;$i++)
          {
            
            switch ($i) 
            {
                  case 0:
                    //mille
                    $mille = $euro[$i];
                    if ($mille<=19)
                    {
                        $lmille = $tablettre[$mille]." Mille";
                        $lmille = trim($lmille);
                    }

                 
                    break;
                  case 1:
                    //cent
                    $cent = $euro[$i];
                    if ($cent == 0)
                    {

                    }else
                    {
                   
                      $lcent = $tablettre[$cent]." Cent";
                      $lcent =trim($lcent);
                    }                            
                      break;
                  case 2:
                    //dizaine
                    $dix = $euro[$i];
                    if($dix ==1)
                    {
                      $dix1=$dix;
                      $dix=$dix.$euro[$i+1];
                      $ldix = $tablettre[$dix];

                    }
                    if($dix <=7 )
                    {
                      $ldix = $tablet[$dix];

                    }elseif($dix == 8 || $dix == 9)
                    {
                      $ldix = "Quatre-Vingt";
                      $dix1 = $dix;
                    }
                       
                      break;
                  case 3:
                    //unité
                    if ($dix1=1)
                    { 
                    
                    }
                        $unite = $euro[$i];
                        if($unite == 1 && $dix1 != 1)
                        {
                          $lunite="-et-Un";

                        }
                            if ($dix1!=1)
                            { 
                              $unite = $dix1.$unite ;
                              $lunite=" ".$tablettre[$unite];
                            }
                            if($unite != 0)
                            {
                              if($dix == 7 || $dix == 9)
                              {
                                  $unite = 10+$unite;

                              }
                              $lunite =" ".$tablettre[$unite];
                            
                                                  
                            }else
                            {
                              
                              if($dix == 7 || $dix == 9)
                              {
                                  $lunite =" ".$tablettre[10];

                              }else
                              {
                                $lunite =" ".$tablettre[$unite];

                              }
          
                            }   
                          
                           
                            
                    $ldix = $ldix.$lunite;
                    
                    break;
                  
                 
            }
           

                         
          }
         $lettre = ($lmille ." ".$lcent." ".$ldix." Euros ".$ldixcent);  
        
/////////Fin salaire en lettre
         
        
        
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
    
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
        //table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];
        //table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT * FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date ASC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();


        //table documents => liste des documents à fournir par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM documents
                                       ");
         $requete->execute();
         $docs = $requete->fetchAll();
         foreach ($docs as $doc) {
           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = null
                                        ");
           $requete->execute();
         }
         //table docsafournirsalarie => liste des documents déjà fournis par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $salers = $requete->fetchAll();
         foreach ($salers as $saler){


           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = $idsalarie
                                          WHERE documents.iddoc = $saler[iddocuments]
                                        ");
           $requete->execute();
         
         }


         ////table datechantier contenant les dates de formation par chantier
          $requete = $this->pdo->prepare("SELECT * FROM datechantier as datchant
                                          LEFT JOIN lieutravail on idlieut = lieutravail.idlieu
                                          LEFT JOIN formation on idformation = idform
                                          WHERE idlieu = ?
                                         ");
          $requete->execute([$id_lieu_travail]);
          $datechantier = $requete->fetchAll();






        ////table cqp contenant liste formation effectuée par salariés
         $requete = $this->pdo->prepare("SELECT * FROM cqp
                                         LEFT JOIN datechantier  on iddate = iddatechant
                                         LEFT JOIN formation on idformation = idform
                                         WHERE idsalarie = ?
                                        ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();


         ////table pmsmp contenant pmsmp effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                          WHERE idsalarie = ?
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmps = $requete->fetchAll();

 ////table pmsmp contenant pmsmpprol effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmpprol
                                          WHERE idsalarie = ?
                                          ORDER BY idpmsmp ASC
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmpprols = $requete->fetchAll();
           
          ////table zone
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                          ");
          $requete->execute();
          $zones = $requete->fetchAll();

        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                        ORDER by documents ASC  
                                     ");
        $requete->execute();
        $docs = $requete->fetchAll();

        // dump($idform);
        $idform=$idform+1;
        // dump($idform);

        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();



        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

        if ($rqth!="O"){
          $rqth="NON";
        }else{
          $rqth="OUI";
        }
        if ($btp!="N"){
          $btp="OUI";
        }else{
          $btp="NON";
        }
        
       
        $datein = utf8_encode(strftime('%d %B %Y',strtotime("today"))); 
      
          $nbjour = $essai -1;
          $dateessai= date("Y-m-d", strtotime($datedebcontrat."+".$nbjour."days"));
          

        $nbjour = $nbjour + 1;
       $datenaissance   = utf8_encode(strftime('%d %B %Y',strtotime($datenaissance)));  
       $datedebcontrat  = utf8_encode(strftime('%d %B %Y',strtotime($datedebcontrat))); 
       $datefininitial  = utf8_encode(strftime('%d %B %Y',strtotime($datefininitial))); 
      
      
       $datenass =strval($datenaissance);
       
      
       $dateessai = utf8_encode(strftime('%d %B %Y',strtotime($dateessai))); 
       
      
       $this->render($response, 'force/contrat.twig',
                              [
                                  'titre'                 =>'contrat salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'perm'                  => $perm,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'pmsmps'                => $pmsmps,
                                  'pmsmpprols'            => $pmsmpprols,
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'datefse'               => $datefse,
                                  'idfse'                 => $idfse,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'dateessai'             => $dateessai,
                                  'datein'                => $datein,
                                  'lieutravail'           => $lieutravail,
                                  'fse'                   => $fse,
                                  'civ'                   => $civ,
                                  'article'               => $article,
                                  'nomsignataire'         => $nomsignataire,
                                  'prenomsignataire'      => $prenomsignataire,
                                  'fonctionsignataire'    => $fonctionsignataire,
                                  'typecontrat'           => $typecontrat,
                                  'ress'                  => $ress,
                                  'nbjour'                => $nbjour,
                                  'smic'                  => $smic,
                                  'contrat'               => $contrat,
                                  'essai'                 => $essai,
                                  'lettre'                => $lettre
                             
                              ]);
  }

 // Méthode modifsuivi
  // permet modifier les suivis
  public function modifsuivi(RequestInterface $request, ResponseInterface $response, $args)
    {
      $idsuivi = $args['idsuivi'];   

$requete = $this->pdo->prepare("SELECT * FROM suivi
                                LEFT JOIN presence on suivi.idpresence = presence.idpresence
                                WHERE idsuivi = ?
                                ORDER BY datesuivi ASC
");
$requete->execute([$idsuivi]);

$experiences= $requete->fetchAll();

foreach($experiences as $experience){
      $args['idsalarie'] = $experience[idsalarie];
     
}    
 //dump($args['idsalarie']);
 //die();       
    
 include 'fichier.php';
$requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
$requete->execute([$id_lieu_travail]);
$lieu = $requete->fetchAll();
$lieutravail = $lieu[0][0];
// dump($lieu);
// dump(  $lieutravail );
//table locomotion
$requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
$requete->execute([$locomotion]);
$loco = $requete->fetchAll();
$locomotion = $loco[0][0];

//table niveauetude => niveau d'études
$requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
$requete->execute([$niveauetude]);
$niveau = $requete->fetchAll();
$niveauetude = $niveau[0][0];
//table ressources
$requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
$requete->execute([$ressources]);
$ressource = $requete->fetchAll();
$ressources = $ressource[0][0];

//table presence => liste des types de présence
// //
$requete = $this->pdo->prepare("SELECT * FROM presence
");
$requete->execute();
$presences = $requete->fetchAll();
//table mission => liste des types de mission
// //
$requete = $this->pdo->prepare("SELECT * FROM mission
 ");
$requete->execute();
$missions = $requete->fetchAll();
//table zone
// //
$requete = $this->pdo->prepare("SELECT * FROM zone
  ");
$requete->execute();
$zones = $requete->fetchAll();
//table renouvellement
// //
$requete = $this->pdo->prepare("SELECT * FROM renouvellement
     WHERE idsalarie = ?

   ");
$requete->execute([$idsalarie]);
$renouvellement = $requete->fetchAll();



//préparation requête table entretien pour extraction experience, diagnostic, projet et objectifs par idsalarie

$requete = $this->pdo->prepare("SELECT * FROM datechantier
 LEFT JOIN formation on datechantier.idformation = formation.idform
 WHERE idlieut = ?
");
$requete->execute([$id_lieu_travail]);
$datechantier = $requete->fetchAll();

////////////////////////////////////////
$requete = $this->pdo->prepare("SELECT * FROM formation

 ");
$requete->execute();
$formations = $requete->fetchAll();
foreach( $formations as $formation){
$idform=$formation[idformation];
}
$requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
 LEFT JOIN locomotion on idlocomotion = locomotion.id

 WHERE idsal = ?
");
$requete->execute([$idsalarie]);
$locosalarie = $requete->fetchAll();
$requete = $this->pdo->prepare("SELECT * FROM permissalarie
 LEFT JOIN permis on idperm = permis.idpermis

 WHERE idsal = ?
");
$requete->execute([$idsalarie]);
$permsalarie = $requete->fetchAll();




//dump($idform);
$idform=$idform+1;
//dump($idform);


if ($genre!="Mr"){
$civilite="Madame";
}else{
$civilite="Monsieur";
}

if ($rqth!="O"){
$rqth="NON";
}else{
$rqth="OUI";
}

if ($btp!="N"){
$btp="OUI";
}else{
$btp="NON";
}

// selon le serveur c'est fr ou fr_FR ou fr_FR.ISO8859-1 qui est correct.
// setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
// strftime("%A %d %B %Y."); //Affichera par exemple "date du jour en français : samedi 24 juin 2006."

// $ddate=date("d-m-y");
// $vdate =$madate = new \DateTime($ddate);
// dump($vdate);
// setlocale(LC_TIME, 'fra', 'fr_FR');

// $mydate = datetime();

// format "mercredi, 1 avril 2009"
//$ddate = strftime("%A, %e/%m/%Y", strtotime($ddate));
//dump($ddate);
// convertir les accents (pour encodage UTF-8)
//$ddate = mb_convert_encoding($ddate, 'utf8');

////



$this->render($response, 'force/fichemodifsuivi.twig',
[
'titre'                 =>'Modification Suivi ',
'idsalarie'             => $idsalarie,
'lignes'                => $lignes,
'idpole'                => $idpole,
'nom'                   => $nom,
'prenom'                => $prenom,
'datenaissance'         => $datenaissance,
'lieunaissance'         => $lieunaissance,
'dptnaissance'          => $dptnaissance,
'paysnaissance'         => $paysnaissance,
'genre'                 => $genre,
'adresse1'              => $adresse1,
'adresse2'              => $adresse2,
'villeadresse'          => $villeadresse,
'codepostaladresse'     => $codepostaladresse,
'datedebcontrat'        => $datedebcontrat,
'datefin'               => $datefin,
'ressources'            => $ressources,
'id_lieu_travail'       => $id_lieu_travail,
'motifsortie'           => $motifsortie,
'typesortie'            => $typesortie,
'detailsortie'          => $detailsortie,
'id_user'               => $id_user,
'dateametra'            => $dateametra,
'telephone'             => $telephone,
'paysnaissance'         => $paysnaissance,
'nationalite'           => $nationalite,
'datesejour'            => $datesejour,
'contact'               => $contact,
'datepoleemploi'        => $datepoleemploi,
'securitesociale'       => $securitesociale,
'niveauetude'           => $niveauetude,
'qpv'                   => $qpv,
'prescripteur'          => $prescripteur,
'referent'              => $referent,
'telreferent'           => $telreferent,
'mailreferent'          => $mailreferent,
'adressereferent'       => $adressereferent,
'rqth'                  => $rqth,
'numcaf'                => $numcaf,
'dureechomage'          => $dureechomage,
'situfamiliale'         => $situfamiliale,
'enfants'               => $enfants,
'contratpendantperiode' => $contratpendantperiode,
'droitdif'              => $droitdif,
'nbreheuredif'          => $nbreheuredif,
'sommedif'              => $sommedif,
'attestationcert'       => $attestationcert,
'poleemploi'            => $poleemploi,
'lieutravail'           => $lieutravail,
'locomotion'            => $locomotion,
'renouvellement'        => $renew,
'permis'                => $permis,
'civilite'              => $civilite,
'numagrementpole'       => $numagrementpole,
'renew'                 => $renew,
'freins'                => $freins,
'dossier'               => $dossier,
'experiences'           => $experiences,
'diagnostics'           => $diagnostics,
'projets'               => $projets,
'objectifs'             => $objectifs,
'btp'                   => $btp,
'datechantier'          => $datechantier,
'idform'                => $idform,
'presences'             => $presences,
'missions'              => $missions,
'ddate'                 => $ddate,
'vdate'                 => $vdate,
'locosalarie'           => $locosalarie,
'permsalarie'           => $permsalarie,
'zones'                 => $zones,
'renouvellement'        => $renouvellement,
'mailsalarie'           => $mailsalarie,
'trancheage'            => $trancheage,
'obspermis'             => $obspermis,
'idextranet'            => $idextranet,
'ress'                  => $ress,
                             ]
                 );
  }

 // Méthode updatesalarieformation
  // permet de saisir les heures effectives et validation de formation d'un salarié
  public function updatesalarieformation(RequestInterface $request, ResponseInterface $response, $args)
    {
      $idcqp = $args['idcqp']; 
     
      

$requete = $this->pdo->prepare("SELECT * FROM cqp
                                LEFT JOIN datechantier  on iddate = iddatechant
                                LEFT JOIN lieutravail on idlieu = idlieut
                                LEFT JOIN formation on idform = idformation
                                WHERE idcqp = ?
                                

");
$requete->execute([$idcqp]);

$cqps= $requete->fetchAll();

foreach($cqps as $cqp){
      $args['idsalarie'] = $cqp[idsalarie];

}    
$requete = $this->pdo->prepare("SELECT nom, prenom FROM salarie
                               
                                WHERE id = ?
                                

");
$requete->execute([$args['idsalarie']]);

$salarie= $requete->fetchAll();


$this->render($response, 'force/ficheupdatesalarieformation.twig',
[
'titre'                 =>'Saisie Heures effectives et Validation de Formation',
'cqps'                  => $cqps,
'salarie'               => $salarie,      
'idcqp'                 => $idcqp,
                             ]
                 );
  }







  // Méthode delsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function delsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT *      
                                    FROM salarie
                                    where id = ?");

    $requete->execute([$args['idsalarie']]);
    $lignes = $requete->fetchAll();
    //dump($lignes);
    //Récupéation des valeurs pour affichage dans fiche du salariés
        //table salarie
    $idsalarie            = $args['idsalarie'];
    foreach( $lignes as $ligne){
      $nom                  = $ligne[nom];
      $prenom               = $ligne[prenom];
      $idpole               = $ligne[idpole];
      $matricule            = $ligne[matricule];
      $datenaissance        = $ligne[datenaissance];
      $lieunaissance        = $ligne[lieunaissance];
      $dptnaissance         = $ligne[dptnaissance];
      $genre                = $ligne[genre];
      $adresse1             = $ligne[adresse1];
      $adresse2             = $ligne[adresse2];
      $villeadresse         = $ligne[villeadresse];
      $codepostaladresse    = $ligne[codepostaladresse];
      $datedebcontrat       = $ligne[datedebcontrat];
      $datefininitial       = $ligne[datefininitial];
      $ressources           = $ligne[ressources];
      $id_lieu_travail      = $ligne[id_lieu_travail];
      $motifsortie          = $ligne[motifsortie];
      $typesortie           = $ligne[typesortie];
      $detailsortie         = $ligne[detailsortie];
      $id_user              = $ligne[id_user];
      $dateametra           = $ligne[dateametra];
      $telephone            = $ligne[telephone];
      $paysnaissance        = $ligne[paysnaissance];
      $nationalite          = $ligne[nationalite];
      $datesejour           = $ligne[datesejour];
      $contact              = $ligne[contact];
      $datepoleemploi       = $ligne[datepoleemploi];
      $securitesociale      = $ligne[securitesociale];
      $niveauetude          = $ligne[niveauetude];
      $qpv                  = $ligne[qpv];
      $prescripteur         = $ligne[prescripteur];
      $referent             = $ligne[referent];
      $telreferent          = $ligne[telreferent];
      $mailreferent         = $ligne[mailreferent];
      $adressereferent      = $ligne[adressereferent];
      $rqth                 = $ligne[rqth];
      $numcaf               = $ligne[numcaf];
      $dureechomage         = $ligne[dureechomage];
      $situfamiliale        = $ligne[situfamiliale];
      $enfants              = $ligne[enfants];
      $locomotion           = $ligne[locomotion];
      $contratpendantperiode= $ligne[contratpendantperiode];
      $droitdif             = $ligne[droitdif];
      $nbreheuredif         = $ligne[nbreheuredif];
      $sommedif             = $ligne[sommedif];
      $attestationcert      = $ligne[attestationcert];
      $poleemploi           = $ligne[poleemploi];
      $permis               = $ligne[permis];
      $numagrementpole      = $ligne[numagrementpole];
      $btp                  = $ligne[btp];
      $mailsalarie          = $ligne[mailsalarie];
      $trancheage           = $ligne[trancheage];
      $obspermis            = $ligne[obspermis];
      $idextranet           = $ligne[idextranet];
      //table lieutravail=>chantier

    }



        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
        //table situfamiliale => situation familiale
        $requete = $this->pdo->prepare(" SELECT situation FROM situfamiliale WHERE id = ?");
        $requete->execute([$situfamiliale]);
        $situ = $requete->fetchAll();
        $situfamiliale = $situ[0][0];
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE id = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
        //table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE id = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];
        //table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT nouvelle_date, observation FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date DESC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();
        if ($renew!=null){
            $datefin=$renew[0][0];
            // foreach ($renew AS $ren){
            //
            // }
        }else{

            $datefin=$datefininitial;
        }


        //table documents => liste des documents à fournir par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM documents
                                      ");
         $requete->execute();
         $docs = $requete->fetchAll();
         foreach ($docs as $doc) {
           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = null
                                        ");
           $requete->execute();
         }
         //table docsafournirsalarie => liste des documents déjà fournis par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $salers = $requete->fetchAll();
         foreach ($salers as $saler){


           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = $idsalarie
                                          WHERE documents.iddoc = $saler[iddocuments]
                                        ");
           $requete->execute();
         }
         ////table datechantier contenant les dates de formation par chantier
          $requete = $this->pdo->prepare("SELECT * FROM datechantier as datchant
                                          LEFT JOIN lieutravail on idlieut = lieutravail.idlieu
                                          LEFT JOIN formation on idformation = idform
                                          WHERE idlieu = ?
                                         ");
          $requete->execute([$id_lieu_travail]);
          $datechantier = $requete->fetchAll();






        ////table cqp contenant liste formation effectuée par salariés
         $requete = $this->pdo->prepare("SELECT * FROM cqp
                                         LEFT JOIN datechantier  on iddate = iddatechant
                                         LEFT JOIN formation on idformation = idform
                                         WHERE idsalarie = ?
                                        ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();


         ////table pmsmp contenant pmsmp effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                          WHERE idsalarie = ?
                                         ");
          $requete->execute([$idsalarie]);
          $pmsmps = $requete->fetchAll();

          ////table permis contenant Liste permis
           $requete = $this->pdo->prepare("SELECT * FROM permis
                                           WHERE idpermis = ?
                                          ");
           $requete->execute([$permis]);
           $permis = $requete->fetchAll();
           foreach($permis as $permi ){
              $perm=$permi[permis];
           }

        ////////////////////////////////////////
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                     ");
        $requete->execute();
        $docs = $requete->fetchAll();

        // dump($idform);
        $idform=$idform+1;
        // dump($idform);


        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

        if ($rqth!="O"){
          $rqth="NON";
        }else{
          $rqth="OUI";
        }

        if ($btp!="N"){
          $btp="OUI";
        }else{
          $btp="NON";
        }

        ////



    $this->render($response, 'force/deletesalarie.twig',
                              [
                                  'titre'                 =>'Suppression Salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefin'               => $datefin,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'perm'                  => $perm,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'pmsmps'                => $pmsmps,
                                  'mailsalarie'           => $lignemailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                             ]
                 );
  }
  // Méthode editsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function editsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT *

                                    FROM salarie
                                    where id = ?");

    $requete->execute([$args['idsalarie']]);
    $lignes = $requete->fetchAll();
    //dump($lignes);
    //Récupéation des valeurs pour affichage dans fiche du salariés
        //table salarie
    $idsalarie            = $args['idsalarie'];
    foreach( $lignes as $ligne){
      $matricule            = $ligne[matricule];
      $nom                  = $ligne[nom];
      $prenom               = $ligne[prenom];
      $idpole               = $ligne[idpole];
      $matricule            = $ligne[matricule];
      $datenaissance        = $ligne[datenaissance];
      $age                  = $ligne[trancheage];
      $lieunaissance        = $ligne[lieunaissance];
      $dptnaissance         = $ligne[dptnaissance];
      $genre                = $ligne[genre];
      $adresse1             = $ligne[adresse1];
      $adresse2             = $ligne[adresse2];
      $villeadresse         = $ligne[villeadresse];
      $codepostaladresse    = $ligne[codepostaladresse];
      $datedebcontrat       = $ligne[datedebcontrat];
      $datefininitial       = $ligne[datefininitial];
      $ress                 = $ligne[ressources];
      $id_lieu_travail      = $ligne[id_lieu_travail];
      $motifsortie          = $ligne[motifsortie];
      $typesortie           = $ligne[typesortie];
      $detailsortie         = $ligne[detailsortie];
      $id_user              = $ligne[id_user];
      $title                = $ligne[titre];
      $dateametra           = $ligne[dateametra];
      $datesejour           = $ligne[datesejour];
      $telephone            = $ligne[telephone];
      $paysnaissance        = $ligne[paysnaissance];
      $nationalite          = $ligne[nationalite];
      $datesejour           = $ligne[datesejour];
      $datemutuelle         = $ligne[datemutuelle];
      $mutuelle             = $ligne[mutuelleforce];
      $portabilite          = $ligne[portabilite];
      $contact              = $ligne[contact];
      $datepoleemploi       = $ligne[datepoleemploi];
      $securitesociale      = $ligne[securitesociale];
      $etude                = $ligne[niveauetude];
      $qpv                  = $ligne[qpv];
      $prescripteur         = $ligne[prescripteur];
      $referent             = $ligne[referent];
      $telreferent          = $ligne[telreferent];
      $mailreferent         = $ligne[mailreferent];
      $adressereferent      = $ligne[adressereferent];
      $rqth                 = $ligne[rqth];
      $numcaf               = $ligne[numcaf];
      $duree                = $ligne[dureechomage];
      $situfamiliale        = $ligne[situfamiliale];
      $enfants              = $ligne[enfants];
      $loc                  = $ligne[locomotion];
      $contratpendantperiode= $ligne[contratpendantperiode];
      $droitdif             = $ligne[droitdif];
      $nbreheuredif         = $ligne[nbreheuredif];
      $sommedif             = $ligne[sommedif];
      $attestationcert      = $ligne[attestationcert];
      $poleemploi           = $ligne[poleemploi];
      $perm                 = $ligne[permis];
      $obspermis            = $ligne[obspermis];
      $numagrementpole      = $ligne[numagrementpole];
      $btp                  = $ligne[cartebtp];
      $mailsalarie          = $ligne[mailsalarie];
      $dpae                 = $ligne[dpae];
      $numurssaf            = $ligne[numurssaf];
      $datefse              = $ligne[datefse];
      $idfse                = $ligne[idfse];
      $datesortiefse        = $ligne[datesortiefse];
      $mailsalarie          = $ligne[mailsalarie];
      $trancheages          = $ligne[trancheage];
      $obspermis            = $ligne[obspermis];
      $idextranet           = $ligne[idextranet];
      $droitimage           =$ligne[droitimage];

            //table lieutravail=>chantier
      }
      $datese = date("d/m/Y",strtotime("$datesejour"));


        $requete = $this->pdo->prepare("SELECT idlieu, lieu FROM lieutravail ");
        $requete->execute();
        $chantiers = $requete->fetchAll();
        //Préparation liste pièces d'identité
        $requete = $this->pdo->prepare("SELECT * FROM titre ");
        $requete->execute();
        $titres = $requete->fetchAll();
        //Préparation liste zone
        $requete = $this->pdo->prepare("SELECT * FROM zone ");
        $requete->execute();
        $zones = $requete->fetchAll();


        // var_dump($idtitre[idtitre]);
         $idtitre=$idtitre[idtitre];
         $idtitre=$idtitre+1;
        // var_dump($idtitre);
        //die();
        //Préparation liste tranche d'âge
        $requete = $this->pdo->prepare("SELECT * FROM trancheage ");
        $requete->execute();
        $tranches = $requete->fetchAll();
        //Préparation permis
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie

                                        WHERE idsal=?
                                      ");

        $requete->execute([$args['idsalarie']]);
        $permisal = $requete->fetchAll();
        //Préparation liste Niveau d'études
        $requete = $this->pdo->prepare("SELECT idetude, niveau FROM niveauetude ");
        $requete->execute();
        $niveaux = $requete->fetchAll();
        //Préparation liste durée de chômage
        $requete = $this->pdo->prepare("SELECT idchomage, duree FROM dureechomage ");
        $requete->execute();
        $chomages = $requete->fetchAll();
        //Préparation liste ressources
        $requete = $this->pdo->prepare("SELECT idressources, ressources FROM ressources ");
        $requete->execute();
        $ressources = $requete->fetchAll();
        //Préparation liste situation familiale
        $requete = $this->pdo->prepare("SELECT idsitu, situation FROM situfamiliale ");
        $requete->execute();
        $situations = $requete->fetchAll();
                        //Préparation liste locomotion
                       $requete = $this->pdo->prepare("SELECT id, locomotion FROM locomotion ");
                         $requete->execute();
                       $locomotions = $requete->fetchAll();

        //Préparation permis
        //table permis => liste permis par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM permis
                                      ");
         $requete->execute();
         $perms = $requete->fetchAll();
         foreach ($perms as $perm) {
           $requete = $this->pdo->prepare("UPDATE  permis
                                          SET coche = null
                                        ");
           $requete->execute();

         }

         //Préparation locomotion
         //table locomotion => liste locomotion par le salarié
          $requete = $this->pdo->prepare("SELECT * FROM locomotion
                                       ");
          $requete->execute();
          $locos = $requete->fetchAll();
          foreach ($locos as $loco) {
            $requete = $this->pdo->prepare("UPDATE  locomotion
                                           SET coche = null
                                         ");
            $requete->execute();

          }

          //table permisalarie => liste des permis du salarié
         $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                         WHERE idsal=?
                                       ");
         $requete->execute([$idsalarie]);
         $salers = $requete->fetchAll();
         foreach ($salers as $saler){


           $requete = $this->pdo->prepare("UPDATE  permis
                                          SET coche = $idsalarie
                                          WHERE permis.idpermis = $saler[idperm]
                                        ");
         $requete->execute();
        }

        //table locomotionsalarie => liste des locomotions du salarié
       $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                       WHERE idsal=?
                                     ");
       $requete->execute([$idsalarie]);
       $salerlocos = $requete->fetchAll();
       foreach ($salerlocos as $salerloco){
         $requete = $this->pdo->prepare("UPDATE  locomotion
                                        SET coche = $idsalarie
                                        WHERE locomotion.id = $salerloco[idlocomotion]
                                      ");
         $requete->execute();
      }


       $requete = $this->pdo->prepare("SELECT * FROM permis ");
       $requete->execute();
       $permis = $requete->fetchAll();

       $requete = $this->pdo->prepare("SELECT * FROM locomotion ");
       $requete->execute();
       $locomotion = $requete->fetchAll();


        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);

        //table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT nouvelle_date, observation FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date DESC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();
        if ($renew!=null){
            $datefin=$renew[0][0];
            // foreach ($renew AS $ren){
            //
            // }
        }else{

            $datefin=$datefininitial;
        }


        //table documents => liste des documents à fournir par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM documents
                                      ");
         $requete->execute();
         $docs = $requete->fetchAll();
         foreach ($docs as $doc) {
           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = null
                                        ");
           $requete->execute();
         }
         //table docsafournirsalarie => liste des documents déjà fournis par le salarié
         $requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $salers = $requete->fetchAll();
         foreach ($salers as $saler){


           $requete = $this->pdo->prepare("UPDATE  documents
                                          SET coche = $idsalarie
                                          WHERE documents.iddoc = $saler[iddocuments]
                                        ");
           $requete->execute();
         }
         ////table datechantier contenant les dates de formation par chantier
          $requete = $this->pdo->prepare("SELECT * FROM datechantier as datchant
                                          LEFT JOIN lieutravail on idlieut = lieutravail.idlieu
                                          LEFT JOIN formation on idformation = idform
                                          WHERE idlieu = ?
                                         ");
          $requete->execute([$id_lieu_travail]);
          $datechantier = $requete->fetchAll();






        ////table cqp contenant liste formation effectuée par salariés ====> FORMATION SALARIES ET NON CQP PROPREMENT DIT
         $requete = $this->pdo->prepare("SELECT * FROM cqp
                                         LEFT JOIN datechantier  on iddate = iddatechant
                                         LEFT JOIN formation on idformation = idform
                                         WHERE idsalarie = ?
                                        ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();


         ////table pmsmp contenant pmsmp effectuée par salarié
          $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                          WHERE idsalarie = ?
                                         ");
          $requete->execute([$idsalarie]);
          ////table zone
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                          ");
           $requete->execute();
          $zones = $requete->fetchAll();

        ////////////////////////////////////////
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                     ");
        $requete->execute();
        $docs = $requete->fetchAll();

        // dump($idform);
        $idform=$idform+1;
        // dump($idform);

        //table sanctionsalarie
          $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
          LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
          LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
          WHERE idsalarie = ?
          ");
          $requete->execute([$args['idsalarie']]);
          $sanctionsals = $requete->fetchAll();


        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

           

        ////
    $this->render($response, 'force/modifsalarie.twig',
                              [
                                  'titre'                 =>'Mise à jour Dossier salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'matricule'             => $matricule,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'age'                   => $age,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'chantiers'             => $chantiers,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefin'               => $datefin,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'ress'                  => $ress,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'title'                 => $title,
                                  'titres'                => $titres,
                                  'datese'                => $datese,
                                  'datesejour'            => $datesejour,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveaux'               => $niveaux,
                                  'etude'                 => $etude,
                                  'tranches'              => $tranches,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'duree'                 => $duree,
                                  'chomages'              => $chomages,
                                  'situations'            => $situations,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'loc'                   => $loc,
                                  'locomotion'           => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'perm'                  => $perm,
                                  'permisal'              => $permisal,
                                  'obspermis'             => $obspermis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'pmsmps'                => $pmsmps,
                                  'mailsalarie'           => $mailsalarie,
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'zones'                 => $zones,
                                  'datefse'               => $datefse,
                                  'datesortiefse'         => $datesortiefse,
                                  'idfse'                 => $idfse,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'droitimage'            => $droitimage,
                             ]
                 );
  }

  // Méthode sortiesalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function sortiesalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';

//table lieutravail=>chantier
        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
//table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];

//table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
//table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];
//table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT nouvelle_date, observation FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date DESC");
        $requete->execute([$idsalarie]);
        $renews = $requete->fetchAll();
        if ($renews != null){
            $datefin=$renews[0][0];
          
        }else{

            $datefin=$datefininitial;
        }

//table documents => liste des documents déjà fournis par le salarié
        // // LEFT JOIN DOCUMENTS
         $requete = $this->pdo->prepare("SELECT documents FROM docsafournirsalarie
                                         LEFT JOIN documents on docsafournirsalarie.iddocuments = documents.iddoc
                                         WHERE idsalarie = ?
                                       ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();

//table documents => liste des documents à fournir
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                      ");
        $requete->execute();
        $docs = $requete->fetchAll();

//table motifsortie
        $requete = $this->pdo->prepare("SELECT * FROM motifsortie
                                      ");
        $requete->execute();
        $motifs = $requete->fetchAll();
//table typesortie
        $requete = $this->pdo->prepare("SELECT * FROM typesortie
                                      ");
        $requete->execute();
        $typesorties = $requete->fetchAll();
//table sortie
                $requete = $this->pdo->prepare("SELECT * FROM sortie
                                              ");
                $requete->execute();
                $sorties = $requete->fetchAll();

//préparation requête table entretien pour extraction experience, diagnostic, projet et objectifs par idsalarie

        $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                        LEFT JOIN formation on datechantier.idformation = formation.idform
                                        WHERE idlieut = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();

        ////////////////////////////////////////

//table formation
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }

       // dump($idform);
        $idform=$idform+1;
        // dump($idform);






        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

    
        ////



    $this->render($response, 'force/sortiesalarie.twig',
                              [
                                  'titre'                 =>'Fiche de sortie salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'idsortie'              => $idsortie,
                                  'datesortie'            => $datesortie,
                                  'motifsortie'           => $motifsortie,
                                  'typesort'              => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'sorties'               => $sorties,
                                  'typesorties'           => $typesorties,
                                  'motifs'                => $motifs,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage
                             ]
                 );
  }

  // Méthode sanctionsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function sanctionsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';
//table lieutravail=>chantier
        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
//table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];

//table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
//table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];
//table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT nouvelle_date, observation FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date DESC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();
        if ($renew!=null){
            $datefin=$renew[0][0];
            // foreach ($renew AS $ren){
            //
            // }
        }else{

            $datefin=$datefininitial;
        }

//table documents => liste des documents déjà fournis par le salarié
        // // LEFT JOIN DOCUMENTS
         $requete = $this->pdo->prepare("SELECT documents FROM docsafournirsalarie
                                         LEFT JOIN documents on docsafournirsalarie.iddocuments = documents.iddoc
                                         WHERE idsalarie = ?
                                       ");
         $requete->execute([$idsalarie]);
         $dossier = $requete->fetchAll();

//table documents => liste des documents à fournir
        $requete = $this->pdo->prepare("SELECT * FROM documents
                                      ");
        $requete->execute();
        $docs = $requete->fetchAll();

//table sanction
        $requete = $this->pdo->prepare("SELECT * FROM sanction
                                      ");
        $requete->execute();
        $motifs = $requete->fetchAll();
//table motifsanction
        $requete = $this->pdo->prepare("SELECT * FROM motifsanction
                                      ");
        $requete->execute();
        $typesorties = $requete->fetchAll();
//table sortie
                $requete = $this->pdo->prepare("SELECT * FROM sortie
                                              ");
                $requete->execute();
                $sorties = $requete->fetchAll();

//préparation requête table entretien pour extraction experience, diagnostic, projet et objectifs par idsalarie

        $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                        LEFT JOIN formation on datechantier.idformation = formation.idform
                                        WHERE idlieut = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();

        ////////////////////////////////////////

//table formation
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        // dump($idform);
        $idform=$idform+1;
        // dump($idform);

        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();

//table sanctionsalarie
$requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
                                LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
                                LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
                                WHERE idsalarie = ?
                              ");
$requete->execute([$args['idsalarie']]);
$sanctionsals = $requete->fetchAll();



        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

     

        ////



    $this->render($response, 'force/sanctionsalarie.twig',
                              [
                                  'titre'                 =>'Fiche de sanction salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'       => $datefininitial,
                                  'ressources'            => $ressources,
                                  'idlieu'                => $id_lieu_travail,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'docs'                  => $docs,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'sorties'               => $sorties,
                                  'typesorties'           => $typesorties,
                                  'motifs'                => $motifs,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage
                              ]);
  }


  // Méthode suivisalarie
  // permet d'afficher la fiche de suivi d'un salarié en insertion
  public function suivisalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';
   


 


        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
      
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
               
        //table renouvellement=>affichage renouvellement date fin contrat
        $requete = $this->pdo->prepare("SELECT nouvelle_date, observation FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date DESC");
        $requete->execute([$idsalarie]);
        $renew = $requete->fetchAll();
        if ($renew!=null){
            $datefin=$renew[0][0];
            // foreach ($renew AS $ren){
            //
            // }
        }else{

            $datefin=$datefininitial;
        }

        //table documents => liste des documents déjà fournis par le salarié
        // // LEFT JOIN DOCUMENTS
         $requete = $this->pdo->prepare("SELECT * FROM entretien

                                         WHERE idsalarie = ?
                                       ");
         $requete->execute([$idsalarie]);
         $experiences= $requete->fetchAll();


         // suppression coche dans frein objectifs et projets


         $requete = $this->pdo->prepare("SELECT * FROM frein
                                        order by idfrein ASC
                                       ");
         $requete->execute();
         $nettoyages = $requete->fetchAll();
         foreach ($nettoyages as $nettoyage) {
           $requete = $this->pdo->prepare("UPDATE  frein
                                          SET coche = null

                                        ");
           $requete->execute();
         }

          
        // fin coche frein objectif et projets

        $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                      WHERE idsalarie=?
                                      ");
        $requete->execute([$idsalarie]);
        $renouvellement = $requete->fetchAll();


         // ajout coche dans frein objecif et projet

         // freins

         $requete = $this->pdo->prepare("SELECT * FROM freinsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $sales = $requete->fetchAll();
         foreach ($sales as $sale){


           $requete = $this->pdo->prepare("UPDATE  frein
                                          SET coche = $idsalarie
                                          WHERE frein.idfrein = $sale[idfreine]
                                        ");
           $requete->execute();
         }


         //// fin ajout coche dans frein objecif et projet

         $requete = $this->pdo->prepare("SELECT * FROM frein
                                         order by idfrein ASC
                                       ");
          $requete->execute();
          $sites = $requete->fetchAll();

          $requete = $this->pdo->prepare("SELECT * FROM zone
                                        ");
           $requete->execute();
           $zones = $requete->fetchAll();


       //table entretien => liste des freins à l'entré en action
      $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                       LEFT JOIN locomotion on idlocomotion = locomotion.id

                                       WHERE idsal = ?
                                    ");
       $requete->execute([$idsalarie]);
       $locosalarie = $requete->fetchAll();
       $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                       LEFT JOIN permis on idperm = permis.idpermis

                                       WHERE idsal = ?
                                    ");
       $requete->execute([$idsalarie]);
       $permsalarie = $requete->fetchAll();
      $requete = $this->pdo->prepare("SELECT * FROM entretien

                                         WHERE idsalarie = ?
                                       ");
         $requete->execute([$idsalarie]);
         $experiences= $requete->fetchAll();
         //
         $requete = $this->pdo->prepare("SELECT * FROM frein
                                        order by idfrein ASC
                                       ");
         $requete->execute();
         $nettoyages = $requete->fetchAll();
         foreach ($nettoyages as $nettoyage) {
           $requete = $this->pdo->prepare("UPDATE  frein
                                          SET coche = null

                                        ");
           $requete->execute();
         }

       ///objectifs
       $requete = $this->pdo->prepare("SELECT * FROM objectifs");
      $requete->execute();
      $objectifs = $requete->fetchAll();
       ///projets
       $requete = $this->pdo->prepare("SELECT * FROM projets");
      $requete->execute();
      $projets = $requete->fetchAll();

        ///objectifs salaries

      $requete = $this->pdo->prepare("SELECT * FROM objectifsalarie
                                      LEFT JOIN objectifs on idobj = objectifs.idobjectif
                                      WHERE idsalarie = ?
                                      ORDER BY idobj ASC, datesaisie ASC
     ");
       $requete->execute([$idsalarie]);
       $objsals = $requete->fetchAll();

        ///projets salaries
     
       $requete = $this->pdo->prepare("SELECT * FROM projetsalarie
                                       LEFT JOIN projets on idproj = projets.idprojet
                                      WHERE idsalarie = ?
                                      ORDER BY idproj ASC, datesaisie ASC
               ");
       $requete->execute([$idsalarie]);
       $projsals = $requete->fetchAll();


       //table sanctionsalarie
          $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
          LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
          LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
          WHERE idsalarie = ?
          ");
          $requete->execute([$args['idsalarie']]);
          $sanctionsals = $requete->fetchAll();


       /////////////////////////////
        ///////////////////////////


        $idform=$idform+1;
        //dump($idform);


        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

       

        ////


    $this->render($response, 'force/fichesuivisalarie.twig',
                              [
                                  'titre'                 =>'1er Entretien / Diagnostic',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'datefin'               => $datefin,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'freins'                => $freins,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'diagnostics'           => $diagnostics,
                                  'sites'                 => $sites,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'renouvellement'        => $renouvellement,
                                  'objectifs'             => $objectifs,
                                  'projets'               => $projets,
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'objsals'               => $objsals,
                                  'projsals'              => $projsals,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datefse'               => $datefse,
                                  'idfse'                 => $idfse,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage
                              ]  
                 );
  }
  // Méthode diagnosticsalarie
  // permet d'afficher la fiche de diagnostic d'un salarié en insertion
  public function diagnosticsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idsalarie=$args[idsalarie];

    $requete = $this->pdo->prepare("SELECT *
                                    FROM salarie
                                    where id = ? ");

    $requete->execute([$args['idsalarie']]);
    $lignes = $requete->fetchAll();
    //dump($lignes);
    //Récupéation des valeurs pour affichage dans fiche du salariés
        //table salarie
    $idsalarie            = $args['idsalarie'];
    foreach( $lignes as $ligne){
      $nom                  = $ligne[nom];
      $prenom               = $ligne[prenom];
      $idpole               = $ligne[idpole];
      $matricule            = $ligne[matricule];
      $datenaissance        = $ligne[datenaissance];
      $lieunaissance        = $ligne[lieunaissance];
      $dptnaissance         = $ligne[dptnaissance];
      $genre                = $ligne[genre];
      $adresse1             = $ligne[adresse1];
      $adresse2             = $ligne[adresse2];
      $villeadresse         = $ligne[villeadresse];
      $codepostaladresse    = $ligne[codepostaladresse];
      $datedebcontrat       = $ligne[datedebcontrat];
      $datefininitial       = $ligne[datefininitial];
      $ressources           = $ligne[ressources];
      $id_lieu_travail      = $ligne[id_lieu_travail];
      $motifsortie          = $ligne[motifsortie];
      $typesortie           = $ligne[typesortie];
      $detailsortie         = $ligne[detailsortie];
      $id_user              = $ligne[id_user];
      $dateametra           = $ligne[dateametra];
      $telephone            = $ligne[telephone];
      $paysnaissance        = $ligne[paysnaissance];
      $nationalite          = $ligne[nationalite];
      $datesejour           = $ligne[datesejour];
      $contact              = $ligne[contact];
      $datepoleemploi       = $ligne[datepoleemploi];
      $securitesociale      = $ligne[securitesociale];
      $niveauetude          = $ligne[niveauetude];
      $qpv                  = $ligne[qpv];
      $prescripteur         = $ligne[prescripteur];
      $referent             = $ligne[referent];
      $telreferent          = $ligne[telreferent];
      $mailreferent         = $ligne[mailreferent];
      $adressereferent      = $ligne[adressereferent];
      $rqth                 = $ligne[rqth];
      $numcaf               = $ligne[numcaf];
      $dureechomage         = $ligne[dureechomage];
      $situfamiliale        = $ligne[situfamiliale];
      $enfants              = $ligne[enfants];
      $locomotion           = $ligne[locomotion];
      $contratpendantperiode= $ligne[contratpendantperiode];
      $droitdif             = $ligne[droitdif];
      $nbreheuredif         = $ligne[nbreheuredif];
      $sommedif             = $ligne[sommedif];
      $attestationcert      = $ligne[attestationcert];
      $poleemploi           = $ligne[poleemploi];
      $permis               = $ligne[permis];
      $numagrementpole      = $ligne[numagrementpole];
      $btp                  = $ligne[btp];
      $mailsalarie          = $ligne[mailsalarie];
      $trancheage          = $ligne[trancheage];
      $obspermis            = $ligne[obspermis];
      $idextranet           = $ligne[idextranet];
      //table lieutravail=>chantier

    }

        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
        //table situfamiliale => situation familiale
        $requete = $this->pdo->prepare(" SELECT situation FROM situfamiliale WHERE id = ?");
        $requete->execute([$situfamiliale]);
        $situ = $requete->fetchAll();
        $situfamiliale = $situ[0][0];
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE id = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
        //table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE id = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];

        //table presence => liste des types de présence
        // //
         $requete = $this->pdo->prepare("SELECT * FROM presence
                                       ");
         $requete->execute();
         $presences = $requete->fetchAll();
         //table mission => liste des types de mission
         // //
          $requete = $this->pdo->prepare("SELECT * FROM mission
                                        ");
          $requete->execute();
          $missions = $requete->fetchAll();


            $requete = $this->pdo->prepare("SELECT * FROM frein
                                           order by idfrein ASC
                                          ");
            $requete->execute();
            $nettoyages = $requete->fetchAll();
            foreach ($nettoyages as $nettoyage) {
              $requete = $this->pdo->prepare("UPDATE  frein
                                             SET coche = null

                                           ");
              $requete->execute();
            }


            $requete = $this->pdo->prepare("SELECT * FROM freinsalarie
                                            WHERE idsalarie=?
                                          ");
            $requete->execute([$idsalarie]);
            $sales = $requete->fetchAll();
            foreach ($sales as $sale){


              $requete = $this->pdo->prepare("UPDATE  frein
                                             SET coche = $idsalarie
                                             WHERE frein.idfrein = $sale[idfreine]
                                           ");
              $requete->execute();
            }

            $requete = $this->pdo->prepare("SELECT * FROM frein
                                           order by idfrein ASC
                                         ");
            $requete->execute();
            $sites = $requete->fetchAll();


            $requete = $this->pdo->prepare("SELECT * FROM frein
                                            order by idfrein ASC
                                          ");
             $requete->execute();
             $sites = $requete->fetchAll();
             foreach ($sites as $essai ) {

            }

           //die();

          //table freinsalarie => liste des freins à l'entré en action
          $requete = $this->pdo->prepare("SELECT diagnostic, competence FROM suivi
                                          WHERE idsalarie=?
                                          order by datesuivi ASC

                                        ");
          $requete->execute([$idsalarie]);
          $diagnostics = $requete->fetchAll();




        //dump($idform);
        $idform=$idform+1;
        //dump($idform);


        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

        if ($rqth!="O"){
          $rqth="NON";
        }else{
          $rqth="OUI";
        }
        if ($qpv!="N"){
          $qpv="OUI";
        }else{
          $qpv="NON";
        }
        if ($btp!="N"){
          $btp="OUI";
        }else{
          $btp="NON";
        }

// selon le serveur c'est fr ou fr_FR ou fr_FR.ISO8859-1 qui est correct.
          // setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
          // strftime("%A %d %B %Y."); //Affichera par exemple "date du jour en français : samedi 24 juin 2006."

         // $ddate=date("d-m-y");
         // $vdate =$madate = new \DateTime($ddate);
         // dump($vdate);
         // setlocale(LC_TIME, 'fra', 'fr_FR');

        // $mydate = datetime();

// format "mercredi, 1 avril 2009"
//$ddate = strftime("%A, %e/%m/%Y", strtotime($ddate));
//dump($ddate);
// convertir les accents (pour encodage UTF-8)
//$ddate = mb_convert_encoding($ddate, 'utf8');

              ////



    $this->render($response, 'force/fichediagnosticsalarie.twig',
                              [
                                  'titre'                 =>'Diagnostic ',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefin'               => $datefin,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'freins'                => $freins,
                                  'diagnostics'           => $diagnostics,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'presences'             => $presences,
                                  'missions'              => $missions,
                                  'ddate'                 => $ddate,
                                  'vdate'                 => $vdate,
                                  'sites'                 => $sites,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                             ]
                 );
  }

  // Méthode demarchesalarie
  // permet d'afficher la fiche de diagnostic d'un salarié en insertion
  public function demarchesalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idsalarie=$args[idsalarie];
    // dump("ok");
    // dump($idsalarie);

    include 'fichier.php';
        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
      
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
         //table presence => liste des types de présence
        // //
         $requete = $this->pdo->prepare("SELECT * FROM presence
                                       ");
         $requete->execute();
         $presences = $requete->fetchAll();

         //table zone
         // //
          $requete = $this->pdo->prepare("SELECT * FROM zone
                                        ");
          $requete->execute();
          $zones = $requete->fetchAll();
         //table mission => liste des types de mission
         // //
          $requete = $this->pdo->prepare("SELECT * FROM mission
                                        ");
          $requete->execute();
          $missions = $requete->fetchAll();
          //table demarche => liste des démarches
          $requete = $this->pdo->prepare("SELECT * FROM demarche

                                          order by iddemarche DESC
                                        ");
          $requete->execute();
          $demarches = $requete->fetchAll();

          //table renouvellement
          // //
           $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                           WHERE idsalarie = ?

                                         ");
           $requete->execute([$idsalarie]);
           $renouvellement = $requete->fetchAll();
     

               
          //table demarchesalarie => liste des démarches salariés

          
          $requete = $this->pdo->prepare("SELECT * FROM demarchesalarie
                                          WHERE idsalarie = ?
                                          ORDER BY datesaisie ASC, iddemarche ASC, idtypedemarsal ASC, iddetailtypesal ASC
                                          ");
          $requete->execute([$idsalarie]);
          $demarchesalaries = $requete->fetchAll();

          $requete = $this->pdo->prepare("SELECT * FROM demarchesalarie
                                          WHERE idsalarie = ?
                                          ORDER BY datesaisie DESC, iddemarche ASC
                                          ");
          $requete->execute([$idsalarie]);
          $demarchees = $requete->fetchAll();
          
          
        
 
          $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                          LEFT JOIN demarche ON iddemar = demarche.iddemarche
                                          ORDER BY iddemar ASC, idtypedemarche ASC
                                                                        ");
          $requete->execute();
          $typedemarches = $requete->fetchAll();
  
          $requete = $this->pdo->prepare("SELECT * FROM detailtypedemarche
                                         ORDER BY idtypedemar ASC
          ");
          $requete->execute();
          $detailtypedemarches = $requete->fetchAll();

          /////Affichage démarche salariés ////////////////////////

          $requete = $this->pdo->prepare("SELECT * FROM detailtypedemarche
                                          LEFT JOIN demarche ON iddem = demarche.iddemarche
                                          LEFT JOIN typedemarche ON idtypedemar = typedemarche.idtypedemarche
                                          ORDER BY iddem ASC
          ");
          $requete->execute();
          $affichages = $requete->fetchAll();

          //// affichages

          $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                          ORDER BY idtypedemarche ASC
                                        ");
          $requete->execute();
          $Atypedemarches = $requete->fetchAll();

          $requete = $this->pdo->prepare("SELECT * FROM demarche
                                          ORDER BY iddemarche ASC
                                        ");
          $requete->execute();
          $Ademarches = $requete->fetchAll();

          $requete = $this->pdo->prepare("SELECT * FROM detailtypedemarche
                                          ORDER BY iddetailtype ASC
                                        ");
          $requete->execute();
          $Adetailtypes = $requete->fetchAll();


          /////////////////

        $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                        LEFT JOIN formation on datechantier.idformation = formation.idform
                                        WHERE idlieut = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();

        ////////////////////////////////////////
        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();

        $requete = $this->pdo->prepare("SELECT * FROM formation
                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        //dump($idform);
        $idform=$idform+1;
        //dump($idform);

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();


        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

        

// selon le serveur c'est fr ou fr_FR ou fr_FR.ISO8859-1 qui est correct.
          // setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
                 
         
/*
  Déclaration tableau 
*/         
$demarchs = array(demarche,typedemarche,detail);
/*dump($demarchs);
die();
*/


        // $mydate = datetime();

// format "mercredi, 1 avril 2009"
//$ddate = strftime("%A, %e/%m/%Y", strtotime($ddate));
//dump($ddate);
// convertir les accents (pour encodage UTF-8)
//$ddate = mb_convert_encoding($ddate, 'utf8');

              ////



    $this->render($response, 'force/fichedemarchesalarie.twig',
                              [
                                  'titre'                 =>'Démarche ',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefin'               => $datefin,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'demarchees'            => $demarchees,
                                  'typedemarches'         => $typedemarches,
                                  'detailtypedemarches'   => $detailtypedemarches,
                                  'Ademarches'            => $Ademarches,
                                  'Atypedemarches'        => $Atypedemarches,
                                  'Adetailtypes'          => $Adetailtypes,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'presences'             => $presences,
                                  'missions'              => $missions,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'renouvellement'        => $renouvellement,
                                  'demarches'             => $demarches,
                                  'affichages'            => $affichages,
                                  'demarchesalaries'      => $demarchesalaries,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage


                             ]
                 );
  }
  // Méthode entretiensalarie
  // permet d'afficher la fiche de entretien d'un salarié en insertion
  public function entretiensalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';

        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
     
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
    

        //table presence => liste des types de présence
        // //
         $requete = $this->pdo->prepare("SELECT * FROM presence
                                       ");
         $requete->execute();
         $presences = $requete->fetchAll();
         //table mission => liste des types de mission
         // //
          $requete = $this->pdo->prepare("SELECT * FROM mission
                                        ");
          $requete->execute();
          $missions = $requete->fetchAll();
          //table zone
          // //
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                         ");
           $requete->execute();
           $zones = $requete->fetchAll();
           //table renouvellement
           // //
            $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                            WHERE idsalarie = ?

                                          ");
            $requete->execute([$idsalarie]);
            $renouvellement = $requete->fetchAll();



         //préparation requête table entretien pour extraction experience, diagnostic, projet et objectifs par idsalarie

        $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                        LEFT JOIN formation on datechantier.idformation = formation.idform
                                        WHERE idlieut = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();

        ////////////////////////////////////////
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();



        $requete = $this->pdo->prepare("SELECT * FROM suivi
                                        LEFT JOIN presence ON suivi.idpresence = presence.idpresence
                                        WHERE idsalarie = ?
                                        ORDER BY datesuivi DESC
                                      ");
        $requete->execute([$idsalarie]);
        $experiences= $requete->fetchAll();
        //dump($idform);
        $idform=$idform+1;
        //dump($idform);

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();



        
        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

       


        

  // selon le serveur c'est fr ou fr_FR ou fr_FR.ISO8859-1 qui est correct.
          // setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
          // strftime("%A %d %B %Y."); //Affichera par exemple "date du jour en français : samedi 24 juin 2006."

         // $ddate=date("d-m-y");
         // $vdate =$madate = new \DateTime($ddate);
         // dump($vdate);
         // setlocale(LC_TIME, 'fra', 'fr_FR');

        // $mydate = datetime();

  // format "mercredi, 1 avril 2009"
  //$ddate = strftime("%A, %e/%m/%Y", strtotime($ddate));
  //dump($ddate);
  // convertir les accents (pour encodage UTF-8)
  //$ddate = mb_convert_encoding($ddate, 'utf8');

              ////



    $this->render($response, 'force/ficheentretiensalarie.twig',
                              [
                                  'titre'                 =>'Suivi ',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'freins'                => $freins,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'presences'             => $presences,
                                  'missions'              => $missions,
                                  'ddate'                 => $ddate,
                                  'vdate'                 => $vdate,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'renouvellement'        => $renouvellement,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage
                             ]
                 );
  }

// Méthode recapentretien
  // permet d'afficher la fiche de entretien d'un salarié en insertion
  public function recapentretien(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';

        $requete = $this->pdo->prepare(" SELECT * FROM lieutravail ");
        $requete->execute();
        $lieus = $requete->fetchAll();
       
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
     
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
    

        //table presence => liste des types de présence
        // //
         $requete = $this->pdo->prepare("SELECT * FROM presence
                                       ");
         $requete->execute();
         $presences = $requete->fetchAll();
         //table mission => liste des types de mission
         // //
          $requete = $this->pdo->prepare("SELECT * FROM mission
                                        ");
          $requete->execute();
          $missions = $requete->fetchAll();
          //table zone
          // //
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                         ");
           $requete->execute();
           $zones = $requete->fetchAll();
           //table renouvellement
           // //
            $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                            WHERE idsalarie = ?

                                          ");
            $requete->execute([$idsalarie]);
            $renouvellement = $requete->fetchAll();



         //préparation requête table entretien pour extraction experience, diagnostic, projet et objectifs par idsalarie

        $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                        LEFT JOIN formation on datechantier.idformation = formation.idform
                                        WHERE idlieut = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();

        ////////////////////////////////////////
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();



        $requete = $this->pdo->prepare("SELECT * FROM suivi
                                        LEFT JOIN presence ON suivi.idpresence = presence.idpresence
                                        WHERE idsalarie = ?
                                        ORDER BY datesuivi DESC
                                      ");
        $requete->execute([$idsalarie]);
        $experiences= $requete->fetchAll();
        //dump($idform);
        $idform=$idform+1;
        //dump($idform);

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();



        
        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

       


        

  // selon le serveur c'est fr ou fr_FR ou fr_FR.ISO8859-1 qui est correct.
          // setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
          // strftime("%A %d %B %Y."); //Affichera par exemple "date du jour en français : samedi 24 juin 2006."

         // $ddate=date("d-m-y");
         // $vdate =$madate = new \DateTime($ddate);
         // dump($vdate);
         // setlocale(LC_TIME, 'fra', 'fr_FR');

        // $mydate = datetime();

  // format "mercredi, 1 avril 2009"
  //$ddate = strftime("%A, %e/%m/%Y", strtotime($ddate));
  //dump($ddate);
  // convertir les accents (pour encodage UTF-8)
  //$ddate = mb_convert_encoding($ddate, 'utf8');

              ////



    $this->render($response, 'force/recapentretien.twig',
                              [
                                  'titre'                 =>'Recap Entretien',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieus'                 => $lieus,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'freins'                => $freins,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'presences'             => $presences,
                                  'missions'              => $missions,
                                  'ddate'                 => $ddate,
                                  'vdate'                 => $vdate,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'renouvellement'        => $renouvellement,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                                  'droitimage'            => $droitimage
                             ]
                 );
  }


 // Méthode foramtionsalarie
  // permet de saisir les heures effectives et validation formation salarié
  public function formationsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    include 'fichier.php';

        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];
        // dump($lieu);
        // dump(  $lieutravail );
        //table locomotion
        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];
        //table situfamiliale => situation familiale
        $requete = $this->pdo->prepare(" SELECT situation FROM situfamiliale WHERE id = ?");
        $requete->execute([$situfamiliale]);
        $situ = $requete->fetchAll();
        $situfamiliale = $situ[0][0];
        //table niveauetude => niveau d'études
        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE id = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];
        //table ressources
        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE id = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];

        //table presence => liste des types de présence
        // //
         $requete = $this->pdo->prepare("SELECT * FROM presence
                                       ");
         $requete->execute();
         $presences = $requete->fetchAll();
         //table mission => liste des types de mission
         // //
          $requete = $this->pdo->prepare("SELECT * FROM mission
                                        ");
          $requete->execute();
          $missions = $requete->fetchAll();
          //table zone
          // //
           $requete = $this->pdo->prepare("SELECT * FROM zone
                                         ");
           $requete->execute();
           $zones = $requete->fetchAll();
           //table renouvellement
           // //
            $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                            WHERE idsalarie = ?

                                          ");
            $requete->execute([$idsalarie]);
            $renouvellement = $requete->fetchAll();



         //préparation requête table entretien pour extraction experience, diagnostic, projet et objectifs par idsalarie

        $requete = $this->pdo->prepare("SELECT * FROM datechantier
                                        LEFT JOIN formation on datechantier.idformation = formation.idform
                                        WHERE idlieut = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();

        ////////////////////////////////////////
        $requete = $this->pdo->prepare("SELECT * FROM formation

                                        ");
        $requete->execute();
        $formations = $requete->fetchAll();
        foreach( $formations as $formation){
          $idform=$formation[idformation];
        }
        $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                        LEFT JOIN locomotion on idlocomotion = locomotion.id

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $locosalarie = $requete->fetchAll();
        $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                        LEFT JOIN permis on idperm = permis.idpermis

                                        WHERE idsal = ?
                                     ");
        $requete->execute([$idsalarie]);
        $permsalarie = $requete->fetchAll();



        $requete = $this->pdo->prepare("SELECT * FROM suivi
                                        LEFT JOIN presence ON suivi.idpresence = presence.idpresence
                                        WHERE idsalarie = ?
                                        ORDER BY datesuivi DESC
                                      ");
        $requete->execute([$idsalarie]);
        $experiences= $requete->fetchAll();
        //dump($idform);
        $idform=$idform+1;
        //dump($idform);

        //table sanctionsalarie
        $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
        LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
        LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
        WHERE idsalarie = ?
        ");
        $requete->execute([$args['idsalarie']]);
        $sanctionsals = $requete->fetchAll();



        
        if ($genre!="Mr"){
          $civilite="Madame";
        }else{
          $civilite="Monsieur";
        }

        if ($rqth!="O"){
          $rqth="NON";
        }else{
          $rqth="OUI";
        }

        if ($btp!="N"){
          $btp="OUI";
        }else{
          $btp="NON";
        }


        

  // selon le serveur c'est fr ou fr_FR ou fr_FR.ISO8859-1 qui est correct.
          // setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
          // strftime("%A %d %B %Y."); //Affichera par exemple "date du jour en français : samedi 24 juin 2006."

         // $ddate=date("d-m-y");
         // $vdate =$madate = new \DateTime($ddate);
         // dump($vdate);
         // setlocale(LC_TIME, 'fra', 'fr_FR');

        // $mydate = datetime();

  // format "mercredi, 1 avril 2009"
  //$ddate = strftime("%A, %e/%m/%Y", strtotime($ddate));
  //dump($ddate);
  // convertir les accents (pour encodage UTF-8)
  //$ddate = mb_convert_encoding($ddate, 'utf8');

              ////



    $this->render($response, 'force/ficheentretiensalarie.twig',
                              [
                                  'titre'                 =>'Suivi ',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
                                  'nom'                   => $nom,
                                  'prenom'                => $prenom,
                                  'datenaissance'         => $datenaissance,
                                  'lieunaissance'         => $lieunaissance,
                                  'dptnaissance'          => $dptnaissance,
                                  'paysnaissance'         => $paysnaissance,
                                  'genre'                 => $genre,
                                  'adresse1'              => $adresse1,
                                  'adresse2'              => $adresse2,
                                  'villeadresse'          => $villeadresse,
                                  'codepostaladresse'     => $codepostaladresse,
                                  'datedebcontrat'        => $datedebcontrat,
                                  'datefininitial'        => $datefininitial,
                                  'ressources'            => $ressources,
                                  'id_lieu_travail'       => $id_lieu_travail,
                                  'motifsortie'           => $motifsortie,
                                  'typesortie'            => $typesortie,
                                  'detailsortie'          => $detailsortie,
                                  'id_user'               => $id_user,
                                  'dateametra'            => $dateametra,
                                  'telephone'             => $telephone,
                                  'paysnaissance'         => $paysnaissance,
                                  'nationalite'           => $nationalite,
                                  'datesejour'            => $datesejour,
                                  'contact'               => $contact,
                                  'datepoleemploi'        => $datepoleemploi,
                                  'securitesociale'       => $securitesociale,
                                  'niveauetude'           => $niveauetude,
                                  'qpv'                   => $qpv,
                                  'prescripteur'          => $prescripteur,
                                  'referent'              => $referent,
                                  'telreferent'           => $telreferent,
                                  'mailreferent'          => $mailreferent,
                                  'adressereferent'       => $adressereferent,
                                  'rqth'                  => $rqth,
                                  'numcaf'                => $numcaf,
                                  'dureechomage'          => $dureechomage,
                                  'situfamiliale'         => $situfamiliale,
                                  'enfants'               => $enfants,
                                  'contratpendantperiode' => $contratpendantperiode,
                                  'droitdif'              => $droitdif,
                                  'nbreheuredif'          => $nbreheuredif,
                                  'sommedif'              => $sommedif,
                                  'attestationcert'       => $attestationcert,
                                  'poleemploi'            => $poleemploi,
                                  'lieutravail'           => $lieutravail,
                                  'locomotion'            => $locomotion,
                                  'renouvellement'        => $renew,
                                  'permis'                => $permis,
                                  'civilite'              => $civilite,
                                  'numagrementpole'       => $numagrementpole,
                                  'renew'                 => $renew,
                                  'freins'                => $freins,
                                  'dossier'               => $dossier,
                                  'experiences'           => $experiences,
                                  'diagnostics'           => $diagnostics,
                                  'projets'               => $projets,
                                  'objectifs'             => $objectifs,
                                  'btp'                   => $btp,
                                  'datechantier'          => $datechantier,
                                  'idform'                => $idform,
                                  'presences'             => $presences,
                                  'missions'              => $missions,
                                  'ddate'                 => $ddate,
                                  'vdate'                 => $vdate,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'renouvellement'        => $renouvellement,
                                  'sanctionsals'          => $sanctionsals,
                                  'mailsalarie'           => $mailsalarie,
                                  'trancheage'            => $trancheage,
                                  'obspermis'             => $obspermis,
                                  'idextranet'            => $idextranet,
                                  'datemutuelle'          => $datemutuelle,
                                  'mutuelle'              => $mutuelle,
                                  'portabilite'           => $portabilite,
                                  'ress'                  => $ress,
                             ]
                 );
  }


  // Méthode insertion nouveau salarié
  // permet d'insérer des salariés dans la table salarie
  public function insertsal(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout salarié : ".$request->getParsedBody()['mail']);
      
      //insert table salarie
      $requete = $this->pdo->prepare("INSERT INTO `salarie` (
                                                              idpole,
                                                              matricule,
                                                              nom,
                                                              prenom,
                                                              datenaissance,
                                                              trancheage,
                                                              lieunaissance,
                                                              dptnaissance,
                                                              genre,
                                                              adresse1,
                                                              adresse2,
                                                              villeadresse,
                                                              codepostaladresse,
                                                              datedebcontrat,
                                                              datefininitial,
                                                              datefinreelle,
                                                              ressources,
                                                              id_lieu_travail,
                                                              motiffincontrat,
                                                              situffincontrat,
                                                              id_user,
                                                              dateametra,
                                                              telephone,
                                                              paysnaissance,
                                                              nationalite,
                                                              datesejour,
                                                              datemutuelle,
                                                              mutuelleforce,
                                                              portabilite,
                                                              contact,
                                                              datepoleemploi,
                                                              securitesociale,
                                                              niveauetude,
                                                              qpv,
                                                              prescripteur,
                                                              referent,
                                                              telreferent,
                                                              mailreferent,
                                                              adressereferent,
                                                              rqth,
                                                              numcaf,
                                                              dureechomage,
                                                              situfamiliale,
                                                              enfants,
                                                              contratpendantperiode,
                                                              droitdif,
                                                              nbreheuredif,
                                                              sommedif,
                                                              attestationcert,
                                                              locomotion,
                                                              obspermis,
                                                              cartebtp,
                                                              titre,
                                                              mailsalarie,
                                                              dpae,
                                                              numurssaf,
                                                              idextranet,
                                                              droitimage
                                                             )
                                      VALUES (
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,
                                              ?,?,?,?,?,?,?
                                             )");
      $requete->execute([
        $request->getParsedBody()['idpole'],
        $request->getParsedBody()['matricule'],
        $request->getParsedBody()['nom'],
        $request->getParsedBody()['prenom'],
        $request->getParsedBody()['datenaissance'],
        $request->getParsedBody()['tranche'],
        $request->getParsedBody()['lieunaissance'],
        $request->getParsedBody()['dptnaissance'],
        $request->getParsedBody()['genre'],
        $request->getParsedBody()['adresse1'],
        $request->getParsedBody()['adresse2'],
        $request->getParsedBody()['villeadresse'],
        $request->getParsedBody()['codepostaladresse'],
        $request->getParsedBody()['datedebcontrat'],
        $request->getParsedBody()['datefininitial'],
        $request->getParsedBody()['datefininitial'],
        $request->getParsedBody()['ressources'],
        $request->getParsedBody()['id_lieu_travail'],
        $request->getParsedBody()['motiffincontrat'],
        $request->getParsedBody()['situffincontrat'],
        $_SESSION['userid'],
        $request->getParsedBody()['dateametra'],
        $request->getParsedBody()['telephone'],
        $request->getParsedBody()['paysnaissance'],
        $request->getParsedBody()['nationalite'],
        $request->getParsedBody()['datesejour'],
        $request->getParsedBody()['datemutuelle'],
        $request->getParsedBody()['mutuelle'],
        $request->getParsedBody()['portabilite'],
        $request->getParsedBody()['contact'],
        $request->getParsedBody()['datepoleemploi'],
        $request->getParsedBody()['securitesociale'],
        $request->getParsedBody()['niveauetude'],
        $request->getParsedBody()['qpv'],
        $request->getParsedBody()['prescripteur'],
        $request->getParsedBody()['referent'],
        $request->getParsedBody()['telreferent'],
        $request->getParsedBody()['mailreferent'],
        $request->getParsedBody()['adressereferent'],
        $request->getParsedBody()['rqth'],
        $request->getParsedBody()['numcaf'],
        $request->getParsedBody()['dureechomage'],
        $request->getParsedBody()['situfamiliale'],
        $request->getParsedBody()['enfants'],
        //$request->getParsedBody()['permis'], si ajout , ajouter ? et permis dans table et variable
        $request->getParsedBody()['contratpendantperiode'],
        $request->getParsedBody()['droitdif'],
        $request->getParsedBody()['nbreheuredif'],
        $request->getParsedBody()['sommedif'],
        $request->getParsedBody()['attestationcert'],
        $request->getParsedBody()['locomotion'],
        $request->getParsedBody()['obspermis'],
        $request->getParsedBody()['cartebtp'],
        $request->getParsedBody()['titre'],
        $request->getParsedBody()['mailsalarie'],
        $request->getParsedBody()['dpae'],
        $request->getParsedBody()['numurssaf'],
        $request->getParsedBody()['idextranet'],
        $request->getParsedBody()['droitimage'],



      ]);
      //$idsal=salarie[id];
      $idsal = $this->pdo->lastInsertId();
      //insert table registre
      $requete = $this->pdo->prepare("INSERT INTO `registre` (
                                                              securitesociale,
                                                              nationalite,
                                                              datenaissance,
                                                              sexe,
                                                              emploi,
                                                              dateentree,
                                                              contrat,
                                                              id_user
                                                             )
                                      VALUES (
                                              ?, ?, ?, ?, ?, ?, ?, ?
                                             )");
      $requete->execute([
        $request->getParsedBody()['securitesociale'],
        $request->getParsedBody()['nationalite'],
        $request->getParsedBody()['datenaissance'],
        $request->getParsedBody()['genre'],
        "Salarié Polyvalent",
        $request->getParsedBody()['datedebcontrat'],
        "CDDI",
        $_SESSION['userid']

      ]);

      //insert table permissalarie
    foreach($request->getParsedBody()['permis'] as $spermis){

      $requete = $this->pdo->prepare("INSERT INTO `permissalarie` (
                                                      idsal,
                                                      idperm,
                                                      idtravail
                                                                  )
                                      VALUES (
                                              ?, ?, ?
                                             )");
      $requete->execute([
        $idsal,
        $spermis,
        $request->getParsedBody()['id_lieu_travail'],
      ]);
    }
      //insert table locomotionsalarie
    foreach($request->getParsedBody()['locomotion'] as $slocomotion){
      $requete = $this->pdo->prepare("INSERT INTO `locomotionsalarie` (
                                                      idsal,
                                                      idlocomotion,
                                                      idtravail

                                                                  )
                                      VALUES (
                                              ?, ?, ?
                                             )");
      $requete->execute([
        $idsal,
        $slocomotion,
        $request->getParsedBody()['id_lieu_travail'],
      ]);
    }

        $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));
      }

    return $response;
  }
  // Méthode updatesal
  // permet la mise à jour des salariés dans la table salarie
  public function updatesal(RequestInterface $request, ResponseInterface $response)
  {
        // Vérification CSRF()
        if (false === $request->getAttribute('csrf_status')) {
          die("Opération impossible...");
        } else {
          $this->logger->info("==>Ajout salarié : ".$request->getParsedBody()['mail']);
          $datesejour = ($request->getParsedBody()['datesejour']);
          $datesejour = date("$datesejour");
          $salarie = $request->getParsedBody()['idsalarie'];
            //dump("essai ".$datesejour);
        /*  if ($datesejour != null) {
              dump("oui ".$datesejour);
              echo gettype($datesejour);
              $dates = date("d-m-Y", strtotime('$datesejour'));
              echo gettype($dates);
          }else{
            //  dump("non ".$datesejour);
              $dates = 0;
          }
          dump($dates);*/
          //insert table salarie
          $requete = $this->pdo->prepare("UPDATE `salarie` set    idpole =?,
                                                                  matricule=?,
                                                                  nom =?,
                                                                  prenom =?,
                                                                  datenaissance =?,
                                                                  trancheage =?,
                                                                  lieunaissance =?,
                                                                  dptnaissance =?,
                                                                  genre =?,
                                                                  adresse1 =?,
                                                                  adresse2 =?,
                                                                  villeadresse =?,
                                                                  codepostaladresse =?,
                                                                  datedebcontrat =?,
                                                                  datefininitial =?,
                                                                  datefinreelle =?,
                                                                  ressources =?,
                                                                  id_lieu_travail =?,
                                                                  motiffincontrat =?,
                                                                  situffincontrat =?,
                                                                  id_user =?,
                                                                  dateametra =?,
                                                                  telephone =?,
                                                                  paysnaissance =?,
                                                                  nationalite =?,
                                                                  datesejour =?,
                                                                  datemutuelle =?,
                                                                  mutuelleforce=?,
                                                                  portabilite=?,
                                                                  contact =?,
                                                                  poleemploi =?,
                                                                  datepoleemploi =?,
                                                                  securitesociale =?,
                                                                  niveauetude =?,
                                                                  qpv =?,
                                                                  prescripteur =?,
                                                                  referent =?,
                                                                  telreferent =?,
                                                                  mailreferent =?,
                                                                  adressereferent =?,
                                                                  rqth =?,
                                                                  numcaf =?,
                                                                  dureechomage =?,
                                                                  situfamiliale =?,
                                                                  enfants =?,
                                                                  contratpendantperiode =?,
                                                                  droitdif =?,
                                                                  nbreheuredif =?,
                                                                  sommedif =?,
                                                                  attestationcert =?,
                                                                  locomotion =?,
                                                                  permis =?,
                                                                  obspermis =?,
                                                                  cartebtp =?,
                                                                  titre =?,
                                                                  mailsalarie=?,
                                                                  numagrementpole=?,
                                                                  dpae=?,
                                                                  numurssaf=?,
                                                                  datefse=?,
                                                                  datesortiefse=?,
                                                                  idfse=?,
                                                                  idextranet = ?,
                                                                  droitimage = ?
                                                                

                                        WHERE id = ? ");
          $requete->execute([
            $request->getParsedBody()['idpole'],
            $request->getParsedBody()['matricule'],
            $request->getParsedBody()['nom'],
            $request->getParsedBody()['prenom'],
            $request->getParsedBody()['datenaissance'],
            $request->getParsedBody()['tranche'],
            $request->getParsedBody()['lieunaissance'],
            $request->getParsedBody()['dptnaissance'],
            $request->getParsedBody()['genre'],
            $request->getParsedBody()['adresse1'],
            $request->getParsedBody()['adresse2'],
            $request->getParsedBody()['villeadresse'],
            $request->getParsedBody()['codepostaladresse'],
            $request->getParsedBody()['datedebcontrat'],
            $request->getParsedBody()['datefininitial'],
            $request->getParsedBody()['datefininitial'],
            $request->getParsedBody()['ressources'],
            $request->getParsedBody()['id_lieu_travail'],
            $request->getParsedBody()['motiffincontrat'],
            $request->getParsedBody()['situffincontrat'],
            $_SESSION['userid'],
            $request->getParsedBody()['dateametra'],
            $request->getParsedBody()['telephone'],
            $request->getParsedBody()['paysnaissance'],
            $request->getParsedBody()['nationalite'],
            $datesejour,
            $request->getParsedBody()['datemutuelle'],
            $request->getParsedBody()['mutuelle'],
            $request->getParsedBody()['portabilite'],
            $request->getParsedBody()['contact'],
            $request->getParsedBody()['poleemploi'],
            $request->getParsedBody()['datepoleemploi'],
            $request->getParsedBody()['securitesociale'],
            $request->getParsedBody()['niveauetude'],
            $request->getParsedBody()['qpv'],
            $request->getParsedBody()['prescripteur'],
            $request->getParsedBody()['referent'],
            $request->getParsedBody()['telreferent'],
            $request->getParsedBody()['mailreferent'],
            $request->getParsedBody()['adressereferent'],
            $request->getParsedBody()['rqth'],
            $request->getParsedBody()['numcaf'],
            $request->getParsedBody()['dureechomage'],
            $request->getParsedBody()['situfamiliale'],
            $request->getParsedBody()['enfants'],
            $request->getParsedBody()['contratpendantperiode'],
            $request->getParsedBody()['droitdif'],
            $request->getParsedBody()['nbreheuredif'],
            $request->getParsedBody()['sommedif'],
            $request->getParsedBody()['attestationcert'],
            $request->getParsedBody()['locomotion'],
            $request->getParsedBody()['permis'],
            $request->getParsedBody()['obspermis'],
            $request->getParsedBody()['cartebtp'],
            $request->getParsedBody()['titre'],
            $request->getParsedBody()['mailsalarie'],
            $request->getParsedBody()['numagrementpole'],
            $request->getParsedBody()['dpae'],
            $request->getParsedBody()['numurssaf'],
            $request->getParsedBody()['datefse'],
            $request->getParsedBody()['datesortiefse'],
            $request->getParsedBody()['idfse'],
            $request->getParsedBody()['idextranet'],
            $request->getParsedBody()['image'],
            $request->getParsedBody()['idsalarie'],
            

          ]);

          //Mise à jour fichier permisal
          //$this->logger->info("==>Ajout documents Salarié: ".$request->getParsedBody()['mail']);
          $requete = $this->pdo->prepare("DELETE FROM `permissalarie`
                                          WHERE idsal = ?

                                        ");
          $requete->execute([ $request->getParsedBody()['idsalarie']]);
            foreach($request->getParsedBody()['permis'] as $spermis){
              $requete = $this->pdo->prepare("INSERT INTO `permissalarie` (
                                                                            idsal,
                                                                            idperm,
                                                                            idtravail
                                                                     )
                                               VALUES (?,?,?)");
              $requete->execute([ $request->getParsedBody()['idsalarie'],
                                  $spermis,
                                  $request->getParsedBody()['id_lieu_travail']
                                ]);
            }
          //Mise à jour fichier locomtionsalarie
          //$this->logger->info("==>Ajout documents Salarié: ".$request->getParsedBody()['mail']);
          $requete = $this->pdo->prepare("DELETE FROM `locomotionsalarie`
                                          WHERE idsal = ?

                                        ");
          $requete->execute([ $request->getParsedBody()['idsalarie']]);

            foreach($request->getParsedBody()['locomotion'] as $slocom){
              //dump($slocom);

              $requete = $this->pdo->prepare("INSERT INTO `locomotionsalarie` (
                                                                            idsal,
                                                                            idlocomotion,
                                                                            idtravail
                                                                     )
                                               VALUES (?,?,?)");
              $requete->execute([ $request->getParsedBody()['idsalarie'],
                                  $slocom,
                                  $request->getParsedBody()['id_lieu_travail']
                                ]);

              //die();
            }
        }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));
      return $response;
  }

  // Méthode delsalarie
  //suppression salarie
  public function okdelsalarie(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
      $this->logger->info("==>Suppression Salarié : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("DELETE FROM `salarie`
                                      WHERE id = ?

                                    ");
      $requete->execute([
                        $args['idsalarie']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));


    return $response;
  }




  // Méthode insertion nouveau document à fournir
  // permet d'insérer des nouveux documents à fournir
  public function insertpresence(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout documents : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `presence` (
                                                                presence

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['presence']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('presence'));
    }

    return $response;
  }
  // Méthode insertion nouveau document à fournir
  // permet d'insérer des nouveux documents à fournir
  public function insertdoc(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout documents : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `documents` (
                                                                documents

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['documents']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('DocList'));
    }

    return $response;
  }
  // Méthode insertion sortie sur la table salarie
  // permet de mettre à jour
  public function insertsortiesal(RequestInterface $request, ResponseInterface $response,$args)
  {
    $id=$request->getParsedBody()['idsalarie'];


    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {

      $this->logger->info("==>Ajout documents : ".$request->getParsedBody()['mail']);

        $vtypesortie = $request->getParsedBody()['typesortie'];
        dump($vtypesortie);

        $requete = $this->pdo->prepare("SELECT idsortie FROM typesortie
                                        WHERE idtypesortie = ?
                                      ");
        $requete->execute([$vtypesortie]);
        $sortie = $requete->Fetch();
        $vsortie = $sortie[0];
        $idsalarie = $request->getParsedBody()['idsalarie'];
        dump($sortie);
        dump($vsortie);

       if($request->getParsedBody()['datesortie']){
         $vdate = $request->getParsedBody()['datesortie'];
       }else{
        $vdate = null;
       }
       

          $requete = $this->pdo->prepare("UPDATE salarie set
                                                            idsortie = ?,
                                                            typesortie = ?,
                                                            motifsortie = ?,
                                                            datesortie = ?,
                                                            detailsortie = ?,
                                                            id_usersortie = ?
                                                            
                                     WHERE id =  $idsalarie");
          $requete->execute([
                            $vsortie,
                            $request->getParsedBody()['typesortie'],
                            $request->getParsedBody()['motifsortie'],
                            $vdate,
                            $request->getParsedBody()['detail'],
                            $_SESSION['userid']
                            ]);
   
      }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));
   

    return $response;
  }


  // Méthode insertion nouvelle frein à l'entrée
  // permet d'insérer des lignes dans diagnostic
  public function insertdiag(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout documents : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `frein` (
                                                                libelle

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['diagnostic']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('newdiag'));
    }

    return $response;
  }

  // permet d'insérer des nouveux documents à fournir
   public function inserttitre(RequestInterface $request, ResponseInterface $response)
   {
     // Vérification CSRF()
     if (false === $request->getAttribute('csrf_status')) {
       die("Opération impossible...");
     } else {
       $this->logger->info("==>Ajout type de pièces d'identités : ".$request->getParsedBody()['mail']);
       $identite = strtoupper($request->getParsedBody()['identite']);
       // dump($identite);
       $requete = $this->pdo->prepare("INSERT INTO `titre` (
                                                                 titre
                                                           )
       VALUES (?)");
       $requete->execute([
                         $identite
                        ]);
       $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home'));
     }

     return $response;
   }

  // Méthode insertion nouveau document fournis par le salarié
  // permet d'insérer des nouveux documents à fournir
  public function docsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {

      $this->logger->info("==>Ajout documents Salarié: ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("DELETE FROM `docsafournirsalarie`
                                      WHERE idsalarie = ?

                                    ");
      $requete->execute([ $request->getParsedBody()['idsalarie'] ]);
        foreach($request->getParsedBody()['check'] as $scheck){
          $requete = $this->pdo->prepare("INSERT INTO `docsafournirsalarie` (
                                                                        idsalarie,
                                                                        iddocuments
                                                                            )
                                           VALUES (?,?)");
          $requete->execute([ $request->getParsedBody()['idsalarie'],
                              $scheck
                            ]);

      }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',[ 'idsalarie' =>$request->getParsedBody()['idsalarie'] ]));
      return $response;

  }

  // Méthode insertion démarche salarié

  public function insertdemarchesalarie(RequestInterface $request, ResponseInterface $response, $args)
  {

  /*    $requete = $this->pdo->prepare("DELETE FROM demarchesalarie
                                      WHERE idsalarie = ?
                                   ");
      $requete->execute([ $request->getParsedBody()['idsalarie']]);
  */


      $this->logger->info("==>Ajout démarches salarié : ".$request->getParsedBody()['mail']);
    
      $vdate=date("y.m.d");
   
        
      foreach($request->getParsedBody()['checkdetail'] as $scheckt){

        dump($scheckt);
       
        
        $requete = $this->pdo->prepare("SELECT * from `detailtypedemarche`
                                        WHERE iddetailtype =$scheckt");
         $requete->execute();
         $deta=$requete->fetch();
         $type=$deta[idtypedemar];
         $demas=$deta[iddem];
         dump($deta);
         dump($type);
         dump($dema);
         

         
        
          $requete = $this->pdo->prepare("INSERT INTO `demarchesalarie` ( idsalarie,
                                                                          iddemarche,
                                                                          idtypedemarsal,
                                                                          iddetailtypesal,
                                                                          idlieu,
                                                                          iduser,
                                                                          datesaisie
                                                                        )
                                        VALUES (?,?,?,?,?,?,?)");
         $requete->execute([$request->getParsedBody()['idsalarie'],
         $demas,
         $type,
         $scheckt,
         $request->getParsedBody()['idlieu'],
         $_SESSION['userid'],
         $vdate
         //$request->getParsedBody()['ddate']
        
                         ]);

     }
     $type = "";
     foreach($request->getParsedBody()['checktype'] as $scheckt){

      $requete = $this->pdo->prepare("SELECT * from `typedemarche`
                                      WHERE idtypedemarche =$scheckt");
          $requete->execute();
          $deta=$requete->fetch();
          $dema=$deta[iddemar];
          dump($deta);
          dump($dema);
      $requete = $this->pdo->prepare("INSERT INTO `demarchesalarie` ( idsalarie,
                                                                      iddemarche,
                                                                      idtypedemarsal,
                                                                      iddetailtypesal,
                                                                      idlieu,
                                                                      iduser,
                                                                      datesaisie
                                                                    )
VALUES (?,?,?,?,?,?,?)");
$requete->execute([$request->getParsedBody()['idsalarie'],
$dema,
$scheckt,
$type,
$request->getParsedBody()['idlieu'],
$_SESSION['userid'],
$vdate,
//$request->getParsedBody()['ddate'],
        
]);

       

     }


      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('demarchesalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
      return $response;

  }

  // Méthode insertion frein salarié
  // permet d'insérer freins
  public function freinsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {

      $this->logger->info("==>Ajout freins salarié : ".$request->getParsedBody()['mail']);
      $idsalarie=$request->getParsedBody()['idsalarie'];
      $requete = $this->pdo->prepare("DELETE FROM `freinsalarie`
                                      WHERE idsalarie = ?

                                    ");
      $requete->execute([ $request->getParsedBody()['idsalarie']]);
      foreach($request->getParsedBody()['check'] as $scheck){
          $requete = $this->pdo->prepare("INSERT INTO `freinsalarie` (
                                                                        idsalarie,
                                                                        idfreine,
                                                                        idtravail
                                                                            )
                                           VALUES (?,?,?)");
          $requete->execute([$request->getParsedBody()['idsalarie'],
                              $scheck,
                              $request->getParsedBody()['idlieu']
                            ]);

      }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
      return $response;

  }

  // Méthode insertion projet salarié
  // permet d'insérer le projet du salarié
  public function projetsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {

      $this->logger->info("==>Ajout projet salarié : ".$request->getParsedBody()['mail']);
      dump($request->getParsedBody()['idlieu']);
      $vdate = date("y.m.d");
 
      $idsalarie=$request->getParsedBody()['idsalarie'];
        
      foreach($request->getParsedBody()['checkprojets'] as $scheckprojet){
          $requete = $this->pdo->prepare("INSERT INTO `projetsalarie` (
                                                                        idsalarie,
                                                                        idproj,
                                                                        idtravail,
                                                                        datesaisie
                                                                            )
                                           VALUES (?,?,?,?)");
          $requete->execute([$request->getParsedBody()['idsalarie'],
                              $scheckprojet,
                              $request->getParsedBody()['idlieu'],
                              $vdate
                            ]);

      }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
      return $response;

  }
  // Méthode insertion objectif salarié
  // permet d'insérer l'objectif' du salarié
  public function objectifsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {

      $this->logger->info("==>Ajout objectif salarié : ".$request->getParsedBody()['mail']);
      $idsalarie=$request->getParsedBody()['idsalarie'];
      $vdate = date("y.m.d");
      $obsobj = $request->getParsedBody()['obsobj'];
    dump($request->getParsedBody()['obsobj']);
    
      foreach($request->getParsedBody()['checkobjectifs'] as $scheckobjectif){
          $requete = $this->pdo->prepare("INSERT INTO `objectifsalarie` (
                                                                        idsalarie,
                                                                        idobj,
                                                                        idtravail,
                                                                        datesaisie,
                                                                        objectifs

                                                                            )
                                           VALUES (?,?,?,?,?)");
          $requete->execute([$request->getParsedBody()['idsalarie'],
                              $scheckobjectif,
                              $request->getParsedBody()['idlieu'],
                              $vdate,
                              $request->getParsedBody()['obsobj'],
                            ]);

      }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
      return $response;

  }

  
  // Méthode insertentretien
  // permet d'insérer entretien, diagnostic, projet, objectifs
  public function insertentretien(RequestInterface $request, ResponseInterface $response, $args)
  {

      $this->logger->info("==>Ajout entretien : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `entretien` (
                                                               idsalarie,
                                                               experience,
                                                               diagnostic,
                                                               competence,
                                                               vdate,
                                                               id_user

                                                               )
                                      VALUES (?,?,?,?,?,?)");
           //$requete->execute([$request->getParsedBody()['idsalarie']]);
      $requete->execute([
                              $request->getParsedBody()['idsalarie'],
                              $request->getParsedBody()['experience'],
                              $request->getParsedBody()['diagnostic'],
                              $request->getParsedBody()['competence'],
                              $request->getParsedBody()['vdate'],
                              $_SESSION['userid'],

                         ]);

    


  $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
  return $response;
  }
// Méthode update modification entretien

public function updatemodifentretien(RequestInterface $request, ResponseInterface $response,$args)
{
  
  //$vdate=datetime($ddate);
  // Vérification CSRF()
  if (false === $request->getAttribute('csrf_status')) {
    die("Opération impossible...");
  } else {
    $this->logger->info("==>Modification 1er entretien salarié : ".$request->getParsedBody()['mail']);
    $identretien = $request->getParsedBody()['identretien'];
    $idsalarie = $request->getParsedBody()['idsalarie'];
  /*  dump($identretien);
    die();*/
        $requete = $this->pdo->prepare("UPDATE entretien
        SET   experience=?,
              diagnostic=?,
              competence=?,
              vdate = ?,
              id_user =?
        WHERE identretien= ? ");

    $requete->execute([ $request->getParsedBody()['experience'],
    $request->getParsedBody()['diagnostic'],
    $request->getParsedBody()['competence'],
    $request->getParsedBody()['vdate'],
    $_SESSION['userid'],
    $identretien]);
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    }

    return $response;
  }

  // Méthode insertion suivi

  public function insertsuivi(RequestInterface $request, ResponseInterface $response,$args)
  {

    //$vdate=datetime($ddate);
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de permis : ".$request->getParsedBody()['mail']);
     
      $requete = $this->pdo->prepare("INSERT INTO `suivi` (
                                                            idsalarie,
                                                            idpresence,
                                                            idmission,
                                                            suivi,
                                                            pointchantier,
                                                            datesuivi,
                                                            id_user
                                                          )
                                      VALUES (?,?,?,?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idsalarie'],
                        $request->getParsedBody()['idpresence'],
                        $request->getParsedBody()['idmission'],
                        $request->getParsedBody()['suivi'],
                        $request->getParsedBody()['point'],
                        $request->getParsedBody()['vdate'],
                        $_SESSION['userid']]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('entretiensalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    }

    return $response;
  }

// Méthode insertion modification suivi

public function insertmodifsuivi(RequestInterface $request, ResponseInterface $response)
{
  
  //$vdate=datetime($ddate);
  // Vérification CSRF()
  if (false === $request->getAttribute('csrf_status')) {
    die("Opération impossible...");
  } else {
    $this->logger->info("==>Modification Suivi salarié : ".$request->getParsedBody()['mail']);
    $idsuivi = $request->getParsedBody()['idsuivi'];
    dump($idsuivi);
        $requete = $this->pdo->prepare("UPDATE suivi
        SET   idpresence=?,
              idmission=?,
              suivi=?,
              pointchantier = ?,
              datesuivi=?,
              id_user =?
        WHERE idsuivi = $idsuivi ");

    $requete->execute([ $request->getParsedBody()['idpresence'],
    $request->getParsedBody()['idmission'],
    $request->getParsedBody()['suivi'],
    $request->getParsedBody()['point'],
    $request->getParsedBody()['vdate'],
    $_SESSION['userid'] ]);
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('entretiensalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    }

    return $response;
  }

  // Méthode suppression suivi

public function supprsuivi(RequestInterface $request, ResponseInterface $response,$args)
{
  
  
    $this->logger->info("==>Supression suivi: ".$request->getParsedBody()['mail']);
    $idsuivi = $args['idsuivi'];
    $idsala = $args[idsala];
    dump($idsala);
    dump($request->getParsedBody()['idsala']);
    
   
    
    $requete = $this->pdo->prepare("DELETE FROM suivi
                                        WHERE idsuivi = ? ");

    $requete->execute([$idsuivi]);
    $requete = $this->pdo->prepare("ALTER TABLE suivi AUTO_INCREMENT = 0 ");

    $requete->execute();
   
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('entretiensalarie',['idsalarie' =>$idsala]));
   

    return $response;
  }


 // Méthode suppression formation salarié

 public function supprformationsal(RequestInterface $request, ResponseInterface $response,$args)
 {
   
   
     $this->logger->info("==>Supression formation: ".$request->getParsedBody()['mail']);
     $idcqp = $args['idcqp'];
          
     $requete = $this->pdo->prepare("DELETE FROM cqp
                                         WHERE idcqp = ? ");
 
     $requete->execute([$idcqp]);
     $requete = $this->pdo->prepare("ALTER TABLE cqp AUTO_INCREMENT = 0 ");
 
     $requete->execute();
    
     
     $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$args['idsalarie']]));
    
 
     return $response;
   }

   // Méthode suppression pmsmp salarié

 public function supprpmsmp(RequestInterface $request, ResponseInterface $response,$args)
 {
   
   
     $this->logger->info("==>Supression pmsmp: ".$request->getParsedBody()['mail']);
     $id = $args['id'];
          
     $requete = $this->pdo->prepare("DELETE FROM pmsmp
                                         WHERE id = ? ");
 
     $requete->execute([$id]);
     $requete = $this->pdo->prepare("ALTER TABLE pmsmp AUTO_INCREMENT = 0 ");
 
     $requete->execute();
    
     
     $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$args['idsalarie']]));
    
 
     return $response;
   }
  // Méthode modification pmsmp salarié

  public function modifpmsmp(RequestInterface $request, ResponseInterface $response,$args)
  {
    $idpmsmp = $args[id];
    $idsalarie = $args[idsalarie];
    //dump($idpmsmp);
    //dump($idsalarie);

    $requete = $this->pdo->prepare("SELECT nom, prenom  FROM salarie
                                         WHERE id = ? ");
 
    $requete->execute([$idsalarie]);
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                         WHERE id = ? ");
 
    $requete->execute([$idpmsmp]);
    $pmsmps = $requete->fetchAll();
    //dump($pmsmps);
    //dump($salaries);
    //die();  
           
    $this->render($response, 'force/modifpmsmp.twig',
                              [  
                                'titre'     => 'Modification PMSMP de : ',
                                'pmsmps'    => $pmsmps,
                                'idpmsmp'   => $idpmsmp,
                                'idsalarie' => $idsalarie,
                                'salaries'  => $salaries
                              
                              ]);      
    }

    /// Méthode update pmsmp sauvegarde dans la table des données modifiées
    public function updatepmsmp(RequestInterface $request, ResponseInterface $response,$args)
    {
       
      $this->logger->info("==>Ajoutrenouvellement: ".$request->getParsedBody()['mail']);
      $idpmsmp = $request->getParsedBody()[idpmsmp];
      $idsalarie = $request->getParsedBody()[idsalarie];
      dump($idpmsmp);
      dump($idsalarie);
    
     
      $requete = $this->pdo->prepare("UPDATE pmsmp set 
                                        id            = ?,
                                        idsalarie     = ?,
                                        datedeb       = ?,
                                        datefin       = ?,
                                        entreprise    = ?,
                                        siret         = ?,
                                        activite      = ?,
                                        ville         = ?,
                                        departement   = ?,
                                        contactpmsmp  = ?,
                                        telephone     = ?,
                                        mail          = ?,
                                        nbheures      = ?,
                                        bilan         = ?,
                                        prolongation  = ?
                                      WHERE id = $idpmsmp ");
   
      $requete->execute([ $request->getParsedBody()['idpmsmp'],
                          $request->getParsedBody()['idsalarie'],
                          $request->getParsedBody()['datedeb'],
                          $request->getParsedBody()['datefin'],
                          $request->getParsedBody()['entreprise'],
                          $request->getParsedBody()['siret'],
                          $request->getParsedBody()['activite'],
                          $request->getParsedBody()['ville'],
                          $request->getParsedBody()['departement'],
                          $request->getParsedBody()['contact'],
                          $request->getParsedBody()['telephone'],
                          $request->getParsedBody()['mail'],
                          $request->getParsedBody()['nbreh'],
                          $request->getParsedBody()['bilan'],
                          $request->getParsedBody()['prolongation'],
                           ]);
      
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
      return $response;
      }
   
      // Méthode suppression date chantier  salarié

 public function supprdatechant(RequestInterface $request, ResponseInterface $response,$args)
 {
   
   
     $this->logger->info("==>Supression formation chantier: ".$request->getParsedBody()['mail']);
     $iddatechant = $args['iddatechant'];
     
          
     $requete = $this->pdo->prepare("DELETE FROM datechantier
                                         WHERE iddatechant = ? ");
 
     $requete->execute([$iddatechant]);
     $requete = $this->pdo->prepare("ALTER TABLE datechantier AUTO_INCREMENT = 0 ");
 
     $requete->execute();
    
     
     $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('cqpchantier',['idlieu' =>$args['idlieut']]));
    
 
     return $response;
   }

   
    // Méthode suppression sanction salarie

public function supprsanction(RequestInterface $request, ResponseInterface $response,$args)
{
  
  
    $this->logger->info("==>Supression sanction salarié: ".$request->getParsedBody()['mail']);
    
   $idsanctionsal = $args['idsanctionsal'];
   
    $requete = $this->pdo->prepare("DELETE FROM sanctionsalarie
                                        WHERE idsanctionsal = ? ");

    $requete->execute([$idsanctionsal]);
    $requete = $this->pdo->prepare("ALTER TABLE sanctionsalarie AUTO_INCREMENT = 0 ");

    $requete->execute();
   
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('sanctionsalarie',['idsalarie' =>$args['idsalarie']]));
   

    return $response;
  }

      // Méthode suppression renouvellement salarie

public function supprrenewsal(RequestInterface $request, ResponseInterface $response,$args)
{
  
  
    $this->logger->info("==>Supression renouvellement salarié: ".$request->getParsedBody()['mail']);
    
   $idrenewsal = $args['idrenewsal'];
   
    $requete = $this->pdo->prepare("DELETE FROM renouvellement
                                        WHERE idrenewsal = ? ");

    $requete->execute([$idrenewsal]);
    $requete = $this->pdo->prepare("ALTER TABLE renouvellement AUTO_INCREMENT = 0 ");

    $requete->execute();
   
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$args['idsalarie']]));
   

    return $response;
  }
      // Méthode modification renouvellement salarie

      public function modifrenewsal(RequestInterface $request, ResponseInterface $response,$args)
      {
        
        
          $this->logger->info("==>Modification renouvellement salarié: ".$request->getParsedBody()['mail']);
          
         $idrenewsal = $args['idrenewsal'];
         $requete = $this->pdo->prepare("SELECT * FROM renouvellement
                                        LEFT JOIN salarie on salarie.id = renouvellement.idsalarie
                                        WHERE idrenewsal = ? ");

        $requete->execute([$idrenewsal]);
        $renews  = $requete->fetchAll();
        foreach ($renews as $renew ){
          $idsalarie = $renew[idsalarie];
               
        }  

        $this->render($response, 'force/modifrenewsal.twig', ['titre'=>'Modification Renouvellement Salarié', 
                                                                'renews'=> $renews,
                                                                'idsalarie' => $idsalarie
                                                              ]);
      }      
        
         
      
   
      

    // Méthode suppression 1er entretien

public function supprentretien(RequestInterface $request, ResponseInterface $response,$args)
{
  
  
    $this->logger->info("==>Supression entretien: ".$request->getParsedBody()['mail']);
    $identretien = $args['identretien'];
    
    dump($request->getParsedBody()['idsala']);
    
    $requete = $this->pdo->prepare("DELETE FROM entretien
                                        WHERE identretien = ? ");

    $requete->execute([$identretien]);
    $requete = $this->pdo->prepare("ALTER TABLE entretien AUTO_INCREMENT = 0 ");

    $requete->execute();
   
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$args['idsala']]));
      
    return $response;
  }

  public function modifentretien(RequestInterface $request, ResponseInterface $response,$args)
  {
  
  
    $this->logger->info("==>Supression entretien: ".$request->getParsedBody()['mail']);
   
    $identretien = $args['identretien'];
        
    $requete = $this->pdo->prepare("SELECT * FROM entretien
                                    LEFT JOIN salarie on salarie.id = entretien.idsalarie
                                    WHERE identretien = ? ");

    $requete->execute([$identretien]);
    $entretiens = $requete->fetchAll();
    foreach($entretiens as $entretien){
    $idsalarie = $entretien[idsalarie];
    
    }
    $this->render($response, 'force/modifentretien.twig', ['titre'=>'Modification 1er Entretien', 
                                                            'entretiens'=> $entretiens,
                                                            'idsalarie' => $idsalarie
                                                          ]);
  }
    // Méthode suppression objectif du salarié

    public function supprobjectif(RequestInterface $request, ResponseInterface $response,$args)
    {
      
      
        $this->logger->info("==>Supression objectif salarié: ".$request->getParsedBody()['mail']);
        $idobjsal = $args['idobjsal'];
        $idsalarie = $args['idsalarie'];
        $idsal = $request->getParsedBody()['idsal'];
        $idsala = $args['idsala'];
        
  
        $requete = $this->pdo->prepare("DELETE FROM objectifsalarie
                                            WHERE idobjsal = ? ");
    
        $requete->execute([$idobjsal]);
        $requete = $this->pdo->prepare("ALTER TABLE objectifsalarie AUTO_INCREMENT = 0 ");
    
        $requete->execute();
       
        
        $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$args['idsala']]));
      
       
    
        return $response;
      }

          // Méthode suppression projet du salarié

    public function supprprojet(RequestInterface $request, ResponseInterface $response,$args)
    {
      
      
        $this->logger->info("==>Supression objectif salarié: ".$request->getParsedBody()['mail']);
        $idprojsal = $args['idprojsal'];
        dump($idsalarie);
        dump($request->getParsedBody()['idsalarie']);
        
        $requete = $this->pdo->prepare("DELETE FROM projetsalarie
                                            WHERE idprojsal = ? ");
    
        $requete->execute([$idprojsal]);
        $requete = $this->pdo->prepare("ALTER TABLE projetsalarie AUTO_INCREMENT = 0 ");
    
        $requete->execute();
       
        
        $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$args['idsala']]));
      
       
    
        return $response;
      }
    

   // Méthode suppression démarche salarié

public function supprdemarchesal(RequestInterface $request, ResponseInterface $response,$args)
{
  
  
    $this->logger->info("==>Supression suivi: ".$request->getParsedBody()['mail']);
    $iddemsal = $args['iddemsal'];
        
    $requete = $this->pdo->prepare("DELETE FROM demarchesalarie
                                        WHERE iddemsal = ? ");

    $requete->execute([$iddemsal]);
    $requete = $this->pdo->prepare("ALTER TABLE demarchesalarie AUTO_INCREMENT = 0 ");

    $requete->execute();
   
    
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('demarchesalarie',['idsalarie' =>$args['idsala']]));
   

    return $response;
  }


 

  // Méthode insertion diagnostic

  public function insertdiagnostic(RequestInterface $request, ResponseInterface $response)
  {

    //$vdate=datetime($ddate);
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de permis : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `suivi` (
                                                            idsalarie,
                                                            diagnostic,
                                                            competence,
                                                            datesuivi,
                                                            id_user
                                                          )
                                      VALUES (?,?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idsalarie'],
                        $request->getParsedBody()['diagnostic'],
                        $request->getParsedBody()['competence'],
                        $request->getParsedBody()['vdate'],
                        $_SESSION['userid'],
                       ]);


      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));
    }

    return $response;
  }
  // Méthode insertion nouveau type de permis

  public function insertpermis(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de permis : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `permis` (
                                                                permis

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['permis']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('permis'));
    }

    return $response;
  }
  // Méthode insertion nouveau type de zone
  public function insertzone(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de zone : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `zone` (
                                                                zone

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['zone']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('zone'));
    }

    return $response;
  }
 // Méthode insertion nouvel objectif
 public function insertobjectif(RequestInterface $request, ResponseInterface $response)
 {
   // Vérification CSRF()
   if (false === $request->getAttribute('csrf_status')) {
     die("Opération impossible...");
   } else {
     $this->logger->info("==>Ajout type objectif : ".$request->getParsedBody()['mail']);
     $requete = $this->pdo->prepare("INSERT INTO `objectifs` (
                                                               objectif

                                                             )
     VALUES (?)");
     $requete->execute([
                       $request->getParsedBody()['objectif']
                      ]);
     $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('objectif'));
   }

   return $response;
 }

 // Méthode insertion nouveau projet
 public function insertprojet(RequestInterface $request, ResponseInterface $response)
 {
   // Vérification CSRF()
   if (false === $request->getAttribute('csrf_status')) {
     die("Opération impossible...");
   } else {
     $this->logger->info("==>Ajout type projet : ".$request->getParsedBody()['mail']);
     $requete = $this->pdo->prepare("INSERT INTO `projets` (
                                                               projet

                                                             )
     VALUES (?)");
     $requete->execute([
                       $request->getParsedBody()['projet']
                      ]);
     $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('projet'));
   }

   return $response;
 }

  // Méthode insertion nouveau type de sanction
  public function insertsanction(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de sanction : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `sanction` (
                                                                sanctions

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['sanction']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('sanction'));
    }

    return $response;
  }
  // Méthode insertion sanction alarié
  public function insertsanctionsal(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {

      $this->logger->info("==>Ajout type de sanction : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `sanctionsalarie` (
                                                                      idsalarie,
                                                                      datesanction,
                                                                      sanction,
                                                                      motif,
                                                                      dateenvoi,
                                                                      refenvoi,
                                                                      idlieu,
                                                                      obs,
                                                                      iduser
                                                                    )
      VALUES (?,?,?,?,?,?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idsalarie'],
                        $request->getParsedBody()['datesanction'],
                        $request->getParsedBody()['typesanction'],
                        $request->getParsedBody()['motifsanction'],
                        $request->getParsedBody()['dateenvoi'],
                        $request->getParsedBody()['refenvoi'],
                        $request->getParsedBody()['idlieu'],
                        $request->getParsedBody()['observ'],
                        $_SESSION['userid'],
                       ]);

    }
      //$response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('demarchesalarie',['idsalarie' =>$args['idsala']]));                       
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('sanctionsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));

    return $response;
  }

  

  // Méthode insertion nouveau type de motif de sanction
  public function insertmotifsanction(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de motif de sanction : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `motifsanction` (
                                                                motifsanction

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['sanction']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('motifsanction'));
    }

    return $response;
  }

  // Méthode insertion nouveau type de sortie

  public function insertsortie(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de sortie : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `sortie` (
                                                                sortie

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['sortie']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('sortie'));
    }

    return $response;
  }
  // Méthode insertion nouveau motif de sortie

  public function insertmotif(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout motif de sortie : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `motifsortie` (
                                                                motifsortie

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['motif']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('motifsortie'));
    }

    return $response;
  }
  // Méthode insertion nouveau détail de sortie

  public function insertdetailsortie(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de sortie : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `typesortie` (
                                                                idsortie,
                                                                typesortie
                                                              )
      VALUES (?,?)");
      $requete->execute([
                        $request->getParsedBody()['idsortie'],
                        $request->getParsedBody()['typesortie']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('typesortie'));
    }

    return $response;
  }

  // Méthode insertion nouveau détail de sortie

  public function insertdemarche(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de détaile démarche : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `demarche` (
                                                               demarche
                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['typedemarche']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('newdemarche'));
    }

    return $response;
  }

  // Méthode insertion nouveau détail de sortie

  public function insertdetaildemarche(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de détaile démarche : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `typedemarche` (
                                                                iddemar,
                                                                typedemarche
                                                              )
      VALUES (?,?)");
      $requete->execute([
                        $request->getParsedBody()['iddemarche'],
                        $request->getParsedBody()['typedemarche']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('detaildemarche'));
    }

    return $response;
  }

  // Méthode insertion nouveau détail de sortie

  public function insertdetailtypedemarche(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout détail de type de démarche : ".$request->getParsedBody()['mail']);
     


      $requete = $this->pdo->prepare("SELECT iddemar FROM typedemarche
      WHERE (idtypedemarche = ?)");
      $requete->execute([
                        $request->getParsedBody()['idtypedemarche'],
                       ]);

      $lignes = $requete->fetch();
      $iddemarc= $lignes[iddemar];

      $requete = $this->pdo->prepare("INSERT INTO `detailtypedemarche` (
                                                                idtypedemar,
                                                                iddem,
                                                                detailtypedemarche,
                                                                iduser
                                                              )
      VALUES (?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idtypedemarche'],
                        $request->getParsedBody()['iddem'],
                        $request->getParsedBody()['detailtypedemarche'],
                        $_SESSION['userid'],

                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('detailtypedemarche'));
    }

    return $response;
  }
  // Méthode insertion nouveau type de formation

  public function insertformation(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de permis : ".$request->getParsedBody()['mail']);
                        
      $formation = $request->getParsedBody()['formation'];
      $formation = strtoupper($formation);
      $requete = $this->pdo->prepare("INSERT INTO `formation` (
                                                                formation

                                                              )
      VALUES (?)");
      $requete->execute([
                        $formation
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('formation'));
    }

    return $response;
  }

//Méthode insertion nouvelle date de renouvellement de contrat
  public function insertrenouv(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajoutrenouvellement: ".$request->getParsedBody()['mail']);
      $idsalarie=$request->getParsedBody()['idsalarie'];
      dump($idsalarie);
      $requete = $this->pdo->prepare("INSERT INTO `renouvellement` (
                                                                      idsalarie,
                                                                      newdebdate,
                                                                      nouvelle_date,
                                                                      engagement,
                                                                      observation,
                                                                      id_user
                                                                   )
                                      VALUES (?,?,?,?,?,?)
                                    ");
      $requete->execute([
                        $request->getParsedBody()['idsalarie'],
                        $request->getParsedBody()['newdatedeb'],
                        $request->getParsedBody()['newdatefin'],
                        $request->getParsedBody()['engagement'],
                        $request->getParsedBody()['observation'],
                        $_SESSION['userid'],
                       ]);

//// test date fin réelle de contrat (changement si salarie[datefinreelle] < $request->getParsedBody()['newdatefin']) 
    
      $requete = $this->pdo->prepare("SELECT * from `salarie`
                                      WHERE id = $idsalarie ");
      $requete->execute();
      $saltables = $requete->fetchAll();  
      
      foreach($saltables as $saltable){
     
            if ($saltable[datefinreelle] < $request->getParsedBody()['newdatefin']){
                  $requete = $this->pdo->prepare("UPDATE `salarie`
                                                  SET datefinreelle = ?
                                                  WHERE id = $idsalarie 
                                                ");
                  $requete->execute([ $request->getParsedBody()['newdatefin']
                                                                         ]);
            }
      }
    }

    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    return $response;
  }

  //Méthode Mise à jour renouvellement et test nouvelle date de renouvellement de contrat dans salarie
  public function updaterenewsal(RequestInterface $request, ResponseInterface $response,$args)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Updaterenouvellement: ".$request->getParsedBody()['mail']);
      $idsalarie=$request->getParsedBody()['idsalarie'];
      $idrenewsal = $request->getParsedBody()['idrenewsal'];
     
     
      
        //// Mise à jour renouvellement

      $requete = $this->pdo->prepare("UPDATE `renouvellement` SET
                                                                      newdebdate =?,
                                                                      nouvelle_date=?,
                                                                      engagement=?,
                                                                      observation=?,
                                                                      id_user=?
                                                                   
                                    WHERE idrenewsal = $idrenewsal ");
      $requete->execute([
                        $request->getParsedBody()['newdatedeb'],
                        $request->getParsedBody()['newdatefin'],
                        $request->getParsedBody()['engagement'],
                        $request->getParsedBody()['observation'],
                        $_SESSION['userid']
                        ]);
      
      
      //// test date fin réelle de contrat (changement si salarie[datefinreelle] < $request->getParsedBody()['newdatefin']) 
    
      $requete = $this->pdo->prepare("SELECT * from `salarie`
                                      WHERE id = $idsalarie ");
      $requete->execute();
      $saltables = $requete->fetchAll();
     
      foreach($saltables as $saltable){
        
       
          if ($saltable[datefinreelle] < $request->getParsedBody()['newdatefin']){
           
            
           $requete = $this->pdo->prepare("UPDATE `salarie`
            SET datefinreelle = ?

            WHERE id = ? ");
            $requete->execute([ $request->getParsedBody()['newdatefin'],
                                $idsalarie
                              ]);
                              
          }else{

            
          }
      }
      
    }            
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    return $response;
  }


  // Méthode insertion nouveau moyen de locomotion

  public function insertlocomotion(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout moyen de locomotion : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `locomotion` (
                                                                locomotion

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['locomotion']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('locomotion'));
    }

    return $response;
  }

  // Méthode insertion nouveau niveau d'études

  public function insertetudes(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout moyen de locomotion : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `niveauetude` (
                                                                niveau

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['niveau']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('etudes'));
    }

    return $response;
  }

  // Méthode insertion nouvelle situation familiale
  public function insertsituation(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout situation familiale : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `situfamiliale` (
                                                                situation

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['situation']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('situation'));
    }

    return $response;
  }

  // Méthode insertion nouvelle ressource
  public function insertressources(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout nouvelles Ressources : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `ressources` (
                                                                ressources

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['ressources']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('ressources'));
    }

    return $response;
  }



  // Méthode insertion nouveau chantier
  // permet d'insérer des chantiers dans la table lieutravail
  public function insertchantier(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout chantier : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `lieutravail` (
                                                                lieu,
                                                                fse,
                                                                encadrant,
                                                                dpt,
                                                                id_user

                                                                )
      VALUES (?,?,?,?,?)");
      $requete->execute([
        $request->getParsedBody()['chantier'],
        $request->getParsedBody()['fse'],
        $request->getParsedBody()['encadrant'],
        $request->getParsedBody()['departement'],
        $_SESSION['userid']
      ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('lieu'));
    }

    return $response;
  }

  // Méthode modif chantier
  // permet la modif des chantiers dans la table lieutravail
  public function insertmodifchantier(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout chantier : ".$request->getParsedBody()['mail']);
       $idlieu = ($request->getParsedBody()['idlieu']);
       dump($request->getParsedBody()['departement']);
       dump($request->getParsedBody()['chantier']);
       dump($request->getParsedBody()['fse']);
       // die();

    $requete = $this->pdo->prepare("UPDATE lieutravail
                                    SET   lieu=?,
                                          fse=?,
                                          encadrant=?,
                                          dpt = ?,
                                          contratinitial = ?,
                                          smic = ?,
                                          essai = ?,
                                          id_user =?
                                    WHERE idlieu = $idlieu ");

    $requete->execute([ $request->getParsedBody()['chantier'],
                        $request->getParsedBody()['fse'],
                        $request->getParsedBody()['encadrant'],
                        $request->getParsedBody()['departement'],
                        $request->getParsedBody()['contratinitial'],
                        $request->getParsedBody()['smic'],
                        $request->getParsedBody()['essai'],
                        $_SESSION['userid'] ]);
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('lieu'));
    }

    return $response;
  }

  // Méthode photo
  
  // permet d'uploader les photos des salariés

  public function photo(RequestInterface $request, ResponseInterface $response)
  {
    $this->logger->info("==>Ajout photo : ".$request->getParsedBody()['mail']);
     
      if(isset($_FILES['file']))
      {
        $tmpName = $_FILES['file']['tmp_name'];
        $name = $_FILES[file]['name'];
        $size = $_FILES['file']['size'];
        $error = $_FILES['file']['error'];
        dump($tmpName);
        dump($name);
        echo getcwd();
        var_dump($request->getParsedBody()['idsalarie']);
        $name = $request->getParsedBody()['idsalarie'].".jpg";
        dump($name);
              
        $uploaddir = '/../home/formatioye/force/public/assets/images/salaries/';
        copy($_FILES['file'][$tmpName], $uploaddir.$name);
        move_uploaded_file($tmpName, $uploaddir.$name);
       
      }
      
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    return $response;
  }



  // public function Search(RequestInterface $request, ResponseInterface $response)
  // {
  //   $this->render($response, 'force/salariesearch.twig', ['titre'=>'Recherche d\'un salarié']);
  // }



  // Méthode suivi
  // permet d'afficher le suivi des salariés en insertion
  public function suivi(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT nom,prenom,adresse1,adresse2 FROM salarie ");
    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/suivi.twig', ['titre'=>'Suivi des Salariés en insertion', 'lignes'=> $lignes]);
  }

  // Méthode de Recherche après POST
  // public function Requete(RequestInterface $request, ResponseInterface $response)
  // {
  //   $recherche = $request->getParsedBody()['recherche'];
  //   $requete = $this->pdo->prepare("SELECT marque,vehicule,moteur FROM vehicules WHERE marque LIKE ? OR vehicule LIKE ? OR moteur LIKE ?");
  //   $requete->execute(array($recherche.'%', $recherche.'%', $recherche.'%'));
  //   $lignes = $requete->fetchAll();
  //   $this->render($response, 'vehicules/vehicules.twig', ['titre'=>'Résultats par marque, véhicule ou moteur', 'lignes'=> $lignes]);
  // }


  // Méthode de Recherche après POST
  public function search(RequestInterface $request, ResponseInterface $response)
  {
    $recherche = $request->getParsedBody()['recherche'];
    $requete = $this->pdo->prepare("SELECT *
                                    FROM salarie
                                    LEFT JOIN lieutravail on salarie.id_lieu_travail = lieutravail.idlieu
                                    WHERE nom LIKE ? OR prenom LIKE ?");
    $requete->execute(array($recherche.'%', $recherche.'%'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/salarie.twig', ['titre'=>'Résultats' , 'lignes'=> $lignes]);
  }



  // Méthode de test TWIG
  public function Vide(RequestInterface $request, ResponseInterface $response)
  {
    $this->render($response, 'salarie/vide.twig', ['titre'=>'Cette page est vide']);
  }



  public function TestPDO(RequestInterface $request, ResponseInterface $response)
  {
  $requete = $this->pdo->query("SELECT marque,vehicule,moteur FROM vehicules");
  $lignes = $requete->fetchAll();
  echo "
  <table border='1'>
    <tr>
      <th>
        Marque
      </th>
      <th>
        Véhicule
      </th>
      <th>
        Moteur
      </th>
    <tr/>";
  foreach ($lignes AS $ligne) {
    echo "<tr><td>".$ligne['marque']."</td><td>".$ligne['vehicule']."</td><td>".$ligne['moteur']."</td></tr>";
  }
  echo "
  </table>
  ";
  }
}


