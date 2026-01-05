<?php

namespace src\ubis\domain\entity;

use core\ConfigGlobal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;


class Ubi
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe vuit.
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public static function NewUbi($id_ubi): Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos|null
    {
        if (ConfigGlobal::is_dmz()) {
            $CentroRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
            // para la sf (comienza por 2).
            if ((int)substr($id_ubi, 0, 1) === 2) {
                $CentroRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            }
            $oCentro = $CentroRepository->findById($id_ubi);
        } else {
            // para la sf (comienza por 2).
            if ((int)substr($id_ubi, 0, 1) === 2) {
                // Si soy sv solo tengo acceso a los de la dl,
                // En caso contrario puedo ver los de todas las regiones.
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $CentroRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                } else {
                    $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
                }
            } else {
                // Si soy sf solo tengo acceso a los de la dl,
                // En caso contrario puedo ver los de todas las regiones.
                if (ConfigGlobal::mi_sfsv() === 2) {
                    $CentroRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
                } else {
                    $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
                }
            }
            $oCentro = $CentroRepository->findById($id_ubi);
            if ($oCentro !== null) {
                $tipo_ubi = $oCentro->getTipo_ubi();
                switch ($tipo_ubi) {
                    case 'ctrdl':
                        $oCentro = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->findById($id_ubi);
                        break;
                    case 'ctrex':
                        $oCentro = $GLOBALS['container']->get(CentroExRepositoryInterface::class)->findById($id_ubi);
                        break;
                    default:
                        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                        exit ($err_switch);
                }
                $oUbi = $oCentro;
            } else {
                $oUbi = $GLOBALS['container']->get(CasaRepositoryInterface::class)->findById($id_ubi);
            }
        }
        return $oUbi;
    }
}