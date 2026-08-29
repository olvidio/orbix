<?php

declare(strict_types=1);

namespace Tests\unit\personas\infrastructure;

use PHPUnit\Framework\TestCase;
use src\personas\infrastructure\persistence\postgresql\CpSacdContexto;

final class CpSacdContextoTest extends TestCase
{
    // --- dlDeEsquema() ---------------------------------------------------

    public function test_dl_de_esquema_esquema_simple(): void
    {
        $this->assertSame('dlb', CpSacdContexto::dlDeEsquema('H-dlb'));
    }

    public function test_dl_de_esquema_quita_sufijo_v(): void
    {
        $this->assertSame('dlb', CpSacdContexto::dlDeEsquema('H-dlbv'));
    }

    public function test_dl_de_esquema_quita_sufijo_f(): void
    {
        $this->assertSame('dlb', CpSacdContexto::dlDeEsquema('H-dlbf'));
    }

    public function test_dl_de_esquema_sin_sufijo_v_ni_f(): void
    {
        $this->assertSame('crCong', CpSacdContexto::dlDeEsquema('Cong-crCong'));
    }

    public function test_dl_de_esquema_sin_guion_da_vacio(): void
    {
        $this->assertSame('', CpSacdContexto::dlDeEsquema('Nada'));
    }

    public function test_dl_de_esquema_cr_se_completa_con_la_region(): void
    {
        $this->assertSame('crH', CpSacdContexto::dlDeEsquema('H-cr'));
    }

    // --- tabla() ---------------------------------------------------

    public function test_tabla_sin_esquema_no_se_cualifica(): void
    {
        $contexto = new CpSacdContexto($this->pdoFalso(), '', 'dlb');

        $this->assertSame('cp_sacd', $contexto->tabla());
    }

    public function test_tabla_con_esquema_se_cualifica(): void
    {
        $contexto = new CpSacdContexto($this->pdoFalso(), 'H-dlb', 'dlb');

        $this->assertSame('"H-dlb".cp_sacd', $contexto->tabla());
    }

    public function test_tabla_escapa_comillas_del_esquema(): void
    {
        $contexto = new CpSacdContexto($this->pdoFalso(), 'H-dl"b', 'dlb');

        $this->assertSame('"H-dl""b".cp_sacd', $contexto->tabla());
    }

    /**
     * `tabla()` y `dlDeEsquema()` no usan la conexión: basta un PDO que nunca
     * llega a conectar. `pdo_sqlite` no está disponible en este entorno, así que
     * se usa un mock (createMock no invoca el constructor real de PDO).
     */
    private function pdoFalso(): \PDO
    {
        return $this->createMock(\PDO::class);
    }
}
