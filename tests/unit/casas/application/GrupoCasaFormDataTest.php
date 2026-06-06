<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\GrupoCasaFormData;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;

final class GrupoCasaFormDataTest extends TestCase
{
    public function test_nuevo_sin_consultar_grupo(): void
    {
        $repoCasa = $this->createMock(CasaDlRepositoryInterface::class);
        $repoCasa->method('getArrayCasas')->willReturn(['1' => 'Casa A']);

        $rta = (new GrupoCasaFormData(
            $this->createMock(GrupoCasaRepositoryInterface::class),
            $repoCasa,
        ))->execute(['id_item' => 'nuevo']);

        $this->assertTrue($rta['es_nuevo']);
        $this->assertSame('nuevo', $rta['id_item']);
        $this->assertSame(0, $rta['id_ubi_padre']);
        $this->assertSame(['1' => 'Casa A'], $rta['opciones_casas']);
    }

    public function test_edita_grupo_existente(): void
    {
        $grupo = $this->createMock(GrupoCasa::class);
        $grupo->method('getId_ubi_padre')->willReturn(10);
        $grupo->method('getId_ubi_hijo')->willReturn(20);

        $repoGrupo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repoGrupo->method('findById')->with(5)->willReturn($grupo);

        $repoCasa = $this->createMock(CasaDlRepositoryInterface::class);
        $repoCasa->method('getArrayCasas')->willReturn([]);

        $rta = (new GrupoCasaFormData($repoGrupo, $repoCasa))->execute(['id_item' => '5']);

        $this->assertFalse($rta['es_nuevo']);
        $this->assertSame('5', $rta['id_item']);
        $this->assertSame(10, $rta['id_ubi_padre']);
        $this->assertSame(20, $rta['id_ubi_hijo']);
    }

    public function test_id_inexistente_trata_como_nuevo(): void
    {
        $repoGrupo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repoGrupo->method('findById')->with(99)->willReturn(null);

        $repoCasa = $this->createMock(CasaDlRepositoryInterface::class);
        $repoCasa->method('getArrayCasas')->willReturn([]);

        $rta = (new GrupoCasaFormData($repoGrupo, $repoCasa))->execute(['id_item' => '99']);

        $this->assertTrue($rta['es_nuevo']);
        $this->assertSame('nuevo', $rta['id_item']);
    }
}
