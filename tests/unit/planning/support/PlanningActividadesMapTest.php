<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../frontend/planning/helpers/planning_support.php';

/**
 * {@see planning_actividades_map}: la vista plana (`planning_persona_ver`) devuelve
 * una lista de mapas `p#id#nombre => actividades`; las personas sin actividades
 * deben conservarse con lista vacía.
 */
final class PlanningActividadesMapTest extends TestCase
{
    public function test_lista_plana_conserva_personas_sin_actividades(): void
    {
        $raw = [
            ['p#100516785#Persona 1' => []],
            ['p#100517176#Persona 2' => []],
            ['p#100517042#Persona 3' => [['nom_curt' => 'x']]],
        ];

        $out = planning_actividades_map($raw);
        $keys = [];
        foreach ($out as $group) {
            $keys = array_merge($keys, array_keys($group));
        }

        $this->assertSame([
            'p#100516785#Persona 1',
            'p#100517176#Persona 2',
            'p#100517042#Persona 3',
        ], $keys);
        $this->assertSame([], $out[0]['p#100516785#Persona 1']);
        $this->assertSame([], $out[1]['p#100517176#Persona 2']);
    }

    public function test_mapa_anidado_por_persona_sigue_funcionando(): void
    {
        $raw = [
            [
                'p#100#A#Centro' => [],
                'p#200#B#Centro' => [['nom_curt' => 'a']],
            ],
        ];

        $out = planning_actividades_map($raw);
        $keys = array_keys($out[0]);

        $this->assertSame(['p#100#A#Centro', 'p#200#B#Centro'], $keys);
        $this->assertSame([], $out[0]['p#100#A#Centro']);
    }

    public function test_agrupacion_por_centro_conserva_filas_numeradas(): void
    {
        $raw = [
            'Centro Unico' => [
                0 => ['p#1#Primero#Centro Unico' => []],
                1 => ['p#2#Segundo#Centro Unico' => [['nom_curt' => 'x']]],
            ],
        ];

        $out = planning_actividades_map($raw);

        $this->assertSame([
            0 => ['p#1#Primero#Centro Unico' => []],
            1 => ['p#2#Segundo#Centro Unico' => [['nom_curt' => 'x']]],
        ], $out['Centro Unico']);
    }
}
