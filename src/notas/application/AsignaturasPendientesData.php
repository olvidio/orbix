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

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly TablaAlumnosAsignaturas $tablaAlumnosAsignaturas,
    ) {
    }
    /**
     * @param array<string, mixed> $post
     * @return array{
     *   cabeceras: array<int, string>,
     *   filas: array<int, array<int, mixed>>,
     *   delegaciones: array<int|string, string>,
     *   ambito_rstgr: bool
     * }
     */
    public function execute(array $post = []): array
    {
        if ($post === []) {
            $post = $_POST;
        }

        $service = $this->tablaAlumnosAsignaturas;

        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $qdl = [];
            if (isset($post['dl']) && is_array($post['dl'])) {
                foreach ($post['dl'] as $id) {
                    if (!is_scalar($id) || (string) $id === '') {
                        continue;
                    }
                    $qdl[] = (int) $id;
                }
            }

            $regionStgr = ConfigGlobal::mi_dele();
            $repoDelegacion = $this->delegacionRepository;
            $aDelegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$regionStgr]);
            /** @var array<int, string> $aDelegacionesStgr */

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
