<?php

declare(strict_types=1);

namespace Tests\integration\personas\application;

use PHPUnit\Framework\Attributes\DataProvider;
use src\personas\application\PersonasSelectData;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Buscar personas por el nombre de su centro, con nombres de centro reales que
 * llevan apóstrofo, «+» o paréntesis: exacto y por el principio del nombre.
 */
final class PersonasSelectDataCentrosTest extends myTest
{
    public static function providerCentros(): \Generator
    {
        yield 'apóstrofo' => ["'"];
        yield 'más' => ['+'];
        yield 'paréntesis' => ['('];
    }

    #[DataProvider('providerCentros')]
    public function test_centro_con_caracteres_especiales(string $caracter): void
    {
        $stmt = GlobalPdo::get('oDB')->prepare('SELECT nombre_ubi FROM u_centros_dl WHERE strpos(nombre_ubi, :c) > 0 ORDER BY id_ubi LIMIT 1');
        $stmt->execute(['c' => $caracter]);
        $nombre = $stmt->fetchColumn();
        if ($nombre === false) {
            $this->markTestSkipped("Ningún centro con «{$caracter}»");
        }
        $uc = $GLOBALS['container']->get(PersonasSelectData::class);

        $exacto = $uc->execute(['tabla' => 'p_numerarios', 'centro' => $nombre, 'exacto' => '1', 'cmb' => '1']);
        $this->assertArrayNotHasKey('error', $exacto, "exacto «{$nombre}»");

        $prefijo = $uc->execute(['tabla' => 'p_numerarios', 'centro' => $nombre, 'cmb' => '1']);
        $this->assertArrayNotHasKey('error', $prefijo, "por el principio «{$nombre}»");

        $corto = mb_substr($nombre, 0, mb_strpos($nombre, $caracter) + 1);
        $this->assertArrayNotHasKey('error', $uc->execute(['tabla' => 'p_numerarios', 'centro' => $corto, 'cmb' => '1']), "por el principio «{$corto}»");
    }
}
