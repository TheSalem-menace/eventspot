<?php

namespace App\Controller;

use App\Entity\TagEvenement;
use App\Form\TagType;
use App\Repository\TagEvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tags')]
class TagEvenementController extends AbstractController
{
    #[Route('', name: 'app_tag_evenement_index')]
    public function index(TagEvenementRepository $repo): Response
    {
        return $this->render('tag_evenement/index.html.twig', ['tags' => $repo->findAll()]);
    }

    #[Route('/nouveau', name: 'app_tag_evenement_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new TagEvenement();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash('success', 'Tag créé !');
            return $this->redirectToRoute('app_tag_evenement_index');
        }
        return $this->render('tag_evenement/new.html.twig', ['form' => $form, 'tag' => null]);
    }

    #[Route('/{id}', name: 'app_tag_evenement_show', requirements: ['id' => '\d+'])]
    public function show(TagEvenement $tag): Response
    {
        return $this->render('tag_evenement/show.html.twig', ['tag' => $tag]);
    }

    #[Route('/{id}/modifier', name: 'app_tag_evenement_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, TagEvenement $tag, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Tag modifié !');
            return $this->redirectToRoute('app_tag_evenement_index');
        }
        return $this->render('tag_evenement/new.html.twig', ['form' => $form, 'tag' => $tag]);
    }

    #[Route('/{id}/supprimer', name: 'app_tag_evenement_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, TagEvenement $tag, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($tag);
            $em->flush();
            $this->addFlash('success', 'Tag supprimé.');
        }
        return $this->redirectToRoute('app_tag_evenement_index');
    }
}
