<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionDefaultGuardar;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

final class ActivacionDefaultGuardarTest extends TestCase
{
    public function test_valor_vacio(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $this->assertNotSame('', (new ActivacionDefaultGuardar(new Activacion($repo)))->execute(''));
    }

    public function test_guarda_default(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->with($this->callback(function (PasarelaConfig $c): bool {
            $json = $c->getJson_valor(returnArray: true);
            $this->assertIsArray($json);
            $this->assertSame('5 días', $json['default'] ?? null);

            return true;
        }))->willReturn(true);

        $this->assertSame('', (new ActivacionDefaultGuardar(new Activacion($repo)))->execute('5 días'));
    }
}
