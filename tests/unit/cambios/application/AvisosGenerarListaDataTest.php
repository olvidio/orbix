<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\AvisosGenerarListaData;
use src\cambios\application\CambioAvisoTxtBuilder;
use src\cambios\application\CambioParaAvisoLookup;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\application\PersonaNombreParaAvisoInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

final class AvisosGenerarListaDataTest extends TestCase
{
    private function createUseCase(
        UsuarioRepositoryInterface $usuarioRepository,
        PreferenciaRepositoryInterface $preferenciaRepository,
        CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
    ): AvisosGenerarListaData {
        $cambioParaAvisoLookup = new CambioParaAvisoLookup(
            $this->createMock(CambioRepositoryInterface::class),
            $this->createMock(CambioDlRepositoryInterface::class),
        );
        $cambioAvisoTxtBuilder = new CambioAvisoTxtBuilder(
            new ActividadParaAvisoLookup(
                $this->createMock(ActividadAllRepositoryInterface::class),
                $this->createMock(ActividadExRepositoryInterface::class),
            ),
            $this->createMock(CambioRepositoryInterface::class),
            $this->createMock(PersonaNombreParaAvisoInterface::class),
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $this->createMock(RepeticionRepositoryInterface::class),
            $this->createMock(ActividadFaseRepositoryInterface::class),
        );

        return new AvisosGenerarListaData(
            $usuarioRepository,
            $preferenciaRepository,
            $cambioUsuarioRepository,
            $cambioParaAvisoLookup,
            $cambioAvisoTxtBuilder,
        );
    }

    public function test_sin_id_usuario_no_emite_ctx(): void
    {
        $useCase = $this->createUseCase(
            $this->createMock(UsuarioRepositoryInterface::class),
            $this->createMock(PreferenciaRepositoryInterface::class),
            $this->createMock(CambioUsuarioRepositoryInterface::class),
        );

        $result = $useCase->execute(['is_admin' => true, 'id_usuario' => 0]);

        $this->assertArrayNotHasKey('ctx_eliminar_fecha', $result);
        $this->assertSame('', $result['url_eliminar'] ?? '');
    }

    public function test_con_id_usuario_emite_ctx_eliminar_fecha_accion_only(): void
    {
        $usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepository->method('getArrayUsuarios')->willReturn([]);

        $preferenciaRepository = $this->createMock(PreferenciaRepositoryInterface::class);
        $preferenciaRepository->method('findById')->willReturn(null);

        $cambioUsuarioRepository = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $cambioUsuarioRepository->method('getCambiosUsuario')->willReturn([]);

        $useCase = $this->createUseCase($usuarioRepository, $preferenciaRepository, $cambioUsuarioRepository);

        $result = $useCase->execute(['is_admin' => true, 'id_usuario' => 7, 'aviso_tipo' => 1]);

        $this->assertSame([], $result['a_valores']);
        $this->assertSame([], HashB::open($result['ctx_eliminar_fecha'], 'cambio_usuario_eliminar_hasta_fecha'));
        $this->assertSame('f_fin!ctx_eliminar_fecha', $result['hash_eliminar_fecha']['campos_form']);
    }
}
