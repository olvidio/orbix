<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\misas\application\support\MisasBuildInput;
use src\ubis\domain\entity\Ubi;

class GuardarEncargoZona
{

    public function __construct(
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
    }
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
    /**
     * @param array<string, mixed> $input
     * @return array{error: string, data: array{id_enc: int, lugar: string}}
     */
    public function execute(array $input): array
    {
        $id_enc = MisasBuildInput::int($input, 'id_enc');
        $id_tipo_enc = MisasBuildInput::int($input, 'id_tipo_enc');
        $id_ubi = MisasBuildInput::int($input, 'id_ubi');
        $orden = MisasBuildInput::int($input, 'orden');
        $prioridad = MisasBuildInput::int($input, 'prioridad');
        $id_zona = MisasBuildInput::int($input, 'id_zona');
        $descripcion_lugar = MisasBuildInput::string($input, 'descripcion_lugar');
        $encargo = MisasBuildInput::string($input, 'encargo');
        $idioma_enc = MisasBuildInput::string($input, 'idioma_enc');
        $observ = MisasBuildInput::string($input, 'observ');

        if (empty($id_enc)) {
            $newIdItem = $this->encargoRepository->getNewId();
            $oEncargo = new Encargo();
            $oEncargo->setId_enc($newIdItem);
        } else {
            $oEncargo = $this->encargoRepository->findById($id_enc);
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
            $oUbi = Ubi::NewUbi($id_ubi);
            $nombre_ubi = $oUbi !== null ? $oUbi->getNombre_ubi() : '';
        }

        if ($this->encargoRepository->Guardar($oEncargo) === false) {
            return [
                'error' => $this->encargoRepository->getErrorTxt(),
                'data' => ['id_enc' => (int)$oEncargo->getId_enc(), 'lugar' => $nombre_ubi],
            ];
        }

        return [
            'error' => '',
            'data' => ['id_enc' => (int)$oEncargo->getId_enc(), 'lugar' => $nombre_ubi],
        ];
    }
}
