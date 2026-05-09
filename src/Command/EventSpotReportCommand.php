<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use App\Repository\LieuRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:eventspot:report', description: 'Génère un rapport des événements EventSpot')]
class EventSpotReportCommand extends Command
{
    public function __construct(
        private EvenementRepository $evenementRepo,
        private LieuRepository $lieuRepo,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('upcoming', null, InputOption::VALUE_NONE, 'Afficher uniquement les prochains événements')
            ->addOption('lieu', null, InputOption::VALUE_REQUIRED, 'Filtrer par lieu (ID)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Rapport EventSpot');

        if ($input->getOption('upcoming')) {
            $events = $this->evenementRepo->findUpcoming(10);
            $io->section('Prochains événements (10)');
        } elseif ($lieuId = $input->getOption('lieu')) {
            $lieu = $this->lieuRepo->find($lieuId);
            if (!$lieu) {
                $io->error("Lieu ID $lieuId introuvable.");
                return Command::FAILURE;
            }
            $events = $this->evenementRepo->findBy(['lieu' => $lieu]);
            $io->section('Événements au lieu : ' . $lieu->getNom());
        } else {
            $events = $this->evenementRepo->findAll();
            $io->section('Tous les événements');
        }

        $rows = [];
        foreach ($events as $e) {
            $rows[] = [
                $e->getId(),
                $e->getTitre(),
                $e->getDateDebut()->format('d/m/Y'),
                $e->getCategorie(),
                $e->getNbInscrits() . '/' . $e->getCapaciteMax(),
                $e->getStatut(),
            ];
        }

        $io->table(['ID', 'Titre', 'Date', 'Catégorie', 'Inscrits/Max', 'Statut'], $rows);
        $io->success(count($events) . ' événement(s) trouvé(s).');
        return Command::SUCCESS;
    }
}
