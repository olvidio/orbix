<?php

namespace actividadestudios\model;

use actividades\model\entity\ActividadAll;
use asignaturas\model\entity\Asignatura;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\Persona;
use src\configuracion\domain\entity\GestorDbSchema;
use web\Hash;
use web\Lista;

/**
 * Gestiona el dossier 3005: Asignaturas de una actividad.
 *
 *
 * @package    orbix
 * @subpackage    actividadestudios
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select3005
{
    /* @var $mwg_err string */
    private $msg_err;
    /* @var $a_valores array */
    private $a_valores;
    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private $txt_eliminar;
    private $txt_no_permiso;
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
    /**
     *
     * @var $dl_org string
     */
    private $dl_org;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;
    private string $LinkInsert;

    public function getBotones()
    {
        $a_botones = [];
        if ($this->permiso == 3) {
            $a_botones = array(
                array('txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"),
                array('txt' => _("quitar asignatura"), 'click' => "fnjs_borrar_asignatura(this.form)"),
            );
        }
        $a_botones[] = array('txt' => _("actas"), 'click' => "fnjs_actas(this.form)");
        return $a_botones;
    }

    public function getCabeceras()
    {
        $a_cabeceras = array(_("asignatura"),
            _("créditos"),
            _("tipo"),
            _("profesor"),
            _("prof. avisado"),
            _("inicio"),
            _("fin")
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

    private function getTabla()
    {
        $this->txt_eliminar = _("¿Está seguro que desea quitar esta asignatura?");
        $this->txt_no_permiso = _("No puede modificar una asignatura que depende de otra dl");

        $GesActivAsignaturas = new entity\GestorActividadAsignatura();
        $cActivAsignaturas = $GesActivAsignaturas->getActividadAsignaturas(array('id_activ' => $this->id_pau, '_ordre' => 'id_asignatura'));

        $mi_dele = ConfigGlobal::mi_delef();
        $gesDbSchemas = new GestorDbSchema();
        $c = 0;
        $a_valores = [];
        foreach ($cActivAsignaturas as $oActividadAsignatura) {
            $c++;
            $id_activ = $oActividadAsignatura->getId_activ();
            $id_asignatura = $oActividadAsignatura->getId_asignatura();
            $oAsignatura = new Asignatura($id_asignatura);
            $nombre_corto = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos();
            $id_schema = $oActividadAsignatura->getId_schema();
            $cDbSchemas = $gesDbSchemas->getDbSchemas(['id' => $id_schema]);
            $a_reg = explode('-', $cDbSchemas[0]->getSchema());
            $dl_matricula = substr($a_reg[1], 0, -1); // quito la v o la f.
            if ($dl_matricula != $this->dl_org) {
                $nombre_corto = "($dl_matricula) $nombre_corto";
            }
            if ($dl_matricula != $mi_dele) {
                $permiso = 'false';
            } else {
                $permiso = 'true';
            }

            $id_profesor = $oActividadAsignatura->getId_profesor();
            if (!empty($id_profesor)) {
                $oPersona = Persona::NewPersona($id_profesor);
                if (!is_object($oPersona)) {
                    $this->msg_err .= "<br>$oPersona con id_nom: $id_profesor (profesor) en  " . __FILE__ . ": line " . __LINE__;
                    $nom = '';
                } else {
                    $nom = $oPersona->getPrefApellidosNombre();
                }
            } else {
                $nom = '';
            }
            switch ($oActividadAsignatura->getAvis_profesor()) {
                case "a":
                    $aviso = _("avisado");
                    break;
                case "c":
                    $aviso = _("confirmado");
                    break;
                default:
                    $aviso = "";
            }
            $tipo = $oActividadAsignatura->getTipo();
            $f_ini = $oActividadAsignatura->getF_ini()->getFromLocal();
            $f_fin = $oActividadAsignatura->getF_fin()->getFromLocal();

            $a_valores[$c]['sel'] = "$id_activ#$id_asignatura#$permiso";
            $a_valores[$c][1] = "$nombre_corto";
            $a_valores[$c][2] = $creditos;
            $a_valores[$c][3] = $tipo;
            $a_valores[$c][4] = $nom;
            $a_valores[$c][5] = $aviso;
            $a_valores[$c][6] = $f_ini;
            $a_valores[$c][7] = $f_fin;
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
    }

    public function getHtml()
    {
        $oActividad = new ActividadAll($this->id_pau);
        $this->dl_org = $oActividad->getDl_org();
        // Finalmente hay que dar permiso a todos, porque pueden crear asignaturas para su dl
        $this->permiso = 3;

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
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        //Hay que ponerlo antes, para que calcule los chk.
        $oTabla = new Lista();
        $oTabla->setId_tabla('select3005');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        // para que genere las variables $aLink
        $this->setLinksInsert();

        $a_campos = ['oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => $this->LinkInsert,
            'txt_eliminar' => $this->txt_eliminar,
            'txt_no_permiso' => $this->txt_no_permiso,
            'bloque' => $this->bloque,
        ];

        $oView = new ViewPhtml(__NAMESPACE__);
        $oView->renderizar('select3005.phtml', $a_campos);
    }

    public function setLinksInsert()
    {
        $this->LinkInsert = '';
        if ($this->permiso === 3) {
            $a_dataUrl = array('pau' => $this->pau, 'id_pau' => $this->id_pau);
            // el hppt_build_query no pasa los valores null
            if (is_array($a_dataUrl)) {
                array_walk($a_dataUrl, 'core\poner_empty_on_null');
            }
            $this->LinkInsert = Hash::link(ConfigGlobal::getWeb() . "/apps/actividadestudios/controller/form_3005.php?" . http_build_query($a_dataUrl));
        }
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