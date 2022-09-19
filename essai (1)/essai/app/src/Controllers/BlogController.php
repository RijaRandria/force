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

  // Méthode permettant de récupérer les articles d'une famille et de les afficher
  public function Article(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT families.name AS name_family,
                                           id_family,
                                           subfamilies.id,
                                           subfamilies.name
                                    FROM subfamilies
                                    LEFT JOIN families ON subfamilies.id_family = families.id
                                    WHERE subfamilies.id = ?");
    $requete->execute([$args['id']]);
    $sousfamille = $requete->fetch();

    // On récupère l'ensemble des articles de la sous-famille
    $requete = $this->pdo->prepare("SELECT articles.id, articles.id_subfamily, articles.title, articles.content, articles.created_at, articles.updated_at, pictures.url, pictures.caption
                                    FROM articles
                                    LEFT JOIN pictures ON articles.id = pictures.id_article
                                    WHERE id_subfamily = ?
                                    GROUP BY articles.id
                                    ");
    $requete->execute([$args['id']]);
    $articles = $requete->fetchAll();

    // On appelle la vue avec les données récupérées au préalable
    $this->render($response, 'blog/articles.twig', ['articles' => $articles, 'sousfamille' => $sousfamille, 'idsousfamille' => $args['id']]);
  }

  // Méthode permettant d'afficher le formulaire de création d'un nouvel article
  public function FormNouvArticle(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT name FROM subfamilies where id = ?");
    $requete->execute([$args['idsousfamille']]);
    $NomSousFamille = $requete->fetch();
    // On appelle la vue avec l'identifiant de la sous-famille à laquelle on va rattacher notre article
    $this->render($response, 'blog/fichearticle.twig', ['idsousfamille' => $args['idsousfamille'], 'titre' => 'Nouvel article pour la sous-famille '.$NomSousFamille['name'] ] );
  }

  // Méthode permettant d'enregistrer un nouvel article
  public function AjoutArticle(RequestInterface $request, ResponseInterface $response)
  {
    // Ecriture en base de données
    $requete = $this->pdo->prepare("INSERT INTO articles (title,content,id_subfamily,id_user,created_at) VALUES (?, ?, ?, ?, NOW() ) ");
    $requete->execute([$request->getParsedBody()['title'], $request->getParsedBody()['content'], $request->getParsedBody()['sousfamille'], $_SESSION['userid']]);

    // On retourne vers la liste des articles de la sous-famille
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('article',['id' => $request->getParsedBody()['sousfamille'] ]));
    return $response;
  }

  // Suppression d'un article
  public function SupprimerArticle(RequestInterface $request, ResponseInterface $response, $args)
  {
    // Suppression en base de données
    $requete = $this->pdo->prepare("DELETE FROM articles WHERE id = ?");
    $requete->execute([$args['id']]);

    // On retourne vers la liste des articles de la sous-famille
    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('article',['id' => $args['sousfam']]));
    return $response;
  }

  // Méthode permettant d'afficher le formulaire d'ajout d'image
  public function FormAjoutImage(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT id_subfamily,title FROM articles where id = ?");
    $requete->execute([$args['id']]);
    $NomArticle = $requete->fetch();
    // On appelle la vue avec l'id de l'article et son titre
    $this->render($response, 'blog/ajoutimagerticle.twig', ['id' => $args['id'], 'idsousfamille' => $NomArticle['id_subfamily'], 'titre' => 'Ajout d\'une nouvelle image à l\'article '.$NomArticle['title'] ] );
  }

  // Méthode recevant l'image chargée'
  public function AjoutImage(RequestInterface $request, ResponseInterface $response, $settings)
  {
    // Par défaut, on part du principe qu'il n'y a pas de chargement d'image
    $chargementFichier = false;
    $url = $request->getParsedBody()['url'];

    $uploadedFiles = $request->getUploadedFiles();
    $uploadedFile = $uploadedFiles['fileimage'];

    // On teste la présence du fichier téléchargé
    if ($uploadedFile->getError() === UPLOAD_ERR_OK)  {
      $file_extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
      $file_extension = strtolower($file_extension);

      // Liste des extensions fichiers autorisées
      if ($file_extension == 'jpg' || $file_extension == 'jpeg') {
        $chargementFichier = true;
        $url = "upload";
        }
      }

    // Enregistrement des données de l'image dans la base de données
    $requete = $this->pdo->prepare("INSERT INTO `pictures` (id_article, url, caption) VALUES (?, ? ,?)");
    $requete->execute([$request->getParsedBody()['articleid'], $url, $request->getParsedBody()['caption'] ]);
    //echo "<br/>$url";

    if ($chargementFichier) {
      $nouvelIdentifiant = $this->pdo->lastInsertId();

      // Chemin du dossier public/upload
      $emplacement = __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'public' . DIRECTORY_SEPARATOR. 'uploads';
      //echo "<br/>".$emplacement;
      $uploadedFile->moveTo($emplacement . DIRECTORY_SEPARATOR . $nouvelIdentifiant.".jpg");
    }

    // Afin  de revenir sur la page de la sous-famille de départ, on récupére l'id de la sous-famille de l'article
    $requete = $this->pdo->prepare("SELECT `id_subfamily` FROM articles WHERE id = ?");
    $requete->execute([$request->getParsedBody()['articleid']]);
    $sousfamille = $requete->fetch();

    $response = $response->withRedirect($this->slim->getContainer()->get('router')->pathFor('article',['id' => $sousfamille['id_subfamily']]));
    return $response;
  }

  // Affiche un carrousel contenant les photos liées à un article
  public function Carrousel(RequestInterface $request, ResponseInterface $response, $args)
  {
    $requete = $this->pdo->prepare("SELECT articles.id_subfamily,
                                           IF(pictures.url = 'upload', CONCAT('/uploads/',pictures.id,'.jpg'), url) AS url,
                                           pictures.caption
                                    FROM articles
                                    LEFT JOIN pictures ON articles.id = pictures.id_article
                                    WHERE articles.id = ?");
    $requete->execute([$args['id']]);
    $images = $requete->fetchAll();

    $this->render($response, 'blog/carrouselarticle.twig', ['images' => $images, 'titre' => 'Carrousel des photos' ] );
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
