<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasBalanceQueData;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Con `id_tipo_activ` fijado se evita la rama de {@see TiposActividades}.
 */
final class PlazasBalanceQueDataTest extends TestCase
{
    public function test_id_tipo_explicito_y_delegaciones_vacias(): void
    {
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getDelegaciones')
            ->with(['active' => true, '_ordre' => 'nombre_dl'])
            ->willReturn([]);

        $out = (new PlazasBalanceQueData($repo))->execute(['id_tipo_activ' => '123456']);
        $this->assertSame('123456', $out['id_tipo_activ']);
        $this->assertSame([], $out['delegaciones_opciones']);
    }
}
