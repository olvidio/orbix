<?php

namespace src\actividades\application;

use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/**
 * Cápsulas HashB de mutación de actividad: listados (por fila) y apertura de `sel[]`.
 */
final class ActividadMutationCtx
{
    /**
     * Tokens por fila según el modo del listado. `sel` sigue siendo `id#nom`
     * porque `jsForm.mandar` lo usa para navegar a pantallas frontend.
     *
     * @return array<string, string>
     */
    public static function rowTokens(int $idActiv, string $modo): array
    {
        if ($idActiv <= 0) {
            return [];
        }

        $ctx = ['id_activ' => $idActiv];
        if ($modo === 'importar') {
            return ['ctx_importar' => HashB::sign('actividad_importar', $ctx)];
        }
        if ($modo === 'publicar') {
            return ['ctx_publicar' => HashB::sign('actividad_publicar', $ctx)];
        }

        return [
            'ctx_eliminar' => HashB::sign('actividad_eliminar', $ctx),
            'ctx_duplicar' => HashB::sign('actividad_duplicar', $ctx),
        ];
    }

    /**
     * Abre cada cápsula de `sel[]` y devuelve los `id_activ` verificados
     * como strings (el formato que esperan los casos de uso con `strtok`).
     *
     * @param list<string> $sel
     * @return list<string>
     */
    public static function openSelIds(array $sel, string $action): array
    {
        $ids = [];
        foreach ($sel as $capsule) {
            try {
                $ctx = HashB::open((string) $capsule, $action);
            } catch (HashBInvalidException $e) {
                continue;
            }
            $id = FuncTablasSupport::inputInt($ctx, 'id_activ');
            if ($id > 0) {
                $ids[] = (string) $id;
            }
        }

        return $ids;
    }
}
