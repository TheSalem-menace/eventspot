<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Form\EvenementType;
use App\Form\InscriptionType;
use App\Repository\EvenementRepository;
use App\Repository\TagEvenementRepository;
use App\Service\EvenementManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evenements')]
class EvenementController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private EvenementManager $evenementManager,
    ) {}

    #[Route('', name: 'app_evenement_index')]
    public function index(
        Request $request,
        EvenementRepository $repo,
        TagEvenementRepository $tagRepo,
        PaginatorInterface $paginator
    ): Response {
        $titre     = $request->query->get('titre');
        $categorie = $request->query->get('categorie');
        $ville     = $request->query->get('ville');
        $tagId     = $request->query->getInt('tag') ?: null;

        // Pass Query object so KnpPaginator can add LIMIT/OFFSET efficiently
        $query = $repo->findByFiltersQuery($titre, $categorie, $ville, $tagId);
        $tags  = $tagRepo->findAll();

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 9);

        return $this->render('evenement/index.html.twig', [
            'pagination' => $pagination,
            'tags'       => $tags,
            'filters'    => compact('titre', 'categorie', 'ville', 'tagId'),
        ]);
    }

    #[Route('/nouveau', name: 'app_evenement_new')]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function new(Request $request): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->setOrganisateur($this->getUser());
            $evenement->setStatut('publie');
            $this->em->persist($evenement);
            $this->em->flush();
            $this->addFlash('success', 'L\'événement a été créé avec succès !');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/new.html.twig', ['form' => $form, 'evenement' => null]);
    }

    #[Route('/{id}', name: 'app_evenement_show', requirements: ['id' => '\d+'])]
    public function show(Evenement $evenement, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $recent  = $session->get('recent_events', []);
        if (!in_array($evenement->getId(), $recent)) {
            array_unshift($recent, $evenement->getId());
            $session->set('recent_events', array_slice($recent, 0, 5));
        }

        $isInscrit = false;
        if ($this->getUser()) {
            $isInscrit = $this->evenementManager->estInscrit($this->getUser(), $evenement);
        }

        return $this->render('evenement/show.html.twig', [
            'evenement'       => $evenement,
            'placesRestantes' => $this->evenementManager->getPlacesRestantes($evenement),
            'isInscrit'       => $isInscrit,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_evenement_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function edit(Request $request, Evenement $evenement): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'L\'événement a été modifié.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/new.html.twig', ['form' => $form, 'evenement' => $evenement]);
    }

    #[Route('/{id}/supprimer', name: 'app_evenement_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->getPayload()->getString('_token'))) {
            $this->em->remove($evenement);
            $this->em->flush();
            $this->addFlash('success', 'L\'événement a été supprimé.');
        }
        return $this->redirectToRoute('app_evenement_index');
    }

    #[Route('/{id}/inscription', name: 'app_evenement_inscription', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function inscription(Request $request, Evenement $evenement, MailerInterface $mailer): Response
    {
        if ($this->evenementManager->estInscrit($this->getUser(), $evenement)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit(e) à cet événement.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        if ($this->evenementManager->getPlacesRestantes($evenement) <= 0) {
            $this->addFlash('danger', 'Cet événement est complet.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        $inscription = new Inscription();
        $inscription->setEvenement($evenement);
        $inscription->setParticipant($this->getUser());
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inscription->setStatut('confirmee');
            $this->em->persist($inscription);
            $this->em->flush();

            try {
                $email = (new Email())
                    ->from('noreply@eventspot.fr')
                    ->to($this->getUser()->getEmail())
                    ->subject('Confirmation d\'inscription — ' . $evenement->getTitre())
                    ->html($this->renderView('emails/confirmation_inscription.html.twig', [
                        'evenement'   => $evenement,
                        'inscription' => $inscription,
                        'user'        => $this->getUser(),
                    ]));
                $result = $mailer->send($email);
                error_log('Email sent successfully to: ' . $this->getUser()->getEmail());
            } catch (\Exception $e) {
                error_log('Email sending failed: ' . $e->getMessage());
                $this->addFlash('warning', 'Inscription enregistrée mais email non envoyé: ' . $e->getMessage());
            }

            $this->addFlash('success', 'Inscription confirmée ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/inscription.html.twig', [
            'evenement' => $evenement,
            'form'      => $form,
        ]);
    }
}
