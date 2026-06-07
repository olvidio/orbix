<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;

/**
 * Devuelve el texto descriptivo (`sfsv asistentes actividad`) para un
 * `id_tipo_activ`. Lo consumen los formularios `form_modificar` desde el
 * frontend para mostrar a qué tipo de actividad corresponde la fila editada.
 */
final class TipoActivTxtData
{
    /**
     * @return array{tipo_txt: string}
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
        return [
            'tipo_txt' => $tipo_txt,
        ];
    }
}
