<?php

namespace src\ubis\domain\entity;

use core\ConfigGlobal;
use src\ubis\application\repositories\CasaRepository;
use src\ubis\application\repositories\CentroDlRepository;
use src\ubis\application\repositories\CentroEllasRepository;
use src\ubis\application\repositories\CentroEllosRepository;
use src\ubis\application\repositories\CentroExRepository;
use src\ubis\application\repositories\CentroRepository;

/**
 * Clase que implementa la entidad ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
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


    public static function NewUbi($id_ubi)
    {
        // para la sf (comienza por 2).
        if ((int)substr($id_ubi, 0, 1) === 2) {
            // Si soy sv solo tengo acceso a los de la dl,
            // En caso contrario puedo ver los de todas las regiones.
            if (ConfigGlobal::mi_sfsv() === 1) {
                $CentroRepository = new CentroEllasRepository();
            } else {
                $CentroRepository = new CentroRepository();
            }
        } else {
            // Si soy sf solo tengo acceso a los de la dl,
            // En caso contrario puedo ver los de todas las regiones.
            if (ConfigGlobal::mi_sfsv() === 2) {
                $CentroRepository = new CentroEllosRepository();
            } else {
                $CentroRepository = new CentroRepository();
            }
        }
        $oCentro = $CentroRepository->findById($id_ubi);
        if (!empty($oCentro)) {
            $tipo_ubi = $oCentro->getTipo_ubi();
            switch ($tipo_ubi) {
                case 'ctrdl':
                    $oCentro = (new CentroDlRepository())->findById($id_ubi);
                    break;
                case 'ctrex':
                    $oCentro = (new CentroExRepository())->findById($id_ubi);
                    break;
                default:
                    $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                    exit ($err_switch);
            }
            $oUbi = $oCentro;
        } else {
            $oUbi = (new CasaRepository())->findById($id_ubi);
        }
        return $oUbi;
    }
}