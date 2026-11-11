<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\AsistenteGuardar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\actividadplazas\domain\value_objects\PlazaId;

final class AsistenteGuardarTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
        // Tests de plaza/mover asumen el módulo instalado (como en DL con plazas).
        $_SESSION['config'] = [
            'a_apps' => ['actividadplazas' => 99],
            'app_installed' => [99],
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    private function createSut(
        ?AsistenteApplicationService $app = null,
        ?ActividadAllRepositoryInterface $actividadRepo = null,
        ?DossierRepositoryInterface $dosRepo = null,
        ?AsistenteEliminar $asistenteEliminar = null,
        ?PlazaPropietarioAsignacionInterface $plazaPropietario = null,
    ): AsistenteGuardar {
        $app ??= $this->createMock(AsistenteApplicationService::class);
        $actividadRepo ??= $this->createMock(ActividadAllRepositoryInterface::class);
        $dosRepo ??= $this->createMock(DossierRepositoryInterface::class);
        $asistenteEliminar ??= new AsistenteEliminar(
            $this->createMock(AsistenteApplicationService::class),
            $this->createMock(MatriculaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );
        $plazaPropietario ??= $this->createMock(PlazaPropietarioAsignacionInterface::class);
        return new AsistenteGuardar($app, $actividadRepo, $dosRepo, $asistenteEliminar, $plazaPropietario);
    }

    public function test_mod_no_soportado(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['mod' => 'otro', 'id_activ' => 1, 'id_nom' => 1]));
    }

    public function test_faltan_ids(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['mod' => 'nuevo', 'id_activ' => 0, 'id_nom' => 1]));
        $this->assertNotSame('', $sut->execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 0]));
    }

    public function test_nuevo_acepta_ids_negativos_de_paso(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(-2001456, -1001123)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getId_activ() === -2001456 && $a->getId_nom() === -1001123;
        });

        $sut = $this->createSut($app, null, $dosRepo);

        $this->assertSame('', $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => -2001456,
            'id_nom' => -1001123,
            'plaza' => 1,
        ]));
    }

    public function test_mover_sin_id_activ_old(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute([
            'mod' => 'mover',
            'id_activ' => 2,
            'id_nom' => 3,
            'id_activ_old' => 0,
        ]));
    }

    public function test_nuevo_abre_dossier_existente_y_guarda(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getId_activ() === 10 && $a->getId_nom() === 20;
        });

        $sut = $this->createSut($app, null, $dosRepo);

        $this->assertSame('', $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => 10,
            'id_nom' => 20,
            'plaza' => 1,
        ]));
    }

    public function test_editar_sin_permiso(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(false);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->never())->method('guardar');

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute([
            'mod' => 'editar',
            'id_activ' => 1,
            'id_nom' => 2,
        ]));
    }

    public function test_editar_guarda(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        $sut = $this->createSut($app);

        $this->assertSame('', $sut->execute([
            'mod' => 'editar',
            'id_activ' => 1,
            'id_nom' => 2,
            'encargo' => 'x',
            'plaza' => 1,
        ]));
    }

    public function test_mover_usa_propietario_de_la_actividad_destino(): void
    {
        $destino = new ActividadAll();
        $destino->setDl_org('bcnf');

        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->with(20)->willReturn($destino);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(20, 10)->willReturn(null);
        $guardado = null;
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) use (&$guardado) {
            $guardado = $a;

            return true;
        });

        $plaza = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $plaza->expects($this->once())->method('asegurar')->willReturnCallback(function (Asistente $a) {
            $prop = $a->getPropietarioVo()?->value() ?? '';

            return $prop === 'bcn>dl' ? '' : 'Ya están todas las plazas ocupadas';
        });

        $oOrigen = $this->createMock(Asistente::class);
        $oOrigen->method('perm_modificar')->willReturn(true);
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->method('findById')->with(5, 10)->willReturn($oOrigen);
        $elimApp->method('eliminar')->willReturn(true);
        $eliminar = new AsistenteEliminar(
            $elimApp,
            $this->createMock(MatriculaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );

        $sut = $this->createSut($app, $actividadRepo, null, $eliminar, $plaza);

        $this->assertSame('', $sut->execute([
            'mod' => 'mover',
            'id_activ' => 20,
            'id_nom' => 10,
            'id_activ_old' => 5,
            'plaza' => 4,
            // Cupo de la actividad origen (dl ajena). No debe usarse en el destino.
            'propietario' => 'mad>dl',
        ]));
        $this->assertInstanceOf(Asistente::class, $guardado);
        $this->assertSame('bcn>dl', $guardado->getPropietarioVo()?->value());
    }

    public function test_mover_no_elimina_si_falla_guardar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);
        $o->method('setEncargo')->willReturnSelf();
        $o->method('setObserv')->willReturnSelf();
        $o->method('setObservEstVo')->willReturnSelf();
        $o->method('setPropio')->willReturnSelf();
        $o->method('setEst_ok')->willReturnSelf();
        $o->method('setCfi')->willReturnSelf();
        $o->method('setFalta')->willReturnSelf();
        $o->method('setCfi_con')->willReturnSelf();
        $o->method('setDlResponsableVo')->willReturnSelf();
        $o->method('setPropietarioVo')->willReturnSelf();
        $o->method('setPlazaVoComprobando')->willReturn('Ya están todas las plazas ocupadas');

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(20, 10)->willReturn($o);
        $app->expects($this->never())->method('guardar');

        // AsistenteEliminar es final (no mockeable): usamos uno real cuyo colaborador
        // demuestra que execute() NO se invoca (no llega a buscar ni eliminar nada).
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->expects($this->never())->method('findById');
        $elimApp->expects($this->never())->method('eliminar');
        $eliminar = new AsistenteEliminar(
            $elimApp,
            $this->createMock(MatriculaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );

        $sut = $this->createSut($app, null, null, $eliminar);

        $this->assertSame(
            'Ya están todas las plazas ocupadas',
            $sut->execute([
                'mod' => 'mover',
                'id_activ' => 20,
                'id_nom' => 10,
                'id_activ_old' => 5,
                'plaza' => 4,
            ])
        );
    }

    public function test_mover_elimina_solo_despues_de_guardar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);
        $o->method('setEncargo')->willReturnSelf();
        $o->method('setObserv')->willReturnSelf();
        $o->method('setObservEstVo')->willReturnSelf();
        $o->method('setPropio')->willReturnSelf();
        $o->method('setEst_ok')->willReturnSelf();
        $o->method('setCfi')->willReturnSelf();
        $o->method('setFalta')->willReturnSelf();
        $o->method('setCfi_con')->willReturnSelf();
        $o->method('setDlResponsableVo')->willReturnSelf();
        $o->method('setPropietarioVo')->willReturnSelf();
        $o->method('setPlazaVoComprobando')->willReturn('');

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(20, 10)->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        // AsistenteEliminar es final (no mockeable): usamos uno real y verificamos a
        // través de sus colaboradores que se elimina el origen (id_activ_old=5, id_nom=10)
        // sólo tras un guardado correcto.
        $oOrigen = $this->createMock(Asistente::class);
        $oOrigen->method('perm_modificar')->willReturn(true);

        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->method('findById')->with(5, 10)->willReturn($oOrigen);
        $elimApp->expects($this->once())->method('eliminar')->with($oOrigen)->willReturn(true);

        $elimMatRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $elimMatRepo->method('matching')->willReturn([]);

        $elimDosRepo = $this->createMock(DossierRepositoryInterface::class);
        $elimDosRepo->method('findByPk')->willReturn(null);

        $eliminar = new AsistenteEliminar($elimApp, $elimMatRepo, $elimDosRepo);

        $sut = $this->createSut($app, null, null, $eliminar);

        $this->assertSame('', $sut->execute([
            'mod' => 'mover',
            'id_activ' => 20,
            'id_nom' => 10,
            'id_activ_old' => 5,
            'plaza' => 4,
        ]));
    }

    public function test_nuevo_sin_modulo_plazas_no_exige_plaza(): void
    {
        $_SESSION['config'] = [
            'a_apps' => [],
            'app_installed' => [],
        ];

        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getPlazaVo() === null
                && $a->getId_activ() === 10
                && $a->getId_nom() === 20;
        });

        $sut = $this->createSut($app, null, $dosRepo);

        $this->assertSame('', $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => 10,
            'id_nom' => 20,
            // sin campo plaza (como el form cuando no hay actividadplazas)
        ]));
    }

    /**
     * Servicio que no encuentra al asistente (o devuelve el indicado) y captura lo que se guarda.
     *
     * @param-out Asistente|null $guardado
     */
    private function appQueGuarda(?Asistente &$guardado, ?Asistente $existente = null, bool $resultado = true): AsistenteApplicationService
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($existente);
        $app->method('guardar')->willReturnCallback(function (Asistente $a) use (&$guardado, $resultado): bool {
            $guardado = $a;

            return $resultado;
        });

        return $app;
    }

    private function dossierRepo(): DossierRepositoryInterface
    {
        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($this->createMock(Dossier::class));

        return $dosRepo;
    }

    private function asistenteDe(string $dlResponsable): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(10);
        $a->setId_nom(20);
        $a->setDl_responsable($dlResponsable);
        $a->setEncargo('encargo anterior');
        $a->setPropio(true);

        return $a;
    }

    public function test_nuevo_graba_los_campos_del_formulario_tal_como_llegan_por_post(): void
    {
        $guardado = null;
        $plaza = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $plaza->expects($this->once())->method('asegurar')
            ->with($this->isInstanceOf(Asistente::class), PlazaId::PEDIDA, PlazaId::ASIGNADA, false)
            ->willReturn('');
        $sut = $this->createSut($this->appQueGuarda($guardado), null, $this->dossierRepo(), null, $plaza);

        $msg = $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => '10',
            'id_nom' => '20',
            'encargo' => '  cocina ',
            'observ' => 'llega tarde',
            'observ_est' => 'convalida latín',
            'propio' => 't',
            'est_ok' => 'on',
            'cfi' => '1',
            'falta' => 'false',
            'cfi_con' => '3',
            'propietario' => 'dlb>dl',
            'plaza' => '4',
        ]);

        $this->assertSame('', $msg);
        $this->assertInstanceOf(Asistente::class, $guardado);
        $this->assertSame([10, 20], [$guardado->getId_activ(), $guardado->getId_nom()]);
        $this->assertSame('cocina', $guardado->getEncargo());
        $this->assertSame('llega tarde', $guardado->getObserv());
        $this->assertSame('convalida latín', $guardado->getObserv_est());
        $this->assertSame([true, true, true, false], [$guardado->isPropio(), $guardado->isEst_ok(), $guardado->isCfi(), $guardado->isFalta()]);
        $this->assertSame(3, $guardado->getCfi_con());
        $this->assertSame('dlb>dl', $guardado->getPropietarioVo()?->value());
        $this->assertSame(PlazaId::ASIGNADA, $guardado->getPlazaVo()?->value());
        $this->assertSame('dl', $guardado->getDl_responsable());
    }

    /**
     * `is_true()` no reconoce «si»: una casilla que llegue con ese valor se guarda como falsa.
     */
    public function test_los_campos_vacios_o_no_reconocidos_se_guardan_vacios_o_falsos(): void
    {
        $guardado = null;
        $sut = $this->createSut($this->appQueGuarda($guardado), null, $this->dossierRepo());

        $this->assertSame('', $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => 10,
            'id_nom' => 20,
            'encargo' => '   ',
            'propio' => 'si',
            'propietario' => 'xxx',
            'plaza' => '0',
        ]));

        $this->assertNull($guardado->getEncargoVo());
        $this->assertNull($guardado->getObservVo());
        $this->assertFalse($guardado->isPropio());
        $this->assertNull($guardado->getPropietarioVo(), '«xxx» es «sin propietario»');
        $this->assertSame(0, $guardado->getCfi_con());
        $this->assertSame(PlazaId::PEDIDA, $guardado->getPlazaVo()?->value(), 'plaza 0 pasa a «pedida»');
    }

    /**
     * Hallazgo: un encargo de más de 255 caracteres lanza `InvalidArgumentException`, que no es
     * `RuntimeException`: `execute()` no la captura y la petición acaba en error en lugar de devolver un mensaje.
     */
    public function test_un_encargo_demasiado_largo_lanza_una_excepcion_sin_capturar(): void
    {
        $guardado = null;
        $sut = $this->createSut($this->appQueGuarda($guardado), null, $this->dossierRepo());

        $this->expectException(\InvalidArgumentException::class);

        $sut->execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 20, 'encargo' => str_repeat('x', 256)]);
    }

    /**
     * @param array<string, mixed> $entrada
     */
    #[DataProvider('seleccionProvider')]
    public function test_los_ids_salen_de_la_seleccion_segun_el_dossier(array $entrada, int $idActiv, int $idNom): void
    {
        $guardado = null;
        $sut = $this->createSut($this->appQueGuarda($guardado), null, $this->dossierRepo());

        $this->assertSame('', $sut->execute(['mod' => 'nuevo'] + $entrada));

        $this->assertSame([$idActiv, $idNom], [$guardado->getId_activ(), $guardado->getId_nom()]);
    }

    /**
     * @return iterable<string, array{array<string, mixed>, int, int}>
     */
    public static function seleccionProvider(): iterable
    {
        yield 'dossier de persona: actividad en sel, persona en id_pau' => [['pau' => 'p', 'sel' => ['10#x'], 'id_pau' => '20', 'id_activ' => 99], 10, 20];
        yield 'dossier de persona sin id_pau: persona en id_nom' => [['pau' => 'p', 'sel' => ['10#x'], 'id_nom' => '20'], 10, 20];
        yield 'dossier de actividad: persona en sel, actividad en id_pau' => [['pau' => 'a', 'sel' => ['20#x'], 'id_pau' => '10', 'id_nom' => 99], 10, 20];
        yield 'sin selección: id_activ e id_nom' => [['id_activ' => '10', 'id_nom' => '20'], 10, 20];
        yield 'sin selección ni id_nom: persona en id_pau' => [['id_activ' => '10', 'id_pau' => '20'], 10, 20];
    }

    public function test_con_seleccion_de_un_dossier_desconocido_faltan_los_ids(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->expects($this->never())->method('guardar');

        $this->assertNotSame('', $this->createSut($app)->execute(['mod' => 'nuevo', 'pau' => 'u', 'sel' => ['10#20'], 'id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_nuevo_crea_el_dossier_1301_si_la_persona_no_lo_tiene(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');
        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn(null);
        $dosRepo->expects($this->once())->method('crearDossier')
            ->with($this->callback(static fn (DossierPk $pk): bool => $pk == DossierPk::fromArray(['tabla' => 'p', 'id_pau' => 20, 'id_tipo_dossier' => 1301])))
            ->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier);
        $guardado = null;

        $this->assertSame('', $this->createSut($this->appQueGuarda($guardado), null, $dosRepo)->execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_editar_y_mover_no_tocan_el_dossier(): void
    {
        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->expects($this->never())->method('findByPk');
        $guardado = null;

        $this->createSut($this->appQueGuarda($guardado, $this->asistenteDe('dl')), null, $dosRepo)
            ->execute(['mod' => 'editar', 'id_activ' => 10, 'id_nom' => 20]);
    }

    public function test_si_el_repositorio_no_guarda_se_devuelve_el_error(): void
    {
        $guardado = null;
        $sut = $this->createSut($this->appQueGuarda($guardado, null, false), null, $this->dossierRepo());

        $this->assertNotSame('', $sut->execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_una_runtime_exception_al_guardar_se_devuelve_como_mensaje(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn(null);
        $app->method('guardar')->willThrowException(new \RuntimeException('tabla bloqueada'));

        $this->assertSame('tabla bloqueada', $this->createSut($app, null, $this->dossierRepo())->execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_editar_actualiza_el_asistente_existente_de_mi_dl(): void
    {
        $guardado = null;
        $existente = $this->asistenteDe('dl');

        $this->assertSame('', $this->createSut($this->appQueGuarda($guardado, $existente))->execute([
            'mod' => 'editar', 'id_activ' => 10, 'id_nom' => 20, 'encargo' => 'sacristía', 'plaza' => '4',
        ]));

        $this->assertSame($existente, $guardado);
        $this->assertSame('sacristía', $guardado->getEncargo());
        $this->assertFalse($guardado->isPropio(), 'una casilla sin marcar desmarca el valor anterior');
    }

    public function test_editar_un_asistente_que_no_existe_se_rechaza_como_si_fuera_de_otra_dl(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn(null);
        $app->expects($this->never())->method('guardar');

        $this->assertNotSame('', $this->createSut($app)->execute(['mod' => 'editar', 'id_activ' => 10, 'id_nom' => 20]));
    }

    /**
     * Regresión: `perm_modificar()` solo se comprobaba con `mod=editar`. Con `nuevo` o `mover` sobre un asistente
     * que ya existía y era de otra dl, se sobrescribían sus datos y pasaba a tener mi dl como responsable.
     */
    #[DataProvider('modosProvider')]
    public function test_una_asistencia_existente_de_otra_dl_no_se_sobrescribe_con_ningun_modo(string $mod): void
    {
        $ajeno = $this->asistenteDe('dlp');
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($ajeno);
        $app->expects($this->never())->method('guardar');
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->expects($this->never())->method('eliminar');
        $eliminar = new AsistenteEliminar($elimApp, $this->createMock(MatriculaRepositoryInterface::class), $this->createMock(DossierRepositoryInterface::class));

        $msg = $this->createSut($app, null, $this->dossierRepo(), $eliminar)->execute([
            'mod' => $mod, 'id_activ' => 10, 'id_nom' => 20, 'id_activ_old' => 5, 'encargo' => 'otro',
        ]);

        $this->assertNotSame('', $msg);
        $this->assertSame('encargo anterior', $ajeno->getEncargo());
        $this->assertSame('dlp', $ajeno->getDl_responsable());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function modosProvider(): iterable
    {
        yield 'nuevo' => ['nuevo'];
        yield 'editar' => ['editar'];
        yield 'mover' => ['mover'];
    }

    public function test_nuevo_sobre_una_asistencia_existente_de_mi_dl_la_actualiza(): void
    {
        $guardado = null;
        $mia = $this->asistenteDe('dl');

        $this->assertSame('', $this->createSut($this->appQueGuarda($guardado, $mia), null, $this->dossierRepo())->execute([
            'mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 20, 'encargo' => 'otro',
        ]));

        $this->assertSame($mia, $guardado);
        $this->assertSame('otro', $guardado->getEncargo());
    }

    public function test_mover_marca_la_asistencia_como_propia(): void
    {
        $guardado = null;
        $oOrigen = $this->asistenteDe('dl');
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->method('findById')->willReturn($oOrigen);
        $elimApp->method('eliminar')->willReturn(true);
        $eliminar = new AsistenteEliminar($elimApp, $this->createMock(MatriculaRepositoryInterface::class), $this->createMock(DossierRepositoryInterface::class));

        $this->assertSame('', $this->createSut($this->appQueGuarda($guardado), null, null, $eliminar)->execute([
            'mod' => 'mover', 'id_activ' => 20, 'id_nom' => 10, 'id_activ_old' => 5,
        ]));

        $this->assertTrue($guardado->isPropio());
    }

    public function test_mover_a_una_actividad_que_no_existe_deja_el_propietario_vacio(): void
    {
        $guardado = null;
        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->willReturn(null);
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->method('findById')->willReturn($this->asistenteDe('dl'));
        $elimApp->method('eliminar')->willReturn(true);
        $eliminar = new AsistenteEliminar($elimApp, $this->createMock(MatriculaRepositoryInterface::class), $this->createMock(DossierRepositoryInterface::class));

        $this->createSut($this->appQueGuarda($guardado), $actividadRepo, null, $eliminar)->execute([
            'mod' => 'mover', 'id_activ' => 20, 'id_nom' => 10, 'id_activ_old' => 5, 'propietario' => 'mad>dl',
        ]);

        $this->assertNull($guardado->getPropietarioVo());
    }

    /**
     * Hallazgo: al mover, el destino se guarda antes de borrar el origen y sin transacción. Si el origen no se
     * puede borrar (no se encuentra, es de otra dl o falla), se devuelve error pero la persona ya está en las dos
     * actividades.
     */
    public function test_mover_si_no_se_puede_borrar_el_origen_devuelve_error_con_el_destino_ya_guardado(): void
    {
        $guardado = null;
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->method('findById')->willReturn($this->asistenteDe('dlp'));
        $elimApp->expects($this->never())->method('eliminar');
        $eliminar = new AsistenteEliminar($elimApp, $this->createMock(MatriculaRepositoryInterface::class), $this->createMock(DossierRepositoryInterface::class));

        $msg = $this->createSut($this->appQueGuarda($guardado), null, null, $eliminar)->execute([
            'mod' => 'mover', 'id_activ' => 20, 'id_nom' => 10, 'id_activ_old' => 5,
        ]);

        $this->assertNotSame('', $msg);
        $this->assertInstanceOf(Asistente::class, $guardado);
        $this->assertSame(20, $guardado->getId_activ());
    }

    public function test_sin_modulo_de_plazas_la_plaza_del_formulario_se_ignora(): void
    {
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $guardado = null;
        $plaza = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $plaza->expects($this->never())->method('asegurar');

        $this->createSut($this->appQueGuarda($guardado), null, $this->dossierRepo(), null, $plaza)->execute([
            'mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 20, 'plaza' => '4', 'propietario' => 'dlb>dl',
        ]);

        $this->assertNull($guardado->getPlazaVo());
        $this->assertSame('dlb>dl', $guardado->getPropietarioVo()?->value());
    }
}
