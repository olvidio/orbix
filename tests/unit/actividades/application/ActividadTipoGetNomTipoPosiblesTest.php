<?php

namespace Tests\unit\actividades\application;

use src\actividades\application\ActividadTipoGetNomTipoPosibles;
use src\actividades\domain\entity\TiposActividades;
use Tests\myTest;

/** Códigos y nombres de tipo (dos cifras) posibles para un prefijo; la tabla la monta el frontend. */
final class ActividadTipoGetNomTipoPosiblesTest extends myTest
{
    public function test_devuelve_los_pares_codigo_nombre_del_prefijo(): void
    {
        $r = (new ActividadTipoGetNomTipoPosibles())->execute(['entrada' => '1']);

        $esperado = [];
        foreach ((new TiposActividades('1..', true))->getNom_tipoPosibles2Digitos() as $id => $nom) {
            $esperado[] = [(string) $id, (string) $nom];
        }
        $this->assertNotSame([], $esperado, 'la BD de pruebas tiene tipos de sv');
        $this->assertSame(['nom_tipos' => $esperado], $r);
    }
}
