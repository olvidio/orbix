<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoLstTipoEncData;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTipo;
use src\shared\domain\criteria\Criteria;
use Tests\support\DescribeCriteria;

final class EncargoLstTipoEncDataTest extends TestCase
{
    private function tipo(int $id, string $texto): EncargoTipo
    {
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getId_tipo_enc')->willReturn($id);
        $t->method('getTipo_enc')->willReturn($texto);

        return $t;
    }

    public function test_filtra_los_tipos_por_el_prefijo_del_grupo(): void
    {
        $repo = $this->createMock(EncargoTipoRepositoryInterface::class);
        $repo->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_tipo_enc matches ^21'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        (new EncargoLstTipoEncData($repo))->execute('21', null);
    }

    public function test_las_opciones_son_pares_en_el_orden_del_repositorio(): void
    {
        $repo = $this->createMock(EncargoTipoRepositoryInterface::class);
        // Orden deliberadamente no numérico: un mapa JS lo reordenaría por clave.
        $repo->method('matching')->willReturn([$this->tipo(2100, 'Zeta'), $this->tipo(501, 'Alfa'), $this->tipo(1003, 'Beta')]);

        $out = (new EncargoLstTipoEncData($repo))->execute('2', '501');

        $this->assertSame([['2100', 'Zeta'], ['501', 'Alfa'], ['1003', 'Beta']], $out['opciones']);
        $this->assertSame('501', $out['selected']);
    }

    public function test_contrato_del_desplegable(): void
    {
        $repo = $this->createMock(EncargoTipoRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $out = (new EncargoLstTipoEncData($repo))->execute('1', null);

        $this->assertSame(
            ['id' => 'id_tipo_enc', 'opciones' => [], 'selected' => '', 'blanco' => true, 'val_blanco' => '', 'action' => ''],
            $out
        );
    }
}
