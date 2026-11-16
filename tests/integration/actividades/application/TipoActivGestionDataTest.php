<?php

declare(strict_types=1);

namespace Tests\integration\actividades\application;

use src\actividades\application\TipoActivFormModificarData;
use src\actividades\application\TipoActivFormNuevoData;
use src\actividades\application\TipoActivListaData;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * Datos de la pantalla de gestión de tipos de actividad. El HTML (lista y formularios) lo monta `frontend/`.
 */
final class TipoActivGestionDataTest extends myTest
{
    public function test_la_lista_trae_todos_los_tipos_por_id_con_su_nombre(): void
    {
        $tipos = $GLOBALS['container']->get(TipoActivListaData::class)->execute()['tipos'];

        $ids = array_column($tipos, 'id_tipo_activ');
        $this->assertCount(count($GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class)->matching(Criteria::create())), $ids);
        $this->assertNotSame([], $tipos, 'la BD de pruebas tiene tipos de actividad');
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
        $this->assertSame((new TiposActividades($ids[0]))->getNom(), $tipos[0]['nom']);
        $this->assertIsInt($ids[0]);
    }

    public function test_el_alta_trae_su_capsula(): void
    {
        $r = $GLOBALS['container']->get(TipoActivFormNuevoData::class)->execute();

        $this->assertSame([], HashB::open($r['ctx_nuevo'], 'tipo_activ_nuevo'));
    }

    public function test_modificar_trae_nombres_y_capsulas_del_tipo(): void
    {
        $id = $GLOBALS['container']->get(TipoActivListaData::class)->execute()['tipos'][0]['id_tipo_activ'];
        $t = new TiposActividades($id);

        $r = $GLOBALS['container']->get(TipoActivFormModificarData::class)->execute(['id_tipo_activ' => (string) $id]);

        $this->assertSame($id, $r['id_tipo_activ']);
        $this->assertSame($t->getSfsvText() . ' ' . $t->getAsistentesText() . ' ' . $t->getActividadText(), $r['nom_actividad']);
        $this->assertSame($t->getNom_tipoText(), $r['nom_tipo']);
        $this->assertSame(['id_tipo_activ' => $id], HashB::open($r['ctx_guardar'], 'tipo_activ_update'));
        $this->assertSame(['id_tipo_activ' => $id], HashB::open($r['ctx_eliminar'], 'tipo_activ_eliminar'));
    }

    public function test_modificar_sin_tipo_avisa(): void
    {
        $r = $GLOBALS['container']->get(TipoActivFormModificarData::class)->execute([]);

        $this->assertSame(['error' => _("Tipo no válido")], $r);
    }
}
