<?php
namespace personas\legacy;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use personas\model\entity\PersonaDl;
use personas\model\entity\PersonaPub;
use web\Desplegable;

/**
 * GestorPersonaDl
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
abstract class GestorPersonaGlobal extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private $sApeNom = "apellido1||
	case when nx2 = '' or nx2 isnull then ' ' else ' '||nx2||' ' end 
	||
	case when apellido2 = '' or apellido2 isnull then '' else ''||apellido2||'' end 
	||', '||
	case when trato isnull or trato = '' then '' else trato||' ' end 
	||COALESCE(apel_fam, nom)||
	case when nx1 = '' or nx1 isnull then '' else ' '||nx1||' ' end 
	";

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array amb els id cels ctr
     *
     * @param string sdonde (condición del sql. debe empezar por AND).
     * @return array
     */
    function getListaCtr($sdonde = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ctr
			FROM $nom_tabla
		   	WHERE situacion='A' $sdonde
			GROUP BY id_ctr
		   	";
        //echo "qry: $sQuery<br>";
        $aLista = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $aLista[$aDades['id_ctr']] = $aDades['id_ctr'];
        }
        return $aLista;
    }

    /**
     * retorna un array
     * Els posibles Sacd
     *
     * @param string sdonde (condición del sql. debe empezar por AND).
     * @return array|false
     */
    function getArraySacd($sdonde = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $aSacd = [];
        $sQuery = "SELECT id_nom, " . $this->sApeNom . " as ape_nom
		   	FROM $nom_tabla
		   	WHERE situacion='A' AND sacd='t' $sdonde
		   	ORDER by apellido1,apellido2,nom";
        //echo "qry: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorPersonaDl.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $aSacd[$aDades['id_nom']] = $aDades['ape_nom'];
        }
        return $aSacd;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles Sacd
     *
     * @param string sdonde (condición del sql. debe empezar por AND).
     * @return array|false
     */
    function getListaSacd($sdonde = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_nom, " . $this->sApeNom . " as ape_nom
		   	FROM $nom_tabla
		   	WHERE situacion='A' AND sacd='t' $sdonde
		   	ORDER by apellido1,apellido2,nom";
        //echo "qry: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorPersonaDl.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }

    function getArrayPersonas($id_tabla = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $aPersonas = [];
        if ($nom_tabla === 'p_de_paso_ex') {
            $Qry_tabla = empty($id_tabla) ? '' : "AND id_tabla = '$id_tabla'";
            $sQuery = "SELECT id_nom, " . $this->sApeNom . " || ' (' || p.dl || ')' as ape_nom
				FROM $nom_tabla p 
				WHERE p.situacion='A' $Qry_tabla
				ORDER by apellido1,apellido2,nom";
            //echo "qry: $sQuery<br>";
        } else {
            $sQuery = "SELECT id_nom, " . $this->sApeNom . " || ' (' ||   COALESCE(c.nombre_ubi, '-') || ')' as ape_nom
				FROM $nom_tabla p LEFT JOIN u_centros_dl c ON (c.id_ubi=p.id_ctr)
				WHERE p.situacion='A'
				ORDER by apellido1,apellido2,nom";
            //echo "qry: $sQuery<br>";
        }
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorPersonaDl.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $aPersonas[$aDades['id_nom']] = $aDades['ape_nom'];
        }
        return $aPersonas;
    }

    /**
     * retorna una llista id_nom=>apellidosNombre
     *
     * @param string sTabla
     * @return array|false
     */
    function getListaPersonas($id_tabla = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($nom_tabla === 'p_de_paso_ex') {
            $Qry_tabla = empty($id_tabla) ? '' : "AND id_tabla = '$id_tabla'";
            $sQuery = "SELECT id_nom, " . $this->sApeNom . " || ' (' || p.dl || ')' as ape_nom
				FROM $nom_tabla p 
				WHERE p.situacion='A' $Qry_tabla
				ORDER by apellido1,apellido2,nom";
            //echo "qry: $sQuery<br>";
        } else {
            $sQuery = "SELECT id_nom, " . $this->sApeNom . " || ' (' ||   COALESCE(c.nombre_ubi, '-') || ')' as ape_nom
				FROM $nom_tabla p LEFT JOIN u_centros_dl c ON (c.id_ubi=p.id_ctr)
				WHERE p.situacion='A'
				ORDER by apellido1,apellido2,nom";
            //echo "qry: $sQuery<br>";
        }
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorPersonaDl.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus PersonaDl
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getPersonas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $clasename = get_class($this);
        $nomClase = implode('', array_slice(explode('\\', $clasename), -1));

        $oPersonaDlSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        //echo "query: $sQry<br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorPersonaDl.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorPersonaDl.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }

        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_nom' => $aDades['id_nom']);
            switch ($nomClase) {
                case 'GestorPersonaDl':
                    $oPersonaDl = new PersonaDl($a_pkey);
                    break;
                case 'GestorPersonaIn':
                case 'GestorPersonaStgr':
                    $a_pkey['id_schema'] = $aDades['id_schema'];
                    $oPersonaDl = new PersonaIn($a_pkey);
                    break;
                case 'GestorPersonaOut':
                    $oPersonaDl = new PersonaOut($a_pkey);
                    break;
                case 'GestorPersonaEx':
                    $oPersonaDl = new PersonaEx($a_pkey);
                    break;
                case 'GestorPersonaPub':
                    $oPersonaDl = new PersonaPub($a_pkey);
                    break;
                case 'GestorPersonaSacd':
                    $oPersonaDl = new PersonaSacd($a_pkey);
                    break;
                default:
                    $oPersonaDl = new PersonaDl($a_pkey);
            }
            $oPersonaDlSet->add($oPersonaDl);
        }
        return $oPersonaDlSet->getTot();
    }

    /**
     * retorna l'array d'objectes del tipus que se li passa com a paràmetre
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @param string Nom del objecte
     * @return array|void
     */
    function getPersonasObj($Obj, $aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        //echo "query: $sQry<br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorPersonaObj.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorPersonaObj.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_nom' => $aDades['id_nom']);
            if (str_contains($Obj, 'PersonaIn')) {
                $a_pkey['id_schema'] = $aDades['id_schema'];
            }
            $oPersona = new $Obj($a_pkey);
            $oPersonaSet->add($oPersona);
        }
        return $oPersonaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
