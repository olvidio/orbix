<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\BuscarPlanSacdData;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * Cubre la rama p-sacd sin ser jefe de zona: el desplegable debe incluir
 * únicamente al propio sacerdote.
 */
final class BuscarPlanSacdDataTest extends TestCase
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
            'idioma' => 'es',
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_p_sacd_sin_zonas_muestra_solo_su_nombre(): void
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(443);
        $oUsuario->setId_role(77);
        $oUsuario->setCsvIdPauVo('9988');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(443)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([77 => 'p-sacd']);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([]);

        $inicialesSacdService = $this->createMock(InicialesSacdService::class);
        $inicialesSacdService->method('obtenerNombreConIniciales')->with(9988)->willReturn('Garcia, Luis (GLG)');
        $inicialesSacdService->method('obtenerIniciales')->with(9988)->willReturn('GLG');

        $useCase = new BuscarPlanSacdData(
            $usuarioRepo,
            $roleRepo,
            $zonaRepo,
            $inicialesSacdService,
            $this->createStub(ZonaSacdRepositoryInterface::class),
            $this->createStub(PersonaSacdRepositoryInterface::class),
        );

        $out = $useCase->getData();

        $this->assertSame(['9988#GLG' => 'Garcia, Luis (GLG)'], $out['sacd_opciones']);
        $this->assertSame('9988#GLG', $out['sacd_selected']);
    }

    public function test_p_sacd_con_zonas_vacias_usa_fallback_con_su_id(): void
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(443);
        $oUsuario->setId_role(77);
        $oUsuario->setCsvIdPauVo('501');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([77 => 'p-sacd']);

        $zonaStub = $this->createStub(\src\zonassacd\domain\entity\Zona::class);
        $zonaStub->method('getId_zona')->willReturn(10);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([$zonaStub]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getIdSacdsDeZona')->with(10)->willReturn([]);

        $inicialesSacdService = $this->createMock(InicialesSacdService::class);
        $inicialesSacdService->method('obtenerNombreConIniciales')->with(501)->willReturn('Perez, Juan (PJ)');
        $inicialesSacdService->method('obtenerIniciales')->with(501)->willReturn('PJ');

        $useCase = new BuscarPlanSacdData(
            $usuarioRepo,
            $roleRepo,
            $zonaRepo,
            $inicialesSacdService,
            $zonaSacdRepo,
            $this->createStub(PersonaSacdRepositoryInterface::class),
        );

        $out = $useCase->getData();

        $this->assertSame(['501#PJ' => 'Perez, Juan (PJ)'], $out['sacd_opciones']);
    }
}
