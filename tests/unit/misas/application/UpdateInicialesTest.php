<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\UpdateIniciales;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;

final class UpdateInicialesTest extends TestCase
{
    public function test_execute_creates_row_when_missing_and_normalizes_color(): void
    {
        $captured = null;
        $repo = $this->createMock(InicialesSacdRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->with(42)->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(
            static function (InicialesSacd $e) use (&$captured): bool {
                $captured = $e;

                return true;
            }
        );

        $this->assertSame('', (new UpdateIniciales($repo))->execute(42, 'XYZ', '#ABC'));
        $this->assertNotNull($captured);
        $this->assertSame(42, $captured->getId_nom());
        $this->assertSame('XYZ', $captured->getIniciales());
        $this->assertSame('aabbcc', $captured->getColor());
    }

    public function test_execute_updates_existing_entity(): void
    {
        $existing = new InicialesSacd();
        $existing->setId_nom(7);
        $existing->setIniciales('OLD');
        $existing->setColor('111111');

        $captured = null;
        $repo = $this->createMock(InicialesSacdRepositoryInterface::class);
        $repo->method('findById')->willReturn($existing);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(
            static function (InicialesSacd $e) use (&$captured): bool {
                $captured = $e;

                return true;
            }
        );

        $this->assertSame('', (new UpdateIniciales($repo))->execute(7, 'NEW', ''));
        $this->assertSame($existing, $captured);
        $this->assertSame('NEW', $existing->getIniciales());
        $this->assertSame('', $existing->getColor());
    }

    public function test_execute_returns_repository_error_when_guardar_fails(): void
    {
        $repo = $this->createMock(InicialesSacdRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('fallo al guardar');

        $this->assertSame('fallo al guardar', (new UpdateIniciales($repo))->execute(1, 'A', '#000000'));
    }
}
