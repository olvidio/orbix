<?php

namespace encargossacd\model\entity;

use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula encargos_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */

/**
 * Clase que implementa la entidad encargos_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoSacd extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de EncargoSacd
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de EncargoSacd
     *
     * @var array
     */
    protected $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    protected $bLoaded = FALSE;

    /**
     * Id_item de EncargoSacd
     *
     * @var integer
     */
    protected $iid_item;
    /**
     * Id_enc de EncargoSacd
     *
     * @var integer
     */
    protected $iid_enc;
    /**
     * Id_nom de EncargoSacd
     *
     * @var integer
     */
    protected $iid_nom;
    /**
     * Modo de EncargoSacd
     *
     * @var integer
     */
    protected $imodo;
    /**
     * F_ini de EncargoSacd
     *
     * @varDateTimeLocal
     */
    protected $df_ini;
    /**
     * F_fin de EncargoSacd
     *
     * @varDateTimeLocal
     */
    protected $df_fin;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de EncargoSacd
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de EncargoSacd
     *
     * @var string
     */
    protected $sNomTabla;
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
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id;
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('encargos_sacd');
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
        $aDades = [];
        $aDades['id_enc'] = $this->iid_enc;
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['modo'] = $this->imodo;
        $aDades['f_ini'] = $this->df_ini;
        $aDades['f_fin'] = $this->df_fin;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					modo                     = :modo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
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
            $campos = "(id_enc,id_nom,modo,f_ini,f_fin)";
            $valores = "(:id_enc,:id_nom,:modo,:f_ini,:f_fin)";
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
            $this->iid_item = $oDbl->lastInsertId('encargos_sacd_id_item_seq');
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'EncargoSacd.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return FALSE;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades);
                    }
            }
            return TRUE;
        } else {
            return FALSE;
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
            $sClauError = 'EncargoSacd.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        // Eliminar los objetos dependientes:
        $this->DBEliminarRestricciones();
        return TRUE;
    }

    /**
     * Elimina los objetos relacionados con llave foránea
     *
     */
    public function DBEliminarRestricciones()
    {
        $id_item = $this->getId_item();
        $aWhere = ['id_item_tarea_sacd' => $id_item];
        $gesEncargoSacdHorario = new GestorEncargoSacdHorario();
        $cEncargosSacdHorario = $gesEncargoSacdHorario->getEncargoSacdHorarios($aWhere);
        foreach ($cEncargosSacdHorario as $oEncargoSacdHorario) {
            $oEncargoSacdHorario->DBEliminar();
        }
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
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
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de EncargoSacd en un array
     *
     * @return array aDades
     */
    function getTot()
    {
        if (!is_array($this->aDades)) {
            $this->DBCarregar('tot');
        }
        return $this->aDades;
    }

    /**
     * Recupera la clave primaria de EncargoSacd en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de EncargoSacd en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de EncargoSacd
     *
     * @return integer iid_item
     */
    function getId_item()
    {
        if (!isset($this->iid_item) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item;
    }

    /**
     * Establece el valor del atributo iid_item de EncargoSacd
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_enc de EncargoSacd
     *
     * @return integer iid_enc
     */
    function getId_enc()
    {
        if (!isset($this->iid_enc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_enc;
    }

    /**
     * Establece el valor del atributo iid_enc de EncargoSacd
     *
     * @param integer iid_enc='' optional
     */
    function setId_enc($iid_enc = '')
    {
        $this->iid_enc = $iid_enc;
    }

    /**
     * Recupera el atributo iid_nom de EncargoSacd
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        if (!isset($this->iid_nom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de EncargoSacd
     *
     * @param integer iid_nom='' optional
     */
    function setId_nom($iid_nom = '')
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo imodo de EncargoSacd
     *
     * @return integer imodo
     */
    function getModo()
    {
        if (!isset($this->imodo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->imodo;
    }

    /**
     * Establece el valor del atributo imodo de EncargoSacd
     *
     * @param integer imodo='' optional
     */
    function setModo($imodo = '')
    {
        $this->imodo = $imodo;
    }

    /**
     * Recupera el atributo df_ini de EncargoSacd
     *
     * @returnDateTimeLocal df_ini
     */
    function getF_ini()
    {
        if (!isset($this->df_ini) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oConverter = new ConverterDate('date', $this->df_ini);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_ini de EncargoSacd
     * Si df_ini es string, y convert=true se convierte usando el formato web\DateTimeLocal->getFormat().
     * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @paramDateTimeLocal|string df_ini='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_ini($df_ini = '', $convert = TRUE)
    {
        if ($convert === TRUE && !empty($df_ini)) {
            $oConverter = new ConverterDate('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
        } else {
            $this->df_ini = $df_ini;
        }
    }

    /**
     * Recupera el atributo df_fin de EncargoSacd
     *
     * @returnDateTimeLocal df_fin
     */
    function getF_fin()
    {
        if (!isset($this->df_fin) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oConverter = new ConverterDate('date', $this->df_fin);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_fin de EncargoSacd
     * Si df_fin es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
     * Si convert es false, df_fin debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @paramDateTimeLocal|string df_fin='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_fin($df_fin = '', $convert = TRUE)
    {
        if ($convert === TRUE && !empty($df_fin)) {
            $oConverter = new ConverterDate('date', $df_fin);
            $this->df_fin = $oConverter->toPg();
        } else {
            $this->df_fin = $df_fin;
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oEncargoSacdSet = new Set();

        $oEncargoSacdSet->add($this->getDatosId_enc());
        $oEncargoSacdSet->add($this->getDatosId_nom());
        $oEncargoSacdSet->add($this->getDatosModo());
        $oEncargoSacdSet->add($this->getDatosF_ini());
        $oEncargoSacdSet->add($this->getDatosF_fin());
        return $oEncargoSacdSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_enc de EncargoSacd
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_enc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_enc'));
        $oDatosCampo->setEtiqueta(_("id_enc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_nom de EncargoSacd
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_nom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nom'));
        $oDatosCampo->setEtiqueta(_("id_nom"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut imodo de EncargoSacd
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosModo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'modo'));
        $oDatosCampo->setEtiqueta(_("modo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_ini de EncargoSacd
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_ini()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_ini'));
        $oDatosCampo->setEtiqueta(_("f_ini"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_fin de EncargoSacd
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_fin()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_fin'));
        $oDatosCampo->setEtiqueta(_("f_fin"));
        return $oDatosCampo;
    }
}
