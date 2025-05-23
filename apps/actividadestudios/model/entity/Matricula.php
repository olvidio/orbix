<?php

namespace actividadestudios\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use notas\model\entity\Nota;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula d_matriculas_activ
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/11/2014
 */

/**
 * Clase que implementa la entidad d_matriculas_activ
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/11/2014
 */
class Matricula extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Matricula
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de Matricula
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
     * Id_schema de Matricula
     *
     * @var integer
     */
    protected $iid_schema;
    /**
     * Id_activ de Matricula
     *
     * @var integer
     */
    protected $iid_activ;
    /**
     * Id_asignatura de Matricula
     *
     * @var integer
     */
    protected $iid_asignatura;
    /**
     * Id_nivel de Matricula
     *
     * @var integer
     */
    protected $iid_nivel;
    /**
     * Id_nom de Matricula
     *
     * @var integer
     */
    protected $iid_nom;
    /**
     * Id_situacion de Matricula
     *
     * @var integer
     */
    protected $iid_situacion;
    /**
     * Preceptor de Matricula
     *
     * @var boolean
     */
    protected $bpreceptor;
    /**
     * Id_preceptor de Matricula
     *
     * @var integer
     */
    protected $iid_preceptor;
    /**
     * Acta de Matricula
     *
     * @var string
     */
    protected $sacta;
    /**
     * Nota_num de Nota
     *
     * @var integer
     */
    protected $inota_num;
    /**
     * Nota_max de Nota
     *
     * @var integer
     */
    protected $inota_max;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Matricula
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Matricula
     *
     * @var string
     */
    protected $sNomTabla;
    /**
     * Nota_txt de Nota
     *
     * @var string
     */
    protected $sNota_txt;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_asignatura,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id;
                if (($nom_id === 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id;
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_matriculas_activ');
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
        //$aDades['id_schema'] = $this->iid_schema;
        $aDades['id_nivel'] = $this->iid_nivel;
        $aDades['id_situacion'] = $this->iid_situacion;
        $aDades['preceptor'] = $this->bpreceptor;
        $aDades['id_preceptor'] = $this->iid_preceptor;
        $aDades['nota_num'] = $this->inota_num;
        $aDades['nota_max'] = $this->inota_max;
        $aDades['acta'] = $this->sacta;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['preceptor'])) {
            $aDades['preceptor'] = 'true';
        } else {
            $aDades['preceptor'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_nivel             	 = :id_nivel,
					id_situacion             = :id_situacion,
					preceptor                = :preceptor,
					id_preceptor             = :id_preceptor,
					nota_num                 = :nota_num,
					nota_max                 = :nota_max,
					acta                 	 = :acta";

            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = 'Matricula.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Matricula.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_activ, $this->iid_asignatura, $this->iid_nom);
            $campos = "(id_activ,id_asignatura,id_nom,id_nivel,id_situacion,preceptor,id_preceptor,nota_num,nota_max,acta)";
            $valores = "(:id_activ,:id_asignatura,:id_nom,:id_nivel,:id_situacion,:preceptor,:id_preceptor,:nota_num,:nota_max,:acta)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Matricula.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Matricula.insertar.execute';
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_activ) && isset($this->iid_asignatura) && isset($this->iid_nom)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = 'Matricula.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura' AND id_nom=$this->iid_nom")) === false) {
            $sClauError = 'Matricula.eliminar';
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
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_nivel', $aDades)) $this->setId_nivel($aDades['id_nivel']);
        if (array_key_exists('id_situacion', $aDades)) $this->setId_situacion($aDades['id_situacion']);
        if (array_key_exists('preceptor', $aDades)) $this->setPreceptor($aDades['preceptor']);
        if (array_key_exists('id_preceptor', $aDades)) $this->setId_preceptor($aDades['id_preceptor']);
        if (array_key_exists('nota_num', $aDades)) $this->setNota_num($aDades['nota_num']);
        if (array_key_exists('nota_max', $aDades)) $this->setNota_max($aDades['nota_max']);
        if (array_key_exists('acta', $aDades)) $this->setActa($aDades['acta']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_activ('');
        $this->setId_asignatura('');
        $this->setId_nom('');
        $this->setId_nivel('');
        $this->setId_situacion('');
        $this->setPreceptor('');
        $this->setId_preceptor('');
        $this->setNota_num('');
        $this->setNota_max('');
        $this->setActa('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Matricula en un array
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
     * Recupera la clave primaria de Matricula en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_activ' => $this->iid_activ, 'id_asignatura' => $this->iid_asignatura, 'id_nom' => $this->iid_nom);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Matricula en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id;
                if (($nom_id === 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id;
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_activ de Matricula
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        if (!isset($this->iid_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de Matricula
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iid_asignatura de Matricula
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
     * Establece el valor del atributo iid_asignatura de Matricula
     *
     * @param integer iid_asignatura
     */
    function setId_asignatura($iid_asignatura)
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     * Recupera el atributo iid_nom de Matricula
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
     * Establece el valor del atributo iid_nom de Matricula
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_nivel de Matricula
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
     * Establece el valor del atributo iid_nivel de Matricula
     *
     * @param integer iid_nivel='' optional
     */
    function setId_nivel($iid_nivel = '')
    {
        $this->iid_nivel = $iid_nivel;
    }

    /**
     * Recupera el atributo iid_situacion de Matricula
     *
     * @return integer iid_situacion
     */
    function getId_situacion(): int
    {
        if (!isset($this->iid_situacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_situacion;
    }

    /**
     * Establece el valor del atributo iid_situacion de Matricula
     *
     * @param integer iid_situacion='' optional
     */
    function setId_situacion($iid_situacion = '')
    {
        $this->iid_situacion = (int)$iid_situacion;
    }

    /**
     * Recupera el atributo bpreceptor de Matricula
     *
     * @return boolean bpreceptor
     */
    function getPreceptor(): bool
    {
        if (!isset($this->bpreceptor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return is_true($this->bpreceptor);
    }

    /**
     * Establece el valor del atributo bpreceptor de Matricula
     *
     * @param boolean bpreceptor='f' optional
     */
    function setPreceptor($bpreceptor = 'f')
    {
        $this->bpreceptor = $bpreceptor;
    }

    /**
     * Recupera el atributo iid_preceptor de Matricula
     *
     * @return integer iid_preceptor
     */
    function getId_preceptor()
    {
        if (!isset($this->iid_preceptor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_preceptor;
    }

    /**
     * Establece el valor del atributo iid_preceptor de Matricula
     *
     * @param integer iid_preceptor='' optional
     */
    function setId_preceptor($iid_preceptor = '')
    {
        $this->iid_preceptor = $iid_preceptor;
    }

    /**
     * Recupera el atributo sacta de Matricula
     *
     * @return string sacta
     */
    function getActa()
    {
        if (!isset($this->sacta) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sacta;
    }

    /**
     * Establece el valor del atributo sacta de Matricula
     *
     * @param string sacta='' optional
     */
    function setActa($sacta = '')
    {
        $this->sacta = $sacta;
    }

    /**
     * Recupera el atributo inota_num de PersonaNota
     *
     * @return integer inota_num
     */
    function getNota_num()
    {
        if (!isset($this->inota_num) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inota_num;
    }

    /**
     * Establece el valor del atributo inota_num de PersonaNota
     *
     * @param integer inota_num='' optional
     */
    function setNota_num($inota_num = '')
    {
        $this->inota_num = $inota_num;
    }

    /**
     * Recupera el atributo inota_max de PersonaNota
     *
     * @return integer inota_max
     */
    function getNota_max()
    {
        if (!isset($this->inota_max) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inota_max;
    }

    /**
     * Establece el valor del atributo inota_max de PersonaNota
     *
     * @param integer inota_max='' optional
     */
    function setNota_max($inota_max = '')
    {
        $this->inota_max = $inota_max;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    function getNotaSobre()
    {
        $nota_num = $this->getNota_num();
        $nota_max = $this->getNota_max();
        $nota_escala = empty($nota_num) ? '' : "$nota_num [$nota_max]";

        return $nota_escala;
    }

    /**
     * Recupera la nota en forma de text
     *
     * @return string snota_txt
     */
    function getNota_txt()
    {
        $nota_txt = 'Hollla';
        $id_situacion = $this->getId_situacion();
        switch ($id_situacion) {
            case '3': // Magna
                $nota_txt = 'Magna cum laude (8,6-9,5/10)';
                break;
            case '4': // Summa
                $nota_txt = 'Summa cum laude (9,6-10/10)';
                break;
            case '10': // Nota numérica
                $num = $this->getNota_num();
                $max = $this->getNota_max();
                $nota_txt = $num . '/' . $max;
                if ($max == 10) {
                    if ($num > 9.5) {
                        $nota_txt .= ' ' . _("Summa cum laude");
                    } elseif ($num > 8.5) {
                        $nota_txt .= ' ' . _("Magna cum laude");
                    }
                }
                break;
            default:
                $oNota = new Nota($id_situacion);
                $nota_txt = $oNota->getDescripcion();
                break;
        }
        return $nota_txt;
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oMatriculaSet = new Set();

        $oMatriculaSet->add($this->getDatosId_schema());
        $oMatriculaSet->add($this->getDatosId_nivel());
        $oMatriculaSet->add($this->getDatosId_situacion());
        $oMatriculaSet->add($this->getDatosPreceptor());
        $oMatriculaSet->add($this->getDatosNota_num());
        $oMatriculaSet->add($this->getDatosNota_max());
        $oMatriculaSet->add($this->getDatosActa());
        return $oMatriculaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_schema de Matricula
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_schema()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_schema'));
        $oDatosCampo->setEtiqueta(_("id_schema"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_nivel de Matricula
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_nivel()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nivel'));
        $oDatosCampo->setEtiqueta(_("id_nivel"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_situacion de Matricula
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_situacion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_situacion'));
        $oDatosCampo->setEtiqueta(_("id_situacion"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bpreceptor de Matricula
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPreceptor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'preceptor'));
        $oDatosCampo->setEtiqueta(_("preceptor"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_preceptor de Matricula
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_preceptor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_preceptor'));
        $oDatosCampo->setEtiqueta(_("nombre preceptor"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inota_num de PersonaNota
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNota_num()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nota_num'));
        $oDatosCampo->setEtiqueta(_("nota num"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inota_max de PersonaNota
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNota_max()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nota_max'));
        $oDatosCampo->setEtiqueta(_("nota max"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sacta de PersonaNota
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosActa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'acta'));
        $oDatosCampo->setEtiqueta(_("acta"));
        return $oDatosCampo;
    }
}