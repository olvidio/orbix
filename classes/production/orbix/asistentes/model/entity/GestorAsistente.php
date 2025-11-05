<?php
namespace asistentes\model\entity;

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use core\ClaseGestor;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use web\Desplegable;

/**
 * GestorAsistente
 *
 * Classe per gestionar la llista d'objectes de la clase Asistente
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorAsistente extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
        //$oDbl = $GLOBALS['oDB'];
        //$this->setoDbl($oDbl);
        //$this->setNomTabla('d_asistentes_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna un objecte del tipus Desplegable
     * Les posibles asignatures
     *
     * @return array|object
     */
    function getOpcionesPosiblesPlaza()
    {
        $aOpciones[Asistente::PLAZA_PEDIDA] = _("pedida");
        $aOpciones[Asistente::PLAZA_EN_ESPERA] = _("en espera");
//		$aOpciones[Asistente::PLAZA_DENEGADA] = _("denegada");
        $aOpciones[Asistente::PLAZA_ASIGNADA] = _("asignada");
        $aOpciones[Asistente::PLAZA_CONFIRMADA] = _("confirmada");
        return $aOpciones;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Les posibles asignatures
     *
     * @return object del tipus Desplegable
     */
    function getPosiblesPlaza()
    {
        $aOpciones = $this->getOpcionesPosiblesPlaza();
        return new Desplegable('', $aOpciones, '', true);
    }

    /*
     * retorna l'array d'objectes de tipus Asistente
     *
     * @param array aWhereNom associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperadorNom associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @param boolean reverse: TRUE->ordenar por fecha de nuevo a viejo.
     * @return array Una col·lecció d'objectes de tipus Asistente
     */
    function getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhereActividad = [], $aOperadorActividad = [], $reverse = FALSE)
    {
        // todas las actividades de la persona
        $GesActividades = new GestorActividad();
        $a_id_activ_f_ini = $GesActividades->getArrayIdsWithKeyFini($aWhereActividad, $aOperadorActividad);

        //Importa el orden, se queda con la primera.
        $a_Clases[] = array('clase' => 'AsistenteDl', 'get' => 'getAsistentesDl');
        /*
        $a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
         *  El In es suma de Ex(de paso) + Out(de todas las dl menos de mi propia dl).
        */
        //$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
        $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
        $a_Clases[] = array('clase' => 'AsistenteEx', 'get' => 'getAsistentesEx');

        $namespace = __NAMESPACE__;
        $cAsistencias = $this->getConjunt($a_Clases, $namespace, $aWhereNom, $aOperadorNom);

        return $this->ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse);
    }

    function getAsistenciasPersonaDeActividades($id_nom, $a_id_activ_f_ini, $reverse = FALSE)
    {
        $aWhereNom['id_nom'] = $id_nom;
        $aOperadorNom = [];
        //Importa el orden, se queda con la primera.
        $a_Clases[] = array('clase' => 'AsistenteDl', 'get' => 'getAsistentesDl');
        /*
        $a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
         *  El In es suma de Ex(de paso) + Out(de todas las dl menos de mi propia dl).
        */
        //$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
        $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
        $a_Clases[] = array('clase' => 'AsistenteEx', 'get' => 'getAsistentesEx');

        $namespace = __NAMESPACE__;
        $cAsistencias = $this->getConjunt($a_Clases, $namespace, $aWhereNom, $aOperadorNom);

        return $this->ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse);
    }

    private function ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse)
    {
        // descarto los que no están.
        $cActividadesOk = [];
        $id_actividad_old = 0;
        foreach ($cAsistencias as $oAsistente) {
            $id_activ = $oAsistente->getId_activ();
            $oAsistente->DBCarregar();
            // Si es la misma actividad salto.
            if ($id_activ === $id_actividad_old) {
                continue;
            }
            if ($key = array_search($id_activ, $a_id_activ_f_ini)) {
                $cActividadesOk[$key] = $oAsistente;
            }
            $id_actividad_old = $id_activ;
        }
        if ($reverse === true) {
            krsort($cActividadesOk);
        } else {
            ksort($cActividadesOk);
        }
        return $cActividadesOk;
    }

    /**
     * retorna numero de places ocupades
     *
     * @param integer iid_activ el id de l'activitat.
     * @param string sdl sigla de la dl
     * @param string dl_hub sigla de la dl propietaria de las plazas
     * @return integer
     */
    function getPlazasOcupadasPorDl($iid_activ, $sdl = '', $dl_hub = '')
    {
        $mi_dele = ConfigGlobal::mi_delef();
        /* Mirar si la actividad es mia o no */
        $oActividad = new ActividadAll($iid_activ);
        $dl_org = $oActividad->getDl_org();
        $id_tabla = $oActividad->getId_tabla();
        $aWhere['id_activ'] = $iid_activ;
        $aOperators = [];
        $namespace = __NAMESPACE__;
        $msg_err = '';

        if ($sdl == $mi_dele) {
            if ($dl_org == $sdl) {
                //$gesAsistenteDl = new GestorAsistenteDl();
                //$cAsistentes = $gesAsistenteDl->getAsistentesDl(array('id_activ'=>$iid_activ));
                $a_Clases[] = array('clase' => 'AsistenteDl', 'get' => 'getAsistentesDl');
                $a_Clases[] = array('clase' => 'AsistenteIn', 'get' => 'getAsistentesIn');
                $cAsistentes = $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
            } else {
                $a_Clases[] = array('clase' => 'AsistenteEx', 'get' => 'getAsistentesEx');
                $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
                $cAsistentes = $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
            }
        } else {
            // No hace falta saber las plazas ocupadas de otra dl.
            //return -1;
            //o si:
            if ($dl_org == $sdl) {
                $cAsistentes = [];
            } else {
                if ($dl_org == $mi_dele) {
                    //parece que ya estan en IN. $a_Clases[] = array('clase'=>'AsistenteEx','get'=>'getAsistentesEx');
                    $a_Clases[] = array('clase' => 'AsistenteIn', 'get' => 'getAsistentesIn');
                    $cAsistentes = $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
                    //$gesAsistenteIn = new GestorAsistenteIn();
                    //$cAsistentes = $gesAsistenteIn->getAsistentesIn(array('id_activ'=>$iid_activ));
                } else {
                    $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
                    $cAsistentes = $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
                }
            }
        }

        $numAsis = 0;
        foreach ($cAsistentes as $oAsistente) {
            $id_nom = $oAsistente->getId_nom();
            $propietario = $oAsistente->getPropietario() ?? '';
            $padre = strtok($propietario, '>');
            $child = strtok('>');
            //if ($sdl != $mi_dele) {
            if (!empty($dl_hub) && $dl_hub != $padre) continue;
            if ($sdl != $child) continue;
            $oPersona = Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $msg_err .= "<br>$oPersona en  " . __FILE__ . ": line " . __LINE__;
                $msg_err .= "<br>" . _("borro la asistencia");
                $oAsistente->DBEliminar();
                continue;
            }
            // También hay que contar a los de paso (ocupan plaza)
            /*$dl = $oPersona->getDl();
            if ($sdl != $dl) continue;
            */
            $plaza = empty($oAsistente->getPlaza()) ? Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();
            // sólo cuento las asignadas
            if ($plaza < Asistente::PLAZA_ASIGNADA) continue;
            $numAsis++;
        }
        if (!empty($msg_err)) {
            echo $msg_err;
        }
        return $numAsis;
    }

    /**
     * retorna l'array d'objectes de tipus Asistente
     *
     * @param integer iid_activ el id de l'activitat.
     * @param string sOrder(null) l'ordre que es vol. Per defecte: apellido1,apellido1,nom.
     * @return array Una col·lecció d'objectes de tipus Asistente
     */
    function getAsistentesDeActividad($iid_activ)
    {
        // Por el momento si está en la dmz no puede ver las asistencias:
        // Las de los sacd si
        /*
        if (ConfigGlobal::is_dmz()) {
            //return [];
        }
        */

        /* Mirar si la actividad es mia o no */
        $oActividad = new ActividadAll($iid_activ);
        $id_tabla = $oActividad->getId_tabla();
        // si es de la sf quito la 'f'
        $dl = preg_replace('/f$/', '', $oActividad->getDl_org());

        $aWhere['id_activ'] = $iid_activ;
        $aOperators = [];

        $msg_err = '';
        if ($dl == ConfigGlobal::mi_delef()) {
            // Todos los asistentes
            /* Buscar en los tres tipos de asistente: Dl, IN y Out. */
            $a_Clases[] = array('clase' => 'AsistenteDl', 'get' => 'getAsistentesDl');
            $a_Clases[] = array('clase' => 'AsistenteIn', 'get' => 'getAsistentesIn');
            $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
        } else {
            if ($id_tabla === 'dl') {
                $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
            } else {
                $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
                $a_Clases[] = array('clase' => 'AsistenteEx', 'get' => 'getAsistentesEx');
            }
        }
        $namespace = __NAMESPACE__;
        $cAsistentes = $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);

        $cAsistentesOk = [];
        foreach ($cAsistentes as $oAsistente) {
            $id_nom = $oAsistente->getId_nom();
            $oPersona = Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                continue;
            }
            $apellidos = $oPersona->getPrefApellidosNombre();
            $cAsistentesOk[$apellidos] = $oAsistente;
        }
        uksort($cAsistentesOk, "core\strsinacentocmp");
        if (!empty($msg_err)) {
            echo $msg_err;
        }
        return $cAsistentesOk;
    }

    /**
     * retorna l'array d'objectes de tipus Asistente
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Asistente
     */
    function getAsistentes($aWhere = [], $aOperators = array())
    {
        /* Mirar si la actividad es mia o no */
        $iid_activ = $aWhere['id_activ'];
        $oActividad = new ActividadAll($iid_activ);
        // si es de la sf quito la 'f'
        $dl = preg_replace('/f$/', '', $oActividad->getDl_org());
        $id_tabla = $oActividad->getId_tabla();
        if ($dl == ConfigGlobal::mi_delef()) {
            // Todos los asistentes
            /* Buscar en los tres tipos de asistente: Dl, IN y Out. */
            $a_Clases[] = array('clase' => 'AsistenteDl', 'get' => 'getAsistentesDl');
            $a_Clases[] = array('clase' => 'AsistenteIn', 'get' => 'getAsistentesIn');
            $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
        } else {
            if ($id_tabla === 'dl') {
                $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
                // 14.4.2021 añado a la lista a los de paso.
                // Después hay que filtrar y poner sólo los que se han asignado desde la dl
                $a_Clases[] = array('clase' => 'AsistenteEx', 'get' => 'getAsistentesEx');
                // 8.9.2022 para el caso de cr, para ver todos, también los de la dl propia
                if (ConfigGlobal::mi_ambito() === 'rstgr') {
                    $a_Clases[] = array('clase' => 'AsistenteDl', 'get' => 'getAsistentesDl');
                }
            } else {
                /*
                $a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
                $a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
                */
                $a_Clases[] = array('clase' => 'AsistenteOut', 'get' => 'getAsistentesOut');
                $a_Clases[] = array('clase' => 'AsistenteEx', 'get' => 'getAsistentesEx');
            }
        }
        $namespace = __NAMESPACE__;
        return $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
