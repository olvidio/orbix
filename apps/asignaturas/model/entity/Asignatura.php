<?php
namespace asignaturas\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/11/2010
 */

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/11/2010
 */
class Asignatura extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Asignatura
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Asignatura
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
     * Id_asignatura de Asignatura
     *
     * @var integer
     */
    private $iid_asignatura;
    /**
     * Id_nivel de Asignatura
     *
     * @var integer
     */
    private $iid_nivel;
    /**
     * Nombre_asignatura de Asignatura
     *
     * @var string
     */
    private $snombre_asignatura;
    /**
     * Nombre_corto de Asignatura
     *
     * @var string
     */
    private $snombre_corto;
    /**
     * Creditos de Asignatura
     *
     * @var string
     */
    private $screditos;
    /**
     * Año de Asignatura
     *
     * @var string
     */
    private $syear;
    /**
     * Id_sector de Asignatura
     *
     * @var integer
     */
    private $iid_sector;
    /**
     * Status de Asignatura
     *
     * @var boolean
     */
    private $bstatus;
    /**
     * Id_tipo de Asignatura
     *
     * @var integer
     */
    private $iid_tipo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_asignatura
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_asignatura = (integer)$a_id; 
                $this->aPrimary_key = array('id_asignatura' => $this->iid_asignatura);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_asignaturas');
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
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = [];
        $aDades['id_nivel'] = $this->iid_nivel;
        $aDades['nombre_asignatura'] = $this->snombre_asignatura;
        $aDades['nombre_corto'] = $this->snombre_corto;
        $aDades['creditos'] = $this->screditos;
        $aDades['year'] = $this->syear;
        $aDades['id_sector'] = $this->iid_sector;
        $aDades['status'] = $this->bstatus;
        $aDades['id_tipo'] = $this->iid_tipo;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['status'])) {
            $aDades['status'] = 'true';
        } else {
            $aDades['status'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_nivel                 = :id_nivel,
					nombre_asignatura        = :nombre_asignatura,
					nombre_corto             = :nombre_corto,
					creditos                 = :creditos,
					year                     = :year,
					id_sector                = :id_sector,
					status                   = :status,
					id_tipo                  = :id_tipo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_asignatura='$this->iid_asignatura'")) === false) {
                $sClauError = 'Asignatura.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Asignatura.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_asignatura);
            $campos = "(id_asignatura,id_nivel,nombre_asignatura,nombre_corto,creditos,year,id_sector,status,id_tipo)";
            $valores = "(:id_asignatura,:id_nivel,:nombre_asignatura,:nombre_corto,:creditos,:year,:id_sector,:status,:id_tipo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Asignatura.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Asignatura.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_asignatura)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_asignatura='$this->iid_asignatura'")) === false) {
                $sClauError = 'Asignatura.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_asignatura='$this->iid_asignatura'")) === false) {
            $sClauError = 'Asignatura.eliminar';
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
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('id_nivel', $aDades)) $this->setId_nivel($aDades['id_nivel']);
        if (array_key_exists('nombre_asignatura', $aDades)) $this->setNombre_asignatura($aDades['nombre_asignatura']);
        if (array_key_exists('nombre_corto', $aDades)) $this->setNombre_corto($aDades['nombre_corto']);
        if (array_key_exists('creditos', $aDades)) $this->setCreditos($aDades['creditos']);
        if (array_key_exists('year', $aDades)) $this->setYear($aDades['year']);
        if (array_key_exists('id_sector', $aDades)) $this->setId_sector($aDades['id_sector']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
        if (array_key_exists('id_tipo', $aDades)) $this->setId_tipo($aDades['id_tipo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_asignatura('');
        $this->setId_nivel('');
        $this->setNombre_asignatura('');
        $this->setNombre_corto('');
        $this->setCreditos('');
        $this->setYear('');
        $this->setId_sector('');
        $this->setStatus('');
        $this->setId_tipo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Asignatura en un array
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
     * Recupera la clave primaria de Asignatura en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_asignatura' => $this->iid_asignatura);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Asignatura en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_asignatura de Asignatura
     *
     * @return integer iid_asignatura
     */
    function getId_asignatura()
    {
        if (!isset($this->iid_asignatura) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_asignatura;
    }

    /**
     * Establece el valor del atributo iid_asignatura de Asignatura
     *
     * @param integer iid_asignatura
     */
    function setId_asignatura($iid_asignatura)
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     * Recupera el atributo iid_nivel de Asignatura
     *
     * @return integer iid_nivel
     */
    function getId_nivel()
    {
        if (!isset($this->iid_nivel) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nivel;
    }

    /**
     * Establece el valor del atributo iid_nivel de Asignatura
     *
     * @param integer iid_nivel='' optional
     */
    function setId_nivel($iid_nivel = '')
    {
        $this->iid_nivel = $iid_nivel;
    }

    /**
     * Recupera el atributo snombre_asignatura de Asignatura
     *
     * @return string snombre_asignatura
     */
    function getNombre_asignatura()
    {
        if (!isset($this->snombre_asignatura) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_asignatura;
    }

    /**
     * Establece el valor del atributo snombre_asignatura de Asignatura
     *
     * @param string snombre_asignatura='' optional
     */
    function setNombre_asignatura($snombre_asignatura = '')
    {
        $this->snombre_asignatura = $snombre_asignatura;
    }

    /**
     * Recupera el atributo snombre_corto de Asignatura
     *
     * @return string snombre_corto
     */
    function getNombre_corto()
    {
        if (!isset($this->snombre_corto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_corto;
    }

    /**
     * Establece el valor del atributo snombre_corto de Asignatura
     *
     * @param string snombre_corto='' optional
     */
    function setNombre_corto($snombre_corto = '')
    {
        $this->snombre_corto = $snombre_corto;
    }

    /**
     * Recupera el atributo screditos de Asignatura
     *
     * @return string screditos
     */
    function getCreditos()
    {
        if (!isset($this->screditos) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->screditos;
    }

    /**
     * Establece el valor del atributo screditos de Asignatura
     *
     * @param string screditos='' optional
     */
    function setCreditos($screditos = '')
    {
        $this->screditos = $screditos;
    }

    /**
     * Recupera el atributo syear de Asignatura
     *
     * @return string syear
     */
    function getYear()
    {
        if (!isset($this->syear) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->syear;
    }

    /**
     * Establece el valor del atributo syear de Asignatura
     *
     * @param string syear='' optional
     */
    function setYear($syear = '')
    {
        $this->syear = $syear;
    }

    /**
     * Recupera el atributo iid_sector de Asignatura
     *
     * @return integer iid_sector
     */
    function getId_sector()
    {
        if (!isset($this->iid_sector) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_sector;
    }

    /**
     * Establece el valor del atributo iid_sector de Asignatura
     *
     * @param integer iid_sector='' optional
     */
    function setId_sector($iid_sector = '')
    {
        $this->iid_sector = $iid_sector;
    }

    /**
     * Recupera el atributo bstatus de Asignatura
     *
     * @return boolean bstatus
     */
    function getStatus()
    {
        if (!isset($this->bstatus) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bstatus;
    }

    /**
     * Establece el valor del atributo bstatus de Asignatura
     *
     * @param boolean bstatus='f' optional
     */
    function setStatus($bstatus = 'f')
    {
        $this->bstatus = $bstatus;
    }

    /**
     * Recupera el atributo iid_tipo de Asignatura
     *
     * @return integer iid_tipo
     */
    function getId_tipo()
    {
        if (!isset($this->iid_tipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo;
    }

    /**
     * Establece el valor del atributo iid_tipo de Asignatura
     *
     * @param integer iid_tipo='' optional
     */
    function setId_tipo($iid_tipo = '')
    {
        $this->iid_tipo = $iid_tipo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oAsignaturaSet = new Set();

        $oAsignaturaSet->add($this->getDatosId_asignatura());
        $oAsignaturaSet->add($this->getDatosId_nivel());
        $oAsignaturaSet->add($this->getDatosNombre_asignatura());
        $oAsignaturaSet->add($this->getDatosNombre_corto());
        $oAsignaturaSet->add($this->getDatosCreditos());
        $oAsignaturaSet->add($this->getDatosYear());
        $oAsignaturaSet->add($this->getDatosId_sector());
        $oAsignaturaSet->add($this->getDatosStatus());
        $oAsignaturaSet->add($this->getDatosId_tipo());
        return $oAsignaturaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_asignatura de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_asignatura()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_asignatura'));
        $oDatosCampo->setEtiqueta(_("id asignatura"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_nivel de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_nivel()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nivel'));
        $oDatosCampo->setEtiqueta(_("id nivel"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_asignatura de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_asignatura()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_asignatura'));
        $oDatosCampo->setEtiqueta(_("nombre largo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(40);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_corto de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_corto()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_corto'));
        $oDatosCampo->setEtiqueta(_("nombre corto"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut screditos de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCreditos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'creditos'));
        $oDatosCampo->setEtiqueta(_("créditos"));
        $oDatosCampo->setTipo('decimal');
        $oDatosCampo->setArgument(4);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut syear de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosYear()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'year'));
        $oDatosCampo->setEtiqueta(_("año"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(4);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_sector de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_sector()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_sector'));
        $oDatosCampo->setEtiqueta(_("sector"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('asignaturas\model\entity\Sector');
        $oDatosCampo->setArgument2('getSector'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaSectores');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bstatus de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosStatus()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'status'));
        $oDatosCampo->setEtiqueta(_("en uso"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tipo de Asignatura
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('asignaturas\model\entity\AsignaturaTipo');
        $oDatosCampo->setArgument2('getTipo_asignatura'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaAsignaturaTipos');
        return $oDatosCampo;
    }
}

?>
