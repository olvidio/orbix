<?php

namespace src\actividades\application;

/**
 * Recolecta las opciones de desplegables usadas en la pantalla "seleccionar
 * lugar para una actividad" (frontend/actividades/controller/actividad_select_ubi).
 *
 * Devuelve arrays value => label listos para que la vista los monte con
 * frontend\shared\web\Desplegable::desdeOpciones(...).
 */
class ActividadSelectUbiData
{
    /**
     * @param array $input Admite:
     *   - 'dl_org' (string): delegacion organizadora (si esta vacia, no se
     *                        listan opciones frecuentes).
     *   - 'isfsv'  (int):    1 = solo sv, 2 = solo sf, otro = sin filtro.
     * @return array{
     *     opcionesFreq: array<string,string>,
     *     opcionesRegion: array<string,string>
     * }
     */
    public function execute(array $input = []): array
    {
        $dl_org = (string)($input['dl_org'] ?? '');
        $isfsv = (int)($input['isfsv'] ?? 0);

        switch ($isfsv) {
            case 1:
                $donde_sfsv = "AND sv='t'";
                break;
            case 2:
                $donde_sfsv = "AND sf='t'";
                break;
            default:
                $donde_sfsv = '';
        }

        $opcionesFreq = [];
        if ($dl_org !== '') {
            $oDbl = $GLOBALS['oDBC'];
            $sql_freq = "select distinct id_ubi,nombre_ubi "
                . "from a_actividades_dl join u_cdc_dl using (id_ubi) "
                . "where dl_org=" . $oDbl->quote($dl_org) . " $donde_sfsv "
                . "ORDER by nombre_ubi";
            $oDBSt_q_freq = $oDbl->query($sql_freq);
            if ($oDBSt_q_freq !== false) {
                while ($row = $oDBSt_q_freq->fetch(\PDO::FETCH_NUM)) {
                    $opcionesFreq[(string)$row[0]] = (string)$row[1];
                }
            }
        }

        // Desplegable region: delegaciones + regiones activas (se excluyen las cr
        // del listado de delegaciones para evitar duplicados con regiones).
        $oDbl = $GLOBALS['oDBPC'];
        $sql_dl_lugar = "SELECT 'dl|'||u.dl,u.nombre_dl FROM xu_dl u WHERE active='t' AND u.dl !~ '^cr' ";
        $sql_r_lugar = "SELECT 'r|'||u.region,u.nombre_region FROM xu_region u WHERE active='t' ";
        $sql_u_lugar = $sql_dl_lugar . " UNION " . $sql_r_lugar . " ORDER BY 2";
        $oDBSt = $oDbl->query($sql_u_lugar);

        $opcionesRegion = [];
        if ($oDBSt !== false) {
            while ($row = $oDBSt->fetch(\PDO::FETCH_NUM)) {
                $opcionesRegion[(string)$row[0]] = (string)$row[1];
            }
        }

        return [
            'opcionesFreq' => $opcionesFreq,
            'opcionesRegion' => $opcionesRegion,
        ];
    }
}
