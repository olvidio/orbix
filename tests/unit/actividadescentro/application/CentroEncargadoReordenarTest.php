<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentroEncargadoReordenar;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;

final class CentroEncargadoReordenarTest extends TestCase
{
    public function test_parametros_o_direccion_invalidos(): void
    {
        $repo = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $useCase = new CentroEncargadoReordenar($repo);

        $this->assertNotSame('', $useCase->execute(['id_activ' => 0, 'id_ubi' => 1, 'num_orden' => 'mas']));
        $this->assertNotSame('', $useCase->execute(['id_activ' => 1, 'id_ubi' => 0, 'num_orden' => 'mas']));
        $this->assertNotSame('', $useCase->execute(['id_activ' => 1, 'id_ubi' => 1, 'num_orden' => 'arriba']));
    }

    public function test_mas_intercambia_con_anterior(): void
    {
        $a = new CentroEncargado();
        $a->setId_activ(1);
        $a->setId_ubi(10);
        $a->setNum_orden(1);
        $b = new CentroEncargado();
        $b->setId_activ(1);
        $b->setId_ubi(20);
        $b->setNum_orden(2);

        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('getCentrosEncargados')->willReturn([$a, $b]);
        $repo->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $useCase = new CentroEncargadoReordenar($repo);
        $this->assertSame('', $useCase->execute([
            'id_activ' => 1,
            'id_ubi' => 20,
            'num_orden' => 'mas',
        ]));

        $this->assertSame('2', $a->getNum_orden());
        $this->assertSame('1', $b->getNum_orden());
    }
}
