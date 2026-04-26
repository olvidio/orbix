<?php

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Datos para la pantalla `asignaturas_pendientes` (matriz alumnos × asignaturas).
 * La UI (`Lista`, desplegable rstgr) se monta en el controlador frontend.
 */
final class AsignaturasPendientesData
{
    /**
     * @param array<string, mixed> $post
     * @return array{
     *   cabeceras: array<int, string>,
     *   filas: array<int, array<int, mixed>>,
     *   delegaciones: array<int|string, string>,
     *   ambito_rstgr: bool
     * }
     */
    public static function execute(array $post = []): array
    {
        if ($post === []) {
            $post = $_POST;
        }

        $service = new TablaAlumnosAsignaturas();

        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $qdl = [];
            if (isset($post['dl']) && is_array($post['dl'])) {
                foreach ($post['dl'] as $id) {
                    if ($id === null || $id === '') {
                        continue;
                    }
                    $qdl[] = (int)$id;
                }
            }

            $regionStgr = ConfigGlobal::mi_dele();
            $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $aDelegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$regionStgr]);

            if (!empty($qdl)) {
                $datosTabla = $service->paraRegionStgr($qdl, $aDelegacionesStgr);
            } else {
                $datosTabla = ['cabeceras' => [], 'filas' => []];
            }

            return [
                'cabeceras' => $datosTabla['cabeceras'],
                'filas' => $datosTabla['filas'],
                'delegaciones' => $aDelegacionesStgr,
                'ambito_rstgr' => true,
            ];
        }

        $datosTabla = $service->paraDelegacion();

        return [
            'cabeceras' => $datosTabla['cabeceras'],
            'filas' => $datosTabla['filas'],
            'delegaciones' => [],
            'ambito_rstgr' => false,
        ];
    }
}
