<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\LocalesDesplegableData;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

final class LocalesDesplegableDataTest extends TestCase
{
        public function test_devuelve_locales_del_repositorio(): void {
        $locales = ['ca' => 'Català', 'es' => 'Castellano'];
        $repo = $this->createMock(LocalRepositoryInterface::class);
        $repo->expects($this->once())->method('getArrayLocales')->willReturn($locales);

        $out = (new \src\actividadessacd\application\LocalesDesplegableData($repo))->execute();
        $this->assertSame(['a_locales' => $locales], $out);
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
