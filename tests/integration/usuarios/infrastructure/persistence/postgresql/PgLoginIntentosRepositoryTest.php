<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\persistence\postgresql;

use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\usuarios\infrastructure\persistence\postgresql\PgLoginIntentosRepository;
use Tests\integration\usuarios\application\UsuarioDePruebaLogin;
use Tests\myTest;

/**
 * Contra el usuario de prueba de los tests de login (id 4990021 en `"H-dlbv".aux_usuarios`), que se
 * crea y se borra en cada test.
 */
final class PgLoginIntentosRepositoryTest extends myTest
{
    use UsuarioDePruebaLogin;

    private const SFSV = 1;

    private PgLoginIntentosRepository $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepararEscenarioLogin();
        $this->crearUsuario();
        $this->repo = new PgLoginIntentosRepository();
    }

    public function tearDown(): void
    {
        $this->restaurarEscenarioLogin();
        parent::tearDown();
    }

    private function fallo(): void
    {
        $this->repo->anotarFallo(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 3, 10);
    }

    public function test_al_llegar_al_maximo_bloquea_y_vuelve_a_contar_desde_cero(): void
    {
        $this->fallo();
        $this->fallo();
        $this->assertSame(2, $this->falloDeLogin());
        $this->assertFalse($this->repo->estaBloqueado(self::ESQUEMA, self::SFSV, self::ID_USUARIO));

        $this->fallo();

        $this->assertTrue($this->repo->estaBloqueado(self::ESQUEMA, self::SFSV, self::ID_USUARIO));
        $this->assertSame(0, $this->falloDeLogin());
        $this->assertGreaterThan(9 * 60, $this->segundosDeBloqueo());
        $this->assertLessThanOrEqual(10 * 60, $this->segundosDeBloqueo());
    }

    public function test_un_bloqueo_caducado_no_bloquea(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->fallo();
        }
        $this->caducarBloqueo();

        $this->assertFalse($this->repo->estaBloqueado(self::ESQUEMA, self::SFSV, self::ID_USUARIO));
    }

    public function test_un_acierto_quita_fallos_y_bloqueo(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->fallo();
        }

        $this->repo->anotarAcierto(self::ESQUEMA, self::SFSV, self::ID_USUARIO);

        $this->assertFalse($this->repo->estaBloqueado(self::ESQUEMA, self::SFSV, self::ID_USUARIO));
        $this->assertSame(0, $this->falloDeLogin());
        $this->assertSame(0, $this->segundosDeBloqueo());
    }

    public function test_un_paso_totp_solo_se_acepta_si_es_posterior_al_ultimo(): void
    {
        $this->assertTrue($this->repo->aceptarPasoTotp(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 1000));
        $this->assertFalse($this->repo->aceptarPasoTotp(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 1000), 'el mismo código otra vez');
        $this->assertFalse($this->repo->aceptarPasoTotp(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 999), 'un código anterior');
        $this->assertTrue($this->repo->aceptarPasoTotp(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 1001));
    }

    /** Un usuario que no existe no está bloqueado y escribir sus fallos no da error. */
    public function test_usuario_inexistente(): void
    {
        $this->repo->anotarFallo(self::ESQUEMA, self::SFSV, -4990021, 1, 10);

        $this->assertFalse($this->repo->estaBloqueado(self::ESQUEMA, self::SFSV, -4990021));
    }

    /**
     * Si no se puede abrir la conexión, o la tabla aún no tiene las columnas (código desplegado antes
     * que la migración), se deja entrar como antes en lugar de bloquear el login a todos.
     */
    public function test_sin_conexion_o_sin_columnas_deja_entrar(): void
    {
        $sinConexion = new PgLoginIntentosRepository(static fn (string $esquema, int $sfsv): ?PDO => null);
        $this->assertFalse($sinConexion->estaBloqueado(self::ESQUEMA, self::SFSV, self::ID_USUARIO));
        $this->assertTrue($sinConexion->aceptarPasoTotp(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 1));

        // La BD común no tiene `aux_usuarios`: la consulta falla igual que si faltaran las columnas.
        $comun = (new DBConnection((new ConfigDB('comun_select'))->getEsquema('H-dlb')))->getPDO();
        $sinColumnas = new PgLoginIntentosRepository(static fn (string $esquema, int $sfsv): ?PDO => $comun);
        $logPrevio = ini_set('error_log', '/dev/null');
        try {
            $sinColumnas->anotarFallo(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 1, 10);
            $this->assertFalse($sinColumnas->estaBloqueado(self::ESQUEMA, self::SFSV, self::ID_USUARIO));
            $this->assertTrue($sinColumnas->aceptarPasoTotp(self::ESQUEMA, self::SFSV, self::ID_USUARIO, 1));
        } finally {
            ini_set('error_log', $logPrevio === false ? '' : $logPrevio);
        }
    }
}
