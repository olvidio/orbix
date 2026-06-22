<?php

namespace frontend\actividades\helpers;

require_once __DIR__ . '/actividades_support.php';

use frontend\shared\AppInstalled;
use frontend\shared\PostRequest;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;

/**
 * Rellena {@see \src\permisos\domain\PermisosActividades::setFasesCompletadas}
 * vía backend antes de {@see \src\permisos\domain\PermisosActividades::getPermisoActual}
 * en controladores frontend sin contenedor DI completo en la misma petición.
 */
final class PrefillPermActividadesFases
{
    public static function desdeBackend(int $idActiv): void
    {
        if (!AppInstalled::is('procesos')) {
            return;
        }
        $oPermActividades = actividades_o_perm_actividades();
        if (!$oPermActividades instanceof PermisosActividades) {
            return;
        }
        // PermisosActividadesTrue no usa fases; sus métodos devuelven permiso total.
        if ($oPermActividades instanceof PermisosActividadesTrue) {
            return;
        }

        if ($idActiv <= 0) {
            $oPermActividades->setFasesCompletadas([]);

            return;
        }

        $row = PostRequest::getDataFromUrl('/src/actividades/actividad_fases_completadas_datos', [
            'id_activ' => $idActiv,
        ], false);
        if (!empty($row['error'])) {
            $oPermActividades->setFasesCompletadas([]);

            return;
        }
        $fases = $row['fases_completadas'] ?? [];
        $oPermActividades->setFasesCompletadas(actividades_fases_completadas_from_payload($fases));
    }
}
