<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    private const SHOPS = [
        'cote-montagne' => [
            'slug' => 'cote-montagne',
            'name' => 'Côté Montagne',
            'tagline' => 'Décoration montagne & ameublement',
            'title' => 'Côté Montagne — Boutique de décoration montagne et d\'ameublement à Saint-Lary',
            'description' => 'Côté Montagne, boutique de décoration montagne à Saint-Lary-Soulan : mobilier bois, luminaires, linge de maison, art de la table, bougies. 43 rue Vincent Mir, 05 62 40 23 35.',
            'intro' => 'Le showroom de notre studio de décoration. Mobilier, textiles, luminaires, art de la table et bougies : les pièces que nous aimons utiliser dans nos projets, à découvrir et à emporter.',
            'street' => '43 rue Vincent Mir',
            'phone' => '05 62 40 23 35',
            'phoneIntl' => '+33562402335',
            'email' => 'contact@cote-montagne.fr',
            'hours' => [
                ['Lundi – jeudi', '10h – 12h30 · 15h – 19h'],
                ['Vendredi', '10h – 12h30 · 15h – 19h30'],
                ['Samedi', '9h30 – 13h · 15h – 19h30'],
                ['Dimanche', '10h – 13h · 15h – 19h'],
            ],
            'hoursNote' => 'Horaires variables selon la saison, ouvert 7 jours sur 7 en haute saison.',
            'selections' => [
                ['Mobilier & bois', 'Bancs, tabourets et consoles en bois massif, des pièces qui deviennent des meubles de famille.'],
                ['Linge & textiles', 'Plaids en laine, coussins en lin lavé, nappes : des matières qui vieillissent bien.'],
                ['Lumière', 'Suspensions, lampes à poser et appliques pour une lumière chaude et feutrée.'],
                ['Art de la table', 'Vaisselle en grès, verres soufflés, planches : la belle table de montagne.'],
                ['Senteurs & bougies', 'Résine de pin, cèdre, foin coupé, fumée boisée.'],
                ['Accessoires', 'Miroirs, cadres et objets qui finissent un intérieur.'],
            ],
        ],
        'lecrin' => [
            'slug' => 'lecrin',
            'name' => 'L\'Écrin',
            'tagline' => 'Cadeaux & souvenirs',
            'title' => 'L\'Écrin — Boutique de cadeaux et souvenirs à Saint-Lary-Soulan',
            'description' => 'L\'Écrin, boutique de cadeaux et souvenirs des Pyrénées à Saint-Lary-Soulan : sweatshirts, articles régionaux, petite décoration. 35 rue Vincent Mir, ouvert 7j/7.',
            'intro' => 'Le complément de Côté Montagne pour rapporter un morceau de Saint-Lary : cadeaux, sweatshirts, articles régionaux et petite décoration.',
            'street' => '35 rue Vincent Mir',
            'phone' => '05 62 93 43 83',
            'phoneIntl' => '+33562934383',
            'email' => null,
            'hours' => [
                ['Lundi – dimanche', '9h – 20h'],
            ],
            'hoursNote' => 'Ouvert 7 jours sur 7 toute l\'année, jours fériés compris.',
            'selections' => [
                ['Sweatshirts & vêtements', 'Pulls et sweatshirts aux couleurs de Saint-Lary et des Pyrénées.'],
                ['Articles régionaux', 'Produits et objets fabriqués dans la région.'],
                ['Petits souvenirs', 'Cartes postales, magnets, porte-clés.'],
                ['Décoration montagne', 'Petits objets, cartes d\'art et photographies.'],
                ['Idées cadeaux', 'Coffrets et trouvailles à offrir.'],
                ['Nouveautés', 'De nouvelles sélections au fil des saisons.'],
            ],
        ],
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

    #[Route('/qui-sommes-nous/', name: 'a_propos')]
    public function aPropos(): Response
    {
        return $this->render('page/a_propos.html.twig', ['shops' => self::SHOPS]);
    }

    /** Old URLs of the static site, kept alive so their search ranking carries over. */
    #[Route('/a-propos/', name: 'legacy_a_propos')]
    #[Route('/charlene/', name: 'legacy_charlene')]
    public function legacyAbout(): RedirectResponse
    {
        return $this->redirectToRoute('a_propos', status: Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/nos-boutiques/', name: 'boutiques')]
    public function boutiques(): Response
    {
        return $this->render('page/boutiques.html.twig', ['shops' => self::SHOPS]);
    }

    #[Route('/nos-boutiques/{slug}/', name: 'boutique', requirements: ['slug' => 'cote-montagne|lecrin'])]
    public function boutique(string $slug): Response
    {
        return $this->render('page/boutique.html.twig', ['shop' => self::SHOPS[$slug], 'shops' => self::SHOPS]);
    }

    #[Route('/decoration-saint-lary/', name: 'decoration_saint_lary')]
    public function decorationSaintLary(): Response
    {
        return $this->render('page/decoration_saint_lary.html.twig', [
            'services' => self::SERVICES,
            'styles' => self::STYLES,
            'shops' => self::SHOPS,
        ]);
    }

    #[Route('/contact/', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig', ['shops' => self::SHOPS]);
    }

    #[Route('/sitemap.xml', name: 'sitemap', format: 'xml')]
    public function sitemap(): Response
    {
        $routes = ['home', 'decoration_saint_lary', 'prestations', 'realisations', 'boutiques', 'a_propos', 'contact'];
        $urls = array_map(fn (string $route) => $this->generateUrl($route), $routes);
        foreach (array_keys(self::SHOPS) as $slug) {
            $urls[] = $this->generateUrl('boutique', ['slug' => $slug]);
        }

        return $this->render('sitemap.xml.twig', ['urls' => $urls]);
    }
}
