<?php

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioUsuarioEliminar;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\criteria\CambioUsuarioField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\cambios\domain\entity\CambioUsuario;

final class CambioUsuarioEliminarTest extends TestCase
{
    public function test_sel_vacio_devuelve_ok(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $useCase = new CambioUsuarioEliminar($repo);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            $useCase->execute(['sel' => []])
        );
    }

    public function test_si_getCambiosUsuario_devuelve_lista_vacia_continua_sin_error(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('matching')
            ->with(Criteria::create()
                ->where(Filter::eq(CambioUsuarioField::IdItemCambio, 10))
                ->andWhere(Filter::eq(CambioUsuarioField::IdUsuario, 20))
                ->andWhere(Filter::eq(CambioUsuarioField::Sfsv, 1))
                ->andWhere(Filter::eq(CambioUsuarioField::AvisoTipo, 3)))
            ->willReturn([]);

        $useCase = new CambioUsuarioEliminar($repo);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            $useCase->execute(['sel' => ['10#20#1#3']])
        );
    }

    public function test_exito_cuando_eliminar_true(): void
    {
        $o = $this->createMock(CambioUsuario::class);

        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('matching')->willReturn([$o]);
        $repo->method('Eliminar')->with($o)->willReturn(true);

        $useCase = new CambioUsuarioEliminar($repo);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            $useCase->execute(['sel' => ['1#2#0#0']])
        );
    }

    public function test_error_cuando_eliminar_false(): void
    {
        $o = $this->createMock(CambioUsuario::class);

        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('matching')->willReturn([$o]);
        $repo->method('Eliminar')->with($o)->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('fallo sql');

        $useCase = new CambioUsuarioEliminar($repo);

        $rta = $useCase->execute(['sel' => ['9#8#7#6']]);
        $this->assertFalse($rta['ok']);
        $this->assertStringContainsString('fallo sql', $rta['mensaje']);
    }
}
