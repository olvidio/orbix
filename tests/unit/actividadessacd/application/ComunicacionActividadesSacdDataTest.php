<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\ComunicacionActividadesSacdData;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\personas\domain\services\TelecoPersonaService;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

/**
 * Unitarios de {@see ComunicacionActividadesSacdData::resolverContexto}
 * que necesitan mockear `UsuarioRepository` y `RoleRepository`. En
 * particular, cubren la rama `p-sacd` (usuario con ese rol se ve solo a
 * si mismo), que antes llamaba al metodo inexistente `Usuario::getCsv_id_pau()`
 * y fallaba en runtime.
 */
final class ComunicacionActividadesSacdDataTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 443,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
            'idioma' => 'ca',
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_resolver_contexto_rol_p_sacd_fuerza_un_sacd_con_id_pau(): void
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(443);
        $oUsuario->setId_role(77);
        $oUsuario->setCsvIdPauVo('9988');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(443)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([77 => 'p-sacd']);

        $ctx = $this->makeUseCase($usuarioRepo, $roleRepo)->resolverContexto([
            'que' => '',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2030',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('un_sacd', $ctx['que']);
        $this->assertSame(9988, $ctx['id_nom']);
        $this->assertSame('2030-01-01', $ctx['inicioIso']);
        $this->assertSame('2030-12-31', $ctx['finIso']);
    }

    public function test_resolver_contexto_rol_distinto_de_p_sacd_no_fuerza_un_sacd(): void
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(443);
        $oUsuario->setId_role(1);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(443)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([1 => 'admin', 77 => 'p-sacd']);

        $ctx = $this->makeUseCase($usuarioRepo, $roleRepo)->resolverContexto([
            'que' => 'nagd',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('nagd', $ctx['que']);
        $this->assertSame(0, $ctx['id_nom']);
    }

    public function test_resolver_contexto_usuario_no_encontrado_usa_que_input(): void
    {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn(null);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->expects($this->never())->method('getArrayRoles');

        $ctx = $this->makeUseCase($usuarioRepo, $roleRepo)->resolverContexto([
            'que' => 'sssc',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('sssc', $ctx['que']);
    }

    public function test_resolver_contexto_respeta_trimestre_seleccionado(): void
    {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn(null);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);

        $ctx = $this->makeUseCase($usuarioRepo, $roleRepo)->resolverContexto([
            'que' => 'nagd',
            'periodo' => 'trimestre_2',
            'year' => '2026',
        ]);

        $this->assertSame('2026-04-01', $ctx['inicioIso']);
        $this->assertSame('2026-06-30', $ctx['finIso']);
    }

    public function test_execute_nagd_incluye_sacd_sin_actividades_en_el_periodo(): void
    {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn(null);

        $oPersona = new PersonaSacd();
        $oPersona->setId_schema(1);
        $oPersona->setId_nom(4411);
        $oPersona->setIdTablaVo(new PersonaTablaCode('n'));
        $oPersona->setApellido1Vo(new PersonaApellido1Text('García'));
        $oPersona->setSituacionVo(new SituacionCode('A'));

        $personaSacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaSacdRepo->method('getPersonas')->willReturn([$oPersona]);

        $personaExRepo = $this->createMock(PersonaExRepositoryInterface::class);
        $personaExRepo->method('getPersonas')->willReturn([]);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('getCentros')->willReturn([]);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([]);

        $actividadCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $actividadCargoRepo->method('getAsistenteCargoDeActividad')->willReturn([]);

        $helper = new ActividadesSacdHelper(
            $this->createMock(ActividadSacdTextoRepositoryInterface::class),
            $centroDlRepo,
        );
        $service = new ComunicarActividadesSacdService(
            $cargoRepo,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(CentroEncargadoRepositoryInterface::class),
            $actividadCargoRepo,
            $helper,
            $this->createMock(ConfigSchemaRepositoryInterface::class),
            $usuarioRepo,
            $this->createMock(PersonaDlRepositoryInterface::class),
            $centroDlRepo,
            $this->createMock(TelecoPersonaService::class),
            $this->createMock(ColaMailRepositoryInterface::class),
        );

        $out = (new ComunicacionActividadesSacdData(
            $usuarioRepo,
            $this->createMock(RoleRepositoryInterface::class),
            $personaSacdRepo,
            $personaExRepo,
            $service,
            $helper,
        ))->execute([
            'que' => 'nagd',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2026',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertCount(1, $out['sacds']);
        $this->assertSame(4411, $out['sacds'][0]['id_nom']);
        $this->assertSame([], $out['sacds_paso']);
    }

    private function makeUseCase(
        UsuarioRepositoryInterface $usuarioRepo,
        RoleRepositoryInterface $roleRepo,
    ): ComunicacionActividadesSacdData {
        $helper = new ActividadesSacdHelper(
            $this->createMock(ActividadSacdTextoRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
        );
        $service = new ComunicarActividadesSacdService(
            $this->createMock(CargoRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(CentroEncargadoRepositoryInterface::class),
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $helper,
            $this->createMock(ConfigSchemaRepositoryInterface::class),
            $usuarioRepo,
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(TelecoPersonaService::class),
            $this->createMock(ColaMailRepositoryInterface::class),
        );

        return new ComunicacionActividadesSacdData(
            $usuarioRepo,
            $roleRepo,
            $this->createMock(PersonaSacdRepositoryInterface::class),
            $this->createMock(PersonaExRepositoryInterface::class),
            $service,
            $helper,
        );
    }
}
