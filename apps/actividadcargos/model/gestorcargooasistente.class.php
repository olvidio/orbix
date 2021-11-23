<?php
namespace actividadcargos\model;

use core\Set;

/**
 * GestorCargoOAsistente
 *
 * Classe per gestionar la llista d'objectes de la clase CargoOAsistente
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorCargoOAsistente {
	/* ATRIBUTS ----------------------------------------------------------------- */

    /**
     * conexión a l a base de datos PDO
     *
     * @var \PDO
     */
    private $oDbl;

    /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
        $this->oDbl = $GLOBALS['oDBE'];
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna l'array d'objectes tipus CargoOAsistente
	 *
	 * @param integer id_nom
	 * @return array Una col·lecció d'objectes de tipus CargoOAsistente
	 */
	function getCargoOAsistente($iid_nom) {
		$oDbl = $this->oDbl;
		
		$oCargoOAsistenteSet = new Set();
		// lista de id_activ ordenados, primero los propios.
		$sQuery="SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_dl WHERE id_nom=$iid_nom
					UNION ALL
		        SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_out WHERE id_nom=$iid_nom
					UNION ALL
				SELECT id_activ,'f' as propio,id_cargo FROM d_cargos_activ_dl WHERE id_nom=$iid_nom
				ORDER BY 1,2 DESC";
		  
		if (($oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorCargoOAsistente.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aRepe=array();
		$c=0;
		foreach ($oDbl->query($sQuery) as $aDades) {
			if (in_array($aDades['id_activ'],$aRepe)) { // si está repetido, el primero tiene propio=true.
				// Añado al primero el id_cargo del segundo.
				$Obj =$oCargoOAsistenteSet->getElement($c-1);
				$Obj->setId_cargo($aDades['id_cargo']);
				$oCargoOAsistenteSet->setElement($c-1,$Obj);
				continue;
			}
			$oCargoOAsistente= new CargoOAsistente($aDades['id_activ']);
			$oCargoOAsistente->setId_nom($iid_nom);
			$oCargoOAsistente->setPropio($aDades['propio']);
			$oCargoOAsistenteSet->add($oCargoOAsistente);
			$aRepe[]=$aDades['id_activ'];
			$c++;
		}
		return $oCargoOAsistenteSet->getTot();
	}

	function getSolapes($cPersonas, $cActividades) {
	    $oDbl = $this->oDbl;
	    
	    $tabla_tmp = 'tmp_activ_solape';
	    $tabla_p_tmp = 'tmp_sacd_solape';
	    
	    $sqlCreateA="CREATE TEMP TABLE $tabla_tmp (
						id_activ bigint,
						f_ini date, 
                        f_fin date,
                        id_ubi integer
					 )";
	    
	    $oDbl->query($sqlCreateA);
	    
	    $sqlCreateP="CREATE TEMP TABLE $tabla_p_tmp (
						id SERIAL,
                        id_nom integer,
						id_activ bigint,
						f_ini date, 
                        f_fin date,
                        id_ubi integer
					 )";
	    
	    $oDbl->query($sqlCreateP);
	    
        $sql = "INSERT INTO $tabla_tmp (id_activ, f_ini, f_fin, id_ubi) VALUES (:id_activ, :f_ini, :f_fin, :id_ubi);";
	    $sentencia_1 = $oDbl->prepare($sql);
	    foreach ($cActividades as $oActividad) {
	       $aDadesActiv['id_activ'] = $oActividad->getId_activ();
	       $aDadesActiv['f_ini'] = $oActividad->getF_ini()->getIso();
	       $aDadesActiv['f_fin'] = $oActividad->getF_fin()->getIso();
	       $aDadesActiv['id_ubi'] = $oActividad->getId_ubi();
	       
	       $sentencia_1->execute($aDadesActiv);
	    }
	    
	    
        $sql2 = "INSERT INTO $tabla_p_tmp (id_nom, id_activ, f_ini, f_fin, id_ubi) VALUES (:id_nom, :id_activ, :f_ini, :f_fin, :id_ubi);";
	    $sentencia_2 = $oDbl->prepare($sql2);
	    foreach ($cPersonas as $oPersona) {
	        $id_nom = $oPersona->getId_nom();
            $sQuery="SELECT d.id_activ, d.propio, 0 as id_cargo, a.f_ini, a.f_fin, a.id_ubi
                        FROM d_asistentes_dl d JOIN $tabla_tmp a USING (id_activ) WHERE id_nom=$id_nom
                        UNION ALL
                    SELECT d.id_activ, d.propio, 0 as id_cargo, a.f_ini, a.f_fin, a.id_ubi
                        FROM d_asistentes_out d JOIN $tabla_tmp a USING (id_activ) WHERE id_nom=$id_nom
                        UNION ALL
                    SELECT d.id_activ, 'f' as propio, d.id_cargo, a.f_ini, a.f_fin, a.id_ubi 
                        FROM d_cargos_activ_dl d JOIN $tabla_tmp a USING (id_activ) WHERE id_nom=$id_nom
                    ORDER BY 1,2 DESC";
              
            if (($cargosoAsistencias = $oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorCargoOAsistente.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            
            $aRepe = [];
            $c = 0;
            foreach ($cargosoAsistencias as $aDades) {
                if (in_array($aDades['id_activ'],$aRepe)) { // si está repetido, el primero tiene propio=true.
                    continue;
                }
                $aDadesSacd['id_nom'] = $id_nom;
                $aDadesSacd['id_activ'] = $aDades['id_activ'];
                $aDadesSacd['f_ini'] =  $aDades['f_ini'];
                $aDadesSacd['f_fin'] =  $aDades['f_fin'];
                $aDadesSacd['id_ubi'] =  $aDades['id_ubi'];
                // insertar datos en la tabla $tabla_p_tmp:
                $sentencia_2->execute($aDadesSacd);
                
                $aRepe[]=$aDades['id_activ'];
                $c++;
            }
	    }
	    
	    
	    // solapes:
	    // More details can be found in the manual:
	    // http://www.postgresql.org/docs/current/static/rangetypes.html#RANGETYPES-INCLUSIVITY
	    $sQuery = "
                select f1.*
                from $tabla_p_tmp f1
                where exists (select 1
                    from $tabla_p_tmp f2
                    where tsrange(f2.f_ini, f2.f_fin, '[]') && tsrange(f1.f_ini, f1.f_fin, '[]')
                    and f2.id_nom = f1.id_nom
                    and f2.id <> f1.id);
        ";
        if (($solapes = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCargoOAsistente.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        
        $a_solapes = [];
        $id_nom_anterior = '';
        $id_ubi_anterior = '';
        $f_fin_anterior = '';
        foreach ($solapes as $aDades) {
            $id_nom =  $aDades['id_nom'];
            $id_activ = $aDades['id_activ'];
            $f_ini =  $aDades['f_ini'];
            $f_fin =  $aDades['f_fin'];
            $id_ubi =  $aDades['id_ubi'];
        
            if ($id_nom == $id_nom_anterior) {
                // si es el mismo dia y el mismo ubi, no lo pongo:
                if (!( ($f_fin_anterior == $f_ini) && ($id_ubi_anterior == $id_ubi) )) {
                    array_push($a_solapes[$id_nom],$id_activ);
                }
                
            } else {
                $a_solapes[$id_nom] = [$id_activ];
            }
            $id_nom_anterior = $id_nom;
            $id_ubi_anterior = $id_ubi;
            $f_fin_anterior = $f_fin;
        }
        
        return $a_solapes;
	    
	}
	/* METODES PROTECTED --------------------------------------------------------*/
		
	/* METODES GET i SET --------------------------------------------------------*/

}