<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EventSpotExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [$this, 'timeAgo']),
            new TwigFilter('price_format', [$this, 'priceFormat']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('capacity_badge', [$this, 'capacityBadge'], ['is_safe' => ['html']]),
        ];
    }

    public function timeAgo(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);
        if ($diff->days === 0) return 'aujourd\'hui';
        if ($diff->days === 1) return $diff->invert ? 'hier' : 'demain';
        if ($diff->days < 7) return ($diff->invert ? 'il y a ' : 'dans ') . $diff->days . ' jours';
        if ($diff->days < 30) {
            $weeks = intdiv($diff->days, 7);
            return ($diff->invert ? 'il y a ' : 'dans ') . $weeks . ' semaine' . ($weeks > 1 ? 's' : '');
        }
        if ($diff->days < 365) {
            $months = intdiv($diff->days, 30);
            return ($diff->invert ? 'il y a ' : 'dans ') . $months . ' mois';
        }
        $years = $diff->y;
        return ($diff->invert ? 'il y a ' : 'dans ') . $years . ' an' . ($years > 1 ? 's' : '');
    }

    public function priceFormat(?float $price): string
    {
        if ($price === null || $price == 0) return '<span class="badge bg-success">Gratuit</span>';
        return '<span class="badge bg-warning text-dark">' . number_format($price, 2, ',', ' ') . ' €</span>';
    }

    public function capacityBadge(int $placesRestantes, int $capaciteMax): string
    {
        $taux = $capaciteMax > 0 ? ($placesRestantes / $capaciteMax) * 100 : 0;
        if ($placesRestantes === 0) {
            return '<span class="badge bg-danger">Complet</span>';
        }
        if ($taux < 20) {
            return '<span class="badge bg-warning text-dark">' . $placesRestantes . ' place(s) restante(s)</span>';
        }
        return '<span class="badge bg-success">' . $placesRestantes . ' place(s) disponible(s)</span>';
    }
}
