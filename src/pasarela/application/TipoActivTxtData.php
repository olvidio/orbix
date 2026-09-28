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
 * eliminación y otra de guardado por cada familia de excepciones que
 * reutiliza este mismo `form_modificar` (activación, contribución
 * no-duerme, contribución reserva, nombre): cada dispatcher (`*_ajax.php`)
 * solo consume las suyas.
 *
 * `ctx_guardar_*` lleva `modo` para distinguir edición de una fila
 * existente (`id_tipo_activ` real, atado en el contexto) de alta nueva
 * (`id_tipo_activ` vacío: el valor real lo compone el propio formulario a
 * partir del selector `ActividadTipo` y no se puede atar de antemano). El
 * flujo `form_nuevo` de cada dispatcher llama a este mismo endpoint con
 * `id_tipo_activ=''` solo para obtener el `ctx_guardar_*` en modo `nuevo`.
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
     *     ctx_guardar_activacion: string,
     *     ctx_guardar_contribucion_no_duerme: string,
     *     ctx_guardar_contribucion_reserva: string,
     *     ctx_guardar_nombre: string,
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
        $contextoGuardar = [
            'modo' => $id_tipo_activ === '' ? 'nuevo' : 'existing',
            'id_tipo_activ' => $id_tipo_activ,
        ];

        return [
            'tipo_txt' => $tipo_txt,
            'ctx_eliminar_activacion' => HashB::sign('activacion_excepcion_eliminar', $contexto),
            'ctx_eliminar_contribucion_no_duerme' => HashB::sign('contribucion_no_duerme_excepcion_eliminar', $contexto),
            'ctx_eliminar_contribucion_reserva' => HashB::sign('contribucion_reserva_excepcion_eliminar', $contexto),
            'ctx_eliminar_nombre' => HashB::sign('nombre_excepcion_eliminar', $contexto),
            'ctx_guardar_activacion' => HashB::sign('activacion_excepcion_guardar', $contextoGuardar),
            'ctx_guardar_contribucion_no_duerme' => HashB::sign('contribucion_no_duerme_excepcion_guardar', $contextoGuardar),
            'ctx_guardar_contribucion_reserva' => HashB::sign('contribucion_reserva_excepcion_guardar', $contextoGuardar),
            'ctx_guardar_nombre' => HashB::sign('nombre_excepcion_guardar', $contextoGuardar),
        ];
    }
}
