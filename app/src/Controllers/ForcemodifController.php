<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class ForceController extends Controller
{

  //Méthode accueil
  // Affichage interface accueil
  public function accueil(RequestInterface $request, ResponseInterface $response)
  {
    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/force.twig', ['titre'=>'Pages d\'Accueil']);
  }

  //Méthode newsalarie
  // Affichage interface pour saisie new salarié

  public function newsalarie(RequestInterface $request, ResponseInterface $response)
  {
    //Préparation liste chantiers
    $requete = $this->pdo->prepare("SELECT id, lieu FROM lieutravail ");
    $requete->execute();
    $chantiers = $requete->fetchAll();
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
    $requete = $this->pdo->prepare("SELECT id, permis FROM permis ");
    $requete->execute();
    $permis = $requete->fetchAll();






    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/newsalarie.twig', ['titre'      => 'Saisie Nouveau salarié' ,
                                                      'chantiers'   => $chantiers,
                                                      'niveaux'     => $niveaux,
                                                      'durees'      => $durees,
                                                      'ressources'  => $ressources,
                                                      'situations'  => $situations,
                                                      'locomotions' => $locomotions,
                                                      'permis'      => $permis
                                                      ]);


  }
  //Méthode newdoc
  // Affichage interface pour saisie nouveau document à fournir
  public function Newdoc(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/docs/.twig', ['titre'=>'Saisie Nouveau Document à Fournir']);
  }
  //Méthode newpermis
  // Affichage interface pour saisie nouveau type de permis
  public function Newpermis(RequestInterface $request, ResponseInterface $response)
  {

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'force/permis.twig', ['titre'=>'Saisie type de permis']);
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
    $requete = $this->pdo->prepare("SELECT lieu,encadrant,departement, id FROM lieutravail ");
    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/lieu.twig', ['titre'=>'Liste des Chantiers', 'lignes'=> $lignes]);
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

    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM salarie ");
        $requete->execute();
        $lignes = $requete->fetchAll();


        $this->render($response, 'force/salarie.twig', ['titre'=>'Liste des Salariés en insertion', 'lignes'=> $lignes] );
  }

  // Méthode salariechantier
  // permet d'afficher les salariés en insertion d'un chantier défini
  public function salariechantier(RequestInterface $request, ResponseInterface $response, $args)
  {

    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM salarie
                                        WHERE id_lieu_travail = ?
                                      ");
        $requete->execute([$args['idlieu']]);
        //$requete->execute();
        $lignes = $requete->fetchAll();


        $this->render($response, 'force/salariechantier.twig', ['titre'=>'Liste des Salariés en insertion', 'lignes'=> $lignes] );
  }
  // permet d'afficher les salariés en insertion
  public function insertlieu(RequestInterface $request, ResponseInterface $response)
  {

    //préparation requête table lieu pour extraction id
        $requete = $this->pdo->prepare("SELECT * FROM lieutravail ");
        $requete->execute();
        $lieux = $requete->fetchAll();

        $this->render($response, 'force/newsalarie.twig',  ['lieux'=> $lieux] );
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
                                           motiffincontrat,
                                           situfincontrat,
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

    //Récupéation des valeurs
        $idsalarie            = $args['idsalarie'];
        $idpole               = $lignes[0][0];
        $matricule            = $lignes[0][1];
        $nom                  = $lignes[0][2];
        $prenom               = $lignes[0][3];
        $datenaissance        = $lignes[0][4];
        $lieunaissance        = $lignes[0][5];
        $dptnaissance         = $lignes[0][6];
        $genre                = $lignes[0][7];
        $adresse1             = $lignes[0][8];
        $adresse2             = $lignes[0][9];
        $villeadresse         = $lignes[0][10];
        $codepostaladresse    = $lignes[0][11];
        $datedebcontrat       = $lignes[0][12];
        $datefininitial       = $lignes[0][13];
        $ressources           = $lignes[0][14];
        $id_lieu_travail      = $lignes[0][15];
        $motiffincontrat      = $lignes[0][16];
        $situfincontrat       = $lignes[0][17];
        $id_user              = $lignes[0][18];
        $dateametra           = $lignes[0][19];
        $telephone            = $lignes[0][20];
        $paynaissance         = $lignes[0][21];
        $nationalite          = $lignes[0][22];
        $datesejour           = $lignes[0][23];
        $contact              = $lignes[0][24];
        $datepoleemploi       = $lignes[0][25];
        $securitesociale      = $lignes[0][26];
        $niveauetude          = $lignes[0][27];
        $qpv                  = $lignes[0][28];
        $prescripteur         = $lignes[0][29];
        $referent             = $lignes[0][30];
        $telreferent          = $lignes[0][31];
        $mailreferent         = $lignes[0][32];
        $adressereferent      = $lignes[0][33];
        $rqth                 = $lignes[0][34];
        $numcaf               = $lignes[0][35];
        $dureechomage         = $lignes[0][36];
        $situfamiliale        = $lignes[0][37];
        $enfants              = $lignes[0][38];
        $locomotion           = $lignes[0][39];
        $contratpendantperiode= $lignes[0][40];
        $droitdif             = $lignes[0][41];
        $nbreheuredif         = $lignes[0][42];
        $sommedif             = $lignes[0][43];
        $attestationcert      = $lignes[0][44];
        $poleemploi           = $lignes[0][45];
        $permis               = $lignes[0][46];


        $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE id = ?");
        $requete->execute([$id_lieu_travail]);
        $lieu = $requete->fetchAll();
        $lieutravail = $lieu[0][0];

        $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
        $requete->execute([$locomotion]);
        $loco = $requete->fetchAll();
        $locomotion = $loco[0][0];

        $requete = $this->pdo->prepare(" SELECT situation FROM situfamiliale WHERE id = ?");
        $requete->execute([$situfamiliale]);
        $situ = $requete->fetchAll();
        $situfamiliale = $situ[0][0];

        $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE id = ?");
        $requete->execute([$niveauetude]);
        $niveau = $requete->fetchAll();
        $niveauetude = $niveau[0][0];

        $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE id = ?");
        $requete->execute([$ressources]);
        $ressource = $requete->fetchAll();
        $ressources = $ressource[0][0];
        

    $this->render($response, 'force/fichesalarie.twig',
                              [
                                  'titre'             =>'Fiche salarié',
                                  'lignes'            => $lignes,
                                  'idpole'            => $idpole,
                                  'nom'               => $nom,
                                  'prenom'            => $prenom,
                                  'datenaissance'     => $datenaissance,
                                  'lieunaissance'     => $lieunaissance,
                                  'dptnaissance'      => $dptnaissance,
                                  'paysnaissance'     => $paysnaissance,
                                  'genre'             => $genre,
                                  'adresse1'          => $adresse1,
                                  'adresse2'          => $adresse2,
                                  'villeadresse'      => $villeadresse,
                                  'codepostaladresse' => $codepostaladresse,
                                  'datedebcontrat'    => $datedebcontrat,
                                  'datefininitial'    => $datefininitial,
                                  'ressources'        => $ressources,
                                  'id_lieu_travail'   => $id_lieu_travail,
                                  'motiffincontrat'   => $motiffincontrat,
                                  'situfincontrat'    => $situfincontrat,
                                  'id_user'           => $id_user,
                                  'dateametra'        => $dateametra,
                                  'telephone'         => $telephone,
                                  'paysnaissance'     => $paynaissance,
                                  'nationalite'       => $nationalite,
                                  'datesejour'        => $datesejour,
                                  'contact'           => $contact,
                                  'datepoleemploi'    => $datepoleemploi,
                                  'securitesociale'   => $securitesociale,
                                  'niveauetude'       => $niveauetude,
                                  'qpv'               => $qpv,
                                  'prescripteur'      => $prescripteur,
                                  'referent'          => $referent,
                                  'telreferent'       => $telreferent,
                                  'mailreferent'      => $mailreferent,
                                  'adressereferent'   => $adressereferent,
                                  'rqth'              => $rqth,
                                  'numcaf'            => $numcaf,
                                  'dureechomage'      => $dureechomage,
                                  'situfamiliale'     => $situfamiliale,
                                  'enfants'           => $enfants,
                                  'contratpendantperiode'=> $contratpendantperiode,
                                  'droitdif'          => $droitdif,
                                  'nbreheuredif'      => $nbreheuredif,
                                  'sommedif'          => $sommedif,
                                  'attestationcert'   => $attestationcert,
                                  'poleemploi'        => $poleemploi,
                                  'lieutravail'       => $lieutravail,
                                  'locomotion'        => $locomotion,


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
      dump ($id_lieu_travail);
      //insert table salarie
      $requete = $this->pdo->prepare("INSERT INTO `salarie` (
                                                              idpole,
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
                                                              motiffincontrat,
                                                              situfincontrat,
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
                                                              contratpendantperiode,
                                                              droitdif,
                                                              nbreheuredif,
                                                              sommedif,
                                                              attestationcert,
                                                              locomotion,
                                                              permis
                                                             )
                                      VALUES (
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                                              ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                                             )");
      $requete->execute([
        $request->getParsedBody()['idpole'],
        $request->getParsedBody()['nom'],
        $request->getParsedBody()['prenom'],
        $request->getParsedBody()['datenaissance'],
        $request->getParsedBody()['lieunaissance'],
        $request->getParsedBody()['dptnaissance'],
        $request->getParsedBody()['genre'],
        $request->getParsedBody()['adresse1'],
        $request->getParsedBody()['adresse2'],
        $request->getParsedBody()['villeadresse'],
        $request->getParsedBody()['codepostaladresse'],
        $request->getParsedBody()['datedebcontrat'],
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
        $request->getParsedBody()['categoriepermis'],

      ]);
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


        $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('salarie'));
      }

    return $response;
  }

  // Méthode insertion nouveau document
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
                                                                encadrant,
                                                                departement,
                                                                id_user

                                                                )
      VALUES (?, ?,?,?)");
      $requete->execute([
        $request->getParsedBody()['chantier'],
        $request->getParsedBody()['encadrant'],
        $request->getParsedBody()['departement'],
        $_SESSION['userid']
      ]);
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
    $requete = $this->pdo->prepare("SELECT  id,
                                            nom,
                                            prenom,
                                            id_lieu_travail,
                                            datedebcontrat,
                                            datefininitial
                                    FROM salarie
                                    WHERE nom LIKE ? OR prenom LIKE ?");
    $requete->execute(array($recherche.'%', $recherche.'%'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/salariesearch.twig', ['titre'=>'Résultats' , 'lignes'=> $lignes]);
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
