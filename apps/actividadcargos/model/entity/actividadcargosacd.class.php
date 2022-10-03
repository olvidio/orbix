<?php

namespace actividadcargos\model\entity;

use cambios\model\gestorAvisoCambios;
use core\ConfigGlobal;
use function core\is_true;

/**
 * Clase que implementa la entidad d_cargos_activ amb restricció a només els sacd.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/04/2012
 */
class ActividadCargoSacd extends ActividadCargoAbstract
{

    /** de fet no té cap métode adicional (de moment), pero a l'hora de fer els avisos són dues classes diferents,
     * i es poden donar permisos diferents...
     */

    /**
     * Sobre escriu el de ActividadCargoAbstract per afegir al final fer una copia a DB-comun
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
     *
     * @param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet = 0)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = array();
        //$aDades['id_schema'] = $this->iid_schema;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_cargo'] = $this->iid_cargo;
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['puede_agd'] = $this->bpuede_agd;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['puede_agd'])) {
            $aDades['puede_agd'] = 'true';
        } else {
            $aDades['puede_agd'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_activ                 = :id_activ,
					id_cargo                 = :id_cargo,
					id_nom                   = :id_nom,
					puede_agd                = :puede_agd,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item ")) === false) {
                $sClauError = 'ActividadCargo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadCargo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            // Anoto el cambio
            if (empty($quiet) && ConfigGlobal::is_app_installed('cambios')) {
                $oGestorCanvis = new gestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
            }
            $this->aDades = $aDades;
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            $campos = "(id_activ,id_cargo,id_nom,puede_agd,observ)";
            $valores = "(:id_activ,:id_cargo,:id_nom,:puede_agd,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ActividadCargo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadCargo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $id_item = $oDbl->lastInsertId('d_cargos_activ_dl_id_item_seq');
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$id_item")) === false) {
                $sClauError = 'ActividadCargo.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);
            // Anoto el cambio
            if (empty($quiet) && ConfigGlobal::is_app_installed('cambios')) {
                $oGestorCanvis = new gestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'INSERT', $aDadesLast['id_activ'], $this->aDades, array());
            }
        }
        // Modifico la ficha en la BD-comun
        $aDades = $this->aDades;
        $this->copia2Comun($aDades, $bInsert);
        return true;
    }

    /**
     * Sobre escriu el de ActividadCargoAbstract per afegir al final eliminar de la DB-comun
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $this->DBCarregar(); // para cargar el id_item si se ha creado con primaryKey.
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        // que tenga el módulo de 'cambios'
        if (ConfigGlobal::is_app_installed('cambios')) {
            // per carregar les dades a $this->aDadesActuals i poder posar-les als canvis.
            $this->DBCarregar('guardar');
            // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
            $oGestorCanvis = new gestorAvisoCambios();
            $shortClassName = (new \ReflectionClass($this))->getShortName();
            $oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, array(), $this->aDadesActuals);
        }
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'ActividadCargo.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        // Modifico la ficha en la BD-comun
        $this->borraDeComun($this->iid_item);
        return true;
    }

    /**
     *
     * Elimina el registre de la base de dades comun
     *
     */
    private function borraDeComun($iid_item)
    {
        // La conexión a la DB-comun
        $oDbl = $GLOBALS['oDBC'];
        $nom_tabla = 'c' . $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$iid_item")) === false) {
            $sClauError = 'ActividadCargoDBComun.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            //return false;
        }
        return true;
    }

    private function copia2Comun($aDades, $bInsert)
    {
        // La conexión a la DB-comun
        $oDbl = $GLOBALS['oDBC'];
        $nom_tabla = 'c' . $this->getNomTabla();

        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['puede_agd'])) {
            $aDades['puede_agd'] = 'true';
        } else {
            $aDades['puede_agd'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_activ                 = :id_activ,
					id_cargo                 = :id_cargo,
					id_nom                   = :id_nom,
					puede_agd                = :puede_agd,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item ")) === false) {
                $sClauError = 'ActividadCargoDBComun.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadCargoDBComun.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->aDades = $aDades;
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            unset($aDades['id_schema']);
            // Aqui si hay que poner el id_item, pues es copia de DB-sv
            $aDades['id_item'] = $this->iid_item;
            $campos = "(id_activ,id_cargo,id_nom,puede_agd,observ,id_item)";
            $valores = "(:id_activ,:id_cargo,:id_nom,:puede_agd,:observ,:id_item)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ActividadCargoDBComun.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadCargoDBComun.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->aDades = $aDades;
            $this->setAllAtributes($aDades);
        }
        return true;
    }

}