<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;
use src\shared\domain\criteria\Criteria;
use Tests\support\DescribeCriteria;

final class ActaTribunalSyncTest extends TestCase
{
    private ActaTribunalDlRepositoryInterface&MockObject $repo;
    private ActaTribunalSync $sync;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->sync = new ActaTribunalSync($this->repo);
    }

    private function tribunal(int $id): ActaTribunal
    {
        $t = new ActaTribunal();
        $t->setId_item($id);

        return $t;
    }

    public function test_busca_los_tribunales_del_acta(): void
    {
        $this->repo->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['acta eq dlb 12/24'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->assertSame('', $this->sync->rebuild('dlb 12/24', []));
    }

    public function test_elimina_los_existentes_antes_de_crear_los_nuevos(): void
    {
        $this->repo->method('matching')->willReturn([$this->tribunal(1), $this->tribunal(2)]);
        $this->repo->method('getNewId')->willReturn(50);
        $orden = [];
        $this->repo->method('Eliminar')->willReturnCallback(function () use (&$orden): bool {
            $orden[] = 'eliminar';

            return true;
        });
        $this->repo->method('Guardar')->willReturnCallback(function () use (&$orden): bool {
            $orden[] = 'guardar';

            return true;
        });

        $this->sync->rebuild('dlb 12/24', ['Perez']);

        $this->assertSame(['eliminar', 'eliminar', 'guardar'], $orden);
    }

    public function test_crea_un_registro_por_examinador_con_su_orden(): void
    {
        $this->repo->method('matching')->willReturn([]);
        $this->repo->method('getNewId')->willReturnOnConsecutiveCalls(10, 11, 12);
        $guardados = [];
        $this->repo->expects($this->exactly(3))->method('Guardar')->willReturnCallback(function (ActaTribunal $t) use (&$guardados): bool {
            $guardados[] = [$t->getId_item(), $t->getActa(), $t->getExaminador(), $t->getOrden()];

            return true;
        });

        $this->assertSame('', $this->sync->rebuild('dlb 12/24', ['Perez', 'Gil', 'Soler']));

        $this->assertSame([[10, 'dlb 12/24', 'Perez', '1'], [11, 'dlb 12/24', 'Gil', '2'], [12, 'dlb 12/24', 'Soler', '3']], $guardados);
    }

    /**
     * Comportamiento actual: el contador de orden avanza también con los examinadores vacíos,
     * así que el orden guardado puede tener huecos (1, 3).
     */
    public function test_los_examinadores_vacios_se_omiten_pero_dejan_hueco_en_el_orden(): void
    {
        $this->repo->method('matching')->willReturn([]);
        $this->repo->method('getNewId')->willReturn(10);
        $ordenes = [];
        $this->repo->method('Guardar')->willReturnCallback(function (ActaTribunal $t) use (&$ordenes): bool {
            $ordenes[] = [$t->getExaminador(), $t->getOrden()];

            return true;
        });

        $this->sync->rebuild('dlb 12/24', ['Perez', '', 'Soler']);

        $this->assertSame([['Perez', '1'], ['Soler', '3']], $ordenes);
    }

    public function test_sin_examinadores_solo_borra(): void
    {
        $this->repo->method('matching')->willReturn([$this->tribunal(1)]);
        $this->repo->expects($this->once())->method('Eliminar')->willReturn(true);
        $this->repo->expects($this->never())->method('Guardar');

        $this->assertSame('', $this->sync->rebuild('dlb 12/24', []));
    }

    public function test_los_errores_de_borrado_y_de_guardado_se_acumulan_y_el_proceso_continua(): void
    {
        $this->repo->method('matching')->willReturn([$this->tribunal(1), $this->tribunal(2)]);
        $this->repo->method('Eliminar')->willReturn(false);
        $this->repo->method('getNewId')->willReturn(10);
        $this->repo->method('Guardar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('fallo sql');

        $error = $this->sync->rebuild('dlb 12/24', ['Perez', 'Gil']);

        $this->assertSame(4, substr_count($error, 'fallo sql'), '2 borrados + 2 guardados fallidos');
    }
}
