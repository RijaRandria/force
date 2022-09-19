<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ImagesController extends Controller
{
  // Méthode chargement image sécurisée
  public function Charge(RequestInterface $request, ResponseInterface $response, $args)
  {
    $monImage = file_get_contents(__DIR__. '..' . DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'mypicts' . DIRECTORY_SEPARATOR . $args['nom'].".jpg");
    $response=$response->withHeader('Content-Type', 'image/jpeg')->withStatus(200);
    $response->write($monImage);
    return $response;
  }

  // Méthode chargement pdf sécurisé
  public function ChargePDF(RequestInterface $request, ResponseInterface $response, $args)
  {
    $monPDF = file_get_contents(__DIR__. '..' . DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mypdfs' . DIRECTORY_SEPARATOR .$args['nom'].".pdf");
    $response=$response->withHeader('Content-Type', 'application/pdf')->withStatus(200);
    $response->write($monPDF);
    return $response;
  }

  // Méthode nous indiquant le dossier courant
  public function OuSuisje(RequestInterface $request, ResponseInterface $response)
  {
    die(__DIR__);
  }
}
