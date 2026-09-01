<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\infrastructure;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivContexto;

final class CdCargosActivContextoTest extends TestCase
{
    public function test_tabla_sin_esquema_no_se_cualifica(): void
    {
        $contexto = new CdCargosActivContexto($this->pdoFalso(), '');

        $this->assertSame('cd_cargos_activ_dl', $contexto->tabla());
    }

    public function test_tabla_con_esquema_se_cualifica(): void
    {
        $contexto = new CdCargosActivContexto($this->pdoFalso(), 'H-dlb');

        $this->assertSame('"H-dlb".cd_cargos_activ_dl', $contexto->tabla());
    }

    public function test_tabla_escapa_comillas_del_esquema(): void
    {
        $contexto = new CdCargosActivContexto($this->pdoFalso(), 'H-dl"b');

        $this->assertSame('"H-dl""b".cd_cargos_activ_dl', $contexto->tabla());
    }

    /**
     * `tabla()` no usa la conexión: basta un PDO que nunca llega a conectar.
     */
    private function pdoFalso(): \PDO
    {
        return $this->createMock(\PDO::class);
    }
}
