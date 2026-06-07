<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\TipoDossierGuardar;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;

final class TipoDossierGuardarTest extends TestCase
{
    public function test_sin_id(): void
    {
        $useCase = new TipoDossierGuardar($this->createMock(TipoDossierRepositoryInterface::class));
        $this->assertNotSame('', $useCase->execute([]));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $useCase = new TipoDossierGuardar($repo);
        $this->assertNotSame('', $useCase->execute(['id_tipo_dossier' => 2]));
    }

    public function test_falla_guardar(): void
    {
        $tipo = $this->tipoMinimo(3);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Guardar')->willReturn(false);

        $useCase = new TipoDossierGuardar($repo);
        $this->assertNotSame('', $useCase->execute([
            'id_tipo_dossier' => 3,
            'tabla_from' => 'p',
        ]));
    }

    public function test_exito_aplica_campos_y_permisos(): void
    {
        $tipo = $this->tipoMinimo(7);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn($tipo);
        $repo->expects($this->once())->method('Guardar')->with($tipo)->willReturn(true);

        $useCase = new TipoDossierGuardar($repo);
        $msg = $useCase->execute([
            'id_tipo_dossier' => 7,
            'descripcion' => 'Desc',
            'tabla_from' => 'p',
            'tabla_to' => 't1',
            'campo_to' => 'c1',
            'id_tipo_dossier_rel' => 4,
            'depende_modificar' => 't',
            'app' => 'mimod',
            'class' => 'MiClase',
            'codigo' => '  mi_slug  ',
            'Permiso_lectura' => [1, 4],
            'Permiso_escritura' => [2],
        ]);

        $this->assertSame('', $msg);
        $this->assertSame('Desc', $tipo->getDescripcion());
        $this->assertSame('p', $tipo->getTabla_from());
        $this->assertSame('t1', $tipo->getTabla_to());
        $this->assertSame('c1', $tipo->getCampo_to());
        $this->assertSame(4, $tipo->getId_tipo_dossier_rel());
        $this->assertTrue($tipo->isDepende_modificar());
        $this->assertSame('mimod', $tipo->getApp());
        $this->assertSame('MiClase', $tipo->getClass());
        $this->assertSame('mi_slug', $tipo->getCodigo());
        $this->assertSame(5, $tipo->getPermiso_lectura());
        $this->assertSame(2, $tipo->getPermiso_escritura());
    }

    private function tipoMinimo(int $id): TipoDossier
    {
        $o = new TipoDossier();
        $o->setId_tipo_dossier($id);
        $o->setTabla_from('x');
        $o->setPermiso_lectura(0);
        $o->setPermiso_escritura(0);
        $o->setDepende_modificar(false);
        return $o;
    }
}
