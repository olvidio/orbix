<?php
namespace encargossacd\model;

use encargossacd\model\entity\EncargoSacd;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorPropuestaEncargoSacdHorario;
use encargossacd\model\entity\GestorPropuestaEncargosSacd;
use personas\model\entity\PersonaSacd;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use encargossacd\model\entity\EncargoHorario;
use encargossacd\model\entity\EncargoSacdHorario;
/**
 * GestorEncargoSacd
 *
 * Classe per gestionar la llista d'objectes de la clase EncargoSacd
 *
 * @package orbix
 * @subpackage encargossacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */

class GestorPropuestas {
	/* ATRIBUTS ----------------------------------------------------------------- */
    
	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
	  
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	public function comprobarHorario($id_item,$f_iso) {
	    $gesActualEncargoSacdHorario = new GestorEncargoSacdHorario();
	    $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
	    $aWhere = ['id_item_tarea_sacd' => $id_item, 'id_nom' => 'x'];
	    $aOperador = ['id_nom' => 'IS NOT NULL'];
	    $cPropuestaEncargoSacdHorarios = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
	    foreach($cPropuestaEncargoSacdHorarios as $oPropuestaEncargoSacdHorario) {
	        $dia_ref = $oPropuestaEncargoSacdHorario->getDia_ref();
	        $dia_inc = $oPropuestaEncargoSacdHorario->getDia_inc();
	        $aWhereActual['id_item_tarea_sacd'] = $id_item;
	        $aWhereActual['dia_ref'] = $dia_ref;
	        $aWhereActual['id_nom'] = 'x';
	        $aWhereActual['f_fin'] = 'x';
            $aOperadorActual['id_nom'] = 'IS NOT NULL';
            $aOperadorActual['f_fin'] = 'IS NULL';
            $cActualHorario = $gesActualEncargoSacdHorario->getEncargoSacdHorarios($aWhereActual,$aOperadorActual);
            if (count($cActualHorario) > 0 ) {
                $oEncargoSacdHorario = $cActualHorario[0];
                $dia_inc_actual = $oEncargoSacdHorario->getDia_inc();
                if ($dia_inc != $dia_inc_actual) {
                    // update
                    $oEncargoSacdHorario->setDia_inc($dia_inc);
                    $oEncargoSacdHorario->DBGuardar();
                }
            } else { // nuevo
                $id_enc = $oPropuestaEncargoSacdHorario->getId_enc();
                $id_nom = $oPropuestaEncargoSacdHorario->getId_nom();
                $oNewHorario = new EncargoSacdHorario();
                $oNewHorario->setId_enc($id_enc);
                $oNewHorario->setId_nom($id_nom);
                $oNewHorario->setF_ini($f_iso,FALSE);
                $oNewHorario->setDia_ref($dia_ref);
                $oNewHorario->setDia_inc($dia_inc);
                $oNewHorario->setId_item_tarea_sacd($id_item);
                $oNewHorario->DBGuardar();
            }
	    }
	}

	public function newEncargo($oPropuestaEncargoSacd,$f_iso) {
	    $id_nom_new = $oPropuestaEncargoSacd->getId_nom_new();
	    $id_item = $oPropuestaEncargoSacd->getId_item();
	    $id_enc = $oPropuestaEncargoSacd->getId_enc();
	    $modo = $oPropuestaEncargoSacd->getModo();
	    
	    // Puede ya existir: si se hace más de una vez el 'aprobar'.
	    
	    $oEncargoSacd = new EncargoSacd();
	    $oEncargoSacd->setId_enc($id_enc);
	    $oEncargoSacd->setId_nom($id_nom_new);
	    $oEncargoSacd->setModo($modo);
	    $oEncargoSacd->setF_ini($f_iso, FALSE);
	    $oEncargoSacd->DBGuardar();
	    $id_item_new = $oEncargoSacd->getId_item();
	    // horario
        $this->newHorario($id_item,$id_enc,$id_nom_new,$f_iso,$id_item_new);
	}
	
