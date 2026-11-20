# Côté Montagne — plateforme

Site public, catalogue, stocks par boutique, devis et factures, dans une seule application Symfony.

## Stack

- PHP 8.3+, Symfony 7.4 LTS (passage à Symfony 8 dès que le serveur est en PHP 8.4)
- PostgreSQL 16, Doctrine ORM 3 et migrations
- Pages publiques en Twig rendu serveur, CSS et JS sans framework

## Démarrer en local

```bash
composer install
echo 'DATABASE_URL="postgresql://USER:PASS@127.0.0.1:5432/cote_montagne?serverVersion=16&charset=utf8"' > .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php -S 127.0.0.1:8000 -t public
```

## Tests

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/phpunit
```

## Règles du modèle

- Montants en centimes, HT ; taux (TVA, remise, acompte) en points de base : 2000 = 20 %.
- Quantités de devis en millièmes : 2,5 m² = 2500.
- Le stock d'un produit par lieu ne change que par `StockService`, qui enregistre chaque mouvement.
  Un mouvement venant d'un système externe (Skilou) porte son identifiant, donc une resynchronisation ne le compte jamais deux fois.
- Une facture émise n'est jamais modifiée : on la corrige par un avoir. Les numéros (`F-2026-0001`) se suivent sans trou.
