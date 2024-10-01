<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class DownloadFileController extends AbstractController
{
    #[Route('/download/{TypeFile}/{filename}', name: 'app_download_file')]
    public function downloadfile($TypeFile,$filename): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/files/'.$TypeFile. "/" . $filename;

        // Vérification si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas');
        }

        // Créer la réponse pour le téléchargement
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$filename
        );
      
       return $response;
    }
}
