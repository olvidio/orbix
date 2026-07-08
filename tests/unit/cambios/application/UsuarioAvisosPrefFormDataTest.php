<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\application\UsuarioAvisosPrefFormData;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

final class UsuarioAvisosPrefFormDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $configBackup = [];

    /** @var mixed */
    private $containerBackup = null;

    protected function setUp(): void
    {
        $this->configBackup = $_SESSION['config'] ?? [];
        $this->containerBackup = $GLOBALS['container'] ?? null;

        $_SESSION['session_auth'] = [
            'id_usuario' => 443,
            'sfsv' => 1,
            'esquema' => 'Cong-crCongv',
            'id_role' => 1,
        ];
        $_SESSION['config'] = [
            'a_apps' => [],
            'app_installed' => [],
        ];

        $roleRepository = $this->createMock(RoleRepositoryInterface::class);
        $roleRepository->method('getArrayRolesPau')->willReturn([]);
        $roleRepository->method('getArrayRoles')->willReturn([]);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            RoleRepositoryInterface::class => static fn (): RoleRepositoryInterface => $roleRepository,
        ]);
        $GLOBALS['container'] = $builder->build();
    }

    protected function tearDown(): void
    {
        if ($this->configBackup !== []) {
            $_SESSION['config'] = $this->configBackup;
        }
        if ($this->containerBackup !== null) {
            $GLOBALS['container'] = $this->containerBackup;
        } else {
            unset($GLOBALS['container']);
        }
    }

    /**
     * @param array{procesosInstalados?: bool} $options
     */
    private function configureApps(array $options = []): void
    {
        if (!empty($options['procesosInstalados'])) {
            $_SESSION['config']['a_apps']['procesos'] = 99003;
            $_SESSION['config']['app_installed'] = [99003];
        } else {
            $_SESSION['config']['a_apps'] = [];
            $_SESSION['config']['app_installed'] = [];
        }
    }

    /**
     * @param array{procesosInstalados?: bool, fases?: array<int|string, string>} $options
     */
    private function createUseCase(array $options = []): UsuarioAvisosPrefFormData
    {
        $this->configureApps($options);

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getUsuarioAsString')->willReturn('usuario test');
        $usuario->method('getId_role')->willReturn(1);

        $usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepository->method('findById')->with(443)->willReturn($usuario);

        $tipoDeActividadRepository = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipoDeActividadRepository->method('getTiposDeProcesos')->willReturn(['tipo-a']);

        $actividadFaseRepository = $this->createMock(ActividadFaseRepositoryInterface::class);
        $actividadFaseRepository->method('getArrayActividadFases')
            ->willReturn($options['fases'] ?? [10 => 'fase inicial', 20 => 'fase final']);

        $casaDlRepository = $this->createMock(CasaDlRepositoryInterface::class);
        $casaDlRepository->method('getArrayCasas')->willReturn([1 => 'casa 1']);

        return new UsuarioAvisosPrefFormData(
            $usuarioRepository,
            $this->createMock(GrupoRepositoryInterface::class),
            $tipoDeActividadRepository,
            $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class),
            $actividadFaseRepository,
            $casaDlRepository,
        );
    }

    public function test_sin_procesos_expone_estados_y_textos_de_estado(): void
    {
        $result = $this->createUseCase()->execute([
            'id_usuario' => 443,
            'salida' => 'nuevo',
        ]);

        $this->assertFalse($result['fases_usa_procesos']);
        $this->assertSame((string)_("estado de la actividad de referencia"), $result['label_fase_ref']);
        $this->assertSame(
            (string)_("debe indicar el estado de la actividad de referencia"),
            $result['mensaje_error_fase_ref']
        );
        $this->assertSame(
            (string)_("avisar si la actividad NO está en este estado"),
            $result['texto_aviso_off']
        );
        $this->assertSame(
            (string)_("avisar si la actividad está en este estado"),
            $result['texto_aviso_on']
        );

        $expected = array_diff_key(
            StatusId::getArrayStatus(),
            [StatusId::ALL => true]
        );
        $this->assertSame($expected, $result['aFases']);
        foreach ($expected as $id => $label) {
            $this->assertIsInt($id);
            $this->assertIsString($label);
            $this->assertNotSame((string) $id, $label);
        }
    }

    public function test_con_procesos_expone_fases_y_textos_de_fase(): void
    {
        $fases = [5 => 'planificada', 8 => 'cerrada'];
        $result = $this->createUseCase([
            'procesosInstalados' => true,
            'fases' => $fases,
        ])->execute([
            'id_usuario' => 443,
            'salida' => 'nuevo',
        ]);

        $this->assertTrue($result['fases_usa_procesos']);
        $this->assertSame((string)_("fase de referencia"), $result['label_fase_ref']);
        $this->assertSame((string)_("debe indicar la fase de referencia"), $result['mensaje_error_fase_ref']);
        $this->assertSame((string)_("avisar antes de que esté marcada (off)"), $result['texto_aviso_off']);
        $this->assertSame((string)_("avisar si está marcada (on)"), $result['texto_aviso_on']);
        $this->assertSame($fases, $result['aFases']);
    }
}
