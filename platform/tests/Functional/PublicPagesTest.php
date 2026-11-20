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
        yield ['/a-propos/', 'Charlène & Thibault'];
        yield ['/contact/', '05 62 40 23 35'];
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
    }
}