	private function newHorario($id_item,$id_enc,$id_nom_new,$f_iso,$id_item_new) {
	    $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
	    $aWhere = ['id_item_tarea_sacd' => $id_item, 'id_nom' => 'x'];
	    $aOperador = ['id_nom' => 'IS NOT NULL']; 
	    $cPropuestaEncargoHorarios = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
	    foreach($cPropuestaEncargoHorarios as $oPropuestaEncargoHorario) {
	        $dia_ref = $oPropuestaEncargoHorario->getDia_ref();
	        $dia_inc = $oPropuestaEncargoHorario->getDia_inc();
	        $oNewHorario = new EncargoSacdHorario();
	        $oNewHorario->setId_enc($id_enc);
	        $oNewHorario->setId_nom($id_nom_new);
	        $oNewHorario->setF_ini($f_iso,FALSE);
	        $oNewHorario->setDia_ref($dia_ref);
	        $oNewHorario->setDia_inc($dia_inc);
	        $oNewHorario->setId_item_tarea_sacd($id_item_new);
	        $oNewHorario->DBGuardar();
	    }
	}
	
	public function finEncargo($id_item,$f_iso) {
	    $oEncargoSacd = new EncargoSacd($id_item);
        $oEncargoSacd->DBCarregar();
        $oEncargoSacd->setF_fin($f_iso, FALSE);
        $oEncargoSacd->DBGuardar();
        // horario:
        $this->finHorario($id_item,$f_iso);
        
	}
	private function finHorario($id_item,$f_iso) {
	    $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
	    $aWhere = ['id_item_tarea_sacd' => $id_item, 'id_nom' => 'x'];
	    $aOperador = ['id_nom' => 'IS NOT NULL']; 
	    $cPropuestaEncargoHorarios = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
	    foreach($cPropuestaEncargoHorarios as $oPropuestaEncargoHorario) {
	        $oPropuestaEncargoHorario->setF_fin($f_iso,FALSE);
	        $oPropuestaEncargoHorario->DBGuardar();
	    }
	}
	
	
	public function getListaSimple($filtro_ctr) {
	    
	    switch ($filtro_ctr) {
	        case 1:
	            $GesCentros = new GestorCentroDl();
	            // añado el ctr de oficiales de la dl (14.VIII.07).
	            $aWhere['tipo_ctr'] = 'a.|n.|s[j|m]|of';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	            break;
	        case 2:
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $GesCentros = new GestorCentroEllas();
	            $cCentros = $GesCentros->getCentros($aWhere);
	            break;
	        case 3:
	            $GesCentros = new GestorCentroDl();
	            $aWhere['tipo_ctr'] = 'ss';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	            break;
	        case 4:
	            $GesCentros = new GestorCentroDl();
	            $aWhere['tipo_ctr'] = 'igl';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	            break;
	        case 5:
	            $GesCentros = new GestorCentroDl();
	            $aWhere['tipo_ctr'] = 'cgioc|oc|cgi';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros_sv = $GesCentros->getCentros($aWhere,$aOperador);
	            $GesCentros = new GestorCentroEllas();
	            $aWhere['tipo_ctr'] = 'cgioc|oc|cgi';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros_sf = $GesCentros->getCentros($aWhere,$aOperador);

	            $cCentros = array_merge($cCentros_sv,$cCentros_sf);
	            break;
	        default:
	            $cCentros = [];
	            $GesCentros = new GestorCentroDl();
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'tipo_ctr, nombre_ubi';
	            $cCentros_sv = $GesCentros->getCentros($aWhere);
	            $GesCentros = new GestorCentroEllas();
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'tipo_ctr, nombre_ubi';
	            $cCentros_sf = $GesCentros->getCentros($aWhere);

	            $cCentros = array_merge($cCentros_sv,$cCentros_sf);
	    }
	    
	    $html = '';
	    foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            
            $html .= $this->getEncargosUbiSimple($id_ubi);
	    }
	    
