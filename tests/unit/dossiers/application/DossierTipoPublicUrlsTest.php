<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\DossierTipoPublicUrls;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;

final class DossierTipoPublicUrlsTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_lanza_si_no_hay_tipo(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->with(404)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('404');
        DossierTipoPublicUrls::relativeFormController(404);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
