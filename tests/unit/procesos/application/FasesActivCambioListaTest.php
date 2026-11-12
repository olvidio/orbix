<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\entity\ActividadAll as ActividadDl;
use src\actividades\domain\entity\TipoDeActividad;
use src\procesos\domain\entity\TareaProceso;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\application\FasesActivCambioLista;
use src\procesos\application\ProcesoActividadService;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Listado del cambio de fase en lote: qué actividades cumplen los requisitos
 * para marcar (fases previas) o desmarcar la fase nueva, y el mensaje de cada fila.
 */
final class FasesActivCambioListaTest extends TestCase
{
    private function useCase(): FasesActivCambioLista
    {
        return new FasesActivCambioLista(
            $this->createMock(ActividadDlRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(TipoDeActividadRepositoryInterface::class),
            $this->createMock(TareaProcesoRepositoryInterface::class),
            $this->createMock(ActividadProcesoTareaRepositoryInterface::class),
            $this->createMock(ProcesoActividadService::class),
        );
    }

    public function test_sin_id_fase_nueva_devuelve_mensaje_error(): void
    {
        $data = $this->useCase()->execute([]);
        $this->assertIsArray($data);
        $this->assertSame(_("Debe poner la fase nueva"), $data['error']);
    }

    public function test_id_fase_nueva_vacia_devuelve_mensaje_error(): void
    {
        $data = $this->useCase()->execute([
            'id_fase_nueva' => '',
            'accion' => 'marcar',
        ]);
        $this->assertIsArray($data);
        $this->assertSame(_("Debe poner la fase nueva"), $data['error']);
        $this->assertSame('marcar', $data['accion']);
    }

    /** @var array<int, array{estado: array<string, bool>, completadas: list<int>}> */
    private array $procesos = [];
    /** @var list<array<string, mixed>> */
    private array $previas = [];
    private mixed $sesion = null;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION ?? [];
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesion;
    }

    /** @param list<int> $ids */
    private function conActividades(array $ids): FasesActivCambioLista
    {
        $dl = $this->createMock(ActividadDlRepositoryInterface::class);
        $dl->method('matching')->willReturn(array_map(static function (int $id): ActividadDl {
            $a = new ActividadDl();
            $a->setId_activ($id);
            $a->setId_tipo_activ(112021);
            $a->setNom_activ("ca $id");
            return $a;
        }, $ids));
        $tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (): TipoDeActividad {
            $t = new TipoDeActividad();
            $t->setId_tipo_proceso_sv(22);
            return $t;
        });
        $tareas = $this->createMock(TareaProcesoRepositoryInterface::class);
        $tareas->method('matching')->willReturnCallback(function (): array {
            $t = new TareaProceso();
            $t->setJson_fases_previas($this->previas);
            return [$t];
        });
        $apt = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $apt->method('getListaFaseEstado')->willReturnCallback(fn (int $id): array => $this->procesos[$id]['estado'] ?? []);
        $apt->method('getFasesCompletadas')->willReturnCallback(fn (int $id): array => $this->procesos[$id]['completadas'] ?? []);
        $svc = $this->createMock(ProcesoActividadService::class);
        $svc->method('getMensaje')->willReturnCallback(fn (string $ft): string => 'falta ' . strtok($ft, '#') . '. ');
        return new FasesActivCambioLista($dl, $this->createMock(ActividadRepositoryInterface::class), $tipos, $tareas, $apt, $svc);
    }

    /** @param list<int> $completadas */
    private function proceso(int $id, array $completadas): void
    {
        $estado = [];
        foreach ([1, 2, 3, 5] as $f) {
            $estado["$f#0"] = in_array($f, $completadas, true);
        }
        $this->procesos[$id] = ['estado' => $estado, 'completadas' => $completadas];
    }

    /** @param array<string, mixed> $r @return array<int, string> */
    private static function mensajes(array $r): array
    {
        $m = [];
        foreach ($r['a_valores'] as $k => $fila) {
            if ($k !== 'select') {
                $m[$fila['sel']] = $fila[2];
            }
        }
        return $m;
    }

    private function entrada(string $accion = 'marcar'): array
    {
        return ['id_tipo_activ' => '1.....', 'dl_propia' => 't', 'id_fase_nueva' => '5', 'accion' => $accion, 'periodo' => 'actual'];
    }

    public function test_marcar_segun_las_fases_previas(): void
    {
        $this->previas = [['id_fase' => 1, 'mensaje' => ''], ['id_fase' => 2, 'mensaje' => 'sin la 2']];
        $this->proceso(1, [1, 2]);      // cumple
        $this->proceso(2, [1]);         // falta la 2 (mensaje propio)
        $this->proceso(3, [2]);         // falta la 1
        $this->proceso(4, [1, 2, 5]);   // ya la tiene
        // 5: sin proceso

        $r = $this->conActividades([1, 2, 3, 4, 5])->execute($this->entrada());

        $this->assertSame([1], $r['a_valores']['select']);
        $this->assertSame([5, 1], [$r['num_activ'], $r['num_ok']]);
        $this->assertSame([
            1 => 'ok, tiene la(s) fase(s) previa(s)',
            2 => 'sin la 2',
            3 => 'falta 1. ',
            4 => 'Ya la tiene',
            5 => 'No tiene proceso. Debe crearlo',
        ], self::mensajes($r));
        $this->assertNotSame('', $r['a_valores'][1]['ctx_update']);
    }

    public function test_marcar_sin_fases_previas_y_desmarcar(): void
    {
        $this->proceso(1, [1]);
        $this->proceso(2, [1, 5]);
        $r = $this->conActividades([1, 2])->execute($this->entrada());
        $this->assertSame([1 => 'ok', 2 => 'Ya la tiene'], self::mensajes($r));

        $r = $this->conActividades([1, 2])->execute($this->entrada('desmarcar'));
        $this->assertSame([2], $r['a_valores']['select']);
        $this->assertSame([1 => 'No tiene marcada la fase', 2 => 'ok'], self::mensajes($r));
    }
}
