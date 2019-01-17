<?php 
namespace encargossacd\model;

use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\Desplegable;

class DesplCentros {
    
    public function _construct() {
        
    }
    
    public function getDesplPorFiltro($filtro_ctr) {
        switch ($filtro_ctr) {
            case 1:
                $GesCentros = new GestorCentroDl();
                // añado el ctr de oficiales de la dl (14.VIII.07).
                //$sql_ctr="SELECT id_ubi,nombre_ubi FROM u_centros_dl WHERE tipo_ctr ~ 'a.|n.|s[j|m]|of' AND status='t' ORDER BY nombre_ubi";
                $query="WHERE tipo_ctr ~ 'a.|n.|s[j|m]|of' AND status='t'";
                $oDesplCtr = $GesCentros->getListaCentros($query);
                break;
            case 2:
                $GesCentros = new GestorCentroEllas();
                $oDesplCtr = $GesCentros->getListaCentros();
                break;
            case 3:
                $GesCentros = new GestorCentroDl();
                $query="WHERE tipo_ctr ~ 'ss' AND status='t'";
                $oDesplCtr = $GesCentros->getListaCentros($query);
                break;
            case 4:
                $GesCentros = new GestorCentroDl();
                $query="WHERE tipo_ctr ~ 'igl' AND status='t'";
                $oDesplCtr = $GesCentros->getListaCentros($query);
                break;
            case 5:
                $GesCentros = new GestorCentroDl();
                $query="WHERE tipo_ctr ~ 'cgioc|oc|cgi' AND status='t' ";
                $oDesplCtr1 = $GesCentros->getListaCentros($query);
                $opciones_sv = $oDesplCtr1->getOpciones()->fetchAll(\PDO::FETCH_ASSOC); // para pasarlo a un array
                $GesCentros = new GestorCentroEllas();
                $query="WHERE tipo_ctr ~ 'cgioc|oc|cgi' AND status='t' ";
                $oDesplCtr1 = $GesCentros->getListaCentros($query);
                $opciones_sf = $oDesplCtr1->getOpciones()->fetchAll(\PDO::FETCH_ASSOC);
                $a_opciones = array_merge($opciones_sv,$opciones_sf);
                $aa_op = [];
                foreach($a_opciones as $a_vector){
                    $aa_op[$a_vector['id_ubi']] = $a_vector['nombre_ubi'];
                }
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones($aa_op);
                break;
            default:
                $oDesplCtr = new Desplegable();
                $oDesplCtr->setOpciones([]);
        }

        return $oDesplCtr;
    }
}