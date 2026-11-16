<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\AppsReq;
use Tests\myTest;

/** Aplicaciones requeridas por un módulo: ids de aplicación (la columna `apps_req` es `integer[]`). */
class AppsReqTest extends myTest
{
    public function test_create_valid_appsReq()
    {
        $appsReq = new AppsReq([7]);
        $this->assertSame([7], $appsReq->toArray());
    }

    public function test_to_string_returns_appsReq_value()
    {
        $appsReq = new AppsReq([8, 3]);
        $this->assertEquals('8,3', (string)$appsReq);
    }

    /** Del formulario llegan como texto; se guardan como enteros, sin vacíos ni repetidos. */
    public function test_los_ids_en_texto_se_convierten_a_enteros(): void
    {
        $this->assertSame([7, 10], (new AppsReq(['7', ' 10 ', '', 7]))->toArray());
    }

    public function test_un_nombre_o_un_id_no_positivo_no_es_valido(): void
    {
        foreach ([['notas'], [0], ['-3']] as $apps) {
            try {
                new AppsReq($apps);
                $this->fail('debería rechazar ' . json_encode($apps));
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
