<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

class VerEncargosZonaData
{
    /**
     * Devuelve los datos necesarios para pintar el SlickGrid de encargos de
     * una zona + los desplegables del modal de edicion.
     *
     * Replica la consulta de `apps/misas/controller/ver_encargos_zona.php`:
     * encargos con `id_tipo_enc >= 8100` (grupo `8...`) de la zona indicada,
     * ordenados por `$orden` (`orden`, `prioridad` o `desc_enc`).
     */
    public static function getData(int $id_zona, string $orden): array
    {
        $columns = [
            ['id' => 'encargo', 'name' => 'Encargo', 'field' => 'encargo', 'width' => 250, 'cssClass' => 'cell-title'],
            ['id' => 'tipo_encargo', 'name' => 'Tipo de encargo', 'field' => 'tipo_encargo', 'width' => 200, 'cssClass' => 'cell-title'],
            ['id' => 'lugar', 'name' => 'Lugar', 'field' => 'lugar', 'width' => 150, 'cssClass' => 'cell-title'],
            ['id' => 'orden', 'name' => 'Orden', 'field' => 'orden', 'width' => 100, 'cssClass' => 'cell-title'],
            ['id' => 'prioridad', 'name' => 'Prioridad', 'field' => 'prioridad', 'width' => 100, 'cssClass' => 'cell-title'],
            ['id' => 'descripcion_lugar', 'name' => 'Descripción lugar', 'field' => 'descripcion_lugar', 'width' => 150, 'cssClass' => 'cell-title'],
            ['id' => 'nom_idioma', 'name' => 'Idioma', 'field' => 'nom_idioma', 'width' => 150, 'cssClass' => 'cell-title'],
            ['id' => 'observ', 'name' => 'Observaciones', 'field' => 'observ', 'width' => 150, 'cssClass' => 'cell-title'],
        ];

        $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);

        // Tipos de encargo del grupo 8... con id_tipo_enc >= 8100.
        $cEncargoTipos = $EncargoTipoRepository->getEncargoTipos(
            ['id_tipo_enc' => '^8...'],
            ['id_tipo_enc' => '~'],
        );
        $a_tipo_enc = [];
        $posibles_encargo_tipo = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
                $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
                $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
            }
        }

        $rows = [];
        if (!empty($a_tipo_enc)) {
            $cond_tipo_enc = '{' . implode(', ', $a_tipo_enc) . '}';
            $aWhere = [
                'id_tipo_enc' => $cond_tipo_enc,
                'id_zona' => $id_zona,
                '_ordre' => $orden,
            ];
            $aOperador = ['id_tipo_enc' => 'ANY'];
            $cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);

            foreach ($cEncargos as $oEncargo) {
                $id_enc = $oEncargo->getId_enc();
                $id_ubi = $oEncargo->getId_ubi();
                $id_tipo_enc = $oEncargo->getId_tipo_enc();
                $idioma_enc = $oEncargo->getIdioma_enc();

                $nombre_ubi = '';
                if (!empty($id_ubi)) {
                    $oUbi = Ubi::newUbi($id_ubi);
                    $nombre_ubi = $oUbi->getNombre_ubi();
                }

                $tipo_enc = '';
                if (!empty($id_tipo_enc)) {
                    $oEncargoTipo = $EncargoTipoRepository->findById($id_tipo_enc);
                    if ($oEncargoTipo !== null) {
                        $tipo_enc = $oEncargoTipo->getTipo_enc();
                    }
                }

                $nom_idioma = '';
                if ($idioma_enc !== null && $idioma_enc !== '') {
                    $idioma_enc_str = (string)$idioma_enc;
                    $oLocal = $LocalRepository->findById($idioma_enc_str);
                    if ($oLocal !== null) {
                        $nom_idioma = (string)($oLocal->getNomIdiomaAsString() ?? '');
                    }
                    if ($nom_idioma === '') {
                        $cIdiomas = $LocalRepository->getLocales(['idioma' => $idioma_enc_str]);
                        if (is_array($cIdiomas) && count($cIdiomas) > 0) {
                            $nom_idioma = (string)($cIdiomas[0]->getNomIdiomaAsString() ?? '');
                        }
                    }
                }

                $rows[] = [
                    'encargo' => $oEncargo->getDesc_enc(),
                    'id_enc' => $id_enc,
                    'id_tipo_enc' => $id_tipo_enc,
                    'tipo_encargo' => $tipo_enc,
                    'meta' => '',
                    'id_ubi' => $id_ubi,
                    'lugar' => $nombre_ubi,
                    'idioma_enc' => $idioma_enc,
                    'nom_idioma' => $nom_idioma,
                    'descripcion_lugar' => $oEncargo->getDesc_lugar(),
                    'orden' => $oEncargo->getOrden(),
                    'prioridad' => $oEncargo->getPrioridad(),
                    'observ' => $oEncargo->getObserv(),
                ];
            }
        }

        // Desplegable de centros activos de la zona (ellos + ellas), ordenados por nombre_ubi.
        $GesCentrosSv = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
        $GesCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $aWhere = ['active' => 't', 'id_zona' => $id_zona, '_ordre' => 'nombre_ubi'];
        $cCentros = array_merge(
            $GesCentrosSv->getCentros($aWhere),
            $GesCentrosSf->getCentros($aWhere),
        );
        $aCentros = [];
        foreach ($cCentros as $oCentro) {
            $aCentros[$oCentro->getId_ubi()] = $oCentro->getNombre_ubi();
        }

        return [
            'id_zona' => $id_zona,
            'orden' => $orden,
            'columns' => $columns,
            'rows' => $rows,
            'tipos_encargo' => $posibles_encargo_tipo,
            'centros' => $aCentros,
            'idiomas' => $LocalRepository->getArrayLocales(),
        ];
    }
}
