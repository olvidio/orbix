<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasAjaxMutations;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

final class PropuestasAjaxMutationsTest extends TestCase
{
    public function test_operacion_desconocida_devuelve_error(): void
    {
        $useCase = new PropuestasAjaxMutations(
            $this->createMock(PropuestaEncargoSacdRepositoryInterface::class),
            $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
            $this->createMock(EncargoRepositoryInterface::class),
        );

        $out = $useCase->execute('no_existe');

        $this->assertFalse($out['success']);
        $this->assertNotEmpty($out['mensaje']);
    }
}
