<?php

namespace App\Controller;

use App\Database\FichierManager;
use App\File\UploadService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class HomeController extends AbstractController
{
    public function homepage(
        ResponseInterface $response,
        ServerRequestInterface $request,
        UploadService $uploadService,
        FichierManager $fichierManager
    ) {
        // Récupérer les fichiers envoyés:
        $listeFichiers = $request->getUploadedFiles();

        // Si le formulaire est envoyé
        if (isset($listeFichiers['fichier'])) {
            /** @var UploadedFileInterface $fichier */
            $fichier = $listeFichiers['fichier'];

            // Récupérer le nouveau nom du fichier
            $nouveauNom = $uploadService->saveFile($fichier);

            // Enregistrer les infos du fichier en base de données
            $fichier = $fichierManager->createFichier($nouveauNom, $fichier->getClientFilename(), $fichier->getClientMediaType());

            // Redirection vers la page de succès
            return $this->redirect('success', [
                'id' => $fichier->getId(),
            ]);
        }

        return $this->template($response, 'home.html.twig');
    }

    /**
     * Vérifier que l'identifiant (argument $id) correspond à un fichier existant
     * Si ce n'est pas le cas, rediriger vers une route qui affichera un message d'erreur
     */
    public function success(ResponseInterface $response, int $id, FichierManager $fichierManager)
    {
        $fichier = $fichierManager->getById($id);
        if ($fichier === null) {
            return $this->redirect('file-error');
        }

        return $this->template($response, 'success.html.twig', [
            'fichier' => $fichier
        ]);
    }

    public function fileError(ResponseInterface $response)
    {
        return $this->template($response, 'file_error.html.twig');
    }

    public function download(ResponseInterface $response, int $id, FichierManager $fichierManager)
    {
        // Récupération en base de données
        $fichier = $fichierManager->getById($id);
        if ($fichier === null) {
            return $this->redirect('file-error');
        }

        // Incrémenter le nombre de téléchargements
        $fichier->incrementTelechargements();
        $fichierManager->updateFichier($fichier);

        // Chemin vers le fichier à télécharger
        $path = UploadService::FILES_DIR . '/' . $fichier->getNom();
        if (file_exists($path) === false) {
            return $this->redirect('file-error');
        }

        // Ajout des en-têtes HTTP pour le téléchargement:
        $contentDisposition = 'attachment;filename=' . $fichier->getNomOriginal();
        $response = $response
            ->withHeader('Content-Disposition', $contentDisposition)
            ->withHeader('Content-Type', $fichier->getType());

        // Insertion du contenu du fichier dans le corps de la réponse
        $response->getBody()->write(file_get_contents($path));
        return $response;
    }
}
