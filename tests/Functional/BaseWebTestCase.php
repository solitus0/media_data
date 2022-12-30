<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use VCR\VCR;

abstract class BaseWebTestCase extends WebTestCase
{
    protected HttpKernelBrowser $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
    }

    public static function setUpBeforeClass(): void
    {
        VCR::configure()
            ->setCassettePath(__DIR__ . '/../data/cassettes')
            ->enableRequestMatchers(self::getDefaultMatchers())
        ;

        VCR::turnOn();
    }

    private static function getDefaultMatchers(): array
    {
        return ['method', 'url', 'host', 'query_string', 'body', 'post_fields'];
    }

    protected function fetchClientContent()
    {
        $response = $this->client->getResponse();

        return json_decode($response->getContent(), true);
    }

    protected function assertClientResponse(int $statusCode): void
    {
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
    }

    protected function disableRequestBodyPayloadMatcher(): void
    {
        $matchersWithoutBody = ['method', 'url', 'host', 'query_string', 'post_fields'];

        VCR::configure()
            ->enableRequestMatchers($matchersWithoutBody)
        ;
    }

    protected function resetMatchers(): void
    {
        VCR::configure()
            ->enableRequestMatchers(self::getDefaultMatchers())
        ;
    }

    protected function insertCassette(string $cassette): void
    {
        VCR::insertCassette($cassette);
    }

    protected function ejectCassette(): void
    {
        VCR::eject();
    }
}
