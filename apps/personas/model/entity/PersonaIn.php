<?php

namespace personas\model\entity;
/**
 * Fitxer amb la Classe que accedeix a la taula Hdlb(v|f).pv_de_paso_in
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad pv_de_paso_in
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class PersonaIn extends PersonaPub
{

    /**
     * Para clacular la edad a partir de la fecha de nacimiento
     *
     *
     * @author    Daniel Serrabou
     * @since     25/11/2010.
     *
     */
    function getEdad()
    {
        $oF_nacimiento = $this->getF_nacimiento();
        if (!empty($oF_nacimiento)) {
            $m = (int) $oF_nacimiento->format('m');
            $a = (int) $oF_nacimiento->format('Y');
            $ah = (int) date("Y");
            $mh = (int) date("m");
            $inc_m = 0;
            $mh >= $m ? 0 : $inc_m = 1;
            $edad = $ah - $a - $inc_m;
        } else {
            $edad = "-";
        }
        return $edad;
    }
}
