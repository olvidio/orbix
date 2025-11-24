<?php

namespace personas\model\entity;

use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula Hdlb(v|f).p_de_paso_out
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad p_de_paso_out
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class PersonaOut extends PersonaPub
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_nom = (integer)$a_id; 
                $this->aPrimary_key = array('id_nom' => $this->iid_nom);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_de_paso_out');
    }
    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // ES diferent del pub perque ja tinc el id_nom de la persona (dl origen).
    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     */
    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = [];
        $aDades['id_cr'] = $this->iid_cr;
        $aDades['id_tabla'] = $this->sid_tabla;
        $aDades['dl'] = $this->sdl;
        $aDades['sacd'] = $this->bsacd;
        $aDades['trato'] = $this->strato;
        $aDades['nom'] = $this->snom;
        $aDades['nx1'] = $this->snx1;
        $aDades['apellido1'] = $this->sapellido1;
        $aDades['nx2'] = $this->snx2;
        $aDades['apellido2'] = $this->sapellido2;
        $aDades['f_nacimiento'] = $this->df_nacimiento;
        $aDades['lengua'] = $this->slengua;
        $aDades['situacion'] = $this->ssituacion;
        $aDades['f_situacion'] = $this->df_situacion;
        $aDades['apel_fam'] = $this->sapel_fam;
        $aDades['inc'] = $this->sinc;
        $aDades['f_inc'] = $this->df_inc;
        $aDades['stgr'] = $this->sstgr;
        $aDades['edad'] = $this->iedad;
        $aDades['profesion'] = $this->sprofesion;
        $aDades['eap'] = $this->seap;
        $aDades['observ'] = $this->sobserv;
        $aDades['lugar_nacimiento'] = $this->slugar_nacimiento;
        $aDades['profesor_stgr'] = $this->bprofesor_stgr;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['sacd'])) {
            $aDades['sacd'] = 'true';
        } else {
            $aDades['sacd'] = 'false';
        }
        if (is_true($aDades['profesor_stgr'])) {
            $aDades['profesor_stgr'] = 'true';
        } else {
            $aDades['profesor_stgr'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_cr                    = :id_cr,
					id_tabla                 = :id_tabla,
					dl                       = :dl,
					sacd                     = :sacd,
					trato                    = :trato,
					nom                      = :nom,
					nx1                      = :nx1,
					apellido1                = :apellido1,
					nx2                      = :nx2,
					apellido2                = :apellido2,
					f_nacimiento             = :f_nacimiento,
					lengua                   = :lengua,
					situacion                = :situacion,
					f_situacion              = :f_situacion,
					apel_fam                 = :apel_fam,
					inc                      = :inc,
					f_inc                    = :f_inc,
					stgr                     = :stgr,
					edad                     = :edad,
					profesion                = :profesion,
					eap                      = :eap,
					observ                   = :observ,
					lugar_nacimiento         = :lugar_nacimiento,
					profesor_stgr            = :profesor_stgr";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = get_class($this) . '.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->setAllAttributes($aDades);
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,id_cr,id_tabla,dl,sacd,trato,nom,nx1,apellido1,nx2,apellido2,f_nacimiento,lengua,situacion,f_situacion,apel_fam,inc,f_inc,stgr,edad,profesion,eap,observ,lugar_nacimiento,profesor_stgr)";
            $valores = "(:id_nom,:id_cr,:id_tabla,:dl,:sacd,:trato,:nom,:nx1,:apellido1,:nx2,:apellido2,:f_nacimiento,:lengua,:situacion,:f_situacion,:apel_fam,:inc,:f_inc,:stgr,:edad,:profesion,:eap,:observ,:lugar_nacimiento,:profesor_stgr)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = get_class($this) . '.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = get_class($this) . '.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            // En este caso no hay id_auto. es el id_nom de la tabla originaria (agd, n, s)
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAttributes($aDadesLast);
        }
        return true;
    }


    /* OTROS MÉTODOS  ----------------------------------------------------------*/

    public function import($object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            if ($key == 'sNomTabla') continue;
            if (property_exists($this, $key)) {
                $this->$key = $value;
                if ($key == 'sid_tabla') $this->$key = 'p' . $value;
            }
        }
    }
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

}
