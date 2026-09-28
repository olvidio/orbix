<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\TipoActivTxtData;
use src\shared\security\HashB;

final class TipoActivTxtDataTest extends TestCase
{
    public function test_id_vacio_devuelve_tipo_txt_vacio(): void
    {
        $out = (new TipoActivTxtData())->execute('');
        $this->assertSame('', $out['tipo_txt']);
    }

    public function test_id_conocido_devuelve_texto_compuesto(): void
    {
        $out = (new TipoActivTxtData())->execute('111000');
        $this->assertArrayHasKey('tipo_txt', $out);
        $this->assertNotSame('', trim($out['tipo_txt']));
    }

    public function test_emite_una_capsula_ctx_eliminar_por_familia_atada_al_id_tipo_activ(): void
    {
        $out = (new TipoActivTxtData())->execute('111000');

        $contexto = ['id_tipo_activ' => '111000'];
        $this->assertSame($contexto, HashB::open($out['ctx_eliminar_activacion'], 'activacion_excepcion_eliminar'));
        $this->assertSame(
            $contexto,
            HashB::open($out['ctx_eliminar_contribucion_no_duerme'], 'contribucion_no_duerme_excepcion_eliminar')
        );
        $this->assertSame(
            $contexto,
            HashB::open($out['ctx_eliminar_contribucion_reserva'], 'contribucion_reserva_excepcion_eliminar')
        );
        $this->assertSame($contexto, HashB::open($out['ctx_eliminar_nombre'], 'nombre_excepcion_eliminar'));
    }

    public function test_id_conocido_emite_ctx_guardar_en_modo_existing(): void
    {
        $out = (new TipoActivTxtData())->execute('111000');

        $contexto = ['modo' => 'existing', 'id_tipo_activ' => '111000'];
        $this->assertSame($contexto, HashB::open($out['ctx_guardar_activacion'], 'activacion_excepcion_guardar'));
        $this->assertSame(
            $contexto,
            HashB::open($out['ctx_guardar_contribucion_no_duerme'], 'contribucion_no_duerme_excepcion_guardar')
        );
        $this->assertSame(
            $contexto,
            HashB::open($out['ctx_guardar_contribucion_reserva'], 'contribucion_reserva_excepcion_guardar')
        );
        $this->assertSame($contexto, HashB::open($out['ctx_guardar_nombre'], 'nombre_excepcion_guardar'));
    }

    public function test_id_vacio_emite_ctx_guardar_en_modo_nuevo(): void
    {
        $out = (new TipoActivTxtData())->execute('');

        $contexto = ['modo' => 'nuevo', 'id_tipo_activ' => ''];
        $this->assertSame($contexto, HashB::open($out['ctx_guardar_activacion'], 'activacion_excepcion_guardar'));
    }
}
