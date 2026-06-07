<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\TestCase;
use src\usuarios\application\GruposLista;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\Grupo;

final class GruposListaTest extends TestCase
{
    public function test_sin_username_filtra_por_id_usuario(): void
    {
        $grupo = $this->createMock(Grupo::class);
        $grupo->method('getId_usuario')->willReturn(12);
        $grupo->method('getUsuarioAsString')->willReturn('g1');

        $repo = $this->createMock(GrupoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getGrupos')
            ->with(
                ['id_usuario' => '^5', '_ordre' => 'usuario'],
                ['id_usuario' => '~']
            )
            ->willReturn([$grupo]);

        $useCase = new GruposLista($repo);
        $out = $useCase->execute('');

        $this->assertSame('12#', $out['a_valores'][1]['sel']);
        $this->assertSame('g1', $out['a_valores'][1][1]);
        $this->assertSame('frontend/usuarios/controller/grupo_form.php', $out['a_valores'][1][2]['link_spec']['path']);
    }

    public function test_con_username_usa_sin_acentos(): void
    {
        $repo = $this->createMock(GrupoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getGrupos')
            ->with(
                ['usuario' => 'ana', '_ordre' => 'usuario'],
                ['usuario' => 'sin_acentos']
            )
            ->willReturn([]);

        $useCase = new GruposLista($repo);
        $out = $useCase->execute('ana');

        $this->assertSame([], $out['a_valores']);
    }
}