	    return $html;
	}

	public function getEncargosUbiSimple($id_ubi) {
	    /* busco los datos del encargo que se tengan, para los tipos de encargo de atención de centros: 100,1100,1200,1300,2100,2200,3000. */
	    $aWhere = [];
	    $aOperador = [];
	    $GesEncargos = new GestorEncargo();
	    $aWhere['id_ubi'] = $id_ubi;
	    $aWhere['id_tipo_enc'] = '(1|2|3).0';
	    $aOperador['id_tipo_enc'] = '~';
	    $cEncargos = $GesEncargos->getEncargos($aWhere,$aOperador);
	    $e=0;
	    foreach ($cEncargos as $oEncargo) {
	        $e++;
	        $id_enc = $oEncargo->getId_enc();
	        $a_id_enc[$e] = $id_enc;
	        $id_tipo_enc[$e] = $oEncargo->getId_tipo_enc();
	        
	        $oEncargoTipo  = new EncargoTipo();
	        $oEncargoTipo->setId_tipo_enc($id_tipo_enc[$e]);
	        $oEncargoTipo->DBCarregar();
	        
            // sacd
            $GesEncargoSacd = new GestorPropuestaEncargosSacd();
            $aWhere = array();
            $aOperador = array();
            $aWhere['id_enc'] = $a_id_enc[$e];
            $aWhere['f_fin'] = 'x';
            $aOperador['f_fin'] = 'IS NULL';
            $aWhere['_ordre'] = 'modo,f_ini DESC';
            $cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere,$aOperador);
            $actual_id_sacd_titular[$e] = '';
            $new_id_sacd_titular[$e] = '';
            $id_item_titular[$e] = '';
            $actual_id_sacd_suplente[$e] = '';
            $new_id_sacd_suplente[$e] = '';
            $id_item_suplente[$e] = '';
            $actual_id_sacd_colaborador[$e] = [];
            $new_id_sacd_colaborador[$e] = [];
            $id_item_colaborador[$e] = [];
            $s=0;
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $modo = $oEncargoSacd->getModo();
                $id_sacd = $oEncargoSacd->getId_nom();
                $id_sacd_new = $oEncargoSacd->getId_nom_new();
                $id_item = $oEncargoSacd->getId_item();
                switch($modo){
                    case 2: // titular del cl
                    case 3: // titular no del cl
                        $actual_id_sacd_titular[$e] = $id_sacd;
                        $new_id_sacd_titular[$e] = $id_sacd_new;
                        $id_item_titular[$e] = $id_item;
                        break;
                    case 4: // suplente
                        $actual_id_sacd_suplente[$e] = $id_sacd;
                        $new_id_sacd_suplente[$e] = $id_sacd_new;
                        $id_item_suplente[$e] = $id_item;
                        break;
                    case 5: // colaborador
                        $s++;
                        $actual_id_sacd_colaborador[$e][$s] = $id_sacd;
                        $new_id_sacd_colaborador[$e][$s] = $id_sacd_new;
                        $id_item_colaborador[$e][$s] = $id_item;
                        break;
                }
            }
	    }
	    
	    /* lista sacd posibles */
        $html = '';
	    $e=0;
	    foreach ($cEncargos as $oEncargo) {
	        $e++;
	        $desc_encargo = $oEncargo->getDesc_enc();
	        $id_enc = $oEncargo->getId_enc();
                        
	        $html .= '<table><tr><td colspan=5>';
	        $html .= "<b>$desc_encargo</b>";
	        $html .= '</td></tr>';
	        // titular:
	        $id_sacd = $actual_id_sacd_titular[$e];
	        $id_sacd_new = $new_id_sacd_titular[$e];
	        $id_item = $id_item_titular[$e];
	        $oPersonaSacd = new PersonaSacd($id_sacd);
	        $nom_titular = $oPersonaSacd->getApellidosNombre();
	        $oPersonaSacdNew = new PersonaSacd($id_sacd_new);
	        $nom_titular_new = $oPersonaSacdNew->getApellidosNombre();
	        $nom_titular_new = empty($nom_titular_new)? '-' : $nom_titular_new;
	        
	        $class = ($id_sacd != $id_sacd_new)? 'sf' : '';
	        $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
	        $html .= _("titular");
	        $html .= '</td><td>';
	        $html .= $nom_titular;
	        $html .= '  ('. $this->getHorarioActualTxt($id_enc,$id_sacd) .')';
	        $html .= "</td><td>";
            $html .= $nom_titular_new;
            $html .= '  ('. $this->getHorarioPropuestaTxt($id_enc,$id_sacd_new) .')';
	        $html .= "</td>";
	        $html .= '</td></tr>';
	        
	        // suplente:
	        $id_sacd = $actual_id_sacd_suplente[$e];
	        $id_sacd_new = $new_id_sacd_suplente[$e];
	        $id_item = $id_item_suplente[$e];
	        $id_item = empty($id_item)? '1' : $id_item; // caso de generar uno nuevo.
	        $oPersonaSacd = new PersonaSacd($id_sacd);
	        $nom_suplente = $oPersonaSacd->getApellidosNombre();
	        $oPersonaSacdNew = new PersonaSacd($id_sacd_new);
	        $nom_suplente_new = $oPersonaSacdNew->getApellidosNombre();
	        $nom_suplente_new = empty($nom_suplente_new)? '-' : $nom_suplente_new;

	        $class = ($id_sacd != $id_sacd_new)? 'sf' : '';
	        $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
	        $html .= _("suplente");
	        $html .= '</td><td>';
	        $html .= $nom_suplente;
	        $html .= "</td><td>";
            $html .= $nom_suplente_new;
	        $html .= '</td></tr>';
	        
	        // colaboradores:
	        $s = 0;
	        foreach ($actual_id_sacd_colaborador[$e] as $id_sacd) {
	            $s++;
	            $id_sacd_new = $new_id_sacd_colaborador[$e][$s];
                $id_item = $id_item_colaborador[$e][$s];
                $oPersonaSacd = new PersonaSacd($id_sacd);
                $nom_col = $oPersonaSacd->getApellidosNombre();
                
                $oPersonaSacdNew = new PersonaSacd($id_sacd_new);
                $nom_sacd_new = $oPersonaSacdNew->getApellidosNombre();
                $nom_sacd_new = empty($nom_sacd_new)? '-' : $nom_sacd_new;
                
                $class = ($id_sacd != $id_sacd_new)? 'sf' : '';
                if ($s < 2) {
                    $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
                    $html .= _("colaboradores");
                } else {
                    $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
                }
                $html .= '</td><td>';
                $html .= $nom_col;
                if (!empty($nom_col)) {
                    $html .= '  ('. $this->getHorarioActualTxt($id_enc,$id_sacd) .')';
                }
                $html .= "</td><td>";
                $html .= $nom_sacd_new;
                $html .= '  ('. $this->getHorarioPropuestaTxt($id_enc,$id_sacd_new) .')';
                $html .= '</td></tr>';
	        }
	        
            $html .= '</table>';
	    }
            
        return $html;
	    
	}

	public function getLista($filtro_ctr) {
	    
	    switch ($filtro_ctr) {
	        case 1:
	            $GesCentros = new GestorCentroDl();
	            // añado el ctr de oficiales de la dl (14.VIII.07).
	            $aWhere['tipo_ctr'] = 'a.|n.|s[j|m]|of';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	            break;
	        case 2:
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $GesCentros = new GestorCentroEllas();
	            $cCentros = $GesCentros->getCentros($aWhere);
	            break;
	        case 3:
	            $GesCentros = new GestorCentroDl();
	            $aWhere['tipo_ctr'] = 'ss';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	            break;
	        case 4:
	            $GesCentros = new GestorCentroDl();
	            $aWhere['tipo_ctr'] = 'igl';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	            break;
	        case 5:
	            $GesCentros = new GestorCentroDl();
	            $aWhere['tipo_ctr'] = 'cgioc|oc|cgi';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros_sv = $GesCentros->getCentros($aWhere,$aOperador);
	            $GesCentros = new GestorCentroEllas();
	            $aWhere['tipo_ctr'] = 'cgioc|oc|cgi';
                $aOperador['tipo_ctr'] = '~';
	            $aWhere['status'] = 't';
	            $aWhere['_ordre'] = 'nombre_ubi';
	            $cCentros_sf = $GesCentros->getCentros($aWhere,$aOperador);

	            $cCentros = array_merge($cCentros_sv,$cCentros_sf);
	            break;
	        default:
	            $cCentros = [];
	    }
	    
	    $html = '';
	    foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            
            $html .= $nombre_ubi;
            $html .= '<br>';
            $html .= $this->getEncargosUbi($id_ubi);
	    }
	    
	    return $html;
	}
	
	public function getEncargosUbi($id_ubi) {
	    /* busco los datos del encargo que se tengan, para los tipos de encargo de atención de centros: 100,1100,1200,1300,2100,2200,3000. */
	    $aWhere = [];
	    $aOperador = [];
	    $GesEncargos = new GestorEncargo();
	    $aWhere['id_ubi'] = $id_ubi;
	    $aWhere['id_tipo_enc'] = '(1|2|3).0';
	    $aOperador['id_tipo_enc'] = '~';
	    $cEncargos = $GesEncargos->getEncargos($aWhere,$aOperador);
	    $e=0;
	    foreach ($cEncargos as $oEncargo) {
	        $e++;
	        $id_enc = $oEncargo->getId_enc();
	        $a_id_enc[$e] = $id_enc;
	        $id_tipo_enc[$e] = $oEncargo->getId_tipo_enc();
	        
	        $oEncargoTipo  = new EncargoTipo();
	        $oEncargoTipo->setId_tipo_enc($id_tipo_enc[$e]);
	        $oEncargoTipo->DBCarregar();
	        
            // sacd
            $GesEncargoSacd = new GestorPropuestaEncargosSacd();
            $aWhere = array();
            $aOperador = array();
            $aWhere['id_enc'] = $a_id_enc[$e];
            $aWhere['f_fin'] = 'x';
            $aOperador['f_fin'] = 'IS NULL';
            $aWhere['_ordre'] = 'modo,f_ini DESC';
            $cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere,$aOperador);
            $actual_id_sacd_titular[$e] = '';
            $new_id_sacd_titular[$e] = '';
            $id_item_titular[$e] = '';
            $actual_id_sacd_suplente[$e] = '';
            $new_id_sacd_suplente[$e] = '';
            $id_item_suplente[$e] = '';
            $actual_id_sacd_colaborador[$e] = [];
            $new_id_sacd_colaborador[$e] = [];
            $id_item_colaborador[$e] = [];
            $s=0;
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $modo = $oEncargoSacd->getModo();
                $id_sacd = $oEncargoSacd->getId_nom();
                $id_sacd_new = $oEncargoSacd->getId_nom_new();
                $id_item = $oEncargoSacd->getId_item();
                switch($modo){
                    case 2: // titular del cl
                    case 3: // titular no del cl
                        $actual_id_sacd_titular[$e] = $id_sacd;
                        $new_id_sacd_titular[$e] = $id_sacd_new;
                        $id_item_titular[$e] = $id_item;
                        break;
                    case 4: // suplente
                        $actual_id_sacd_suplente[$e] = $id_sacd;
                        $new_id_sacd_suplente[$e] = $id_sacd_new;
                        $id_item_suplente[$e] = $id_item;
                        break;
                    case 5: // colaborador
                        $s++;
                        $actual_id_sacd_colaborador[$e][$s] = $id_sacd;
                        $new_id_sacd_colaborador[$e][$s] = $id_sacd_new;
                        $id_item_colaborador[$e][$s] = $id_item;
                        break;
                }
            }
	    }
	    
	    /* lista sacd posibles */
        $html = '';
	    $e=0;
	    foreach ($cEncargos as $oEncargo) {
	        $e++;
	        $desc_encargo = $oEncargo->getDesc_enc();
	        $id_enc = $oEncargo->getId_enc();
                        
	        $html .= '<table><tr><td colspan=5>';
	        $html .= "nom encarrec: $desc_encargo";
	        $html .= '</td></tr>';
	        // titular:
	        $id_sacd = $actual_id_sacd_titular[$e];
	        $id_sacd_new = $new_id_sacd_titular[$e];
	        $id_item = $id_item_titular[$e];
	        $oPersonaSacd = new PersonaSacd($id_sacd);
	        $nom_titular = $oPersonaSacd->getApellidosNombre();
	        $oPersonaSacdNew = new PersonaSacd($id_sacd_new);
	        $nom_titular_new = $oPersonaSacdNew->getApellidosNombre();
	        $nom_titular_new = empty($nom_titular_new)? _("nuevo") : $nom_titular_new;
	        
	        $class = ($id_sacd != $id_sacd_new)? 'sf' : '';
	        $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
	        $html .= _("titular");
	        $html .= '</td><td>';
	        $html .= $nom_titular;
	        $html .= '  ('. $this->getHorarioActualTxt($id_enc,$id_sacd) .')';
	        $html .= "</td><td>";
	        $html .= "<span class=\"link\" id=\"titular_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('titular',$id_item,$id_enc)\">";
            $html .= "$nom_titular_new</span>";
	        $html .= '</td><td>';
	        $html .= "<span class=\"link\" onClick=\"fnjs_info('titular',$id_item)\">"._("+ info")."</span>";
	        $html .= '</td><td>';
	        $html .= "<span class=\"link\" onClick=\"fnjs_dedicacion('titular',$id_item,$id_enc)\">";
            $html .= $this->getHorarioPropuestaTxt($id_enc,$id_sacd_new);
            $html .= "</span>";
	        $html .= "</td><td id=\"td_$id_item\">";
	        $html .= '</td></tr>';
	        
	        // suplente:
	        $id_sacd = $actual_id_sacd_suplente[$e];
	        $id_sacd_new = $new_id_sacd_suplente[$e];
	        $id_item = $id_item_suplente[$e];
	        $id_item = empty($id_item)? '1' : $id_item; // caso de generar uno nuevo.
	        $oPersonaSacd = new PersonaSacd($id_sacd);
	        $nom_suplente = $oPersonaSacd->getApellidosNombre();
	        $oPersonaSacdNew = new PersonaSacd($id_sacd_new);
	        $nom_suplente_new = $oPersonaSacdNew->getApellidosNombre();
	        $nom_suplente_new = empty($nom_suplente_new)? _("nuevo") : $nom_suplente_new;

	        $class = ($id_sacd != $id_sacd_new)? 'sf' : '';
	        $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
	        $html .= _("suplente");
	        $html .= '</td><td>';
	        $html .= $nom_suplente;
	        $html .= "</td><td>";
	        $html .= "<span class=\"link\" id=\"suplente_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('suplente',$id_item,$id_enc)\">";
            $html .= "$nom_suplente_new</span>";
	        $html .= '</td><td>';
	        $html .= "<span class=\"link\" onClick=\"fnjs_info('suplente',$id_item)\">"._("+ info")."</span>";
	        $html .= '</td><td>';
	        $html .= "</td><td id=\"td_$id_item\">";
	        $html .= '</td></tr>';
	        
	        // colaboradores:
	        $s = 0;
	        foreach ($actual_id_sacd_colaborador[$e] as $id_sacd) {
	            $s++;
	            $id_sacd_new = $new_id_sacd_colaborador[$e][$s];
                $id_item = $id_item_colaborador[$e][$s];
                $oPersonaSacd = new PersonaSacd($id_sacd);
                $nom_col = $oPersonaSacd->getApellidosNombre();
                
                $oPersonaSacdNew = new PersonaSacd($id_sacd_new);
                $nom_sacd_new = $oPersonaSacdNew->getApellidosNombre();
                $nom_sacd_new = empty($nom_sacd_new)? _("nuevo") : $nom_sacd_new;
                
                $class = ($id_sacd != $id_sacd_new)? 'sf' : '';
                if ($s < 2) {
                    $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
                    $html .= _("colaboradores");
                } else {
                    $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
                }
                $html .= '</td><td>';
                $html .= $nom_col;
                if (!empty($nom_col)) {
                    $html .= '  ('. $this->getHorarioActualTxt($id_enc,$id_sacd) .')';
                }
                $html .= "</td><td>";
                $html .= "<span class=\"link\" id=\"colaborador_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('colaborador',$id_item,$id_enc)\">";
                $html .= "$nom_sacd_new</span>";
                $html .= '</td><td>';
                $html .= "<span class=\"link\" onClick=\"fnjs_info('colaborador',$id_item)\">"._("+ info")."</span>";
                $html .= '</td><td>';
                $html .= "<span class=\"link\" onClick=\"fnjs_dedicacion('colaborador',$id_item,$id_enc)\">";
                $html .= $this->getHorarioPropuestaTxt($id_enc,$id_sacd_new);
                $html .= "</td><td id=\"td_$id_item\">";
                $html .= '</td></tr>';
	        }
	        //Fila de nuevo colaborador:
	        $id_sacd = 1;
	        $id_sacd_new = 1;
	        $id_item = 1;
	        $nom_col = '';
            $nom_sacd_new = _("nuevo");
            if ($s < 1) {
                $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
                $html .= _("colaboradores");
            } else {
                $html .= "<tr id=\"tr_$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
            }
            $html .= '</td><td>';
            $html .= $nom_col;
            $html .= "</td><td>";
            $html .= "<span class=\"link\" id=\"colaborador_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('colaborador',$id_item,$id_enc)\">";
            $html .= "$nom_sacd_new</span>";
            $html .= '</td><td>';
            $html .= "<span class=\"link\" onClick=\"fnjs_info('colaborador',$id_item)\">"._("+ info")."</span>";
            $html .= '</td><td>';
            $html .= "<span class=\"link\" onClick=\"fnjs_dedicacion('colaborador',$id_item,$id_enc)\">";
            $html .= $this->getHorarioPropuestaTxt($id_enc,$id_sacd_new);
            $html .= "</td><td id=\"td_$id_item\">";
            $html .= '</td></tr>';
	        
	        
            $html .= '</table>';
	    }
            
            
        return $html;
	    
	}
	
	public function getTitular($id_ubi) {
	    
	}
	public function getSuplente($id_ubi) {
	    
	}
	public function getColaboradores($id_ubi) {
	    
	}
	
	public function getHorarioPropuestaTxt($id_enc,$id_sacd) {
	    if (empty($id_enc) || empty($id_sacd)) {
	        return '';
	    }
	    $a_dedicacion = $this->getHorario($id_enc,$id_sacd);
	    return $this->getHorarioTxt($a_dedicacion);
	}
	
	public function getHorarioActualTxt($id_enc,$id_sacd) {
	    if (empty($id_enc) || empty($id_sacd)) {
	        return '';
	    }
	    $a_dedicacion = $this->getHorarioActual($id_enc,$id_sacd);
	    return $this->getHorarioTxt($a_dedicacion);
	}
	
	public function getHorarioActual($id_enc,$id_sacd) {
        $gesPropuestaEncargoSacdHorario = new GestorEncargoSacdHorario();
	    $aWhere['id_enc'] = $id_enc;
	    $aWhere['id_nom'] = $id_sacd;
	    $aWhere['f_fin'] = 'x';
	    $aOperador['f_fin'] = 'IS NULL';
	    $aWhere['_ordre'] = 'f_ini DESC';
	    $cEncargoSacdHorarios = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);

        $dedic_m = 0;
        $dedic_t = 0;
        $dedic_v = 0;
        // por módulos.
        foreach ($cEncargoSacdHorarios as $oEncargoSacdHorario) {
            $modulo=$oEncargoSacdHorario->getDia_ref();
            switch ($modulo) {
                case 'm':
                    $dedic_m=$oEncargoSacdHorario->getDia_inc();
                    break;
                case 't':
                    $dedic_t=$oEncargoSacdHorario->getDia_inc();
                    break;
                case 'v':
                    $dedic_v=$oEncargoSacdHorario->getDia_inc();
                    break;
            }
        }
       
       $a_dedicacion = [ 'm' => $dedic_m,
                          't' => $dedic_t,
                          'v' => $dedic_v,
                       ];
       
       return $a_dedicacion;
	}
	
	public function getHorario($id_enc,$id_sacd) {
        $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
	    $aWhere['id_enc'] = $id_enc;
	    $aWhere['id_nom'] = $id_sacd;
	    $aWhere['f_fin'] = 'x';
	    $aOperador['f_fin'] = 'IS NULL';
	    $aWhere['_ordre'] = 'f_ini DESC';
	    $cEncargoSacdHorarios = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);

        $dedic_m = 0;
        $dedic_t = 0;
        $dedic_v = 0;
	    // por módulos.
        foreach ($cEncargoSacdHorarios as $oEncargoSacdHorario) {
            $modulo=$oEncargoSacdHorario->getDia_ref();
            switch ($modulo) {
                case 'm':
                    $dedic_m=$oEncargoSacdHorario->getDia_inc();
                    break;
                case 't':
                    $dedic_t=$oEncargoSacdHorario->getDia_inc();
                    break;
                case 'v':
                    $dedic_v=$oEncargoSacdHorario->getDia_inc();
                    break;
            }
        }
       
        $a_dedicacion = [ 'm' => $dedic_m,
                          't' => $dedic_t,
                          'v' => $dedic_v,
                       ];

        return $a_dedicacion;
	}
	
	private function getHorarioTxt($a_dedicacion) {

	    $m = $a_dedicacion['m'];
	    $t = $a_dedicacion['t'];
	    $v = $a_dedicacion['v'];
	    
	    $html = '';
	    $html .= _("m").':';
	    $html .= "[$m]";
	    $html .= '; ';
	    $html .= _("t1").':';
	    $html .= "[$t]";
	    $html .= '; ';
	    $html .= _("t2").':';
	    $html .= "[$v]";
	    
	    return $html;
	}
	
    /**
     * Crea las tablas para las propuestas
     */
    public function crearTabla() {
        // crear tabla encargos_sacd
        $gesPropuestaEncargosSacd = new GestorPropuestaEncargosSacd();
        $gesPropuestaEncargosSacd->crearTabla();
        // horarios
        $gesPropuestaEncargosSacdHorario = new GestorPropuestaEncargoSacdHorario();
        $gesPropuestaEncargosSacdHorario->crearTabla();
        
        return TRUE;
    }

	public function BorrarTablasPropuestas() {
        // crear tabla encargos_sacd
        $gesPropuestaEncargosSacd = new GestorPropuestaEncargosSacd();
        $gesPropuestaEncargosSacd->borrarTabla();
        // horarios
        $gesPropuestaEncargosSacdHorario = new GestorPropuestaEncargoSacdHorario();
        $gesPropuestaEncargosSacdHorario->borrarTabla();
        
        return TRUE;
	    
	}
}