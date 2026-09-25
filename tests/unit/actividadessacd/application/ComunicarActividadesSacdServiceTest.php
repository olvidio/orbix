<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\permisos\domain\PermisosActividades;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\entity\PersonaSacd;
use src\personas\domain\services\TelecoPersonaService;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * El listado de com_sacd_activ_periodo exige PersonaSacd (o PersonaEx).
 * Si el repositorio hidrata PersonaPub, instanceof falla y sale vacío.
 */
final class ComunicarActividadesSacdServiceTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_incluye_persona_sacd_aunque_no_tenga_actividades(): void
    {
        $service = $this->makeService();
        $service->setInicioIso('2026-01-01');
        $service->setFinIso('2026-12-31');
        $service->setPersonas([$this->personaSacd(4411)]);

        $out = $service->getArrayComunicacion();

        $this->assertArrayHasKey(4411, $out);
        $this->assertSame([], $out[4411]['actividades']);
    }

    public function test_descarta_persona_pub_hidratada_por_error(): void
    {
        $service = $this->makeService();
        $service->setInicioIso('2026-01-01');
        $service->setFinIso('2026-12-31');
        $service->setPersonas([$this->personaPub(4411)]);

        $this->assertSame([], $service->getArrayComunicacion());
    }

    public function test_cada_actividad_se_evalua_con_su_tipo_y_la_ocultada_deja_aviso(): void
    {
        $_SESSION['config'] = [
            'a_apps' => ['procesos' => 77],
            'app_installed' => [77],
        ];

        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->willReturnCallback(function (int $id): ActividadAll {
            $actividad = $this->createStub(ActividadAll::class);
            $actividad->method('getId_tipo_activ')->willReturn($id === 10 ? 111111 : 222222);
            $actividad->method('getDl_org')->willReturn('dlbv');
            $actividad->method('getNom_activ')->willReturn($id === 10 ? 'Mala' : 'Buena');

            return $actividad;
        });

        $vistos = [];
        $perm = $this->createMock(PermisosActividades::class);
        $perm->expects($this->exactly(2))
            ->method('setActividad')
            ->willReturnCallback(function (int $idActiv, string $tipo, string $dl) use (&$vistos): void {
                $vistos[] = [$idActiv, $tipo, $dl];
            });
        $perm->method('havePermisoSacd')->willReturn(false);
        $_SESSION['oPermActividades'] = $perm;

        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('getAsistenteCargoDeActividad')->willReturn([
            ['id_activ' => 10, 'propio' => false, 'id_cargo' => 1],
            ['id_activ' => 20, 'propio' => true, 'id_cargo' => null],
        ]);

        $service = $this->makeService($actividadRepo, $cargoRepo);
        $service->setInicioIso('2026-10-01');
        $service->setFinIso('2026-12-31');
        $service->setPersonas([$this->personaSacd(4411)]);

        $out = $service->getArrayComunicacion();

        $this->assertSame([[10, '111111', 'dlbv'], [20, '222222', 'dlbv']], $vistos);
        $this->assertSame([], $out[4411]['actividades']);
        $avisos = implode("\n", $service->getAvisos());
        $this->assertStringContainsString('Mala', $avisos);
        $this->assertStringContainsString('Buena', $avisos);
        $this->assertStringContainsString('id 10', $avisos);
        $this->assertStringContainsString('id 20', $avisos);
    }

    private function makeService(
        ?ActividadAllRepositoryInterface $actividadRepo = null,
        ?ActividadCargoRepositoryInterface $actividadCargoRepo = null,
    ): ComunicarActividadesSacdService {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([]);

        if ($actividadCargoRepo === null) {
            $actividadCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
            $actividadCargoRepo->method('getAsistenteCargoDeActividad')->willReturn([]);
        }

        $helper = new ActividadesSacdHelper(
            $this->createMock(ActividadSacdTextoRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
        );

        return new ComunicarActividadesSacdService(
            $cargoRepo,
            $actividadRepo ?? $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(CentroEncargadoRepositoryInterface::class),
            $actividadCargoRepo,
            $helper,
            $this->createMock(ConfigSchemaRepositoryInterface::class),
            $this->createMock(UsuarioRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(TelecoPersonaService::class),
            $this->createMock(ColaMailRepositoryInterface::class),
        );
    }

    private function personaSacd(int $idNom): PersonaSacd
    {
        $persona = new PersonaSacd();
        $persona->setId_schema(1);
        $persona->setId_nom($idNom);
        $persona->setIdTablaVo(new PersonaTablaCode('n'));
        $persona->setApellido1Vo(new PersonaApellido1Text('García'));
        $persona->setSituacionVo(new SituacionCode('A'));

        return $persona;
    }

    private function personaPub(int $idNom): PersonaPub
    {
        $persona = new PersonaPub();
        $persona->setId_schema(1);
        $persona->setId_nom($idNom);
        $persona->setIdTablaVo(new PersonaTablaCode('n'));
        $persona->setApellido1Vo(new PersonaApellido1Text('García'));
        $persona->setSituacionVo(new SituacionCode('A'));

        return $persona;
    }
}
