<?php

namespace encargossacd\model\entity;

/**
 * Fitxer amb la Classe que accedeix a la taula propuesta_encargos_sacd
 *
 * @package orbix
 * @subpackage encargossacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */

/**
 * Clase que implementa la entidad propuesta_encargos_sacd
 *
 * @package orbix
 * @subpackage encargossacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */
class PropuestaEncargoSacd extends EncargoSacd
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_nom_new de EncargoSacd
     *
     * @var integer
     */
    private $iid_nom_new;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_item
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; 
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('propuesta_encargos_sacd');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     */
    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === FALSE) {
            $bInsert = TRUE;
        } else {
            $bInsert = FALSE;
        }
        $aDades = array();
        $aDades['id_enc'] = $this->iid_enc;
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['modo'] = $this->imodo;
        $aDades['f_ini'] = $this->df_ini;
        $aDades['f_fin'] = $this->df_fin;
        $aDades['id_nom_new'] = $this->iid_nom_new;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					modo                     = :modo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					id_nom_new               = :id_nom_new";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'EncargoSacd.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'EncargoSacd.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_enc,id_nom,modo,f_ini,f_fin,id_nom_new)";
            $valores = "(:id_enc,:id_nom,:modo,:f_ini,:f_fin,:id_nom_new)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'EncargoSacd.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'EncargoSacd.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('propuesta_encargos_sacd_id_item_seq');
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_enc', $aDades)) $this->setId_enc($aDades['id_enc']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('modo', $aDades)) $this->setModo($aDades['modo']);
        if (array_key_exists('f_ini', $aDades)) $this->setF_ini($aDades['f_ini'], $convert);
        if (array_key_exists('f_fin', $aDades)) $this->setF_fin($aDades['f_fin'], $convert);
        if (array_key_exists('id_nom_new', $aDades)) $this->setId_nom_new($aDades['id_nom_new']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_enc('');
        $this->setId_nom('');
        $this->setModo('');
        $this->setF_ini('');
        $this->setF_fin('');
        $this->setId_nom_new('');
        $this->setPrimary_key($aPK);
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera el atributo iid_nom_new de EncargoSacd
     *
     * @return integer iid_nom_new
     */
    function getId_nom_new()
    {
        if (!isset($this->iid_nom_new) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nom_new;
    }

    /**
     * Establece el valor del atributo iid_nom_new de EncargoSacd
     *
     * @param integer iid_nom_new='' optional
     */
    function setId_nom_new($iid_nom_new = '')
    {
        $this->iid_nom_new = $iid_nom_new;
    }

}