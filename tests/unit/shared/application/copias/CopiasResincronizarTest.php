<?php

declare(strict_types=1);

namespace Tests\unit\shared\application\copias;

use PHPUnit\Framework\TestCase;
use src\shared\application\copias\CopiasResincronizar;

final class CopiasResincronizarTest extends TestCase
{
    public function test_en_sv_lanza_las_tres(): void
    {
        $plan = CopiasResincronizar::planPara('sv');

        $this->assertSame(
            ['cp_sacd', 'cd_cargos_activ_dl', 'cu_centros_dl'],
            array_column($plan, 'nombre'),
        );
        $this->assertSame(
            ['cp_sacd', 'cd_cargos_activ_dl', 'cu_centros_dl'],
            array_column($plan, 'clave'),
        );
        foreach ($plan as $paso) {
            $this->assertNull($paso['omitida']);
        }
    }

    public function test_en_sf_solo_centros_dlf(): void
    {
        $plan = CopiasResincronizar::planPara('sf');

        $this->assertNull($plan[0]['clave']);
        $this->assertNull($plan[1]['clave']);
        $this->assertNotNull($plan[0]['omitida']);
        $this->assertNotNull($plan[1]['omitida']);
        $this->assertSame('cu_centros_dlf', $plan[2]['nombre']);
        $this->assertSame('cu_centros_dlf', $plan[2]['clave']);
        $this->assertNull($plan[2]['omitida']);
    }

    public function test_otra_ubicacion_omite_las_tres(): void
    {
        $plan = CopiasResincronizar::planPara('dmz');

        foreach ($plan as $paso) {
            $this->assertNull($paso['clave']);
            $this->assertNotNull($paso['omitida']);
        }
    }
}
