<?php

declare(strict_types=1);

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use Tests\myTest;

/**
 * `getArrayPersonas()`: lista de personas activas para los desplegables de alta (asistentes, cargos). En las
 * personas de paso se filtra por tipo (`id_tabla`), que llega de `'p' . $_POST['na']`.
 */
final class PersonaGetArrayPersonasTest extends myTest
{
    public function test_las_personas_de_paso_se_filtran_por_tipo(): void
    {
        $repo = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);

        $todas = $repo->getArrayPersonas();
        $pn = $repo->getArrayPersonas('pn');
        $pa = $repo->getArrayPersonas('pa');

        $this->assertNotSame([], $pn, 'la BD de pruebas tiene n de paso');
        $this->assertSame([], array_intersect_key($pn, $pa));
        $this->assertSame(count($pn) + count($pa), count(array_intersect_key($todas, $pn + $pa)));
        $this->assertSame([], $repo->getArrayPersonas('pzz'));
    }

    /**
     * Regresión: el tipo se interpolaba entre comillas en el SQL; con «pn' OR '1'='1» se listaban todas (y con un
     * UNION se podía leer cualquier tabla).
     */
    public function test_el_tipo_no_se_interpola_en_el_sql(): void
    {
        $repo = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);

        $this->assertSame([], $repo->getArrayPersonas("pn' OR '1'='1"));
    }

    public function test_las_personas_de_la_dl_llevan_su_centro(): void
    {
        $personas = $GLOBALS['container']->get(PersonaNRepositoryInterface::class)->getArrayPersonas();

        $this->assertNotSame([], $personas);
        $this->assertMatchesRegularExpression('/\(.+\)$/', (string) reset($personas));
    }
}
