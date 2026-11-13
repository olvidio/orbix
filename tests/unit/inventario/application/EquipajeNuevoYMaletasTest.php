<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\EquipajeMaletas;
use src\inventario\application\EquipajeNuevo;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\entity\Whereis;
use Tests\support\DescribeCriteria;

/**
 * Crear un equipaje ({@see EquipajeNuevo}) y cambios en sus maletas ({@see EquipajeMaletas}): guardar un
 * grupo, añadir documentos sin duplicar, quitar solo los de la maleta de la cápsula y eliminar el grupo.
 */
final class EquipajeNuevoYMaletasTest extends TestCase
{
    /** @var list<string> */
    private array $log = [];
    private bool $existeEgm = false;
    private mixed $sesion;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION ?? [];
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF-8';
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesion;
    }

    public function test_crear_equipaje_y_fecha_mal_escrita(): void
    {
        $guardado = null;
        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(77);
        $repo->method('Guardar')->willReturnCallback(static function (Equipaje $e) use (&$guardado): bool {
            $guardado = $e;
            return true;
        });
        $uc = new EquipajeNuevo($repo);

        $this->assertSame('', $uc->execute(30019170, 'Casa X (1/7-10/7)', '3001,3002', '1/7/2026', '', 'Casa X'));
        $this->assertSame(
            [77, 30019170, 'Casa X (1/7-10/7)', '3001,3002', '2026-07-01', null, 'Casa X'],
            [$guardado->getId_equipaje(), $guardado->getId_ubi_activ(), $guardado->getNom_equipaje(), $guardado->getIds_activ(), $guardado->getF_ini()?->getIso(), $guardado->getF_fin(), $guardado->getLugarVo()?->value()],
        );

        $guardado = null;
        $this->assertSame('Fecha no válida: 31/2/2026', $uc->execute(1, 'x', '', '1/7/2026', '31/2/2026', ''), 'antes se guardaba sin fecha');
        $this->assertNull($guardado);
    }

    private function maletas(): EquipajeMaletas
    {
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('matching')->willReturnCallback(function ($c): array {
            $this->log[] = 'egm? ' . implode(', ', DescribeCriteria::filtros($c));
            if (!$this->existeEgm) {
                return [];
            }
            $m = new Egm();
            $m->setId_item(900);
            return [$m];
        });
        $egms->method('getNewId')->willReturn(901);
        $egms->method('Guardar')->willReturnCallback(function (Egm $m): bool {
            $this->log[] = 'egm+ ' . implode('/', [$m->getId_item(), $m->getId_equipaje(), $m->getId_grupo(), $m->getId_lugar()]);
            return true;
        });
        $egms->method('Eliminar')->willReturnCallback(function (Egm $m): bool {
            $this->log[] = 'egm- ' . $m->getId_item();
            return true;
        });
        $whereis = $this->createMock(WhereisRepositoryInterface::class);
        $whereis->method('matching')->willReturnCallback(static function ($c): array {
            // el documento 12 ya está en la maleta
            return DescribeCriteria::filtros($c)[0] === 'id_doc eq 12' ? [new Whereis()] : [];
        });
        $n = 1000;
        $whereis->method('getNewId')->willReturnCallback(static function () use (&$n): int {
            return ++$n;
        });
        $whereis->method('Guardar')->willReturnCallback(function (Whereis $w): bool {
            $this->log[] = 'doc+ ' . implode('/', [$w->getId_item_whereis(), $w->getId_item_egm(), $w->getId_doc()]);
            return true;
        });
        $whereis->method('findById')->willReturnCallback(static function (int $id): ?Whereis {
            if ($id === 99) {
                return null;
            }
            $w = new Whereis();
            $w->setId_item_whereis($id);
            $w->setId_item_egm($id === 55 ? 800 : 900); // la fila 55 es de otra maleta
            return $w;
        });
        $whereis->method('Eliminar')->willReturnCallback(function (Whereis $w): bool {
            $this->log[] = 'doc- ' . $w->getId_item_whereis();
            return true;
        });

        return new EquipajeMaletas($egms, $whereis);
    }

    public function test_guardar_un_grupo_nuevo_crea_la_maleta_y_anyade_los_documentos_sin_duplicar(): void
    {
        $r = $this->maletas()->guardarGrupo(5, 2, 20, ['11', '12', 'x', '13']);

        $this->assertSame(['error' => '', 'id_item_egm' => 901], $r);
        $this->assertSame([
            'egm? id_equipaje eq 5, id_grupo eq 2, id_lugar eq 20',
            'egm+ 901/5/2/20',
            'doc+ 1001/901/11',
            'doc+ 1002/901/13',
        ], $this->log, 'el 12 ya estaba: no se duplica');
    }

    public function test_guardar_un_grupo_que_ya_existe_reutiliza_la_maleta(): void
    {
        $this->existeEgm = true;

        $this->assertSame(900, $this->maletas()->guardarGrupo(5, 2, 20, [])['id_item_egm']);
        $this->assertCount(1, $this->log, 'no crea otra');
    }

    /** Antes se borraba cualquier fila por su id, fuera o no de la maleta de la cápsula. */
    public function test_quitar_documentos_solo_los_de_esa_maleta(): void
    {
        $this->assertSame('', $this->maletas()->quitarDocumentos(900, ['41', '55', '99', 'x', '42']));
        $this->assertSame(['doc- 41', 'doc- 42'], $this->log);
    }

    public function test_eliminar_el_grupo(): void
    {
        $this->assertSame('', $this->maletas()->eliminarGrupo(5, 2));
        $this->assertSame(['egm? id_equipaje eq 5, id_grupo eq 2'], $this->log, 'sin maleta no hay nada que eliminar');

        $this->existeEgm = true;
        $this->maletas()->eliminarGrupo(5, 2);
        $this->assertSame('egm- 900', end($this->log));
    }
}
