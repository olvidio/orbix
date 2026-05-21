<?php

declare(strict_types=1);

namespace Tests\unit\devel\application;

use PHPUnit\Framework\TestCase;
use src\devel_db_admin\application\RenombrarEsquemaDefaultsCatalog;

final class RenombrarEsquemaDefaultsCatalogTest extends TestCase
{
    public function test_comun_defaults_califican_funciones_en_public(): void
    {
        $defaults = RenombrarEsquemaDefaultsCatalog::comun('V-crV', 'V', 'crV');

        $this->assertNotEmpty($defaults);
        foreach ($defaults as $row) {
            $valor = $row['valor'];
            if (str_contains($valor, 'idschema(')) {
                $this->assertStringStartsWith('public.idschema(', $valor, $row['tabla'] . '.' . $row['campo']);
            }
            if (str_contains($valor, 'bigglobal(')) {
                $this->assertStringStartsWith('public.bigglobal(', $valor, $row['tabla'] . '.' . $row['campo']);
            }
            if (str_contains($valor, 'idglobal(')) {
                $this->assertStringStartsWith('public.idglobal(', $valor, $row['tabla'] . '.' . $row['campo']);
            }
        }
    }
}
