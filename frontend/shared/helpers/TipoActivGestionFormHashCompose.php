<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\security\HashF;

/**
 * Campos ocultos firmados (`HashF::getCamposHtml`) para los formularios de gestión
 * de tipos de actividad ({@see \src\actividades\application\TipoActivFormNuevo},
 * {@see \src\actividades\application\TipoActivFormModificar}).
 */
final class TipoActivGestionFormHashCompose
{
    public static function nuevoHiddenHtml(string $ctxNuevo = ''): string
    {
        $oHash = new HashF();
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_nom_tipo_activ!isfsv_val!nom_tipo_activ');
        $hidden = ['ctx_nuevo' => $ctxNuevo];
        $oHash->setArrayCamposHidden($hidden);

        return $oHash->getCamposHtml();
    }

    public static function modificarHiddenHtml(int $idTipoActiv, string $ctxGuardar = '', string $ctxEliminar = ''): string
    {
        $oHash = new HashF();
        $oHash->setCamposForm('nom_tipo_activ');
        $oHash->setArrayCamposHidden([
            'id_tipo_activ' => $idTipoActiv,
            'ctx_guardar' => $ctxGuardar,
            'ctx_eliminar' => $ctxEliminar,
        ]);

        return $oHash->getCamposHtml();
    }
}
