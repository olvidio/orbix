<?php

namespace frontend\actividades\helpers;

use frontend\shared\PostRequest;

/**
 * Rellena {@see \src\permisos\domain\PermisosActividades::setFasesCompletadas}
 * vía backend antes de {@see \src\permisos\domain\PermisosActividades::getPermisoActual}
 * en controladores frontend sin contenedor DI completo en la misma petición.
 */
final class PrefillPermActividadesFases
{
    public static function desdeBackend(int $idActiv): void
    {
        if ($idActiv <= 0) {
            $_SESSION['oPermActividades']->setFasesCompletadas([]);

            return;
        }

        $row = PostRequest::getDataFromUrl('/src/actividades/actividad_fases_completadas_datos', [
            'id_activ' => $idActiv,
        ]);
        $fases = $row['fases_completadas'] ?? [];
        $_SESSION['oPermActividades']->setFasesCompletadas(\is_array($fases) ? $fases : []);
    }
}
