<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\application\ActividadCargoEditar;
use src\actividadcargos\application\ActividadCargoNuevo;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Alta y edición de un cargo en una actividad: valores grabados, duplicados,
 * dossiers 1302/3102 (cargo) y 1301/3101 (asistencia), y alta/baja de la
 * asistencia según la casilla «asiste».
 */
final class ActividadCargoMutacionesTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private ActividadCargoRepositoryInterface&MockObject $cargos;
    private AsistenteApplicationService&MockObject $asistentes;
    private AsistenteDlRepositoryInterface&MockObject $asisDl;
    private AsistenteOutRepositoryInterface&MockObject $asisOut;
    private AsistenteExRepositoryInterface&MockObject $asisEx;
    private AsistentePubRepositoryInterface&MockObject $asisPub;
    private DossierRepositoryInterface&MockObject $dossiers;
    /** @var list<string> dossiers abiertos «tabla-id_pau-tipo» */
    private array $abiertos = [];
    private mixed $erroresAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->erroresAnterior = $_SESSION['errores'] ?? null;
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->asistentes = $this->createMock(AsistenteApplicationService::class);
        $this->asisDl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $this->asisOut = $this->createMock(AsistenteOutRepositoryInterface::class);
        $this->asisEx = $this->createMock(AsistenteExRepositoryInterface::class);
        $this->asisPub = $this->createMock(AsistentePubRepositoryInterface::class);
        $this->dossiers = $this->createMock(DossierRepositoryInterface::class);
        $this->dossiers->method('findByPk')->willReturnCallback(function (DossierPk $pk): Dossier {
            $d = $this->createMock(Dossier::class);
            $d->method('abrir')->willReturnCallback(function () use ($pk): void {
                $this->abiertos[] = $pk->tabla() . '-' . $pk->idPau() . '-' . $pk->idTipoDossier();
            });
            return $d;
        });
        $this->cargos->method('getNewId')->willReturn(500);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        if ($this->erroresAnterior === null) {
            unset($_SESSION['errores']);
        } else {
            $_SESSION['errores'] = $this->erroresAnterior;
        }
        parent::tearDown();
    }

    private function nuevo(): ActividadCargoNuevo
    {
        return new ActividadCargoNuevo($this->cargos, $this->asistentes, $this->asisDl, $this->asisOut, $this->asisEx, $this->asisPub, $this->dossiers);
    }

    private function editar(): ActividadCargoEditar
    {
        return new ActividadCargoEditar($this->cargos, $this->asistentes, $this->asisDl, $this->asisOut, $this->asisEx, $this->asisPub, $this->dossiers);
    }

    private function persona(?PersonaDl $p): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturn($p);
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    private function asistente(int $idActiv = 10, int $idNom = 100123, string $dl = 'dlp'): Asistente
    {
        $a = new Asistente();
        $a->setId_activ($idActiv);
        $a->setId_nom($idNom);
        $a->setPropio(false);
        $a->setFalta(true);
        $a->setDl_responsable($dl);
        return $a;
    }

    private const ENTRADA = ['id_activ' => '10', 'id_nom' => '100123', 'id_cargo' => '3', 'observ' => 'nota', 'puede_agd' => 't'];

    // --- Nuevo ---

    public function test_nuevo_graba_el_cargo_y_abre_los_dossiers_del_cargo(): void
    {
        $this->cargos->expects($this->once())->method('Guardar')->willReturnCallback(function (ActividadCargo $c): bool {
            $this->assertSame([500, 10, 3, 100123, 'nota', true], [$c->getId_item(), $c->getId_activ(), $c->getId_cargo(), $c->getId_nom(), $c->getObserv(), $c->isPuede_agd()]);
            return true;
        });
        $this->asistentes->expects($this->never())->method('guardar');
        $this->assertSame('', $this->nuevo()->execute(self::ENTRADA));
        $this->assertSame(['p-100123-1302', 'a-10-3102'], $this->abiertos);
    }

    public function test_nuevo_sin_observaciones_y_sin_puede_agd(): void
    {
        $this->cargos->method('Guardar')->willReturnCallback(function (ActividadCargo $c): bool {
            $this->assertNull($c->getObserv());
            $this->assertFalse($c->isPuede_agd());
            return true;
        });
        $this->nuevo()->execute(['id_activ' => 10, 'id_nom' => 100123, 'id_cargo' => 3]);
    }

    public function test_nuevo_faltan_parametros(): void
    {
        $this->cargos->expects($this->never())->method('Guardar');
        foreach (['id_activ', 'id_cargo'] as $campo) {
            $this->assertSame('faltan parametros id_activ / id_nom / id_cargo', $this->nuevo()->execute(array_merge(self::ENTRADA, [$campo => '0'])));
        }
    }

    public function test_nuevo_cargo_duplicado(): void
    {
        $this->cargos->method('Guardar')->willReturn(false);
        $_SESSION['errores'] = ['otro', 'ERROR: duplicate key value violates unique constraint'];
        $this->assertSame('ya existe este cargo para esta actividad', $this->nuevo()->execute(self::ENTRADA));
        $this->assertSame([], $this->abiertos);
    }

    public function test_nuevo_otro_error_devuelve_el_ultimo_error_o_uno_generico(): void
    {
        $this->cargos->method('Guardar')->willReturn(false);
        $_SESSION['errores'] = ['ERROR: violates foreign key'];
        $this->assertSame('ERROR: violates foreign key', $this->nuevo()->execute(self::ENTRADA));
        unset($_SESSION['errores']);
        $this->assertSame('hay un error, no se ha guardado', $this->nuevo()->execute(self::ENTRADA));
    }

    public function test_nuevo_con_asiste_crea_la_asistencia_propia_y_abre_sus_dossiers(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->persona(new PersonaDl());
        $this->asistentes->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a): bool {
            $this->assertSame([10, 100123, true, false, false, false, 'dlb'], [
                $a->getId_activ(), $a->getId_nom(), $a->isPropio(), $a->isFalta(), $a->isEst_ok(), $a->isCfi(), $a->getDl_responsable(),
            ]);
            return true;
        });
        $this->assertSame('', $this->nuevo()->execute(self::ENTRADA + ['asis' => 'on']));
        $this->assertSame(['p-100123-1302', 'a-10-3102', 'p-100123-1301', 'a-10-3101'], $this->abiertos);
    }

    public function test_nuevo_con_asiste_y_persona_inexistente(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->persona(null);
        $this->asistentes->expects($this->never())->method('guardar');
        $this->assertSame('no encuentro a nadie con id_nom: 100123', $this->nuevo()->execute(self::ENTRADA + ['asis' => 'on']));
    }

    public function test_nuevo_con_asiste_si_no_se_guarda_la_asistencia(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->persona(new PersonaDl());
        $this->asistentes->method('guardar')->willReturn(false);
        $this->assertSame('hay un error, no se ha guardado el asistente', $this->nuevo()->execute(self::ENTRADA + ['asis' => 'on']));
        $this->assertNotContains('p-100123-1301', $this->abiertos);
    }

    /**
     * Hallazgo: si la persona ya asiste (aunque la asistencia sea de otra dl, en la
     * tabla out/ex/pub), el alta del cargo con «asiste» la marca como propia y de mi
     * dl, sin comprobar permisos (en AsistenteGuardar se exige el permiso, paso 2.2).
     */
    public function test_nuevo_con_asiste_se_apropia_de_una_asistencia_de_otra_dl(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->persona(new PersonaDl());
        $existente = $this->asistente();
        $this->asisDl->method('findById')->willReturn(null);
        $this->asisOut->expects($this->once())->method('findById')->with(10, 100123)->willReturn($existente);
        $this->asisEx->expects($this->never())->method('findById');
        $this->asistentes->expects($this->once())->method('guardar')->with($this->identicalTo($existente))->willReturn(true);
        $this->nuevo()->execute(self::ENTRADA + ['asis' => 'on']);
        $this->assertSame([true, false, 'dlb'], [$existente->isPropio(), $existente->isFalta(), $existente->getDl_responsable()]);
    }

    // --- Editar ---

    private function cargoExistente(): ActividadCargo
    {
        $c = new ActividadCargo();
        $c->setId_item(77);
        $c->setId_activ(10);
        $c->setId_cargo(2);
        $c->setId_nom(100123);
        $c->setObserv('vieja');
        $c->setPuede_agd(true);
        return $c;
    }

    public function test_editar_actualiza_el_cargo_existente(): void
    {
        $c = $this->cargoExistente();
        $this->cargos->method('findById')->with(77)->willReturn($c);
        $this->cargos->expects($this->once())->method('Guardar')->with($this->identicalTo($c))->willReturn(true);
        $this->assertSame('', $this->editar()->execute(['id_item' => '77', 'id_activ' => '10', 'id_nom' => '100123', 'id_cargo' => '5', 'puede_agd' => '']));
        $this->assertSame([77, 5, null, false], [$c->getId_item(), $c->getId_cargo(), $c->getObserv(), $c->isPuede_agd()]);
        $this->assertSame([], $this->abiertos, 'sin asistencia no se toca ningún dossier');
    }

    public function test_editar_sin_id_item_crea_un_cargo_nuevo(): void
    {
        $this->cargos->expects($this->never())->method('findById');
        $this->cargos->expects($this->once())->method('Guardar')->willReturnCallback(fn (ActividadCargo $c): bool => $c->getId_item() === 500);
        $this->assertSame('', $this->editar()->execute(self::ENTRADA));
    }

    public function test_editar_cargo_inexistente(): void
    {
        $this->cargos->method('findById')->willReturn(null);
        $this->cargos->expects($this->never())->method('Guardar');
        $this->assertSame('no encuentro el cargo', $this->editar()->execute(self::ENTRADA + ['id_item' => 77]));
    }

    public function test_editar_faltan_parametros_y_errores_al_guardar(): void
    {
        $this->assertSame('faltan parametros id_activ / id_nom / id_cargo', $this->editar()->execute(['id_activ' => 10, 'id_nom' => 1]));
        $this->cargos->method('Guardar')->willReturn(false);
        $_SESSION['errores'] = ['duplicate key value'];
        $this->assertSame('ya existe este cargo para esta actividad', $this->editar()->execute(self::ENTRADA));
        $_SESSION['errores'] = ['otra cosa'];
        $this->assertSame('hay un error, no se ha guardado', $this->editar()->execute(self::ENTRADA));
    }

    public function test_editar_marcar_asiste_sin_asistencia_la_crea(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->asistentes->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a): bool {
            $this->assertSame([10, 100123, true, false, 'dlb'], [$a->getId_activ(), $a->getId_nom(), $a->isPropio(), $a->isFalta(), $a->getDl_responsable()]);
            return true;
        });
        $this->assertSame('', $this->editar()->execute(self::ENTRADA + ['asis' => 'on', 'asis_presente' => '1']));
        $this->assertSame(['p-100123-1301', 'a-10-3101'], $this->abiertos);
    }

    public function test_editar_marcar_asiste_si_no_se_guarda(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->asistentes->method('guardar')->willReturn(false);
        $this->assertSame('hay un error, no se ha guardado el asistente', $this->editar()->execute(self::ENTRADA + ['asis' => 'on']));
    }

    public function test_editar_sin_asiste_y_sin_asistencia_no_hace_nada(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->asistentes->expects($this->never())->method('guardar');
        $this->asistentes->expects($this->never())->method('eliminar');
        $this->assertSame('', $this->editar()->execute(self::ENTRADA + ['asis_presente' => '1']));
    }

    public function test_editar_asistencia_existente_y_asiste_marcado_no_la_toca(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->asisDl->method('findById')->willReturn($this->asistente(dl: 'dlb'));
        $this->asistentes->expects($this->never())->method('guardar');
        $this->asistentes->expects($this->never())->method('eliminar');
        $this->assertSame('', $this->editar()->execute(self::ENTRADA + ['asis' => 'on', 'asis_presente' => '1']));
    }

    public function test_editar_sin_la_casilla_en_el_formulario_no_borra_la_asistencia(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->asisDl->method('findById')->willReturn($this->asistente(dl: 'dlb'));
        $this->asistentes->expects($this->never())->method('eliminar');
        $this->assertSame('', $this->editar()->execute(self::ENTRADA));
    }

    /**
     * Hallazgo: desmarcar «asiste» borra la asistencia aunque sea de otra dl (tabla
     * out/ex/pub), sin comprobar permisos; y tras borrarla «abre» los dossiers
     * 1301/3101 en lugar de cerrarlos como hace ActividadCargoEliminar.
     */
    public function test_editar_desmarcar_asiste_borra_la_asistencia_aunque_sea_de_otra_dl(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $otra = $this->asistente(dl: 'dlp');
        $this->asisPub->method('findById')->willReturn($otra);
        $this->asistentes->expects($this->once())->method('eliminar')->with($this->identicalTo($otra))->willReturn(true);
        $this->assertSame('', $this->editar()->execute(self::ENTRADA + ['asis_presente' => '1']));
        $this->assertSame(['p-100123-1301', 'a-10-3101'], $this->abiertos);
    }

    public function test_editar_desmarcar_asiste_si_no_se_borra(): void
    {
        $this->cargos->method('Guardar')->willReturn(true);
        $this->asisEx->method('findById')->willReturn($this->asistente());
        $this->asistentes->method('eliminar')->willReturn(false);
        $this->assertSame('hay un error, no se ha eliminado el asistente', $this->editar()->execute(self::ENTRADA + ['asis_presente' => '1']));
    }

    public function test_dossier_inexistente_se_crea_antes_de_abrirlo(): void
    {
        $dossiers = $this->createMock(DossierRepositoryInterface::class);
        $dossiers->method('findByPk')->willReturn(null);
        $creado = $this->createMock(Dossier::class);
        $creado->expects($this->exactly(2))->method('abrir');
        $dossiers->expects($this->exactly(2))->method('crearDossier')->willReturn($creado);
        $dossiers->expects($this->exactly(2))->method('Guardar')->with($creado);
        $this->cargos->method('Guardar')->willReturn(true);
        $uc = new ActividadCargoNuevo($this->cargos, $this->asistentes, $this->asisDl, $this->asisOut, $this->asisEx, $this->asisPub, $dossiers);
        $this->assertSame('', $uc->execute(self::ENTRADA));
    }
}
