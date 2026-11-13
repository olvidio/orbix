<?php

declare(strict_types=1);

namespace Tests\unit\utils_database\domain;

use src\utils_database\domain\GenerateIdGlobal;
use Tests\myTest;

final class GenerateIdGlobalTest extends myTest
{
    public function test_p_de_paso_ex_usa_restov_no_la_dl_de_sesion(): void
    {
        $this->assertSame('restov', GenerateIdGlobal::schemaForLookup('p_de_paso_ex', 'H-dlbv'));
    }

    public function test_p_de_paso_ex_respeta_restof_si_se_pide(): void
    {
        $this->assertSame('restof', GenerateIdGlobal::schemaForLookup('p_de_paso_ex', 'restof'));
    }

    public function test_p_numerarios_usa_la_dl_de_sesion(): void
    {
        $this->assertSame('H-dlbv', GenerateIdGlobal::schemaForLookup('p_numerarios', 'H-dlbv'));
    }

    public function test_a_actividades_ex_usa_esquema_resto(): void
    {
        $this->assertSame('resto', GenerateIdGlobal::schemaForLookup('a_actividades_ex', 'H-dlbv'));
        $this->assertSame('resto', GenerateIdGlobal::schemaForLookup('a_actividades_ex', 'restov'));
    }

    public function test_compose_id_persona_de_paso_restov(): void
    {
        $this->assertSame(-100161351, GenerateIdGlobal::composeId(-1001, 6, 1351));
    }

    public function test_compose_id_actividad_ex_resto(): void
    {
        $this->assertSame(-30013619, GenerateIdGlobal::composeId(-3001, 0, 3619));
    }

    public function test_tabla_desconocida_lanza(): void
    {
        $this->expectException(\Exception::class);
        GenerateIdGlobal::schemaForLookup('tabla_inexistente', 'H-dlbv');
    }

    /** Contra `public.db_idschema` de la BD de desarrollo: el prefijo es el id del esquema. */
    public function test_genera_el_id_con_el_id_del_esquema_de_la_bd(): void
    {
        $idSchema = (string) $GLOBALS['oDBPC']->query("SELECT id FROM public.db_idschema WHERE schema = 'H-dlbv'")->fetchColumn();
        $this->assertNotSame('', $idSchema);

        $this->assertSame((int) ($idSchema . '1' . '1351'), GenerateIdGlobal::generateIdGlobal('H-dlbv', 'p_numerarios', 1351));
        $this->assertSame((int) ($idSchema . '77'), GenerateIdGlobal::generateIdGlobal('H-dlbv', 'a_actividades_dl', 77));
    }

    public function test_esquema_sin_id_en_la_bd_lanza(): void
    {
        $this->expectExceptionMessage('No se encontró ID para el esquema: Zzt-noexiste');
        GenerateIdGlobal::generateIdGlobal('Zzt-noexiste', 'p_numerarios', 1);
    }

    public function test_tabla_desconocida_lanza_tambien_al_generar(): void
    {
        $this->expectExceptionMessage('Tabla no reconocida para generar ID: x_tabla');
        GenerateIdGlobal::generateIdGlobal('H-dlbv', 'x_tabla', 1);
    }
}
