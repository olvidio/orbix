<?php

namespace personas\model\entity;

use core;
use ubis\model\entity\DescTeleco;

/**
 * Fitxer amb la Classe que accedeix a la taula pv_personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad pv_personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class Persona
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */


    /**
     * Constructor de la classe.
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public static function NewPersona($id_nom)
    {
        // para poder buscar sacd desde la sf
        if (core\ConfigGlobal::mi_sfsv() == 2) {
            if (substr($id_nom, 0, 1) == 1) {
                $gesPersonaDl = new GestorPersonaSacd();
            }
        } else {
            $gesPersonaDl = new GestorPersonaDl();
        }
        $cPersonasDl = $gesPersonaDl->getPersonas(array('id_nom' => $id_nom, 'situacion' => 'A'));
        if (count($cPersonasDl) > 0) {
            $oPersona = $cPersonasDl[0];
        } else {
            $gesPersonaEx = new GestorPersonaEx();
            $cPersonasEx = $gesPersonaEx->getPersonasEx(array('id_nom' => $id_nom, 'situacion' => 'A'));
            if (count($cPersonasEx) > 0) {
                $oPersona = $cPersonasEx[0];
            } else {
                $gesPersonaIn = new GestorPersonaIn();
                $cPersonasIn = $gesPersonaIn->getPersonasIn(array('id_nom' => $id_nom, 'situacion' => 'A'));
                if (count($cPersonasIn) > 0) {
                    $oPersona = $cPersonasIn[0];
                } else {
                    //Puede ser que este buscando una personaDl con situacion != 'A'
                    $cPersonasDl = $gesPersonaDl->getPersonas(array('id_nom' => $id_nom));
                    if (count($cPersonasDl) > 0) {
                        $oPersona = $cPersonasDl[0];
                    } else {
                        // o de otra dl.
                        $gesPersonaIn = new GestorPersonaIn();
                        $cPersonasIn = $gesPersonaIn->getPersonasIn(array('id_nom' => $id_nom));
                        if (count($cPersonasIn) > 0) {
                            $oPersona = $cPersonasIn[0];
                        } else {
                            return sprintf(_("no encuentro a nadie con id: %s"), $id_nom);
                        }
                    }
                }
            }
        }
        return $oPersona;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/


}
