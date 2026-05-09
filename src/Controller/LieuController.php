<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lieux')]
class LieuController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('', name: 'app_lieu_index')]
    public function index(LieuRepository $repo): Response
    {
        return $this->render('lieu/index.html.twig', ['lieux' => $repo->findAll()]);
    }

    #[Route('/nouveau', name: 'app_lieu_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($lieu);
            $this->em->flush();
            $this->addFlash('success', 'Lieu créé !');
            return $this->redirectToRoute('app_lieu_index');
        }
        return $this->render('lieu/new.html.twig', ['form' => $form, 'lieu' => null]);
    }

    #[Route('/{id}', name: 'app_lieu_show', requirements: ['id' => '\d+'])]
    public function show(Lieu $lieu): Response
    {
        return $this->render('lieu/show.html.twig', ['lieu' => $lieu]);
    }

    #[Route('/{id}/modifier', name: 'app_lieu_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Lieu $lieu): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Lieu modifié.');
            return $this->redirectToRoute('app_lieu_index');
        }
        return $this->render('lieu/new.html.twig', ['form' => $form, 'lieu' => $lieu]);
    }

    #[Route('/{id}/supprimer', name: 'app_lieu_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Lieu $lieu): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lieu->getId(), $request->getPayload()->getString('_token'))) {
            $this->em->remove($lieu);
            $this->em->flush();
            $this->addFlash('success', 'Lieu supprimé.');
        }
        return $this->redirectToRoute('app_lieu_index');
    }
}
