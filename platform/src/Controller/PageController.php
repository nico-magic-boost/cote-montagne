<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    private const SERVICES = [
        [
            'slug' => 'conseil-deco',
            'icon' => 'conseil',
            'title' => 'Conseil déco',
            'summary' => 'Une séance chez vous ou en boutique pour clarifier vos envies et repartir avec une direction claire.',
            'points' => ['Analyse de vos pièces et de vos usages', 'Palette de couleurs et de matières', 'Conseils d\'agencement et d\'éclairage', 'Liste d\'achats sélectionnée'],
        ],
        [
            'slug' => 'design-3d',
            'icon' => 'design3d',
            'title' => 'Design 3D',
            'summary' => 'Vous voyez votre intérieur avant le premier achat : plans, volumes, mobilier et matières en situation.',
            'points' => ['Relevé et plan de vos espaces', 'Perspectives 3D réalistes', 'Mobilier de nos marques partenaires intégré', 'Ajustements jusqu\'à validation'],
        ],
        [
            'slug' => 'home-staging',
            'icon' => 'staging',
            'title' => 'Home staging',
            'summary' => 'Valoriser un appartement ou un chalet à vendre ou à louer, pour qu\'il se démarque dès la première photo.',
            'points' => ['Mise en valeur avant vente', 'Préparation de locations saisonnières', 'Location ou vente du mobilier', 'Mise en scène pour les photos'],
        ],
        [
            'slug' => 'amenagement-complet',
            'icon' => 'amenagement',
            'title' => 'Aménagement complet',
            'summary' => 'Du concept à la livraison, nous coordonnons tout : mobilier, artisans, commandes et installation.',
            'points' => ['Conception et devis détaillé', 'Commande auprès des fournisseurs', 'Coordination des artisans', 'Installation et finitions'],
        ],
        [
            'slug' => 'boutiques',
            'icon' => 'boutique',
            'title' => 'Boutiques',
            'summary' => 'Mobilier, luminaires, linge de maison et objets choisis un à un, à découvrir rue Vincent Mir.',
            'points' => ['Mobilier et luminaires', 'Linge de maison et textiles', 'Décoration et senteurs', 'Cadeaux et souvenirs des Pyrénées'],
        ],
    ];

    private const STYLES = [
        ['title' => 'Chaleureux & authentique', 'text' => 'Bois brut, laine, terres cuites : l\'esprit du chalet dans ce qu\'il a de plus accueillant.'],
        ['title' => 'Élégant & intemporel', 'text' => 'Lignes nettes, matières nobles et touches dorées pour un intérieur qui traverse les saisons.'],
        ['title' => 'Naturel & minéral', 'text' => 'Pierre, lin et bois blanchi, la lumière des sommets ramenée à l\'intérieur.'],
        ['title' => 'Doux & harmonieux', 'text' => 'Formes arrondies, teintes apaisées et textiles enveloppants pour se ressourcer.'],
    ];

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('page/home.html.twig', ['services' => self::SERVICES, 'styles' => self::STYLES]);
    }

    #[Route('/prestations/', name: 'prestations')]
    public function prestations(): Response
    {
        return $this->render('page/prestations.html.twig', ['services' => self::SERVICES]);
    }

    #[Route('/realisations/', name: 'realisations')]
    public function realisations(): Response
    {
        return $this->render('page/realisations.html.twig', ['styles' => self::STYLES]);
    }

    #[Route('/a-propos/', name: 'a_propos')]
    public function aPropos(): Response
    {
        return $this->render('page/a_propos.html.twig');
    }

    #[Route('/contact/', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig');
    }
}
