<?php

namespace actividadestudios\model;

use actividades\model\entity\ActividadAll;
use actividadestudios\model\entity\GestorActividadAsignatura;
use actividadestudios\model\entity\GestorActividadAsignaturaDl;
use actividadestudios\model\entity\GestorMatriculaDl;
use asignaturas\model\entity\Asignatura;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\Persona;
use src\asignaturas\application\repositories\AsignaturaRepository;
use web\Hash;
use web\Lista;

/**
 * Gestiona el dossier 3103: Matriculas de una actividad.
 *
 *
 * @package    orbix
 * @subpackage    actividadestudios
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select3103
{
    /* @var $msg_err string */
    private $msg_err;
    /* @var $nom_activ string */
    private $nom_activ;
    /* @var $a_valores array */
    private $a_valores;
    /* @var $a_grupos array */
    private $a_grupos;
    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private $txt_eliminar;
    /* @var $bloque string  necesario para el script */
    private $bloque;

    // ---------- Variables requeridas
    /* @var $queSel integer */
    private $queSel;
    /* @var $id_dossier integer */
    private $id_dossier;
    /* @var $pau string */
    private $pau;
    /* @var $obj_pau string */
    private $obj_pau;
    /* @var $id_pau integer */
    private $id_pau;
    /**
     * 3: para todo, 2, 1:solo lectura
     * @var integer $permiso
     */
    private $permiso;

    private $status;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;

    /* @var $bloque string  necesario para el script */


    public function getBotones()
    {
        $a_botones = array(
            array('txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form)")
        );
        return $a_botones;
    }

    public function getCabeceras()
    {
        $a_cabeceras = array(_("asignatura"),
            _("alumno")
        );
        return $a_cabeceras;
    }

    public function getValores()
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    public function getGrupos()
    {
        if (empty($this->a_grupos)) {
            $this->getTabla();
        }
        return $this->a_grupos;
    }

    private function getTabla()
    {
        $mi_dele = ConfigGlobal::mi_delef();
        $oActividad = new ActividadAll($this->id_pau);
        $this->nom_activ = $oActividad->getNom_activ();
        $dl_org = $oActividad->getDl_org();

        if ($mi_dele == $dl_org) {
            $this->permiso = 3;
            $GesActivAsignaturas = new GestorActividadAsignaturaDl();
        } else {
            $this->permiso = 1;
            $GesActivAsignaturas = new GestorActividadAsignatura();
        }
        $cActividadAsignaturas = $GesActivAsignaturas->getActividadAsignaturas(array('id_activ' => $this->id_pau, '_ordre' => 'id_asignatura'));

        if (is_array($cActividadAsignaturas) && count($cActividadAsignaturas) == 0) {
            echo _("esta actividad no tiene ninguna asignatura");
            die();
        }
        // por cada asignatura
        $a = 0;
        $msg_err = '';
        $aGrupos = [];
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $a++;
            $id_asignatura = $oActividadAsignatura->getId_asignatura();
            $id_profesor = $oActividadAsignatura->getId_profesor();

            if (!empty($id_profesor)) {
                $oPersona = Persona::NewPersona($id_profesor);
                if (!is_object($oPersona)) {
                    $msg_err .= "<br>$oPersona con id_nom: $id_profesor (profesor) en  " . __FILE__ . ": line " . __LINE__;
                    $nom_profesor = '';
                } else {
                    $nom_profesor = $oPersona->getPrefApellidosNombre();
                }
            } else {
                $nom_profesor = '';
            }

            $oAsignatura = (new AsignaturaRepository())->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();
            $aGrupos[$id_asignatura] = $nombre_corto;

            //busco los matriculados:
            $GesMatriculas = new GestorMatriculaDl();
            $cMatriculas = $GesMatriculas->getMatriculas(array('id_activ' => $this->id_pau, 'id_asignatura' => $id_asignatura));
            $m = 0;
            $a_valores = [];
            foreach ($cMatriculas as $oMatricula) {
                $id_nom = $oMatricula->getId_nom();
                $oPersona = Persona::NewPersona($id_nom);
                if (!is_object($oPersona)) {
                    $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                    continue;
                }
                $nom_persona = $oPersona->getPrefApellidosNombre();
                $ctr = $oPersona->getCentro_o_dl();

                $a_valores[$id_asignatura][$m]['sel'] = "$id_nom#$id_asignatura";
                $a_valores[$id_asignatura][$m][1] = "$nombre_corto";
                $a_valores[$id_asignatura][$m][2] = "$nom_persona ($ctr)";
            }
        }
        if (!empty($a_valores)) {
            // Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
            // las pongo al final, porque al contar los valores del array se despista.
            if (isset($this->Qid_sel) && !empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (isset($this->Qscroll_id) && !empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }

        $this->a_valores = $a_valores;
        $this->a_grupos = $aGrupos;
    }

    public function getHtml()
    {
        $this->txt_eliminar = _("¿Está seguro que desea quitar esta matrícula?");

        $oHashSelect = new Hash();
        $oHashSelect->setCamposForm('');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => $this->permiso,
            'bloque' => $this->bloque,
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        //Hay que ponerlo antes, para que calcule los chk.
        $oTabla = new Lista();
        $oTabla->setGrupos($this->getGrupos());
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        $a_campos = [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'txt_eliminar' => $this->txt_eliminar,
            'nom_activ' => $this->nom_activ,
        ];

        $oView = new ViewPhtml('actividadestudios\model');
        $oView->renderizar('select3103.phtml', $a_campos);
    }

    public function getId_dossier()
    {
        return $this->id_dossier;
    }

    public function getPau()
    {
        return $this->pau;
    }

    public function getObj_pau()
    {
        return $this->obj_pau;
    }

    public function getId_pau()
    {
        return $this->id_pau;
    }

    public function getPermiso()
    {
        return $this->permiso;
    }

    public function setId_dossier($Qid_dossier)
    {
        $this->id_dossier = $Qid_dossier;
    }

    public function setPau($Qpau)
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau($Qobj_pau)
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau($Qid_pau)
    {
        $this->id_pau = $Qid_pau;
    }

    public function setPermiso($Qpermiso)
    {
        $this->permiso = $Qpermiso;
    }

    public function setQid_sel($Qid_sel)
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id($Qscroll_id)
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    public function setQueSel($queSel)
    {
        $this->queSel = $queSel;
    }


}
