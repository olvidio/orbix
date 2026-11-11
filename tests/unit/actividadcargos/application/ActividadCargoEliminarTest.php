<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\application\ActividadCargoEliminar;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use Tests\myTest;

/** Quitar un cargo de una actividad y, si se pide y la actividad es de s/sg, también la asistencia. */
final class ActividadCargoEliminarTest extends myTest
{
    private const ID_ITEM = 77;
    private const ID_ACTIV = 3001;
    private const ID_NOM = 10011;
    /** sv + s (4) + actividad 1 + 001 */
    private const TIPO_S = 141001;
    private const TIPO_SG = 151001;
    private const TIPO_N = 111001;

    private ActividadCargoRepositoryInterface&MockObject $cargos;
    private ActividadRepositoryInterface&MockObject $actividades;
    private AsistenteApplicationService&MockObject $asistenteService;
    private AsistenteDlRepositoryInterface&MockObject $asisDl;
    private AsistenteOutRepositoryInterface&MockObject $asisOut;
    private AsistenteExRepositoryInterface&MockObject $asisEx;
    private AsistentePubRepositoryInterface&MockObject $asisPub;
    private DossierRepositoryInterface&MockObject $dossiers;
    /** @var list<int> tipos de dossier cerrados y guardados */
    private array $cerrados = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->asistenteService = $this->createMock(AsistenteApplicationService::class);
        $this->asisDl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $this->asisOut = $this->createMock(AsistenteOutRepositoryInterface::class);
        $this->asisEx = $this->createMock(AsistenteExRepositoryInterface::class);
        $this->asisPub = $this->createMock(AsistentePubRepositoryInterface::class);
        $this->dossiers = $this->createMock(DossierRepositoryInterface::class);
        $this->cerrados = [];
    }

    private function sut(): ActividadCargoEliminar
    {
        return new ActividadCargoEliminar(
            $this->cargos,
            $this->actividades,
            $this->asistenteService,
            $this->asisDl,
            $this->asisOut,
            $this->asisEx,
            $this->asisPub,
            $this->dossiers,
        );
    }

    private function hayCargo(?int $idNom = self::ID_NOM, bool $eliminarOk = true): void
    {
        $cargo = new ActividadCargo();
        $cargo->setId_item(self::ID_ITEM);
        $cargo->setId_activ(self::ID_ACTIV);
        $cargo->setId_nom($idNom);
        $cargo->setId_cargo(3);
        $this->cargos->method('findById')->with(self::ID_ITEM)->willReturn($cargo);
        $this->cargos->method('Eliminar')->willReturn($eliminarOk);
    }

    private function hayActividad(int $tipo): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    private function asistente(bool $deMiDl = true): Asistente&MockObject
    {
        $a = $this->createMock(Asistente::class);
        $a->method('perm_modificar')->willReturn($deMiDl);

        return $a;
    }

    /** Los dossiers existen: se registran los que se cierran y guardan. */
    private function hayDossiers(): void
    {
        $this->dossiers->method('findByPk')->willReturnCallback(function (DossierPk $pk): Dossier {
            $d = $this->createMock(Dossier::class);
            $d->expects($this->once())->method('cerrar');
            $this->assertSame('p', $pk->tabla());
            $this->assertSame(self::ID_NOM, $pk->idPau());
            $d->method('getId_tipo_dossier')->willReturn($pk->idTipoDossier());

            return $d;
        });
        $this->dossiers->method('Guardar')->willReturnCallback(function (Dossier $d): bool {
            $this->cerrados[] = $d->getId_tipo_dossier();

            return true;
        });
    }

    // ---- validación de entrada ---------------------------------------------------------------------------

    public function test_sin_id_item_ni_seleccion_devuelve_error_y_no_toca_nada(): void
    {
        $this->cargos->expects($this->never())->method('findById');
        $this->cargos->expects($this->never())->method('Eliminar');

        $this->assertNotSame('', $this->sut()->execute([]));
        $this->assertNotSame('', $this->sut()->execute(['id_item' => 0, 'sel' => []]));
        $this->assertNotSame('', $this->sut()->execute(['id_item' => 'abc']));
    }

    public function test_un_cargo_que_no_existe_devuelve_error(): void
    {
        $this->cargos->method('findById')->willReturn(null);
        $this->cargos->expects($this->never())->method('Eliminar');

        $this->assertNotSame('', $this->sut()->execute(['id_item' => self::ID_ITEM]));
    }

    public function test_si_el_repositorio_no_elimina_devuelve_error_y_no_cierra_dossiers_ni_asistencia(): void
    {
        $this->hayCargo(eliminarOk: false);
        $this->dossiers->expects($this->never())->method('findByPk');
        $this->actividades->expects($this->never())->method('findById');
        $this->asistenteService->expects($this->never())->method('eliminar');

        $this->assertNotSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
    }

    // ---- solo el cargo -----------------------------------------------------------------------------------

    public function test_elimina_el_cargo_y_cierra_el_dossier_1302_de_la_persona(): void
    {
        $this->hayCargo();
        $this->hayDossiers();
        $this->cargos->expects($this->once())->method('Eliminar');
        $this->actividades->expects($this->never())->method('findById');
        $this->asistenteService->expects($this->never())->method('eliminar');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 1]));
        $this->assertSame([1302], $this->cerrados);
    }

    public function test_si_la_persona_no_tiene_dossier_1302_no_se_guarda_nada(): void
    {
        $this->hayCargo();
        $this->dossiers->method('findByPk')->willReturn(null);
        $this->dossiers->expects($this->never())->method('Guardar');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM]));
    }

    public function test_un_cargo_sin_persona_se_elimina_sin_buscar_dossier(): void
    {
        $this->hayCargo(idNom: null);
        $this->dossiers->expects($this->never())->method('findByPk');
        $this->actividades->expects($this->never())->method('findById');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
    }

    /** Hallazgo: `cerrarDossier` exige `id_pau > 0`, así que a una persona de paso (id negativo) no se le cierra el 1302. */
    public function test_a_una_persona_de_paso_no_se_le_cierra_el_dossier(): void
    {
        $this->hayCargo(idNom: -1001123);
        $this->dossiers->expects($this->never())->method('findByPk');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM]));
    }

    // ---- la selección de la lista ------------------------------------------------------------------------

    public function test_la_seleccion_de_la_lista_aporta_id_item_y_elim_asis(): void
    {
        $this->hayCargo();
        $this->hayDossiers();
        $this->hayActividad(self::TIPO_S);
        $asistente = $this->asistente();
        $this->asisDl->method('findById')->with(self::ID_ACTIV, self::ID_NOM)->willReturn($asistente);
        $this->asistenteService->expects($this->once())->method('eliminar')->with($asistente)->willReturn(true);

        $this->assertSame('', $this->sut()->execute(['sel' => [self::ID_NOM . '#' . self::ID_ITEM . '#2#1']]));
        $this->assertSame([1302, 1301], $this->cerrados);
    }

    /**
     * La lista del dossier 1302 (cargos de una persona) manda "id_item#elim_asis".
     * Antes se leía con el formato de la 3102 y se tomaba `elim_asis` (1 o 2) como
     * `id_item`: «quitar cargo» borraba el cargo 1 o 2, o decía que no lo encontraba.
     */
    public function test_la_seleccion_de_la_lista_de_cargos_de_una_persona_trae_solo_id_item_y_elim_asis(): void
    {
        $this->hayCargo();
        $this->hayDossiers();
        $this->hayActividad(self::TIPO_S);
        $this->cargos->expects($this->once())->method('findById')->with(self::ID_ITEM);
        $this->asisDl->method('findById')->willReturn($this->asistente());
        $this->asistenteService->expects($this->once())->method('eliminar')->willReturn(true);

        $this->assertSame('', $this->sut()->execute(['sel' => [self::ID_ITEM . '#2']]));
        $this->assertSame([1302, 1301], $this->cerrados);
    }

    public function test_la_seleccion_de_cargos_de_una_persona_sin_borrar_asistencia(): void
    {
        $this->hayCargo();
        $this->cargos->expects($this->once())->method('findById')->with(self::ID_ITEM);
        $this->actividades->expects($this->never())->method('findById');

        $this->assertSame('', $this->sut()->execute(['sel' => [self::ID_ITEM . '#1']]));
    }

    public function test_el_id_item_explicito_prevalece_sobre_la_seleccion(): void
    {
        $this->hayCargo();
        $this->cargos->expects($this->once())->method('findById')->with(self::ID_ITEM);
        $this->actividades->expects($this->never())->method('findById');

        // la selección apunta a otro cargo y pide borrar la asistencia: se ignora entera
        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'sel' => ['5#999#2#1']]));
    }

    // ---- cargo + asistencia ------------------------------------------------------------------------------

    /**
     * @dataProvider tiposProvider
     */
    public function test_la_asistencia_solo_se_borra_en_actividades_de_s_o_sg(int $tipo, bool $seBorra): void
    {
        $this->hayCargo();
        $this->hayDossiers();
        $this->hayActividad($tipo);
        $this->asisDl->method('findById')->willReturn($this->asistente());
        $this->asistenteService->expects($seBorra ? $this->once() : $this->never())->method('eliminar')->willReturn(true);

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
        $this->assertSame($seBorra ? [1302, 1301] : [1302], $this->cerrados);
    }

    /** @return array<string, array{int, bool}> */
    public static function tiposProvider(): array
    {
        return [
            's' => [self::TIPO_S, true],
            'sg' => [self::TIPO_SG, true],
            'n' => [self::TIPO_N, false],
        ];
    }

    public function test_sin_elim_asis_2_no_se_mira_la_actividad(): void
    {
        $this->hayCargo();
        $this->actividades->expects($this->never())->method('findById');
        $this->asistenteService->expects($this->never())->method('eliminar');

        foreach ([0, 1, 3] as $elim) {
            $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => $elim]));
        }
    }

    public function test_si_la_actividad_no_existe_el_cargo_queda_eliminado_sin_error(): void
    {
        $this->hayCargo();
        $this->actividades->method('findById')->willReturn(null);
        $this->asistenteService->expects($this->never())->method('eliminar');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
    }

    public function test_el_asistente_se_busca_en_dl_out_ex_y_pub_por_ese_orden(): void
    {
        $this->hayCargo();
        $this->hayActividad(self::TIPO_S);
        $orden = [];
        $enPub = $this->asistente();
        foreach (['dl' => $this->asisDl, 'out' => $this->asisOut, 'ex' => $this->asisEx] as $nombre => $repo) {
            $repo->method('findById')->willReturnCallback(function () use (&$orden, $nombre) {
                $orden[] = $nombre;

                return null;
            });
        }
        $this->asisPub->method('findById')->willReturnCallback(function () use (&$orden, $enPub) {
            $orden[] = 'pub';

            return $enPub;
        });
        $this->asistenteService->expects($this->once())->method('eliminar')->with($enPub)->willReturn(true);

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
        $this->assertSame(['dl', 'out', 'ex', 'pub'], $orden);
    }

    public function test_si_no_hay_asistencia_no_hay_error(): void
    {
        $this->hayCargo();
        $this->hayActividad(self::TIPO_S);
        $this->asistenteService->expects($this->never())->method('eliminar');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
    }

    public function test_la_asistencia_de_otra_dl_no_se_borra_y_no_se_avisa(): void
    {
        $this->hayCargo();
        $this->hayDossiers();
        $this->hayActividad(self::TIPO_S);
        $this->asisDl->method('findById')->willReturn($this->asistente(deMiDl: false));
        $this->asistenteService->expects($this->never())->method('eliminar');

        $this->assertSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
        $this->assertSame([1302], $this->cerrados);
    }

    /** El cargo ya está borrado cuando falla la asistencia: el error informa, pero no se deshace nada. */
    public function test_si_falla_el_borrado_de_la_asistencia_devuelve_error_con_el_cargo_ya_eliminado(): void
    {
        $this->hayCargo();
        $this->hayDossiers();
        $this->hayActividad(self::TIPO_S);
        $this->asisDl->method('findById')->willReturn($this->asistente());
        $this->asistenteService->method('eliminar')->willReturn(false);
        $this->cargos->expects($this->once())->method('Eliminar');

        $this->assertNotSame('', $this->sut()->execute(['id_item' => self::ID_ITEM, 'elim_asis' => 2]));
        $this->assertSame([1302], $this->cerrados, 'el 1301 no se cierra si la asistencia no se ha borrado');
    }
}
