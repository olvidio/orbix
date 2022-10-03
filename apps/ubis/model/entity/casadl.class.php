<?php

namespace ubis\model\entity;

use core;

/**
 * Clase que implementa la entidad u_cdc_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */
class CasaDl extends Casa
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Id_auto de CasaDl
     *
     * @var integer
     */
    private $iid_auto;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array $iid_ubi
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 'i' . $nom_id; //imagino que es un integer
                if ($val_id !== '') $this->$nom_id = (integer)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_ubi = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_ubi' => $this->iid_ubi);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('u_cdc_dl');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
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
        $aDades = array();
        $aDades['tipo_ubi'] = $this->stipo_ubi;
        $aDades['nombre_ubi'] = $this->snombre_ubi;
        $aDades['dl'] = $this->sdl;
        $aDades['pais'] = $this->spais;
        $aDades['region'] = $this->sregion;
        $aDades['status'] = $this->bstatus;
        $aDades['f_status'] = $this->df_status;
        $aDades['sv'] = $this->bsv;
        $aDades['sf'] = $this->bsf;
        $aDades['tipo_casa'] = $this->stipo_casa;
        $aDades['plazas'] = $this->iplazas;
        $aDades['num_sacd'] = $this->inum_sacd;
        $aDades['biblioteca'] = $this->sbiblioteca;
        $aDades['observ'] = $this->sobserv;
        $aDades['plazas_min'] = $this->iplazas_min;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['status'])) {
            $aDades['status'] = 'true';
        } else {
            $aDades['status'] = 'false';
        }
        if (core\is_true($aDades['sv'])) {
            $aDades['sv'] = 'true';
        } else {
            $aDades['sv'] = 'false';
        }
        if (core\is_true($aDades['sf'])) {
            $aDades['sf'] = 'true';
        } else {
            $aDades['sf'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					status                   = :status,
					f_status                 = :f_status,
					sv                       = :sv,
					sf                       = :sf,
					tipo_casa                = :tipo_casa,
					plazas                   = :plazas,
					num_sacd                 = :num_sacd,
					biblioteca               = :biblioteca,
					observ                   = :observ,
					plazas_min               = :plazas_min";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'CasaDl.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CasaDl.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(tipo_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_casa,plazas,num_sacd,biblioteca,observ,plazas_min)";
            $valores = "(:tipo_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_casa,:plazas,:num_sacd,:biblioteca,:observ,:plazas_min)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'CasaDl.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CasaDl.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $aDades['id_auto'] = $oDbl->lastInsertId('u_cdc_dl_id_auto_seq');
            $aDades['id_ubi'] = $oDbl->query("SELECT id_ubi FROM $nom_tabla WHERE id_auto =" . $aDades['id_auto'])->fetchColumn();
        }
        $this->setAllAtributes($aDades);
        return true;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_ubi)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'CasaDl.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return false;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades);
                    }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
            $sClauError = 'CasaDl.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes($aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('tipo_ubi', $aDades)) $this->setTipo_ubi($aDades['tipo_ubi']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('nombre_ubi', $aDades)) $this->setNombre_ubi($aDades['nombre_ubi']);
        if (array_key_exists('dl', $aDades)) $this->setDl($aDades['dl']);
        if (array_key_exists('pais', $aDades)) $this->setPais($aDades['pais']);
        if (array_key_exists('region', $aDades)) $this->setRegion($aDades['region']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
        if (array_key_exists('f_status', $aDades)) $this->setF_status($aDades['f_status'], $convert);
        if (array_key_exists('sv', $aDades)) $this->setSv($aDades['sv']);
        if (array_key_exists('sf', $aDades)) $this->setSf($aDades['sf']);
        if (array_key_exists('tipo_casa', $aDades)) $this->setTipo_casa($aDades['tipo_casa']);
        if (array_key_exists('plazas', $aDades)) $this->setPlazas($aDades['plazas']);
        if (array_key_exists('num_sacd', $aDades)) $this->setNum_sacd($aDades['num_sacd']);
        if (array_key_exists('biblioteca', $aDades)) $this->setBiblioteca($aDades['biblioteca']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('id_auto', $aDades)) $this->setId_auto($aDades['id_auto']);
        if (array_key_exists('plazas_min', $aDades)) $this->setPlazas_min($aDades['plazas_min']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setTipo_ubi('');
        $this->setId_ubi('');
        $this->setNombre_ubi('');
        $this->setDl('');
        $this->setPais('');
        $this->setRegion('');
        $this->setStatus('');
        $this->setF_status('');
        $this->setSv('');
        $this->setSf('');
        $this->setTipo_casa('');
        $this->setPlazas('');
        $this->setNum_sacd('');
        $this->setBiblioteca('');
        $this->setObserv('');
        $this->setId_auto('');
        $this->setPlazas_min('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera el atributo iid_auto de CasaDl
     *
     * @return integer iid_auto
     */
    function getId_auto()
    {
        if (!isset($this->iid_auto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_auto;
    }

    /**
     * Establece el valor del atributo iid_auto de CasaDl
     *
     * @param integer iid_auto='' optional
     */
    function setId_auto($iid_auto = '')
    {
        $this->iid_auto = $iid_auto;
    }

    /**
     * Recupera el atributo iplazas_min de CasaDl
     *
     * @return integer iplazas_min
     */
    function getPlazas_min()
    {
        if (!isset($this->iplazas_min) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iplazas_min;
    }

    /**
     * Establece el valor del atributo iplazas_min de CasaDl
     *
     * @param integer iplazas_min='' optional
     */
    function setPlazas_min($iplazas_min = '')
    {
        $this->iplazas_min = $iplazas_min;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

}
