<?php

declare(strict_types=1);

namespace Tests\unit\frontend\actividades\helpers;

use frontend\actividades\helpers\ActividadTipo;
use PHPUnit\Framework\TestCase;

final class ActividadTipoNomTipoActionTest extends TestCase
{
    public function test_busqueda_no_abre_ficha_de_alta(): void
    {
        $this->assertSame('fnjs_id_activ()', ActividadTipo::nomTipoChangeAction('buscar'));
    }

    public function test_alta_o_edicion_recarga_ficha(): void
    {
        $this->assertSame('fnjs_act_id_activ()', ActividadTipo::nomTipoChangeAction(null));
        $this->assertSame('fnjs_act_id_activ()', ActividadTipo::nomTipoChangeAction(''));
        $this->assertSame('fnjs_act_id_activ()', ActividadTipo::nomTipoChangeAction('nuevo'));
    }
}
