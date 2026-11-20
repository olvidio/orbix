<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\ui\http;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\ui\http\SesionVigencia;

/**
 * Caducidad de la sesión (por inactividad y por tiempo total) y cierre cuando el usuario ya no existe o
 * le han cambiado la contraseña o el rol (revisión de seguridad del 2026-11-20, SES-04 y SES-06).
 */
final class SesionVigenciaTest extends TestCase
{
    private const AHORA = 1_800_000_000;

    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function test_una_sesion_sin_usuario_no_se_toca(): void
    {
        $_SESSION = ['otra' => 1];

        $this->assertTrue(SesionVigencia::alArrancar(self::AHORA));
        $this->assertSame(['otra' => 1], $_SESSION);
    }

    public function test_la_primera_peticion_con_usuario_anota_el_inicio_y_cada_peticion_la_actividad(): void
    {
        $_SESSION = ['session_auth' => ['id_usuario' => 7]];

        $this->assertTrue(SesionVigencia::alArrancar(self::AHORA));
        $this->assertSame(self::AHORA, $_SESSION[SesionVigencia::CLAVE]['inicio']);

        $this->assertTrue(SesionVigencia::alArrancar(self::AHORA + 600));
        $this->assertSame(self::AHORA, $_SESSION[SesionVigencia::CLAVE]['inicio']);
        $this->assertSame(self::AHORA + 600, $_SESSION[SesionVigencia::CLAVE]['actividad']);
    }

    public function test_caduca_por_inactividad_y_la_sesion_queda_vacia(): void
    {
        $_SESSION = ['session_auth' => ['id_usuario' => 7], 'iPermMenus' => 3];
        SesionVigencia::alArrancar(self::AHORA);

        $this->assertTrue(SesionVigencia::alArrancar(self::AHORA + SesionVigencia::inactividadMaxima() - 1));
        $despues = self::AHORA + SesionVigencia::inactividadMaxima() - 1 + SesionVigencia::inactividadMaxima() + 1;
        $this->assertFalse(SesionVigencia::alArrancar($despues));
        $this->assertSame([], $_SESSION);
    }

    public function test_caduca_por_tiempo_total_aunque_haya_actividad(): void
    {
        $_SESSION = ['session_auth' => ['id_usuario' => 7]];
        SesionVigencia::alArrancar(self::AHORA);
        $paso = (int) (SesionVigencia::inactividadMaxima() / 2);

        $t = self::AHORA;
        $viva = true;
        while ($viva && $t < self::AHORA + SesionVigencia::duracionMaxima() + 2 * $paso) {
            $t += $paso;
            $viva = SesionVigencia::alArrancar($t);
        }

        $this->assertFalse($viva);
        $this->assertGreaterThan(self::AHORA + SesionVigencia::duracionMaxima(), $t);
        $this->assertSame([], $_SESSION);
    }

    public function test_los_limites_salen_del_entorno_con_valores_por_defecto(): void
    {
        $this->assertSame(120 * 60, SesionVigencia::inactividadMaxima([]));
        $this->assertSame(12 * 3600, SesionVigencia::duracionMaxima([]));
        $this->assertSame(30 * 60, SesionVigencia::inactividadMaxima(['ORBIX_SESION_INACTIVIDAD_MIN' => '30']));
        $this->assertSame(8 * 3600, SesionVigencia::duracionMaxima(['ORBIX_SESION_MAXIMA_HORAS' => '8']));
        $this->assertSame(120 * 60, SesionVigencia::inactividadMaxima(['ORBIX_SESION_INACTIVIDAD_MIN' => 'x']));
    }

    public function test_la_revalidacion_cierra_si_el_usuario_no_existe_o_cambio_la_contrasena_o_el_rol(): void
    {
        $_SESSION = ['session_auth' => ['id_usuario' => 7]];
        SesionVigencia::alArrancar(self::AHORA);
        $huella = SesionVigencia::huella('hash-1', 3);

        // Primera vez: se anota la huella.
        $this->assertTrue(SesionVigencia::revalidar(static fn (int $id): ?string => $huella, self::AHORA));
        // Dentro del intervalo no se consulta.
        $this->assertTrue(SesionVigencia::revalidar(static fn (int $id): ?string => null, self::AHORA + 10));
        // Pasado el intervalo, misma huella: sigue.
        $t = self::AHORA + SesionVigencia::INTERVALO_REVALIDACION + 1;
        $this->assertTrue(SesionVigencia::revalidar(static fn (int $id): ?string => $huella, $t));
        // Cambio de contraseña o de rol: fuera.
        $t += SesionVigencia::INTERVALO_REVALIDACION + 1;
        $this->assertFalse(SesionVigencia::revalidar(static fn (int $id): ?string => SesionVigencia::huella('hash-2', 3), $t));
        $this->assertSame([], $_SESSION);

        $_SESSION = ['session_auth' => ['id_usuario' => 7]];
        SesionVigencia::alArrancar(self::AHORA);
        SesionVigencia::revalidar(static fn (int $id): ?string => $huella, self::AHORA);
        $this->assertFalse(SesionVigencia::revalidar(static fn (int $id): ?string => null, self::AHORA + SesionVigencia::INTERVALO_REVALIDACION + 1));
        $this->assertNotSame($huella, SesionVigencia::huella('hash-1', 4));
    }

    public function test_el_propio_usuario_puede_cambiar_su_contrasena_sin_perder_su_sesion(): void
    {
        $_SESSION = ['session_auth' => ['id_usuario' => 7]];
        SesionVigencia::alArrancar(self::AHORA);
        SesionVigencia::revalidar(static fn (int $id): ?string => SesionVigencia::huella('hash-1', 3), self::AHORA);

        SesionVigencia::olvidarHuella();

        $t = self::AHORA + SesionVigencia::INTERVALO_REVALIDACION + 1;
        $this->assertTrue(SesionVigencia::revalidar(static fn (int $id): ?string => SesionVigencia::huella('hash-2', 3), $t));
    }

    public function test_el_arranque_comprueba_la_vigencia_y_revalida_al_usuario(): void
    {
        $raiz = __DIR__ . '/../../../../../..';
        $this->assertStringContainsString('LoginBridge::comprobarVigenciaDeLaSesion();', (string) file_get_contents($raiz . '/frontend/usuarios/controller/login.php'));
        $this->assertStringContainsString('SesionVigencia::alArrancar(', (string) file_get_contents($raiz . '/frontend/shared/session/LoginBridge.php'));
        $this->assertStringContainsString('SesionVigencia::revalidarUsuarioDeLaSesion(', (string) file_get_contents($raiz . '/src/shared/global_object.inc'));
        $this->assertStringContainsString('SesionVigencia::revalidarUsuarioDeLaSesion(', (string) file_get_contents($raiz . '/frontend/shared/FrontBootstrap.php'));
    }
}
