<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserController extends Controller
{
  // Affichage du formulaire de connexion
  public function Authentification(RequestInterface $request, ResponseInterface $response)
  {
    $this->render($response, 'user/login.twig');
  }

  // Affichage du formulaire de connexion
  public function VerifAuth(RequestInterface $request, ResponseInterface $response)
  {
    // On vérifie que l'utilisateur existe avec le mot de passe saisi...
    $requete = $this->pdo->prepare("
      SELECT users.id,users.name,usertype.name AS typeuser
      FROM users
      LEFT JOIN usertype ON users.id_type = usertype.id
      WHERE users.name = ? AND users.password = ?
    ");
    $requete->execute([$request->getParsedBody()['login'],$request->getParsedBody()['pword']]);
    $vauth = $requete->fetch();

    if (!$vauth)
      $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('authentification'));
    else {
      // Enregistrer l'heure de connexion de l'typeusager
      $requete = $this->pdo->prepare("UPDATE users SET last_logon = NOW() WHERE id = ?");
      $requete->execute([$vauth['id']]);

      // Les informations renseignées sont correctes, on les enregistre dans la session...
      $_SESSION['estconnecte'] = true;
      $_SESSION['typeuser'] = $vauth['typeuser'];
      $_SESSION['username'] = $vauth['name'];
      $_SESSION['userid'] = $vauth['id'];

      if (isset($_SESSION['routedemandee']) && $_SESSION['routedemandee'] != '/login')
        $response = $response->withRedirect($_SESSION['routedemandee']);
      else
        $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('home'));

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
