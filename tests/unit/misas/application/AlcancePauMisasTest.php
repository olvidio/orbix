<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;
use src\misas\application\support\AlcancePauMisas;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\Zona;
use Tests\myTest;

/**
 * Qué sacds, centros y zonas del plan de misas puede pedir un usuario con un rol limitado a una persona o
 * a un centro (p-sacd, Centro sv/sf). Revisión de seguridad del 2026-11-18, punto 2 (comprobar el id):
 * las pantallas solo le ofrecían esos, pero cambiando el id del formulario veía el plan de cualquiera.
 * `null`: sin límite (usuarios de oficina y jefe de calendario).
 */
final class AlcancePauMisasTest extends myTest
{
    private const ROLES = [1 => 'p-sacd', 2 => 'Centro sv', 3 => 'admin', 4 => 'Centro sf'];
    private const YO = 10011;

    private UsuarioRepositoryInterface&MockObject $usuarios;
    private ZonaRepositoryInterface&MockObject $zonas;
    private mixed $sesionAnterior;
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $_SESSION['session_auth'] = ['id_usuario' => 55];
        unset($_SESSION['oConfig']);
        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->zonas = $this->createMock(ZonaRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->sesionAnterior;
        $_SESSION['oConfig'] = $this->configAnterior;
        parent::tearDown();
    }

    private function usuario(int $idRole, ?string $pau): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn($idRole);
        $u->method('getCsvIdPauAsString')->willReturn($pau);
        $this->usuarios->method('findById')->willReturn($u);
    }

    /** El sacd 10011 es jefe de la zona 3 (sacds 10011 y 20022; centros 301 y 302) y tiene un encargo en el centro 401. */
    private function alcance(bool $jefeZona = true): AlcancePauMisas
    {
        $roles = $this->createMock(RoleRepositoryInterface::class);
        $roles->method('getArrayRoles')->willReturn(self::ROLES);
        $zona = new Zona();
        $zona->setId_zona(3);
        $this->zonas->method('matching')->willReturn($jefeZona ? [$zona] : []);
        $this->zonas->method('getArrayZonas')->willReturn($jefeZona ? [3 => 'Zona 3'] : []);
        $zonaSacd = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacd->method('getIdSacdsDeZona')->willReturnMap([[3, [self::YO, 20022]]]);
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('idUbisDeZona')->willReturnMap([[3, [301, 302]]]);

        $encargoSacd = $this->createMock(EncargoSacd::class);
        $encargoSacd->method('getId_enc')->willReturn(70);
        $encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $encargosSacd->method('matching')->willReturn([$encargoSacd]);
        $encargo = $this->createMock(Encargo::class);
        $encargo->method('getId_tipo_enc')->willReturn(1100);
        $encargo->method('getId_ubi')->willReturn(401);
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('findById')->willReturnMap([[70, $encargo]]);

        return new AlcancePauMisas($this->usuarios, $roles, $this->zonas, $zonaSacd, $encargosSacd, $encargos, new CentrosDeZona($zonaCtr));
    }

    public function test_oficina_sin_limite(): void
    {
        $this->usuario(3, null);
        $a = $this->alcance();

        $this->assertNull($a->sacds());
        $this->assertNull($a->centros());
        $this->assertNull($a->zonas());
    }

    public function test_un_sacd_que_no_es_jefe_de_zona_solo_lo_suyo(): void
    {
        $this->usuario(1, (string) self::YO);
        $a = $this->alcance(false);

        $this->assertSame([self::YO], $a->sacds());
        $this->assertSame([401], $a->centros());
        $this->assertSame([], $a->zonas());
    }

    public function test_un_sacd_jefe_de_zona_tambien_los_de_su_zona(): void
    {
        $this->usuario(1, (string) self::YO);
        $a = $this->alcance();

        $this->assertSame([self::YO, 20022], $a->sacds());
        $this->assertSame([301, 302, 401], $a->centros());
        $this->assertSame([3], $a->zonas());
    }

    public function test_un_sacd_jefe_de_calendario_sin_limite(): void
    {
        $this->usuario(1, (string) self::YO);
        $_SESSION['oConfig'] = new class {
            public function is_jefeCalendario(): bool
            {
                return true;
            }
        };
        $a = $this->alcance();

        $this->assertNull($a->sacds());
        $this->assertNull($a->centros());
        $this->assertNull($a->zonas());
    }

    public function test_un_centro_solo_el_suyo(): void
    {
        foreach ([2, 4] as $idRole) {
            $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
            $this->usuario($idRole, '501');
            $a = $this->alcance();

            $this->assertSame([], $a->sacds());
            $this->assertSame([501], $a->centros());
            $this->assertSame([], $a->zonas());
        }
    }

    /** Un usuario sin persona o centro asociado no ve nada. */
    public function test_sin_pau_nada(): void
    {
        $this->usuario(1, null);
        $a = $this->alcance();

        $this->assertSame([], $a->sacds());
        $this->assertSame([], $a->centros());
        $this->assertSame([], $a->zonas());
    }
}
