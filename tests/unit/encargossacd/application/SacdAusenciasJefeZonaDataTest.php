<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\SacdAusenciasJefeZonaData;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\IdPau;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\Zona;

/**
 * Unitarios para {@see SacdAusenciasJefeZonaData::execute}.
 *
 * Esta clase se invoca via el controlador HTTP y obtiene sus dependencias
 * por `$GLOBALS['container']->get(...)`; el IDE/PHP no detectan typos en
 * los metodos del repositorio hasta que se ejecuta la rama concreta.
 *
 * Cubrimos:
 *  - Usuario inexistente -> a_sacd vacio.
 *  - Jefe con zonas: se recorren las zonas y se piden los sacds al repo.
 *    Este caso es el que reproduce el error de produccion
 *    "Call to undefined method PgZonaSacdRepository::getSacdsZona()" si
 *    en la aplicacion se llama al metodo con nombre equivocado.
 *  - Sin zonas pero con id_sacd propio -> se anyade el propio usuario.
 */
final class SacdAusenciasJefeZonaDataTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth']['id_usuario'] = 443;
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_usuario_inexistente_devuelve_lista_vacia(): void
    {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(443)->willReturn(null);

        $useCase = $this->makeUseCase([
            UsuarioRepositoryInterface::class => $usuarioRepo,
        ]);

        $this->assertSame(['a_sacd' => []], $useCase->execute());
    }

    public function test_jefe_con_zonas_pide_sacds_por_zona_al_repositorio(): void
    {
        // Este test exige que `ZonaSacdRepositoryInterface` reciba una llamada
        // al metodo real del contrato. Si la implementacion usa un nombre
        // inexistente (p. ej. `getSacdsZona` en vez de `getIdSacdsDeZona`),
        // estalla con `Call to undefined method`, reproduciendo el error
        // observado en produccion.
        $oUsuario = $this->miUsuario(id_role: 1, csv_id_pau: '1');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([1 => 'jefe_zona']);

        $oZona = $this->zona(10);
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([$oZona]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        // Afirmamos que se invoca el metodo real del contrato, no un typo.
        $zonaSacdRepo->expects($this->once())
            ->method('getIdSacdsDeZona')
            ->with(10)
            ->willReturn([501, 502]);

        $oPersona1 = $this->createStub(PersonaSacd::class);
        $oPersona1->method('getNombreApellidos')->willReturn('Perez, Juan');
        $oPersona2 = $this->createStub(PersonaSacd::class);
        $oPersona2->method('getNombreApellidos')->willReturn('Lopez, Ana');

        $personaRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaRepo->method('findById')->willReturnMap([
            [501, $oPersona1],
            [502, $oPersona2],
        ]);

        $oIniciales1 = $this->createStub(InicialesSacd::class);
        $oIniciales1->method('getIniciales')->willReturn('PJ');
        $oIniciales2 = $this->createStub(InicialesSacd::class);
        $oIniciales2->method('getIniciales')->willReturn('LA');

        $inicialesRepo = $this->createMock(InicialesSacdRepositoryInterface::class);
        $inicialesRepo->method('findById')->willReturnMap([
            [501, $oIniciales1],
            [502, $oIniciales2],
        ]);

        $useCase = $this->makeUseCase([
            UsuarioRepositoryInterface::class => $usuarioRepo,
            RoleRepositoryInterface::class => $roleRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            PersonaSacdRepositoryInterface::class => $personaRepo,
            InicialesSacdRepositoryInterface::class => $inicialesRepo,
        ]);

        $out = $useCase->execute();

        // `ksort` ordena: "LA#502" antes que "PJ#501".
        $this->assertSame([
            'LA#502' => 'Lopez, Ana',
            'PJ#501' => 'Perez, Juan',
        ], $out['a_sacd']);
    }

    public function test_sin_zonas_pero_con_id_sacd_propio_anyade_al_propio_usuario(): void
    {
        $oUsuario = $this->miUsuario(id_role: 1, csv_id_pau: '999');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([1 => 'otro_rol']);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getZonas')->willReturn([]);

        $oPersona = $this->createStub(PersonaSacd::class);
        $oPersona->method('getNombreApellidos')->willReturn('Ruiz, Maria');
        $personaRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaRepo->method('findById')->with('999')->willReturn($oPersona);

        $oIniciales = $this->createStub(InicialesSacd::class);
        $oIniciales->method('getIniciales')->willReturn('RM');
        $inicialesRepo = $this->createMock(InicialesSacdRepositoryInterface::class);
        $inicialesRepo->method('findById')->willReturn($oIniciales);

        $useCase = $this->makeUseCase([
            UsuarioRepositoryInterface::class => $usuarioRepo,
            RoleRepositoryInterface::class => $roleRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
            PersonaSacdRepositoryInterface::class => $personaRepo,
            InicialesSacdRepositoryInterface::class => $inicialesRepo,
        ]);

        $out = $useCase->execute();

        $this->assertSame(['RM#999' => 'Ruiz, Maria'], $out['a_sacd']);
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function miUsuario(int $id_role, string $csv_id_pau): Usuario
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_role($id_role);
        $oUsuario->setCsvIdPauVo(new IdPau($csv_id_pau));
        return $oUsuario;
    }

    private function zona(int $id_zona): Zona
    {
        $stub = $this->createStub(Zona::class);
        $stub->method('getId_zona')->willReturn($id_zona);
        return $stub;
    }

    /**
     * @param array<class-string, object> $overrides
     */
    private function makeUseCase(array $overrides = []): SacdAusenciasJefeZonaData
    {
        $services = array_merge([
            UsuarioRepositoryInterface::class => $this->createStub(UsuarioRepositoryInterface::class),
            RoleRepositoryInterface::class => $this->createStub(RoleRepositoryInterface::class),
            ZonaRepositoryInterface::class => $this->createStub(ZonaRepositoryInterface::class),
            ZonaSacdRepositoryInterface::class => $this->createStub(ZonaSacdRepositoryInterface::class),
            PersonaSacdRepositoryInterface::class => $this->createStub(PersonaSacdRepositoryInterface::class),
            InicialesSacdRepositoryInterface::class => $this->createStub(InicialesSacdRepositoryInterface::class),
        ], $overrides);

        return new SacdAusenciasJefeZonaData(
            $services[InicialesSacdRepositoryInterface::class],
            $services[PersonaSacdRepositoryInterface::class],
            $services[RoleRepositoryInterface::class],
            $services[UsuarioRepositoryInterface::class],
            $services[ZonaRepositoryInterface::class],
            $services[ZonaSacdRepositoryInterface::class],
        );
    }
}
