<?php

namespace src\encargossacd\application;

use core\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Datos para la comunicacion a los centros.
 * Sustituye la logica de `frontend/encargossacd/controller/listas_com_ctr.php`.
 *
 * El modelo de salida replica el consumido por la vista
 * `listas_com_ctr.phtml`:
 *  - `array_atn_sacd[nombre_ubi]` con titular, suplente, colaboradores y
 *    el texto de comunicacion traducido al idioma del idioma actual.
 *  - `origen_txt` cabecera de emisor y `lugar_fecha` pie.
 */
final class ListasComCtrData
{
    /**
     * @return array{
     *     array_atn_sacd: array<string, array<string, mixed>>,
     *     origen_txt: string,
     *     lugar_fecha: string
     * }
     */
    public static function execute(string $sfsv): array
    {
        $oService = new EncargoAplicacionService();

        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $poblacion = $oService->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        $origen_txt = '';
        $cCentros = [];
        switch ($sfsv) {
            case 'sv':
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $cCentros = $GesCentros->getCentros(['active' => 't', '_ordre' => 'tipo_ctr,nombre_ubi']) ?: [];
                $origen_txt = (string)ConfigGlobal::mi_dele();
                break;
            case 'sf':
                $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $cCentros = $GesCentros->getCentros(['active' => 't', '_ordre' => 'tipo_ctr,nombre_ubi']) ?: [];
                $origen_txt = ConfigGlobal::mi_dele() . 'f';
                break;
        }

        $array_atn_sacd = [];
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);

        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = (string)$oCentro->getNombre_ubi();

            $cEncargos = $EncargoRepository->getEncargos(
                ['id_ubi' => $id_ubi, 'id_tipo_enc' => '(1|2|3).00'],
                ['id_tipo_enc' => '~'],
            ) ?: [];
            if (count($cEncargos) === 0) {
                continue;
            }
            if (count($cEncargos) !== 1) {
                continue;
            }
            $id_enc = $cEncargos[0]->getId_enc();
            if (empty($id_enc)) {
                continue;
            }
            $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(
                ['id_enc' => $id_enc, 'f_fin' => 'x', '_ordre' => 'modo'],
                ['f_fin' => 'IS NULL'],
            ) ?: [];

            $sacd_colaborador = [];
            $array_atn_sacd[$nombre_ubi] = [
                'titular' => '',
                'titular_dedicacion' => '',
                'suplente' => '',
                'colaborador' => [],
                'txt' => [],
            ];
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $id_nom = $oEncargoSacd->getId_nom();
                $oPersona = $PersonaDlRepository->findById($id_nom);
                if ($oPersona === null) {
                    continue;
                }
                $modo = (int)$oEncargoSacd->getModo();
                switch ($modo) {
                    case 2:
                    case 3:
                        $array_atn_sacd[$nombre_ubi]['titular'] = $oPersona->getNombreApellidos();
                        $array_atn_sacd[$nombre_ubi]['titular_dedicacion'] = $oService->dedicacion($id_nom, $id_enc);
                        break;
                    case 4:
                        $array_atn_sacd[$nombre_ubi]['suplente'] = $oPersona->getNombreApellidos();
                        break;
                    case 5:
                        $sacd_colaborador[] = [
                            'nom' => $oPersona->getNombreApellidos(),
                            'dedicacion' => $oService->dedicacion($id_nom, $id_enc),
                        ];
                        break;
                }
            }
            $array_atn_sacd[$nombre_ubi]['colaborador'] = $sacd_colaborador;
            $array_atn_sacd[$nombre_ubi]['txt']['com_ctr'] = $oService->getTraduccion('com_ctr', '');
        }

        return [
            'array_atn_sacd' => $array_atn_sacd,
            'origen_txt' => $origen_txt,
            'lugar_fecha' => $lugar_fecha,
        ];
    }
}
