<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application\services;

use PHPUnit\Framework\TestCase;
use src\ubis\application\services\CentrosDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class CentrosDropdownTest extends TestCase
{
    /** @return array{0: CentrosDropdown, 1: \ArrayObject<int, string>} */
    private function dropdown(): array
    {
        $condiciones = new \ArrayObject();
        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->method('getArrayCentros')->willReturnCallback(
            static function (string $condicion) use ($condiciones): array {
                $condiciones->append($condicion);
                return [10 => 'Uno'];
            }
        );

        return [new CentrosDropdown($repo), $condiciones];
    }

    public function test_sin_filtro_solo_activos(): void
    {
        [$dropdown, $condiciones] = $this->dropdown();

        $this->assertSame([10 => 'Uno'], $dropdown->opciones());
        $this->assertSame("WHERE active = 't'", $condiciones[0]);
    }

    public function test_id_ubi_in_limita_a_esos_ids(): void
    {
        [$dropdown, $condiciones] = $this->dropdown();

        $dropdown->opciones(['id_ubi_in' => [1001, '1002', 0, -5, 'x']]);

        $this->assertSame("WHERE active = 't' AND id_ubi IN (1001,1002)", $condiciones[0]);
    }

    /** Usuario acotado a ubis (casa/centro en DMZ) sin ids válidos: no debe ver todo. */
    public function test_id_ubi_in_vacio_no_devuelve_nada(): void
    {
        [$dropdown, $condiciones] = $this->dropdown();

        $dropdown->opciones(['id_ubi_in' => []]);

        $this->assertSame("WHERE active = 't' AND FALSE", $condiciones[0]);
    }

    public function test_id_ubi_in_solo_con_ids_invalidos_no_devuelve_nada(): void
    {
        [$dropdown, $condiciones] = $this->dropdown();

        $dropdown->opciones(['id_ubi_in' => [-1, 0]]);

        $this->assertSame("WHERE active = 't' AND FALSE", $condiciones[0]);
    }
}
