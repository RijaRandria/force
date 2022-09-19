<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\UploadedFile;

class BlogController extends Controller
{
  // Accueil
  public function Famille(RequestInterface $request, ResponseInterface $response)
  {
    // On récupère les différentes familles
    $requete = $this->pdo->prepare("SELECT id,name,picturelink FROM families ORDER BY name");
    $requete->execute();
    $familles = $requete->fetchAll();

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'blog/familles.twig', ['titre'=>'Nos différentes thématiques', 'familles'=> $familles]);
  }

  public function SousFamille(RequestInterface $request, ResponseInterface $response, $args)
  {
    // On récupère l'identifiant de la famille, le nom de la banque d'images et le nom de la famille
    $requete = $this->pdo->prepare("SELECT name,id,imagebank FROM families WHERE id = ?");
    $requete->execute([$args['id']]);
    $famille = $requete->fetch();

    // On récupère les sous-familles concernées par la famille choisie...
    $requete = $this->pdo->prepare("SELECT id,name FROM subfamilies WHERE id_family = ? ORDER BY name");
    $requete->execute([$args['id']]);
    $sousfamilles = $requete->fetchAll();

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'blog/sousfamilles.twig', ['famille'=>$famille, 'sousfamilles'=> $sousfamilles]);
  }

  // Exemple de remplissage d'un champ Select depuis une base de données pour Twig
  public function Select(RequestInterface $request, ResponseInterface $response)
  {
    // On récupère l'identifiant de la famille, le nom de la banque d'images et le nom de la famille
    $requete = $this->pdo->prepare("SELECT id,name FROM families ORDER BY name");
    $requete->execute();
    $contenuSelect = $requete->fetchAll();

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'blog/select.twig', ['menus'=>$contenuSelect]);
  }
}
