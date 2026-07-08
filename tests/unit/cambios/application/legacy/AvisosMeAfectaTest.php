<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application\legacy;

use DI\Container;
use DI\ContainerBuilder;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\legacy\Avisos;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\PauType;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\Zona;
use Tests\myTest;
use function DI\factory;

/**
 * Unitarios de {@see Avisos::me_afecta()} por rol y tipo de objeto.
 */
final class AvisosMeAfectaTest extends myTest
{
    private const ID_USUARIO = 443;
    private const ID_ACTIV = 1001;
    private const ID_UBI_MIA = 50;
    private const ID_UBI_OTRA = 99;
    private const ID_NOM_SACD = 501;
    private const ID_NOM_OTRO = 777;

    private const ROLE_CDC = 10;
    private const ROLE_SACD = 77;
    private const ROLE_NORMAL = 1;

    private mixed $previousContainer;

    public function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    public function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_usuario_inexistente_siempre_afecta(): void
    {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(self::ID_USUARIO)->willReturn(null);

        $avisos = $this->buildAvisos(usuarioRepo: $usuarioRepo);

        $this->assertTrue($this->meAfecta($avisos, 'Actividad', 'nom_activ'));
    }

    public function test_usuario_sin_rol_pau_ni_id_pau_siempre_afecta(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_NORMAL, ''),
            rolesPau: [],
        );

        $this->assertTrue($this->meAfecta($avisos, 'Actividad', 'nom_activ', idPau: null));
        $this->assertTrue($this->meAfecta($avisos, 'Asistente', 'id_nom', idPau: null));
    }

    public function test_usuario_normal_con_id_pau_en_ubi_de_actividad(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_NORMAL, ''),
            actividadUbi: self::ID_UBI_MIA,
            rolesPau: [],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'Actividad',
            'nom_activ',
            idPau: (string) self::ID_UBI_MIA,
        ));
    }

    public function test_usuario_normal_con_id_pau_fuera_de_ubi(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_NORMAL, ''),
            actividadUbi: self::ID_UBI_OTRA,
            rolesPau: [],
        );

        $this->assertFalse($this->meAfecta(
            $avisos,
            'Actividad',
            'nom_activ',
            idPau: (string) self::ID_UBI_MIA,
        ));
    }

    public function test_usuario_normal_cambio_id_ubi_dentro_de_preferencia(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_NORMAL, ''),
            actividadUbi: self::ID_UBI_OTRA,
            rolesPau: [],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'Actividad',
            'id_ubi',
            valorOld: (string) self::ID_UBI_MIA,
            valorNew: (string) self::ID_UBI_OTRA,
            idPau: (string) self::ID_UBI_MIA,
        ));
    }

    public function test_actividad_inexistente_con_id_pau_devuelve_false(): void
    {
        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->with(self::ID_ACTIV)->willReturn(null);

        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_NORMAL, ''),
            actividadRepo: $actividadRepo,
            rolesPau: [],
        );

        $this->assertFalse($this->meAfecta(
            $avisos,
            'Actividad',
            'nom_activ',
            idPau: (string) self::ID_UBI_MIA,
        ));
    }

    public function test_casa_actividad_en_su_ubi_afecta(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_CDC, (string) self::ID_UBI_MIA),
            actividadUbi: self::ID_UBI_MIA,
            rolesPau: [self::ROLE_CDC => PauType::PAU_CDC],
        );

        $this->assertTrue($this->meAfecta($avisos, 'Actividad', 'nom_activ'));
    }

    public function test_casa_actividad_fuera_de_su_ubi_no_afecta(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_CDC, (string) self::ID_UBI_MIA),
            actividadUbi: self::ID_UBI_OTRA,
            rolesPau: [self::ROLE_CDC => PauType::PAU_CDC],
        );

        $this->assertFalse($this->meAfecta($avisos, 'Actividad', 'nom_activ'));
    }

    public function test_casa_cambio_id_ubi_con_valor_en_sus_ubis(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_CDC, (string) self::ID_UBI_MIA),
            actividadUbi: self::ID_UBI_OTRA,
            rolesPau: [self::ROLE_CDC => PauType::PAU_CDC],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'Actividad',
            'id_ubi',
            valorOld: (string) self::ID_UBI_MIA,
            valorNew: (string) self::ID_UBI_OTRA,
        ));
    }

    /**
     * @dataProvider objetosCargoProvider
     */
    public function test_casa_cargo_observ_no_afecta(string $objeto): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_CDC, (string) self::ID_UBI_MIA),
            actividadUbi: self::ID_UBI_MIA,
            rolesPau: [self::ROLE_CDC => PauType::PAU_CDC],
        );

        $this->assertFalse($this->meAfecta($avisos, $objeto, 'observ'));
    }

    /**
     * @dataProvider objetosCargoProvider
     */
    public function test_casa_cargo_otra_propiedad_afecta(string $objeto): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_CDC, (string) self::ID_UBI_MIA),
            actividadUbi: self::ID_UBI_MIA,
            rolesPau: [self::ROLE_CDC => PauType::PAU_CDC],
        );

        $this->assertTrue($this->meAfecta($avisos, $objeto, 'id_nom'));
    }

    public function test_sacd_asistente_id_nom_coincide_con_permiso(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_SACD),
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'Asistente',
            'id_nom',
            valorOld: self::ID_NOM_SACD,
            valorNew: self::ID_NOM_OTRO,
        ));
    }

    public function test_sacd_asistente_id_nom_no_coincide(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_SACD),
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertFalse($this->meAfecta(
            $avisos,
            'Asistente',
            'id_nom',
            valorOld: self::ID_NOM_OTRO,
            valorNew: 888,
        ));
    }

    public function test_sacd_actividad_cargo_sacd_id_nom_coincide(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_SACD),
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'ActividadCargoSacd',
            'id_nom',
            valorOld: self::ID_NOM_OTRO,
            valorNew: self::ID_NOM_SACD,
        ));
    }

    public function test_sacd_actividad_cargo_no_sacd_id_nom_coincide(): void
    {
        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_SACD),
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'ActividadCargoNoSacd',
            'id_nom',
            valorOld: self::ID_NOM_SACD,
            valorNew: self::ID_NOM_OTRO,
        ));
    }

    public function test_sacd_actividad_con_cargo_y_permiso_ver(): void
    {
        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('getAsistenteCargoDeActividad')->willReturn([
            self::ID_ACTIV => [
                'propio' => 'f',
                'id_cargo' => 12,
            ],
        ]);

        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_SACD),
            cargoRepo: $cargoRepo,
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertTrue($this->meAfecta($avisos, 'Actividad', 'nom_activ'));
    }

    public function test_sacd_actividad_sin_cargo_no_afecta(): void
    {
        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('getAsistenteCargoDeActividad')->willReturn([]);

        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_SACD),
            cargoRepo: $cargoRepo,
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertFalse($this->meAfecta($avisos, 'Actividad', 'nom_activ'));
    }

    public function test_sacd_jefe_zona_afecta_si_algún_sacd_de_zona_tiene_permiso(): void
    {
        $oZona = $this->createStub(Zona::class);
        $oZona->method('getId_zona')->willReturn(10);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([$oZona]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getIdSacdsDeZona')->with(10)->willReturn([
            self::ID_NOM_OTRO,
            self::ID_NOM_SACD,
        ]);

        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_OTRO),
            zonaRepo: $zonaRepo,
            zonaSacdRepo: $zonaSacdRepo,
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertTrue($this->meAfecta(
            $avisos,
            'Asistente',
            'id_nom',
            valorOld: self::ID_NOM_SACD,
            valorNew: self::ID_NOM_OTRO,
        ));
    }

    public function test_sacd_jefe_zona_no_afecta_si_ningun_sacd_de_zona_tiene_permiso(): void
    {
        $oZona = $this->createStub(Zona::class);
        $oZona->method('getId_zona')->willReturn(10);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([$oZona]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getIdSacdsDeZona')->with(10)->willReturn([
            self::ID_NOM_OTRO,
            888,
        ]);

        $avisos = $this->buildAvisos(
            usuario: $this->usuario(self::ROLE_SACD, (string) self::ID_NOM_OTRO),
            zonaRepo: $zonaRepo,
            zonaSacdRepo: $zonaSacdRepo,
            rolesPau: [self::ROLE_SACD => PauType::PAU_SACD],
        );

        $this->assertFalse($this->meAfecta(
            $avisos,
            'Asistente',
            'id_nom',
            valorOld: self::ID_NOM_SACD,
            valorNew: 999,
        ));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function objetosCargoProvider(): array
    {
        return [
            'ActividadCargoSacd' => ['ActividadCargoSacd'],
            'ActividadCargoNoSacd' => ['ActividadCargoNoSacd'],
        ];
    }

    private function meAfecta(
        Avisos $avisos,
        string $objeto,
        string $propiedad,
        int $idActiv = self::ID_ACTIV,
        mixed $valorOld = null,
        mixed $valorNew = null,
        ?string $idPau = null,
    ): bool {
        $avisos->setId_usuario(self::ID_USUARIO);
        $avisos->setObjeto($objeto);

        return $avisos->me_afecta(
            $propiedad,
            $idActiv,
            $valorOld,
            $valorNew,
            $idPau,
            $objeto,
        );
    }

    private function buildAvisos(
        ?Usuario $usuario = null,
        ?int $actividadUbi = self::ID_UBI_MIA,
        ?UsuarioRepositoryInterface $usuarioRepo = null,
        ?ActividadAllRepositoryInterface $actividadRepo = null,
        ?ZonaRepositoryInterface $zonaRepo = null,
        ?ZonaSacdRepositoryInterface $zonaSacdRepo = null,
        ?ActividadCargoRepositoryInterface $cargoRepo = null,
        array $rolesPau = [self::ROLE_CDC => PauType::PAU_CDC, self::ROLE_SACD => PauType::PAU_SACD],
    ): Avisos {
        $usuario ??= $this->usuario(self::ROLE_NORMAL, '');

        if ($usuarioRepo === null) {
            $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
            $usuarioRepo->method('findById')->with(self::ID_USUARIO)->willReturn($usuario);
        }

        if ($actividadRepo === null) {
            $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
            if ($actividadUbi !== null) {
                $oActividad = $this->createStub(ActividadAll::class);
                $oActividad->method('getId_ubi')->willReturn($actividadUbi);
                $actividadRepo->method('findById')->with(self::ID_ACTIV)->willReturn($oActividad);
            }
        }

        $zonaRepo ??= $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([]);

        $zonaSacdRepo ??= $this->createMock(ZonaSacdRepositoryInterface::class);
        $cargoRepo ??= $this->createMock(ActividadCargoRepositoryInterface::class);

        $this->installContainer($rolesPau);

        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepository->method('findById')->willReturn(null);

        return new Avisos(
            $this->createMock(CambioUsuarioRepositoryInterface::class),
            $this->createMock(CambioAnotadoRepositoryInterface::class),
            $usuarioRepo,
            $actividadRepo,
            new ActividadParaAvisoLookup($actividadRepo, $exRepository),
            $zonaRepo,
            $zonaSacdRepo,
            $cargoRepo,
        );
    }

    private function usuario(int $idRole, string $csvIdPau): Usuario
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(self::ID_USUARIO);
        $oUsuario->setId_role($idRole);
        if ($csvIdPau !== '') {
            $oUsuario->setCsvIdPauVo(new IdPau($csvIdPau));
        }

        return $oUsuario;
    }

    /**
     * @param array<int|string, string> $rolesPau
     */
    private function installContainer(array $rolesPau): void
    {
        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRolesPau')->willReturn($rolesPau);

        $GLOBALS['container'] = $this->containerFromMap([
            RoleRepositoryInterface::class => $roleRepo,
            PermisosActividades::class => factory(
                static fn (int $idUsuario): PermisosActividades => new PermisosActividadesTrue($idUsuario),
            ),
        ]);
    }

    /**
     * @param array<class-string, mixed> $definitions
     */
    private function containerFromMap(array $definitions): Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($definitions);

        return $builder->build();
    }
}
