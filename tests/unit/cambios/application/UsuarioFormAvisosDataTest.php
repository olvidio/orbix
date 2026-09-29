<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\cambios\application\UsuarioFormAvisosData;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

final class UsuarioFormAvisosDataTest extends TestCase
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
            'esquema' => 'H-dlbv',
            'id_role' => 1,
        ];
        $_SESSION['config'] = [
            'a_apps' => ['cambios' => 99010],
            'app_installed' => [99010],
        ];

        $tipoRepo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipoRepo->method('getNom_tipoPosibles')->willReturn([
            'tipo_nom' => [],
            'nom_tipo' => [],
        ]);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            TipoDeActividadRepositoryInterface::class => static fn (): TipoDeActividadRepositoryInterface => $tipoRepo,
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

    public function test_fila_emite_ctx_eliminar_atado_al_item(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getUsuarioAsString')->willReturn('usuario test');

        $usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepository->method('findById')->with(443)->willReturn($usuario);

        $pref = $this->createMock(CambioUsuarioObjetoPref::class);
        $pref->method('getId_item_usuario_objeto')->willReturn(77);
        $pref->method('getId_tipo_activ_txt')->willReturn('111000');
        $pref->method('getDl_org')->willReturn('dlb');
        $pref->method('getObjeto')->willReturn('Actividad');
        $pref->method('getAviso_tipo')->willReturn(1);
        $pref->method('getId_fase_ref')->willReturn(10);
        $pref->method('isAviso_off')->willReturn(false);
        $pref->method('isAviso_on')->willReturn(true);
        $pref->method('isAviso_outdate')->willReturn(false);

        $objetoRepo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $objetoRepo->method('getCambioUsuarioObjetoPrefs')->willReturn([$pref]);

        $propiedadRepo = $this->createMock(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $propiedadRepo->method('getCambioUsuarioPropiedadPrefs')->willReturn([]);

        $useCase = new UsuarioFormAvisosData(
            $usuarioRepository,
            $propiedadRepo,
            $objetoRepo,
            $this->createMock(ActividadFaseRepositoryInterface::class),
        );

        $result = $useCase->execute([
            'id_usuario' => 443,
            'quien' => 'usuario',
        ]);

        $this->assertSame('', $result['error']);
        $this->assertCount(1, $result['a_valores']);
        $row = $result['a_valores'][1];
        $this->assertSame('443#77', $row['sel']);
        $this->assertSame(
            ['id_item_usuario_objeto' => 77],
            HashB::open($row['ctx_eliminar'], 'cambio_usuario_objeto_pref_eliminar')
        );
    }
}
