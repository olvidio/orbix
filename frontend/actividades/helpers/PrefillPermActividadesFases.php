<?php

namespace frontend\actividades\helpers;

use frontend\shared\AppInstalled;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\session\SessionPermActividades;

/**
 * Rellena fases completadas vía backend antes de {@see SessionPermActividades::getPermisoActual}
 * en controladores frontend sin contenedor DI completo en la misma petición.
 */
final class PrefillPermActividadesFases
{
    public static function desdeBackend(int $idActiv): void
    {
        if (!AppInstalled::is('procesos')) {
            return;
        }
        if (!SessionPermActividades::isPresent()) {
            return;
        }
        if (SessionPermActividades::isTrueEngine()) {
            return;
        }

        if ($idActiv <= 0) {
            SessionPermActividades::setFasesCompletadas([]);

            return;
        }

        $row = PostRequest::getDataFromUrl('/src/actividades/actividad_fases_completadas_datos', [
            'id_activ' => $idActiv,
        ], false);
        if (!empty($row['error'])) {
            SessionPermActividades::setFasesCompletadas([]);

            return;
        }
        $fasesRaw = $row['fases_completadas'] ?? [];
        /** @var list<int> $fasesList */
        $fasesList = [];
        if (is_array($fasesRaw)) {
            foreach ($fasesRaw as $id) {
                if (is_int($id) || is_string($id)) {
                    $fasesList[] = PayloadCoercion::int($id);
                }
            }
        }
        SessionPermActividades::setFasesCompletadas($fasesList);
    }
}
