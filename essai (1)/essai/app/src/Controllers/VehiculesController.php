<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class VehiculesController extends Controller
{
  // Méthode Renault
  public function Renault(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT marque,vehicule,moteur FROM vehicules WHERE marque = ?");
    $requete->execute(array('Renault'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'vehicules/vehicules.twig', ['titre'=>'Véhicule de marque Renault', 'lignes'=> $lignes]);
  }

  // Méthode Citroen
  public function Citroen(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT marque,vehicule,moteur FROM vehicules WHERE marque = ?");
    $requete->execute(array('Citroen'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'vehicules/vehicules.twig', ['titre'=>'Véhicule de marque Citroen', 'lignes'=> $lignes]);
  }

  // Méthode Audi
  public function  Audi(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT marque,vehicule,moteur FROM vehicules WHERE marque = ?");
    $requete->execute(array('Audi'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'vehicules/vehicules.twig', ['titre'=>'Véhicule de marque Audi', 'lignes'=> $lignes]);
  }

  // Méthode Skoda
  public function  Skoda(RequestInterface $request, ResponseInterface $response)
  {
    $requete = $this->pdo->prepare("SELECT marque,vehicule,moteur FROM vehicules WHERE marque = ?");
    $requete->execute(array('Skoda'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'vehicules/vehicules.twig', ['titre'=>'Véhicule de marque Skoda', 'lignes'=> $lignes]);
  }

  // Méthode de test TWIG
  public function Vide(RequestInterface $request, ResponseInterface $response)
  {
    $this->render($response, 'vehicules/vide.twig', ['titre'=>'Cette page est vide']);
  }

  // Méthode de Recherche
  public function Search(RequestInterface $request, ResponseInterface $response)
  {
    $this->render($response, 'vehicules/recherche.twig', ['titre'=>'Recherche par marque, véhicule ou moteur']);
  }

  // Méthode de Recherche après POST
  public function Requete(RequestInterface $request, ResponseInterface $response)
  {
    $recherche = $request->getParsedBody()['recherche'];
    $requete = $this->pdo->prepare("SELECT marque,vehicule,moteur FROM vehicules WHERE marque LIKE ? OR vehicule LIKE ? OR moteur LIKE ?");
    $requete->execute(array($recherche.'%', $recherche.'%', $recherche.'%'));
    $lignes = $requete->fetchAll();
    $this->render($response, 'vehicules/vehicules.twig', ['titre'=>'Résultats par marque, véhicule ou moteur', 'lignes'=> $lignes]);
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
