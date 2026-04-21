<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;

/**
 * Datos para la ficha de ausencias de un SACD
 * (`frontend/encargossacd/controller/sacd_ausencias_get.php`).
 *
 * Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo
 * 7/4) y las filas asociadas al SACD. Con `historial=1` incluye todas las
 * ausencias; sin historial solo muestra las que aun tienen vigencia.
 */
final class SacdAusenciasGetData
{
    /**
     * @return array{
     *     array_tipo_ausencias: array<int, string>,
     *     filas: list<array{
     *         id_enc: int,
     *         id_tipo_enc: int,
     *         desc_enc: string,
     *         id_item: int,
     *         inicio: ?string,
     *         fin: ?string,
     *         dedic_m: string,
     *         dedic_t: string,
     *         dedic_v: string
     *     }>
     * }
     */
    public static function execute(int $id_nom, int $historial): array
    {
        $hoy = date('Y-m-d');

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);

        $cEncargos = $EncargoRepository->getEncargos(
            ['id_tipo_enc' => '(7|4)...', '_ordre' => 'id_tipo_enc'],
            ['id_tipo_enc' => '~'],
        );
        $array_tipo_ausencias = [];
        if (is_array($cEncargos)) {
            foreach ($cEncargos as $oEncargo) {
                $array_tipo_ausencias[(int)$oEncargo->getId_enc()] = (string)$oEncargo->getDesc_enc();
            }
        }

        if ($historial === 1) {
            $aWhereP = ['id_nom' => $id_nom, '_ordre' => 'f_ini'];
            $aOperadorP = [];
        } else {
            $aWhereP = ['id_nom' => $id_nom, 'f_ini' => $hoy, '_ordre' => 'f_ini'];
            $aOperadorP = ['f_ini' => '>='];
        }
        $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd($aWhereP, $aOperadorP) ?: [];

        $filas = [];
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $id_enc = (int)$oEncargoSacd->getId_enc();
            $oEncargo = $EncargoRepository->findById($id_enc);
            if ($oEncargo === null) {
                continue;
            }
            $id_tipo_enc = (string)$oEncargo->getId_tipo_enc();
            if (!preg_match('/[74]/', $id_tipo_enc)) {
                continue;
            }

            $cHorarios = self::cargarHorarios(
                $EncargoSacdHorarioRepository,
                $id_enc,
                $id_nom,
                $historial,
                $hoy,
            );

            $dedic_m = '';
            $dedic_t = '';
            $dedic_v = '';
            foreach ($cHorarios as $oHorario) {
                switch ((string)$oHorario->getDia_ref()) {
                    case 'm':
                        $dedic_m = (string)$oHorario->getDia_inc();
                        break;
                    case 't':
                        $dedic_t = (string)$oHorario->getDia_inc();
                        break;
                    case 'v':
                        $dedic_v = (string)$oHorario->getDia_inc();
                        break;
                }
            }

            $f_ini = $oEncargoSacd->getF_ini();
            $f_fin = $oEncargoSacd->getF_fin();
            $filas[] = [
                'id_enc' => $id_enc,
                'id_tipo_enc' => (int)$id_tipo_enc,
                'desc_enc' => (string)$oEncargo->getDesc_enc(),
                'id_item' => (int)$oEncargoSacd->getId_item(),
                'inicio' => empty($f_ini) ? null : (string)$f_ini->getFromLocal(),
                'fin' => empty($f_fin) ? null : (string)$f_fin->getFromLocal(),
                'dedic_m' => $dedic_m,
                'dedic_t' => $dedic_t,
                'dedic_v' => $dedic_v,
            ];
        }

        return [
            'array_tipo_ausencias' => $array_tipo_ausencias,
            'filas' => $filas,
        ];
    }

    /**
     * @return list<mixed>
     */
    private static function cargarHorarios(
        EncargoSacdHorarioRepositoryInterface $repo,
        int $id_enc,
        int $id_nom,
        int $historial,
        string $hoy,
    ): array {
        if ($historial === 1) {
            return $repo->getEncargoSacdHorarios(['id_enc' => $id_enc, 'id_nom' => $id_nom]) ?: [];
        }

        $aWhere = ['id_enc' => $id_enc, 'id_nom' => $id_nom, 'f_fin' => "'$hoy'"];
        $aOperador = ['f_fin' => '>'];
        $c1 = $repo->getEncargoSacdHorarios($aWhere, $aOperador) ?: [];

        $aWhere['f_fin'] = '';
        $aOperador['f_fin'] = 'IS NULL';
        $c2 = $repo->getEncargoSacdHorarios($aWhere, $aOperador) ?: [];

        return array_merge($c1, $c2);
    }
}
