<?php
namespace usuarios\model\entity;

/**
 * Clase treballar amb Grups i Usuaris a l'hora.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 21/10/2010
 */
class GrupoOUsuario extends Grupo
{

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
    /**
     * @var int
     */
    private int $isfsv;

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregarTot($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_usuario)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
                $sClauError = 'Grupo.carregar';
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
            echo " i donçs";
            return false;
        }
    }


    /**
     * Recupera el atributo iid_usuario de Grupo
     *
     * @return integer iid_usuario
     */
    function getId_usuario()
    {
        if (!isset($this->iid_usuario)) {
            $this->DBCarregarTot();
        }
        return $this->iid_usuario;
    }

    /**
     * Establece el valor del atributo iid_usuario de Grupo
     *
     * @param integer iid_usuario
     */
    function setId_usuario($iid_usuario)
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     * Recupera el atributo susuario de Grupo
     *
     * @return string susuario
     */
    function getUsuario()
    {
        if (!isset($this->susuario)) {
            $this->DBCarregarTot();
        }
        return $this->susuario;
    }

    /**
     * Establece el valor del atributo susuario de Grupo
     *
     * @param string susuario='' optional
     */
    function setUsuario($susuario = '')
    {
        $this->susuario = $susuario;
    }

    /**
     * Recupera el atributo isfsv de Grupo
     *
     * @return integer isfsv
     */
    function getSfsv()
    {
        if (!isset($this->isfsv)) {
            $this->DBCarregarTot();
        }
        return $this->isfsv;
    }

    /**
     * Establece el valor del atributo isfsv de Grupo
     *
     * @param integer isfsv='' optional
     */
    function setSfsv($isfsv = '')
    {
        $this->isfsv = $isfsv;
    }
}

?>
