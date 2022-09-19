<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserController extends Controller
{
  // Affichage du formulaire de connexion
  public function Authentification(RequestInterface $request, ResponseInterface $response,$args)
  {
    $requete = $this->pdo->prepare("
    SELECT name, password
    FROM users
    ");
  $requete->execute();
  $pwd = $requete->fetchAll();  
   
    $this->render($response, 'user/login.twig',['mdp' =>  $mdp,
                                                'id'  =>  $id,
                                                'pwd' =>  $pwd
                                              ]);
  }
  // Affichage du formulaire changement mot de passe
  public function changepwd(RequestInterface $request, ResponseInterface $response)
  {
    $this->render($response,'user/viewchangepwd.twig');
  }

  // Méthode changement mot de passe

  public function modifpwd(RequestInterface $request, ResponseInterface $response, $args)
  {
    //echo $_SESSION['userid'];
    $id=$_SESSION['userid'];
    dump($id);
    $requete = $this->pdo->prepare("
      SELECT password
      FROM users
      WHERE id = ?

    ");
    $requete->execute($id);
    $modif = $requete->fetch();
    
    dump($modif);
    //echo 'aie';
    //echo $id;
    //die();
    //if ($modif){
      //$response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home'));
    //else {
      $id_user=$modif['id'];
      $newpwd=strtolower($request->getParsedBody()['newpwd']);
      $confirmpword=strtolower($request->getParsedBody()['confirmpword']);
      dump($newpwd);
      dump($confirmpword);
     
          // Enregistrer le nouveau mot de passe de l'utilisateur enregistré
          $requete = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
          $requete->execute([$request->getParsedBody()['newpwd'], $id]);





      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home'));
      return ($response);
    }
    //$response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home'));

  // Affichage du formulaire de connexion
  public function VerifAuth(RequestInterface $request, ResponseInterface $response)
  {
    if (false === $request->getAttribute('csrf_status')) {
        $this->render($response, 'error.twig', ['title' => 'Erreur applicatif', 'message' => 'Ce formulaire n\'est plus valable...']);
    }

    // On vérifie que l'utilisateur existe avec le mot de passe saisi...
    // *** TOUJOURS UTILISER UNE REQUETE PREPAREE ***
    $requete = $this->pdo->prepare("
      SELECT users.id,users.name,usertype.name AS typeuser
      FROM users
      LEFT JOIN usertype ON users.id_type = usertype.id
      WHERE users.name = ? AND users.password = ?
    ");
    $requete->execute([$request->getParsedBody()['login'],$request->getParsedBody()['pword']]);
    $vauth = $requete->fetch();

    // Exemple de mauvaise requête : tester avec ce mdp : ***' OR '1=1*** (enlever les étoiles...)
    // $requete = $this->pdo->prepare("
    //   SELECT users.id,users.name,usertype.name AS typeuser
    //   FROM users
    //   LEFT JOIN usertype ON users.id_type = usertype.id
    //   WHERE users.name = '".$request->getParsedBody()['login']."' AND users.password = '".$request->getParsedBody()['pword']."'
    // ");
    // $requete->execute();
    // $vauth = $requete->fetch();

    if (!$vauth){
    
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('authentification'));
    }  
    else {
      $id_user=$vauth['id'];
      // Enregistrer l'heure de connexion de l'typeusager
      $requete = $this->pdo->prepare("UPDATE users SET last_logon = NOW() WHERE id = ?");
      $requete->execute([$vauth['id']]);

      // Les informations renseignées sont correctes, on les enregistre dans la session...
      $_SESSION['estconnecte'] = true;
      $_SESSION['typeuser'] = $vauth['typeuser'];
      $_SESSION['username'] = $vauth['name'];
      $_SESSION['userid'] = $vauth['id'];
      $type = $_SESSION['typeuser'];
      $type = strtolower($type);
      $comp = "admin";
      if (isset($_SESSION['routedemandee']) && $_SESSION['routedemandee'] != '/login')
              $response = $response->withRedirect($_SESSION['routedemandee']);
      else
        $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home',['type'=>$type],['comp'=>$comp]));
    }

    return ($response);
  }

  // Déconnexion de l'utilisateur
  public function Logout(RequestInterface $request, ResponseInterface $response)
  {
    unset($_SESSION['estconnecte']);
    unset($_SESSION['typeuser']);
    unset($_SESSION['username']);
    unset($_SESSION['userid']);
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home'));
    return ($response);
  }

  // Liste des usagers
  public function Liste(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT users.id,users.name,usertype.name AS typeuser,DATE_FORMAT(last_logon, '%d-%m-%Y à %Hh%i') AS connected
      FROM users
      LEFT JOIN usertype ON users.id_type = usertype.id
      ORDER BY users.name
    ");

    $requete->execute();
    $usagers = $requete->fetchAll();
    $this->render($response, 'user/liste.twig', [
                                                  'titre'   => 'Liste des Utilisateurs',
                                                  'usagers' => $usagers
                                                ]);
  }

  // Liste des freins
  public function frein(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM frein
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/diagnostic.twig', ['lignes' => $lignes]);

  }
// Liste des projets
public function projet(RequestInterface $request, ResponseInterface $response)
{
  $requete = $this->pdo->prepare("
    SELECT *
    FROM projets
    ");

  $requete->execute();
  $lignes = $requete->fetchAll();
  $this->render($response, 'force/projet.twig', ['lignes' => $lignes]);

  }

  // Liste des objectifs
  public function objectif(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM objectifs
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/objectif.twig', ['lignes' => $lignes]);

    
  }
  // Liste des présence
  public function presence(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM presence
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/presence.twig', ['lignes' => $lignes]);
  }

  // Liste des Documents à fournir
  public function DocList(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM documents
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/docafournir.twig', ['lignes' => $lignes]);
  }
  // Liste des Documents à fournir
  public function pieceList(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM titre
      ");

    $requete->execute();
    $titres = $requete->fetchAll();
    $this->render($response, 'force/identitetype.twig', ['titres' => $titres]);
  }
  //liste type de zone
  public function zone(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM zone
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/zonetype.twig', ['lignes' => $lignes]);
  }

  //liste type de sanction
  public function sanction(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM sanction
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/sanctiontype.twig', ['lignes' => $lignes]);
  }

  //liste type de motif de sanction
  public function motifsanction(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM motifsanction
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/motifsanctiontype.twig', ['lignes' => $lignes]);
  }
  //liste type de permis
  public function permis(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM permis
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/permistype.twig', ['lignes' => $lignes]);
  }
  //liste type de sortie table : sortie
  public function typesortie(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM sortie
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/sortie.twig', ['lignes' => $lignes]);
  }

  //liste type de sortie table : demarche
  public function typedemarche(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM demarche
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/demarche.twig', ['lignes' => $lignes]);
  }
  //liste type de sortie table : demarche
  public function demarche(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM demarche
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/demarche.twig', ['lignes' => $lignes]);
  }
  //liste type de sortie table : détails type demarche
  public function detailtypedemarche(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM detailtypedemarche
      LEFT JOIN typedemarche on detailtypedemarche.idtypedemar=typedemarche.idtypedemarche
      LEFT JOIN demarche on typedemarche.iddemar=demarche.iddemarche
      ORDER BY demarche ASC
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/detailtypedemarche.twig', ['lignes' => $lignes]);
  }

  //liste motif de sortie table : motifsortie
  public function motifsortie(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM motifsortie
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/motifsortie.twig', ['lignes' => $lignes]);
  }
  //liste détails de sortie table : typesortie
  public function detailsortie(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM typesortie
      LEFT JOIN sortie on typesortie.idsortie = sortie.idsortie
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();

    $this->render($response, 'force/typesortie.twig', ['lignes' => $lignes,'vid'=>$lignes[idsortie]]);
  }

  //liste détails de demarche table : typedemarche
  public function detaildemarche(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM typedemarche
      LEFT JOIN demarche on typedemarche.iddemar = demarche.iddemarche
      ORDER BY demarche ASC
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();

    $this->render($response, 'force/typedemarche.twig', ['lignes' => $lignes,'vid'=>$lignes[iddemarche]]);
  }
  //liste type de formation
  public function formation(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM formation
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/formationtype.twig', ['lignes' => $lignes]);
  }

  //liste type de moyen de locomotion
  public function locomotion(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM locomotion
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/locomotiontype.twig', ['lignes' => $lignes]);
  }
  //liste type de niveau d'études
  public function etudes(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM niveauetude
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/etudestype.twig', ['lignes' => $lignes]);
  }
  //liste type de situation familiale
  public function situation(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM situfamiliale
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/situationtype.twig', ['lignes' => $lignes]);
  }

  //liste type de ressources
  public function ressources(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT *
      FROM ressources
      ");

    $requete->execute();
    $lignes = $requete->fetchAll();
    $this->render($response, 'force/ressourcestype.twig', ['lignes' => $lignes]);
  }
  // Affichage formulaire ajout usager
  public function FormAjout(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("
      SELECT usertype.id,usertype.name
      FROM usertype
      ORDER BY name
    ");
    $requete->execute();
    $usertypes = $requete->fetchAll();

    $this->render($response, 'user/user.twig', ['titre' => 'Ajout d\'un nouvel utilisateur', 'usertypes' => $usertypes]);
  }


  // Traitement réception nouvel usager
  public function Ajout(RequestInterface $request, ResponseInterface $response)
  {
    // Vérification CSRF()
    if (false === $request->getAttribute('csrf_status')) {
      die("Opération impossible...");
    } else {
      $this->logger->info("==>Ajout utilisateur : ".$request->getParsedBody()['mail']);
      $requete = $this->pdo->prepare("INSERT INTO `users` (id_type,name,password) VALUES (?, ? ,?)");
      $requete->execute([$request->getParsedBody()['id_type'], $request->getParsedBody()['name'], $request->getParsedBody()['password'] ]);
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('listeuser'));
    }
    return $response;
  }


  // Message droits insuffisant...
  public function DroitInsuffisant(RequestInterface $request, ResponseInterface $response)
  {
    $this->render($response, 'user/droitinsuffisant.twig');
  }

  // Examiner le contenu de la session
  public function Session(RequestInterface $request, ResponseInterface $response)
  {
    dump($_SESSION);
    die();
  }
}
