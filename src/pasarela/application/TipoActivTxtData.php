<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;
use src\shared\security\HashB;

/**
 * Devuelve el texto descriptivo (`sfsv asistentes actividad`) para un
 * `id_tipo_activ`. Lo consumen los formularios `form_modificar` desde el
 * frontend para mostrar a qué tipo de actividad corresponde la fila editada.
 *
 * También emite, para el mismo `id_tipo_activ`, una cápsula `HashB` de
 * eliminación por cada familia de excepciones que reutiliza este mismo
 * `form_modificar` (activación, contribución no-duerme, contribución
 * reserva, nombre): cada dispatcher (`*_ajax.php`) solo consume la suya.
 */
final class TipoActivTxtData
{
    /**
     * @return array{
     *     tipo_txt: string,
     *     ctx_eliminar_activacion: string,
     *     ctx_eliminar_contribucion_no_duerme: string,
     *     ctx_eliminar_contribucion_reserva: string,
     *     ctx_eliminar_nombre: string,
     * }
     */
    public function execute(string $id_tipo_activ): array
    {
        $tipo_txt = '';
        if ($id_tipo_activ !== '') {
            $oActividadTipo = new TiposActividades($id_tipo_activ);
            $svsf = $oActividadTipo->getSfsvText();
            $asistentes = $oActividadTipo->getAsistentesText();
            $actividad = $oActividadTipo->getActividadText();
            $tipo_txt = trim("$svsf $asistentes $actividad");
        }
        $contexto = ['id_tipo_activ' => $id_tipo_activ];

        return [
            'tipo_txt' => $tipo_txt,
            'ctx_eliminar_activacion' => HashB::sign('activacion_excepcion_eliminar', $contexto),
            'ctx_eliminar_contribucion_no_duerme' => HashB::sign('contribucion_no_duerme_excepcion_eliminar', $contexto),
            'ctx_eliminar_contribucion_reserva' => HashB::sign('contribucion_reserva_excepcion_eliminar', $contexto),
            'ctx_eliminar_nombre' => HashB::sign('nombre_excepcion_eliminar', $contexto),
        ];
    }
}
