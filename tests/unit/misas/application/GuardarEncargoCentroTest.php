<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\GuardarEncargoCentro;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;

final class GuardarEncargoCentroTest extends TestCase
{
    private const UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_crea_nuevo_cuando_id_item_vacio(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoCtr $e) {
            $this->assertSame(10, $e->getId_enc());
            $this->assertSame(20, $e->getId_ubi());
            $this->assertNotSame('', $e->getUuid_item());

            return true;
        });

        $this->assertSame('', (new GuardarEncargoCentro($repo))->execute('', 10, 20));
    }

    public function test_no_encontrado_cuando_id_item_presente(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');

        $msg = (new GuardarEncargoCentro($repo))->execute(self::UUID, 1, 2);
        $this->assertStringContainsString(self::UUID, $msg);
    }

    public function test_falla_guardar(): void
    {
        $ctr = new EncargoCtr();
        $ctr->setUuid_item(self::UUID);
        $ctr->setId_enc(1);
        $ctr->setId_ubi(1);

        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('save-err');

        $this->assertSame('save-err', (new GuardarEncargoCentro($repo))->execute(self::UUID, 5, 6));
    }

    public function test_exito_actualiza(): void
    {
        $ctr = new EncargoCtr();
        $ctr->setUuid_item(self::UUID);
        $ctr->setId_enc(1);
        $ctr->setId_ubi(1);

        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->expects($this->once())->method('Guardar')->with($ctr)->willReturn(true);

        $this->assertSame('', (new GuardarEncargoCentro($repo))->execute(self::UUID, 99, 88));
        $this->assertSame(99, $ctr->getId_enc());
        $this->assertSame(88, $ctr->getId_ubi());
    }
}
