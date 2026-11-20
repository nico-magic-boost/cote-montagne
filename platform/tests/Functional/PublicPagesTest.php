<?php

namespace App\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PublicPagesTest extends WebTestCase
{
    public static function pages(): iterable
    {
        yield ['/', 'Des intérieurs qui racontent'];
        yield ['/prestations/', 'Home staging'];
        yield ['/realisations/', 'Naturel & minéral'];
        yield ['/qui-sommes-nous/', 'Sœur et frère'];
        yield ['/contact/', '05 62 40 23 35'];
        yield ['/nos-boutiques/', 'Deux adresses rue Vincent Mir'];
        yield ['/nos-boutiques/cote-montagne/', '43 rue Vincent Mir'];
        yield ['/nos-boutiques/lecrin/', '35 rue Vincent Mir'];
        yield ['/decoration-saint-lary/', 'Décoration d\'intérieur à Saint-Lary-Soulan'];
    }

    #[DataProvider('pages')]
    public function testPageIsServedWithItsOwnTitle(string $url, string $expectedText): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', $expectedText);
        self::assertSame(1, $crawler->filter('h1')->count());
        self::assertNotEmpty($crawler->filter('meta[name="description"]')->attr('content'));
        self::assertSame('https://cote-montagne.fr'.$url, $crawler->filter('link[rel="canonical"]')->attr('href'));
        foreach ($crawler->filter('script[type="application/ld+json"]') as $script) {
            self::assertNotNull(json_decode($script->textContent), 'Invalid JSON-LD on '.$url);
        }
    }

    /** Every page of the old static site must still answer, directly or through a permanent redirect. */
    public static function legacyUrls(): iterable
    {
        foreach (['/', '/prestations/', '/realisations/', '/qui-sommes-nous/', '/contact/', '/nos-boutiques/', '/nos-boutiques/cote-montagne/', '/nos-boutiques/lecrin/', '/decoration-saint-lary/'] as $url) {
            yield $url => [$url, null];
        }
        yield '/a-propos/' => ['/a-propos/', '/qui-sommes-nous/'];
        yield '/charlene/' => ['/charlene/', '/qui-sommes-nous/'];
    }

    #[DataProvider('legacyUrls')]
    public function testLegacyUrlIsKept(string $url, ?string $redirectsTo): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        if ($redirectsTo === null) {
            self::assertResponseIsSuccessful();
        } else {
            self::assertResponseStatusCodeSame(301);
            self::assertResponseRedirects($redirectsTo);
        }
    }

    public function testSitemapListsEveryPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        self::assertResponseIsSuccessful();
        $xml = simplexml_load_string($client->getResponse()->getContent());
        $locs = [];
        foreach ($xml->url as $url) {
            $locs[] = (string) $url->loc;
        }

        foreach (self::pages() as [$url]) {
            self::assertContains('https://cote-montagne.fr'.$url, $locs);
        }
    }
}
