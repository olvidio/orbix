<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\IngresoPlazasPrevistasUpdate;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;

final class IngresoPlazasPrevistasUpdateTest extends TestCase
{
    public function test_json_sin_id_activ(): void
    {
        $useCase = new IngresoPlazasPrevistasUpdate(
            $this->createMock(IngresoRepositoryInterface::class),
        );

        $this->assertNotSame('', $useCase->execute([
            'data' => '{}',
            'colName' => '""',
        ]));
    }

    public function test_ingreso_no_encontrado(): void
    {
        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn(null);

        $data = json_encode((object)['id' => 10, 'plazas' => 4]);
        $colName = json_encode('plazas');

        $this->assertNotSame('', (new IngresoPlazasPrevistasUpdate($repo))->execute([
            'data' => $data,
            'colName' => $colName,
        ]));
    }

    public function test_falla_guardar(): void
    {
        $oIngreso = $this->createMock(Ingreso::class);
        $oIngreso->expects($this->once())->method('setNumAsistentesPrevistosVo')->with(7);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->willReturn($oIngreso);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('x');

        $data = json_encode((object)['id' => 3, 'c' => 7]);
        $colName = json_encode('c');

        $msg = (new IngresoPlazasPrevistasUpdate($repo))->execute(['data' => $data, 'colName' => $colName]);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('x', $msg);
    }

    public function test_exito(): void
    {
        $oIngreso = $this->createMock(Ingreso::class);
        $oIngreso->expects($this->once())->method('setNumAsistentesPrevistosVo')->with(12);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->with(8)->willReturn($oIngreso);
        $repo->method('Guardar')->with($oIngreso)->willReturn(true);

        $data = json_encode((object)['id' => 8, 'plazas' => 12]);
        $colName = json_encode('plazas');

        $this->assertSame('', (new IngresoPlazasPrevistasUpdate($repo))->execute([
            'data' => $data,
            'colName' => $colName,
        ]));
    }
}
