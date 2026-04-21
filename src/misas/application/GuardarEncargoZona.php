<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\entity\Ubi;

class GuardarEncargoZona
{
    /**
     * Inserta o actualiza un `Encargo` del grupo `ZONAS_MISAS`.
     *
     * - Si `id_enc` es 0 se crea uno nuevo con `getNewId()`.
     * - Si hay valor, se carga el existente y se modifica.
     *
     * Devuelve un array con:
     *   - `error`: texto vacio si todo fue bien, mensaje del repositorio si no.
     *   - `data` : payload para el frontend con `id_enc`, `lugar` y el nombre
     *             del centro si se resolvio.
     */
    public static function execute(array $input): array
    {
        $id_enc = (int)($input['id_enc'] ?? 0);
        $id_tipo_enc = (int)($input['id_tipo_enc'] ?? 0);
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $orden = (int)($input['orden'] ?? 0);
        $prioridad = (int)($input['prioridad'] ?? 0);
        $id_zona = (int)($input['id_zona'] ?? 0);
        $descripcion_lugar = (string)($input['descripcion_lugar'] ?? '');
        $encargo = (string)($input['encargo'] ?? '');
        $idioma_enc = (string)($input['idioma_enc'] ?? '');
        $observ = (string)($input['observ'] ?? '');

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

        if (empty($id_enc)) {
            $newIdItem = $EncargoRepository->getNewId();
            $oEncargo = new Encargo();
            $oEncargo->setId_enc($newIdItem);
        } else {
            $oEncargo = $EncargoRepository->findById($id_enc);
            if ($oEncargo === null) {
                return [
                    'error' => sprintf(_('No se encuentra el encargo %d'), $id_enc),
                    'data' => ['id_enc' => $id_enc, 'lugar' => ''],
                ];
            }
        }

        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $oEncargo->setGrupoEncargoVo(EncargoGrupo::ZONAS_MISAS);
        $oEncargo->setId_ubi($id_ubi);
        $oEncargo->setOrden($orden);
        $oEncargo->setPrioridad($prioridad);
        $oEncargo->setId_zona($id_zona);
        $oEncargo->setDesc_enc($encargo);
        $oEncargo->setIdioma_enc($idioma_enc);
        $oEncargo->setDesc_lugar($descripcion_lugar);
        $oEncargo->setObserv($observ);

        $nombre_ubi = '';
        if (!empty($id_ubi)) {
            $oUbi = Ubi::newUbi($id_ubi);
            $nombre_ubi = $oUbi->getNombre_ubi();
        }

        if ($EncargoRepository->Guardar($oEncargo) === false) {
            return [
                'error' => $EncargoRepository->getErrorTxt(),
                'data' => ['id_enc' => (int)$oEncargo->getId_enc(), 'lugar' => $nombre_ubi],
            ];
        }

        return [
            'error' => '',
            'data' => ['id_enc' => (int)$oEncargo->getId_enc(), 'lugar' => $nombre_ubi],
        ];
    }
}
