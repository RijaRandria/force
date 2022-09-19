<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class ForceController extends Controller
{

  //Méthode accueil
  // Affichage interface accueil

  ///requête préparée


  public function accueil(RequestInterface $request, ResponseInterface $response)
  {
    $dateout        = strtotime("today");
    $datein         = strtotime("-60 days");
    $datefincontrat = strtotime("+60 days");
    $vdateametra    = strtotime("-3 days");
    $vdatein        = date("d/m/Y",$datein);
    $vdateout       = date("d/m/Y",$dateout);
    $vdate          = date("d/m/y",today);
    $annee          = date_parse($vdateout);
    $vannee1        = date('Y',$dateout);
    $vannee         = $annee[year];

    $requete = $this->pdo->prepare("SELECT COUNT(id) FROM salarie
                                    WHERE (datesortie is null)
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


    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();
    foreach($lieux as $lieu){
      $chantier=$lieu[idlieu];
      $nomchantier=$lieu[lieu];

    }
    /////////

     $requete = $this->pdo->prepare("SELECT * FROM salarie");
     $requete->execute();
     $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT datesejour,nom, prenom,dateametra,datemutuelle,lieu,datefinreelle FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC

                                  ");
    $requete->execute();
    $sejours = $requete->fetchAll();


    $fincontrat = $requete->fetchAll();







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
    $this->render($response, 'force/force.twig', ['titre'=>'Pages d\'Accueil',
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

                                                ]);
  }
  //Méthode fincontrat
  // Affichage la liste de salariés proche de la fin de contrat
  public function fincontrat(RequestInterface $request, ResponseInterface $response)
  {

    $datein  = strtotime("today");
    $dateout = strtotime("+60 days");
    /////////
    $requete = $this->pdo->prepare("SELECT nom, prenom,id_lieu_travail,datefinreelle FROM salarie
                                    LEFT JOIN lieutravail ON id_lieu_travail=idlieu order by lieu ASC

                                  ");
    $requete->execute();
    $salaries = $requete->fetchAll();

    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();


    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/fincontrat.twig', ['titre'=>'Alerte fin CDDI le',
                                                  'salaries'          => $salaries,
                                                  'lieux'             => $lieux,
                                                  'datein'            => $datein,
                                                  'dateout'           => $dateout
                                                                                                ]);
  }



  //Méthode statistique
  // Affichage le stat global
  public function statistique(RequestInterface $request, ResponseInterface $response)
  {
    /////////
    $requete = $this->pdo->prepare("SELECT * FROM lieutravail");
    $requete->execute();
    $lieux = $requete->fetchAll();

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

    $requete = $this->pdo->prepare("SELECT * FROM salarie");
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

    $requete = $this->pdo->prepare("SELECT * FROM projetsalarie");
    $requete->execute();
    $projetsalarie = $requete->fetchAll();





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
                                                  'projetsalarie'       => $projetsalarie
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
    $requete = $this->pdo->prepare("SELECT id, niveau FROM niveauetude ");
    $requete->execute();
    $niveaux = $requete->fetchAll();
    //Préparation liste durée de chômage
    $requete = $this->pdo->prepare("SELECT id, duree FROM dureechomage ");
    $requete->execute();
    $durees = $requete->fetchAll();
    //Préparation liste ressources
    $requete = $this->pdo->prepare("SELECT id, ressources FROM ressources ");
    $requete->execute();
    $ressources = $requete->fetchAll();
    //Préparation liste situation familiale
    $requete = $this->pdo->prepare("SELECT id, situation FROM situfamiliale ");
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
                                                      'zones'       => $zones
                                                      ]);


  }
  //Méthode newdoc
  // Affichage interface pour saisie nouveau document à fournir
  public function Newdoc(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/docs.twig', ['titre'=>'Saisie Nouveau Document à Fournir']);
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
    $this->render($response, 'force/motifsanction.twig', ['titre'=>'Saisie type de motif de sanction']);
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
    $this->render($response, 'force/newdemarche.twig', ['titre'=>'Saisie type de démarche']);
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

  //Méthode newetudes
  // Affichage interface pour saisie nouveau niveau d'études
  public function Newidentite(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/identite.twig', ['titre'=>'Saisie nouvelles pièces d\'identité ']);
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
    $requete = $this->pdo->prepare("SELECT entreprise,
                                           siret,
                                           activite,
                                           ville,
                                           departement,
                                           contact,
                                           telephone,
                                           mail
                                    FROM pmsmp
                                    ORDER BY departement ASC

                                  ");
    $requete->execute();
    $societes = $requete->fetchAll();
    $this->render($response, 'force/societe.twig', ['titre'=>'Liste des Entreprises PMSMP', 'societes'=> $societes]);
  }
  // Méthode trisociete
  // permet d'afficher les lieux de travail
  public function trisociete(RequestInterface $request, ResponseInterface $response, $args)
  {
    $this->logger->info("==>tri société : ".$request->getParsedBody()['mail']);
    $cherche = $request->getParsedBody()['cherche'];
    $requete = $this->pdo->prepare("SELECT entreprise,
                                           siret,
                                           activite,
                                           ville,
                                           departement,
                                           contact,
                                           telephone,
                                           mail
                                    FROM pmsmp
                                    WHERE (entreprise LIKE ? OR activite LIKE ? OR ville  LIKE ? OR departement  LIKE ? OR contact LIKE ?)
                                    ORDER BY departement ASC

                                  ");
    $requete->execute(array('%'.$cherche.'%','%'.$cherche.'%','%'.$cherche.'%','%'.$cherche.'%','%'.$cherche.'%'));
    $societes = $requete->fetchAll();
    $this->render($response, 'force/societe.twig', ['titre'=>'Liste des Entreprises PMSMP', 'societes'=> $societes]);
  }

  /////méthode pmsmpquery
  public function pmsmpquery(RequestInterface $request, ResponseInterface $response)
  {

    $this->render($response, 'force/querysociete.twig', ['titre'=>'Requête sur les Entreprises PMSMP', 'societes'=> $societes]);
  }


  // Méthode lieu
  // permet d'afficher les lieux de travail
  // public function DocList(RequestInterface $request, ResponseInterface $response)
  // {
  //   $requete = $this->pdo->prepare("SELECT documents FROM documents ");
  //   $requete->execute();
  //   $lignes = $requete->fetchAll();
  //   $this->render($response, 'force/docafournir.twig', ['titre'=>'Liste des Documents à fournir', 'lignes'=> $lignes]);
  // }

  // Méthode salarie
  // permet d'afficher les salariés en insertion
  public function salarie(RequestInterface $request, ResponseInterface $response)
  {

    $vdate=getdate();
    $vdate = date("d-m-y");
    //$d=$date || date('y');
    //$y=$date(year);
// test

    //$date1=datetime();
    //$date=date("d m y");
    //dump($date);


    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM salarie
                                        LEFT JOIN lieutravail on salarie.id_lieu_travail = lieutravail.idlieu
                                       
                                        ORDER by lieu ASC
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

    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM salarie LEFT JOIN lieutravail on salarie.id_lieu_travail = lieutravail.idlieu
                                        WHERE id_lieu_travail = ?
                                      ");
        $requete->execute([$args['idlieu']]);
        //$requete->execute();
        $lignes = $requete->fetchAll();
        foreach($lignes as $ligne){
          $titre = "Liste des Salariés en insertion chantier de ".$ligne[lieu];
        }
        $this->render($response, 'force/salarie.twig', ['titre' => $titre,'lignes'=> $lignes] );
  }

  // Méthode cqpchantier
  // permet d'afficher la liste des dates de cqp 'un chantier défini
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
  // permet de saisir les dates cqp et sst
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
                                                                id_user
                                                               )
                                        VALUES (
                                                ?, ?, ?, ? ,?)");
        $requete->execute([
          $request->getParsedBody()['idlieu'],
          $request->getParsedBody()['datedeb'],
          $request->getParsedBody()['datefin'],
          $request->getParsedBody()['idformation'],
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
                                                                contact,
                                                                telephone,
                                                                mail,
                                                                nbheures,
                                                                bilan,
                                                                id_user
                                                        )
                                        VALUES (
                                                ?, ?, ?, ? ,?,
                                                ?, ?, ?, ? ,?,
                                                ?, ?, ?,?)
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
          $_SESSION['userid'],
          ]);
          $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
          return $response;
  }

  // méthode insertprolpmsmp

  public function insertprolpmsmp(RequestInterface $request, ResponseInterface $response, $args)
  {
    $this->logger->info("==>Ajout salarié : ".$request->getParsedBody()['mail']);


    //préparation requête table pmsmp pour prolongation
    $requete = $this->pdo->prepare("UPDATE pmsmp
                                    set datedebprol=?,
                                        datefinprol=?,
                                        nbheuresprol=?
                                    WHERE idsalarie = ? ");
    $requete->execute([ $request->getParsedBody()['datedebprol'],
                        $request->getParsedBody()['datefinprol'],
                        $request->getParsedBody()['nbrehprol'],
                        $request->getParsedBody()['idsalarie'] ]);


    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
    return $response;
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
                                                                  validation,
                                                                  datejury,
                                                                  observation,
                                                                  id_user
                                                                 )
                                          VALUES (
                                                  ?, ?, ?, ? ,?,?,?)");
          $requete->execute([
            $request->getParsedBody()['idsalarie'],
            $iddate,
            $request->getParsedBody()['nbreh'],
            $request->getParsedBody()['validation'],
            $request->getParsedBody()['datejury'],
            $request->getParsedBody()['observation'],
            $_SESSION['userid'],
            ]);


          $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('modifsalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
          return $response;
  }

  // Méthode modifsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function modifsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources,
                                           numagrementpole,
                                           dpae,
                                           numurssaf,
                                           datefse,
                                           idfse

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
      $dpae                 = $ligne[dpae];
      $numurssaf            = $ligne[numurssaf];
      $datefse              = $datefse;
      $idfse                = $idfse;




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
        $requete = $this->pdo->prepare("SELECT newdebdate, nouvelle_date, engagement, observation FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date ASC");
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
                                  'dpae'                  => $dpae,
                                  'numurssaf'             => $numurssaf,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'datefse'               => $datefse,
                                  'idfse'                 => $idfse,
                             ]
                 );
  }

  // Méthode delsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function delsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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
      $datefse              = $lignes[datefse];
      $idfse                = $lignes[idfse];

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
        $requete = $this->pdo->prepare("SELECT id, niveau FROM niveauetude ");
        $requete->execute();
        $niveaux = $requete->fetchAll();
        //Préparation liste durée de chômage
        $requete = $this->pdo->prepare("SELECT id, duree FROM dureechomage ");
        $requete->execute();
        $chomages = $requete->fetchAll();
        //Préparation liste ressources
        $requete = $this->pdo->prepare("SELECT id, ressources FROM ressources ");
        $requete->execute();
        $ressources = $requete->fetchAll();
        //Préparation liste situation familiale
        $requete = $this->pdo->prepare("SELECT id, situation FROM situfamiliale ");
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
    $this->render($response, 'force/modifsalarie.twig',
                              [
                                  'titre'                 =>'Mise à jour Dossier salarié',
                                  'idsalarie'             => $idsalarie,
                                  'lignes'                => $lignes,
                                  'idpole'                => $idpole,
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
                                  'idfse'                 => $idfse,
                             ]
                 );
  }

  // Méthode sortiesalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function sortiesalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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


    }

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
                             ]
                 );
  }

  // Méthode sanctionsalarie
  // permet d'afficher la fiche d'un salarié en insertion et modifier les données
  public function sanctionsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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


    }

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
                             ]
                 );
  }


  // Méthode suivisalarie
  // permet d'afficher la fiche de suivi d'un salarié en insertion
  public function suivisalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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

         $requete = $this->pdo->prepare("SELECT * FROM objectifs
         order by idobjectif ASC
        ");
          $requete->execute();
          $nettoyages = $requete->fetchAll();
          foreach ($nettoyages as $nettoyage) {
          $requete = $this->pdo->prepare("UPDATE  objectifs
                    SET coche = null
          
                  ");
          $requete->execute();
          }
          
          $requete = $this->pdo->prepare("SELECT * FROM projets
                  order by idprojet ASC
                  ");
          $requete->execute();
          $nettoyages = $requete->fetchAll();
          foreach ($nettoyages as $nettoyage) {
          $requete = $this->pdo->prepare("UPDATE  projets
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

        // objectifs

         $requete = $this->pdo->prepare("SELECT * FROM objectifsalarie
                                         WHERE idsalarie=?
                                       ");
         $requete->execute([$idsalarie]);
         $sales = $requete->fetchAll();
         foreach ($sales as $sale){


           $requete = $this->pdo->prepare("UPDATE  objectifs
                                          SET coche = $idsalarie
                                          WHERE objectifs.idobjectif = $sale[idobjectif]
                                        ");
           $requete->execute();
         }

         //  projets


         $requete = $this->pdo->prepare("SELECT * FROM projetsalarie
         WHERE idsalarie=?
                ");
          $requete->execute([$idsalarie]);
          $sales = $requete->fetchAll();
          foreach ($sales as $sale){
          

          $requete = $this->pdo->prepare("UPDATE  projets
                    SET coche = $idsalarie
                    WHERE projets.idprojet = $sale[idprojet]
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


       //table suivi => liste des freins à l'entré en action
       $requete = $this->pdo->prepare("SELECT diagnostic, competence FROM suivi
                                       WHERE idsalarie=?
                                       order by datesuivi ASC

                                     ");
       $requete->execute([$idsalarie]);
       $diagnostics = $requete->fetchAll();


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




        //
        //
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
                                  'diagnostics'           => $diagnostics,
                                  'sites'                 => $sites,
                                  'locosalarie'           => $locosalarie,
                                  'permsalarie'           => $permsalarie,
                                  'zones'                 => $zones,
                                  'renouvellement'        => $renouvellement,
                                  'objectifs'             => $objectifs,
                                  'projets'               => $projets,
                             ]
                 );
  }
  // Méthode diagnosticsalarie
  // permet d'afficher la fiche de diagnostic d'un salarié en insertion
  public function diagnosticsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $idsalarie=$args[idsalarie];

    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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

    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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

                                          order by iddemarche ASC
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



          //table demarche => liste des démarches
          $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                          ");
          $requete->execute();
          $typedemarches = $requete->fetchAll();
          foreach($typedemarches as $typedmarche){

            $requete = $this->pdo->prepare("UPDATE  typedemarche
                                           SET coche = null
                                         ");
            $requete->execute();

          }
          //table detailtypedemarche => liste des démarches
          $requete = $this->pdo->prepare("SELECT * FROM detailtypedemarche
                                          ");
          $requete->execute();
          $detailtypedemarches = $requete->fetchAll();
          foreach($detailtypedemarches as $detailtypedmarche){

            $requete = $this->pdo->prepare("UPDATE  detailtypedemarche
                                           SET cochedetail = null
                                         ");
            $requete->execute();

          }

          $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                          ");
          $requete->execute();
          $typedemarches = $requete->fetchAll();
          foreach($typedemarches as $typedemarche){
          //  dump($typedemarche[idtypedemarche]);
          //table demarchesalarie => liste des démarches salariés
          $requete = $this->pdo->prepare("SELECT * FROM demarchesalarie
                                          WHERE idsalarie = ?
                                          ORDER BY idtypedemarsal ASC
                                          ");
          $requete->execute([$idsalarie]);
          $demarchesalaries = $requete->fetchAll();
          //dump("table");

          foreach($demarchesalaries as $demarchesalarie){

                if($typedemarche[idtypedemarche] == $demarchesalarie[idtypedemarsal] AND $demarchesalarie[iddetailtypesal] == NULL) {
                  //  dump("if" + $demarchesalarie[idtypedemarsal]);
                      $requete = $this->pdo->prepare("UPDATE typedemarche
                                                      set coche = $demarchesalarie[idsalarie]
                                                      WHERE idtypedemarche = $demarchesalarie[idtypedemarsal]
                                                    ");
                      $requete->execute();

                }
                else{
                 $requete = $this->pdo->prepare("UPDATE detailtypedemarche
                                                  set cochedetail = $demarchesalarie[idsalarie]
                                                  WHERE idtypedemar = $demarchesalarie[idtypedemarsal]
                                                 ");
                  $requete->execute();
                }

            }

          }
          $requete = $this->pdo->prepare("SELECT * FROM demarchesalarie
                                          WHERE idsalarie = ?

                                       ");
          $requete->execute([$idsalarie]);
          $demarchesalaries = $requete->fetchAll();


          $requete = $this->pdo->prepare("SELECT * FROM typedemarche
                                          LEFT JOIN detailtypedemarche on idtypedemar = idtypedemarche
                                                                        ");
          $requete->execute();
          $typedemarches = $requete->fetchAll();

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

         $ddate=date("d-m-y");
         $vdate =$madate = new \DateTime($ddate);
        // dump($vdate);
         setlocale(LC_TIME, 'fra', 'fr_FR');

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
                                  'demarches'             => $demarches,
                                  'typedemarches'         => $typedemarches,
                                  'detailtypedemarches'   => $detailtypedemarches,
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

                             ]
                 );
  }
  // Méthode entretiensalarie
  // permet d'afficher la fiche de entretien d'un salarié en insertion
  public function entretiensalarie(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT
                                           idpole,
                                           matricule,
                                           nom,
                                           prenom,
                                           datenaissance,
                                           lieunaissance,
                                           dptnaissance,
                                           genre,
                                           adresse1,
                                           adresse2,
                                           villeadresse,
                                           codepostaladresse,
                                           datedebcontrat,
                                           datefininitial,
                                           ressources,
                                           id_lieu_travail,
                                           motifsortie,
                                           typesortie,
                                           detailsortie,
                                           id_user,
                                           dateametra,
                                           telephone,
                                           paysnaissance,
                                           nationalite,
                                           datesejour,
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
                                           locomotion,
                                           contratpendantperiode,
                                           droitdif,
                                           nbreheuredif,
                                           sommedif,
                                           attestationcert,
                                           poleemploi,
                                           permis,
                                           ressources

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
                                      ");
        $requete->execute([$idsalarie]);
        $experiences= $requete->fetchAll();
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
                                                              datefse,
                                                              idfse,
                                                              numagrementpole,
                                                             )
                                      VALUES (
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,
                                              ?,?,?,?,?
                                             )");
      $requete->execute([
        $request->getParsedBody()['idpole'],
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
        $request->getParsedBody()['datefse'],
        $request->getParsedBody()['idfse'],
        $request->getParsedBody()['idagrement'],

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
                                                                  contact =?,
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
                                                                  numagrementpole=?

                                        WHERE id = ? ");
          $requete->execute([
            $request->getParsedBody()['idpole'],
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

            foreach($request->getParsedBody()['locom'] as $slocom){
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
      dump($request->getParsedBody()['typesortie']);
      dump($request->getParsedBody()['motifsortie']);
      dump($request->getParsedBody()['datesortie']);

      $requete = $this->pdo->prepare("UPDATE salarie set
                                                        typesortie=?,
                                                        motifsortie=?,
                                                        datesortie=?,
                                                        detailsortie=?,
                                                        id_usersortie=?
                                      WHERE id = ? ");
      $requete->execute([$request->getParsedBody()['typesortie'],
                         $request->getParsedBody()['motifsortie'],
                         $request->getParsedBody()['datesortie'],
                         $request->getParsedBody()['sortie'],
                         $_SESSION['userid'],
                         $request->getParsedBody()['idsalarie']
                         
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
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('Diagnostic'));
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
      // dump($request->getParsedBody()['idsalarie']);
      //dump($request->getParsedBody()['checkdetail']);
     foreach($request->getParsedBody()['checkdetail'] as $scheckt){
        dump($scheckt);
        $requete = $this->pdo->prepare("SELECT idtypedemar from `detailtypedemarche`
                                        WHERE iddetailtype =$scheckt");
         $requete->execute();
         $deta=$requete->fetch();
         $type=$deta[idtypedemar];
         $requete = $this->pdo->prepare("INSERT INTO `demarchesalarie` (idsalarie,
                                                                        idtypedemarsal,
                                                                        iddetailtypesal,
                                                                        idlieu,
                                                                        iduser
                                                                         )
                                        VALUES (?,?,?,?,?)");
         $requete->execute([$request->getParsedBody()['idsalarie'],
         $type,
         $scheckt,
         $request->getParsedBody()['idlieu'],
         $_SESSION['userid']
                         ]);

     }

     foreach($request->getParsedBody()['checktype'] as $scheckt){
        $requete = $this->pdo->prepare("INSERT INTO `demarchesalarie` (idsalarie,
                                                                        idtypedemarsal,
                                                                        idlieu,
                                                                        iduser
                                                                       )
                                        VALUES (?,?,?,?)");
         $requete->execute([$request->getParsedBody()['idsalarie'],
         $scheckt,
         $request->getParsedBody()['idlieu'],
         $_SESSION['userid']
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
 
      $idsalarie=$request->getParsedBody()['idsalarie'];
      $requete = $this->pdo->prepare("DELETE FROM `projetsalarie`
                                      WHERE idsalarie = ?

                                    ");
      $requete->execute([ $request->getParsedBody()['idsalarie']]);
      foreach($request->getParsedBody()['checkprojets'] as $scheckprojet){
          $requete = $this->pdo->prepare("INSERT INTO `projetsalarie` (
                                                                        idsalarie,
                                                                        idprojet,
                                                                        idtravail
                                                                            )
                                           VALUES (?,?,?)");
          $requete->execute([$request->getParsedBody()['idsalarie'],
                              $scheckprojet,
                              $request->getParsedBody()['idlieu']  
                            ]);

      }
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
      return $response;

  }
  // Méthode insertion objectif salarié
  // permet d'insérer l'objectif' du salarié
  public function objectifsalarie(RequestInterface $request, ResponseInterface $response, $args)
  {

      $this->logger->info("==>Ajout projet salarié : ".$request->getParsedBody()['mail']);
      $idsalarie=$request->getParsedBody()['idsalarie'];
      $requete = $this->pdo->prepare("DELETE FROM `objectifsalarie`
                                      WHERE idsalarie = ?

                                    ");
      $requete->execute([ $request->getParsedBody()['idsalarie']]);
      foreach($request->getParsedBody()['checkobjectifs'] as $scheckobjectif){
          $requete = $this->pdo->prepare("INSERT INTO `objectifsalarie` (
                                                                        idsalarie,
                                                                        idobjectif,
                                                                        idtravail
                                                                            )
                                           VALUES (?,?,?)");
          $requete->execute([$request->getParsedBody()['idsalarie'],
                              $scheckobjectif,
                              $request->getParsedBody()['idlieu']
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
                                                               projet,
                                                               objectifs,
                                                               vdate,
                                                               id_user

                                                               )
                                      VALUES (?,?,?,?,?,?,?)");
           //$requete->execute([$request->getParsedBody()['idsalarie']]);
      $requete->execute([
                              $request->getParsedBody()['idsalarie'],
                              $request->getParsedBody()['experience'],
                              $request->getParsedBody()['diagnostic'],
                              $request->getParsedBody()['projet'],
                              $request->getParsedBody()['objectifs'],
                              $request->getParsedBody()['vdate'],
                              $_SESSION['userid'],

                         ]);

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




  $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('suivisalarie',['idsalarie' =>$request->getParsedBody()['idsalarie']]));
  return $response;
  }

  // Méthode insertion suivi

  public function insertsuivi(RequestInterface $request, ResponseInterface $response)
  {

    //$vdate=datetime($ddate);
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout type de permis : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `suivi` (
                                                            idsalarie,
                                                            entretien,
                                                            idpresence,
                                                            idmission,
                                                            suivi,
                                                            pointchantier,
                                                            datesuivi,
                                                            id_user
                                                          )
                                      VALUES (?,?,?,?,?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idsalarie'],
                        $request->getParsedBody()['entretien'],
                        $request->getParsedBody()['idpresence'],
                        $request->getParsedBody()['idmission'],
                        $request->getParsedBody()['suivi'],
                        $request->getParsedBody()['point'],
                        $request->getParsedBody()['vdate'],
                        $_SESSION['userid'],
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));
    }

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
                                                                sanction

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['sanction']
                       ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('sanction'));
    }

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
      dump("idtypedemar");dump( $request->getParsedBody()['idtypedemarche']);
      dump("iddemarc");dump( $request->getParsedBody()['iddemarc']);
      dump($request->getParsedBody()['detailtypedemarche']);


      $requete = $this->pdo->prepare("SELECT iddemar FROM typedemarche
      WHERE (idtypedemarche = ?)");
      $requete->execute([
                        $request->getParsedBody()['idtypedemarche'],
                       ]);

      $lignes = $requete->fetch();
      $iddemarc= $lignes[iddemar];

      $requete = $this->pdo->prepare("INSERT INTO `detailtypedemarche` (
                                                                idtypedemar,
                                                                iddemarc,
                                                                detailtypedemarche,
                                                                iduser
                                                              )
      VALUES (?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idtypedemarche'],
                        $iddemarc,
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
      $requete = $this->pdo->prepare("INSERT INTO `formation` (
                                                                formation

                                                              )
      VALUES (?)");
      $requete->execute([
                        $request->getParsedBody()['formation']
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
      $requete = $this->pdo->prepare("INSERT INTO `renouvellement` (
                                                                      idsalarie,
                                                                      newdebdate,
                                                                      nouvelle_date,
                                                                      engagement,
                                                                      observation,
                                                                      id_user
                                                                   )
                                     VALUES (?,?,?,?,?,?)");
      $requete->execute([
                        $request->getParsedBody()['idsalarie'],
                        $request->getParsedBody()['newdatedeb'],
                        $request->getParsedBody()['newdatefin'],
                        $request->getParsedBody()['engagement'],
                        $request->getParsedBody()['observation'],
                        $_SESSION['userid'],
                       ]);


      $requete = $this->pdo->prepare("UPDATE `salarie`
                                      SET datefinreelle = ?

                                      WHERE id = ? ");
      $requete->execute([ $request->getParsedBody()['newdatefin'],
                         $request->getParsedBody()['idsalarie']
                      ]);
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
      $this->logger->info("==>Ajout situation familiale : ".$request->getParsedBody()['mail']);
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
                                                                departement,
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
                                          departement = ?,
                                          id_user =?
                                    WHERE idlieu = $idlieu ");

    $requete->execute([ $request->getParsedBody()['chantier'],
                        $request->getParsedBody()['fse'],
                        $request->getParsedBody()['encadrant'],
                        $request->getParsedBody()['departement'],
                        $_SESSION['userid'] ]);
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('lieu'));
    }

    return $response;
  }

  // Méthode search
  // permet la recherche sur nom et prénom


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
