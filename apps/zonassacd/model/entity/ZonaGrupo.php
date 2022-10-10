<?php

namespace zonassacd\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula zonas_grupos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */

/**
 * Clase que implementa la entidad zonas_grupos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class ZonaGrupo extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ZonaGrupo
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ZonaGrupo
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_grupo de ZonaGrupo
     *
     * @var integer
     */
    private $iid_grupo;
    /**
     * Nombre_grupo de ZonaGrupo
     *
     * @var string
     */
    private $snombre_grupo;
    /**
     * Orden de ZonaGrupo
     *
     * @var integer
     */
    private $iorden;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ZonaGrupo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ZonaGrupo
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
     * @param integer|array iid_grupo
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_grupo') && $val_id !== '') $this->iid_grupo = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_grupo = (integer)$a_id;
                $this->aPrimary_key = array('id_grupo' => $this->iid_grupo);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('zonas_grupos');
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
        $aDades['nombre_grupo'] = $this->snombre_grupo;
        $aDades['orden'] = $this->iorden;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					nombre_grupo             = :nombre_grupo,
					orden                    = :orden";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_grupo='$this->iid_grupo'")) === FALSE) {
                $sClauError = 'ZonaGrupo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ZonaGrupo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(nombre_grupo,orden)";
            $valores = "(:nombre_grupo,:orden)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'ZonaGrupo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ZonaGrupo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_grupo = $oDbl->lastInsertId('zonas_grupos_id_grupo_seq');
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_grupo)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_grupo='$this->iid_grupo'")) === FALSE) {
                $sClauError = 'ZonaGrupo.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_grupo='$this->iid_grupo'")) === FALSE) {
            $sClauError = 'ZonaGrupo.eliminar';
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
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_grupo', $aDades)) $this->setId_grupo($aDades['id_grupo']);
        if (array_key_exists('nombre_grupo', $aDades)) $this->setNombre_grupo($aDades['nombre_grupo']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_grupo('');
        $this->setNombre_grupo('');
        $this->setOrden('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ZonaGrupo en un array
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
     * Recupera la clave primaria de ZonaGrupo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_grupo' => $this->iid_grupo);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ZonaGrupo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_grupo') && $val_id !== '') $this->iid_grupo = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_grupo de ZonaGrupo
     *
     * @return integer iid_grupo
     */
    function getId_grupo()
    {
        if (!isset($this->iid_grupo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_grupo;
    }

    /**
     * Establece el valor del atributo iid_grupo de ZonaGrupo
     *
     * @param integer iid_grupo
     */
    function setId_grupo($iid_grupo)
    {
        $this->iid_grupo = $iid_grupo;
    }

    /**
     * Recupera el atributo snombre_grupo de ZonaGrupo
     *
     * @return string snombre_grupo
     */
    function getNombre_grupo()
    {
        if (!isset($this->snombre_grupo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_grupo;
    }

    /**
     * Establece el valor del atributo snombre_grupo de ZonaGrupo
     *
     * @param string snombre_grupo='' optional
     */
    function setNombre_grupo($snombre_grupo = '')
    {
        $this->snombre_grupo = $snombre_grupo;
    }

    /**
     * Recupera el atributo iorden de ZonaGrupo
     *
     * @return integer iorden
     */
    function getOrden()
    {
        if (!isset($this->iorden) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iorden;
    }

    /**
     * Establece el valor del atributo iorden de ZonaGrupo
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oZonaGrupoSet = new core\Set();

        $oZonaGrupoSet->add($this->getDatosNombre_grupo());
        $oZonaGrupoSet->add($this->getDatosOrden());
        return $oZonaGrupoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut snombre_grupo de ZonaGrupo
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosNombre_grupo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_grupo'));
        $oDatosCampo->setEtiqueta(_("nombre del grupo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de ZonaGrupo
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosOrden()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden'));
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('5');
        return $oDatosCampo;
    }
}
