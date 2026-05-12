<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EvenementRepository $repo, RequestStack $requestStack): Response
    {
        $prochains = $repo->findUpcoming(6);
        
        // Récupérer les événements récemment consultés
        $session = $requestStack->getSession();
        $recentIds = $session->get('recent_events', []);
        $recentEvents = [];
        
        if (!empty($recentIds)) {
            $recentEvents = $repo->findBy(['id' => $recentIds]);
        }
        
        return $this->render('home/index.html.twig', [
            'prochains' => $prochains,
            'recentEvents' => $recentEvents,
        ]);
    }
}
