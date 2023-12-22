<?php

namespace encargossacd\model\entity;

use core;
use web;

/**
 * Fitxer amb la Classe que accedeix a la taula encargo_sacd_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */

/**
 * Clase que implementa la entidad encargo_sacd_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoSacdHorario extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de EncargoSacdHorario
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de EncargoSacdHorario
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
     * Id_item de EncargoSacdHorario
     *
     * @var integer
     */
    protected $iid_item;
    /**
     * Id_enc de EncargoSacdHorario
     *
     * @var integer
     */
    protected $iid_enc;
    /**
     * Id_nom de EncargoSacdHorario
     *
     * @var integer
     */
    protected $iid_nom;
    /**
     * F_ini de EncargoSacdHorario
     *
     * @var web\DateTimeLocal
     */
    protected $df_ini;
    /**
     * F_fin de EncargoSacdHorario
     *
     * @var web\DateTimeLocal
     */
    protected $df_fin;
    /**
     * Dia_ref de EncargoSacdHorario
     *
     * @var string
     */
    protected $sdia_ref;
    /**
     * Dia_num de EncargoSacdHorario
     *
     * @var integer
     */
    protected $idia_num;
    /**
     * Mas_menos de EncargoSacdHorario
     *
     * @var string
     */
    protected $smas_menos;
    /**
     * Dia_inc de EncargoSacdHorario
     *
     * @var integer
     */
    protected $idia_inc;
    /**
     * H_ini de EncargoSacdHorario
     *
     * @var string time
     */
    protected $th_ini;
    /**
     * H_fin de EncargoSacdHorario
     *
     * @var string time
     */
    protected $th_fin;
    /**
     * Id_item_tarea_sacd de EncargoSacdHorario
     *
     * @var integer
     */
    protected $iid_item_tarea_sacd;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de EncargoSacdHorario
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de EncargoSacdHorario
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
     * @param integer|array iid_item,iid_enc,iid_nom
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
        $this->setNomTabla('encargo_sacd_horario');
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
        $aDades['f_ini'] = $this->df_ini;
        $aDades['f_fin'] = $this->df_fin;
        $aDades['dia_ref'] = $this->sdia_ref;
        $aDades['dia_num'] = $this->idia_num;
        $aDades['mas_menos'] = $this->smas_menos;
        $aDades['dia_inc'] = $this->idia_inc;
        $aDades['h_ini'] = $this->th_ini;
        $aDades['h_fin'] = $this->th_fin;
        $aDades['id_item_tarea_sacd'] = $this->iid_item_tarea_sacd;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					dia_ref                  = :dia_ref,
					dia_num                  = :dia_num,
					mas_menos                = :mas_menos,
					dia_inc                  = :dia_inc,
					h_ini                    = :h_ini,
					h_fin                    = :h_fin,
					id_item_tarea_sacd       = :id_item_tarea_sacd";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item' ")) === FALSE) {
                $sClauError = 'EncargoSacdHorario.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'EncargoSacdHorario.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            //array_unshift($aDades, $this->iid_enc, $this->iid_nom);
            $campos = "(id_enc,id_nom,f_ini,f_fin,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin,id_item_tarea_sacd)";
            $valores = "(:id_enc,:id_nom,:f_ini,:f_fin,:dia_ref,:dia_num,:mas_menos,:dia_inc,:h_ini,:h_fin,:id_item_tarea_sacd)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'EncargoSacdHorario.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'EncargoSacdHorario.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            if ($nom_tabla == 'encargo_sacd_horario') {
                $this->id_item = $oDbl->lastInsertId('encargo_sacd_horario_id_item_seq');
            }
            if ($nom_tabla == 'propuesta_encargo_sacd_horario') {
                $this->id_item = $oDbl->lastInsertId('propuesta_encargo_sacd_horario_id_item_seq');
            }
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
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item' ")) === FALSE) {
                $sClauError = 'EncargoSacdHorario.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item' ")) === FALSE) {
            $sClauError = 'EncargoSacdHorario.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
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
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_enc', $aDades)) $this->setId_enc($aDades['id_enc']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('f_ini', $aDades)) $this->setF_ini($aDades['f_ini'], $convert);
        if (array_key_exists('f_fin', $aDades)) $this->setF_fin($aDades['f_fin'], $convert);
        if (array_key_exists('dia_ref', $aDades)) $this->setDia_ref($aDades['dia_ref']);
        if (array_key_exists('dia_num', $aDades)) $this->setDia_num($aDades['dia_num']);
        if (array_key_exists('mas_menos', $aDades)) $this->setMas_menos($aDades['mas_menos']);
        if (array_key_exists('dia_inc', $aDades)) $this->setDia_inc($aDades['dia_inc']);
        if (array_key_exists('h_ini', $aDades)) $this->setH_ini($aDades['h_ini']);
        if (array_key_exists('h_fin', $aDades)) $this->setH_fin($aDades['h_fin']);
        if (array_key_exists('id_item_tarea_sacd', $aDades)) $this->setId_item_tarea_sacd($aDades['id_item_tarea_sacd']);
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
        $this->setF_ini('');
        $this->setF_fin('');
        $this->setDia_ref('');
        $this->setDia_num('');
        $this->setMas_menos('');
        $this->setDia_inc('');
        $this->setH_ini('');
        $this->setH_fin('');
        $this->setId_item_tarea_sacd('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de EncargoSacdHorario en un array
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
     * Recupera la clave primaria de EncargoSacdHorario en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item, 'id_enc' => $this->iid_enc, 'id_nom' => $this->iid_nom);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de EncargoSacdHorario en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                if (($nom_id == 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id;
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de EncargoSacdHorario
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
     * Establece el valor del atributo iid_item de EncargoSacdHorario
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_enc de EncargoSacdHorario
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
     * Establece el valor del atributo iid_enc de EncargoSacdHorario
     *
     * @param integer iid_enc
     */
    function setId_enc($iid_enc)
    {
        $this->iid_enc = $iid_enc;
    }

    /**
     * Recupera el atributo iid_nom de EncargoSacdHorario
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
     * Establece el valor del atributo iid_nom de EncargoSacdHorario
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo df_ini de EncargoSacdHorario
     *
     * @return web\DateTimeLocal df_ini
     */
    function getF_ini()
    {
        if (!isset($this->df_ini) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oConverter = new core\ConverterDate('date', $this->df_ini);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_ini de EncargoSacdHorario
     * Si df_ini es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
     * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param web\DateTimeLocal|string df_ini='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_ini($df_ini = '', $convert = TRUE)
    {
        if ($convert === TRUE && !empty($df_ini)) {
            $oConverter = new core\ConverterDate('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
        } else {
            $this->df_ini = $df_ini;
        }
    }

    /**
     * Recupera el atributo df_fin de EncargoSacdHorario
     *
     * @return web\DateTimeLocal df_fin
     */
    function getF_fin()
    {
        if (!isset($this->df_fin) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oConverter = new core\ConverterDate('date', $this->df_fin);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_fin de EncargoSacdHorario
     * Si df_fin es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
     * Si convert es false, df_fin debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param web\DateTimeLocal|string df_fin='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_fin($df_fin = '', $convert = TRUE)
    {
        if ($convert === TRUE && !empty($df_fin)) {
            $oConverter = new core\ConverterDate('date', $df_fin);
            $this->df_fin = $oConverter->toPg();
        } else {
            $this->df_fin = $df_fin;
        }
    }

    /**
     * Recupera el atributo sdia_ref de EncargoSacdHorario
     *
     * @return string sdia_ref
     */
    function getDia_ref()
    {
        if (!isset($this->sdia_ref) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdia_ref;
    }

    /**
     * Establece el valor del atributo sdia_ref de EncargoSacdHorario
     *
     * @param string sdia_ref='' optional
     */
    function setDia_ref($sdia_ref = '')
    {
        $this->sdia_ref = $sdia_ref;
    }

    /**
     * Recupera el atributo idia_num de EncargoSacdHorario
     *
     * @return integer idia_num
     */
    function getDia_num()
    {
        if (!isset($this->idia_num) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->idia_num;
    }

    /**
     * Establece el valor del atributo idia_num de EncargoSacdHorario
     *
     * @param integer idia_num='' optional
     */
    function setDia_num($idia_num = '')
    {
        $this->idia_num = $idia_num;
    }

    /**
     * Recupera el atributo smas_menos de EncargoSacdHorario
     *
     * @return string smas_menos
     */
    function getMas_menos()
    {
        if (!isset($this->smas_menos) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->smas_menos;
    }

    /**
     * Establece el valor del atributo smas_menos de EncargoSacdHorario
     *
     * @param string smas_menos='' optional
     */
    function setMas_menos($smas_menos = '')
    {
        $this->smas_menos = $smas_menos;
    }

    /**
     * Recupera el atributo idia_inc de EncargoSacdHorario
     *
     * @return integer idia_inc
     */
    function getDia_inc()
    {
        if (!isset($this->idia_inc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->idia_inc;
    }

    /**
     * Establece el valor del atributo idia_inc de EncargoSacdHorario
     *
     * @param integer idia_inc='' optional
     */
    function setDia_inc($idia_inc = '')
    {
        $this->idia_inc = $idia_inc;
    }

    /**
     * Recupera el atributo th_ini de EncargoSacdHorario
     *
     * @return string time th_ini
     */
    function getH_ini()
    {
        if (!isset($this->th_ini) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->th_ini;
    }

    /**
     * Establece el valor del atributo th_ini de EncargoSacdHorario
     *
     * @param string time th_ini='' optional
     */
    function setH_ini($th_ini = '')
    {
        $this->th_ini = $th_ini;
    }

    /**
     * Recupera el atributo th_fin de EncargoSacdHorario
     *
     * @return string time th_fin
     */
    function getH_fin()
    {
        if (!isset($this->th_fin) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->th_fin;
    }

    /**
     * Establece el valor del atributo th_fin de EncargoSacdHorario
     *
     * @param string time th_fin='' optional
     */
    function setH_fin($th_fin = '')
    {
        $this->th_fin = $th_fin;
    }

    /**
     * Recupera el atributo iid_item_tarea_sacd de EncargoSacdHorario
     *
     * @return integer iid_item_tarea_sacd
     */
    function getId_item_tarea_sacd()
    {
        if (!isset($this->iid_item_tarea_sacd) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item_tarea_sacd;
    }

    /**
     * Establece el valor del atributo iid_item_tarea_sacd de EncargoSacdHorario
     *
     * @param integer iid_item_tarea_sacd='' optional
     */
    function setId_item_tarea_sacd($iid_item_tarea_sacd = '')
    {
        $this->iid_item_tarea_sacd = $iid_item_tarea_sacd;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oEncargoSacdHorarioSet = new core\Set();

        $oEncargoSacdHorarioSet->add($this->getDatosF_ini());
        $oEncargoSacdHorarioSet->add($this->getDatosF_fin());
        $oEncargoSacdHorarioSet->add($this->getDatosDia_ref());
        $oEncargoSacdHorarioSet->add($this->getDatosDia_num());
        $oEncargoSacdHorarioSet->add($this->getDatosMas_menos());
        $oEncargoSacdHorarioSet->add($this->getDatosDia_inc());
        $oEncargoSacdHorarioSet->add($this->getDatosH_ini());
        $oEncargoSacdHorarioSet->add($this->getDatosH_fin());
        $oEncargoSacdHorarioSet->add($this->getDatosId_item_tarea_sacd());
        return $oEncargoSacdHorarioSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut df_ini de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_ini()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_ini'));
        $oDatosCampo->setEtiqueta(_("f_ini"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_fin de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_fin()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_fin'));
        $oDatosCampo->setEtiqueta(_("f_fin"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdia_ref de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDia_ref()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dia_ref'));
        $oDatosCampo->setEtiqueta(_("dia_ref"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut idia_num de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDia_num()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dia_num'));
        $oDatosCampo->setEtiqueta(_("dia_num"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut smas_menos de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosMas_menos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'mas_menos'));
        $oDatosCampo->setEtiqueta(_("mas_menos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut idia_inc de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDia_inc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dia_inc'));
        $oDatosCampo->setEtiqueta(_("dia_inc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut th_ini de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosH_ini()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'h_ini'));
        $oDatosCampo->setEtiqueta(_("h_ini"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut th_fin de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosH_fin()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'h_fin'));
        $oDatosCampo->setEtiqueta(_("h_fin"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_item_tarea_sacd de EncargoSacdHorario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_item_tarea_sacd()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_item_tarea_sacd'));
        $oDatosCampo->setEtiqueta(_("id_item_tarea_sacd"));
        return $oDatosCampo;
    }
}
