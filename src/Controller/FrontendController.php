<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class FrontendController extends AbstractController
{
    /**
     * This route catches all requests that are NOT:
     * - starting with /api
     * - starting with /build (where our CSS/JS/Manifest/SW live)
     * - common static files at the root
     */
    #[Route('/{reactRouting}', name: 'app_frontend', requirements: ['reactRouting' => '^(?!api|build|favicon\.ico|robots\.txt).*'], defaults: ['reactRouting' => null], priority: -1)]
    public function index(KernelInterface $kernel): Response
    {
        $indexPath = $kernel->getProjectDir() . '/public/build/index.html';
        
        if (!file_exists($indexPath)) {
            return new Response(
                'Frontend not built. Please run "npm run build" in the frontend directory.',
                Response::HTTP_NOT_FOUND
            );
        }

        $content = file_get_contents($indexPath);
        
        return new Response($content);
    }
}
