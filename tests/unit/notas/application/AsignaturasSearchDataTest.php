<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\AsignaturasSearchData;

final class AsignaturasSearchDataTest extends TestCase
{
    public function test_delega_en_repositorio(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getJsonAsignaturas')
            ->with(['nombre_asignatura' => 'mat'])
            ->willReturn('[{"label":"x"}]');

        $useCase = new AsignaturasSearchData($repo);
        $this->assertSame('[{"label":"x"}]', $useCase->execute(['search' => 'mat']));
    }
}
