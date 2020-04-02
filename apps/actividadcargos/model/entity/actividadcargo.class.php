<?php
namespace actividadcargos\model\entity;
/**
 * Fitxer amb la Classe que accedeix a la taula d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
/**
 * Classe que implementa l'entitat d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class ActividadCargo Extends ActividadCargoAbstract {

    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
     *
     *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet=0) {
        $aDades = $this->getAllAtributes();
        $id_cargo = $this->getId_cargo();
        $oCargo = new Cargo($id_cargo);
        $tipo_cargo = $oCargo->getTipo_cargo(); 
        switch ($tipo_cargo) {
            case 'sacd':
                $oActividadCargoI = new ActividadCargoSacd($this->aPrimary_key);
                $oActividadCargoI->setAllAtributes($aDades);
                $oActividadCargoI->DBGuardar($quiet);
                break;
            default:
                $oActividadCargoI = new ActividadCargoNoSacd($this->aPrimary_key);
                $oActividadCargoI->setAllAtributes($aDades);
                $oActividadCargoI->DBGuardar($quiet);
                break;
        }
    }
    
    /**
     * Elimina el registre de la base de dades corresponent a l'objecte.
     *
     */
    public function DBEliminar() {
        $id_cargo = $this->getId_cargo();
        $oCargo = new Cargo($id_cargo);
        $tipo_cargo = $oCargo->getTipo_cargo(); 
        switch ($tipo_cargo) {
            case 'sacd':
                $oActividadCargoI = new ActividadCargoSacd($this->aPrimary_key);
                $oActividadCargoI->DBEliminar();
                break;
            default:
                $oActividadCargoI = new ActividadCargoNoSacd($this->aPrimary_key);
                $oActividadCargoI->DBEliminar();
                break;
        }
    }
}